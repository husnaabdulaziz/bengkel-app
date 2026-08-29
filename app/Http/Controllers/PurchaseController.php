<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Product;
use App\Http\Controllers\LowStockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'branch'])
            ->where('branch_id', $this->activeBranchId())
            ->latest()->paginate(20);
        return view('inventory.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('nama')->get();
        $products = Product::orderBy('nama')->get(['id', 'nama', 'satuan', 'harga_modal']);
        $branches = auth()->user()->isSuperAdmin()
            ? \App\Models\Branch::all()
            : auth()->user()->branches;
        return view('inventory.purchases.create', compact('suppliers', 'products', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|max:60',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_per_unit' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $total = collect($validated['items'])->sum(fn ($i) => $i['quantity'] * $i['price_per_unit']);
            $purchase = Purchase::create([
                'branch_id' => $validated['branch_id'],
                'supplier_id' => $validated['supplier_id'],
                'invoice_number' => $validated['invoice_number'],
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $total,
                'status' => 'completed',
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $subtotal = $item['quantity'] * $item['price_per_unit'];
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_per_unit' => $item['price_per_unit'],
                    'subtotal' => $subtotal,
                ]);
                StockMovement::create([
                    'branch_id' => $validated['branch_id'],
                    'product_id' => $item['product_id'],
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'notes' => 'Pembelian dari supplier #' . $purchase->invoice_number,
                ]);
            }
        });

        return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil dicatat, stock otomatis bertambah.');
    }

    /** Halaman "Buat PO" — daftar item stock menipis, dikelompokkan per Vendor */
    public function poBuilder()
    {
        $branchId = auth()->user()->isSuperAdmin()
            ? \App\Models\Branch::first()?->id
            : auth()->user()->branches()->value('branches.id');

        $lowStockItems = LowStockController::getLowStockItems();

        $itemsByVendor = $lowStockItems
            ->filter(fn ($item) => $item->product->default_supplier_id)
            ->groupBy('product.default_supplier_id')
            ->map(function ($items, $vendorId) {
                return [
                    'vendor' => Supplier::find($vendorId),
                    'items' => $items->values(),
                ];
            });

        return view('inventory.purchases.create-po', compact('itemsByVendor', 'branchId'));
    }

    /** Simpan PO — status "pending", stock BELUM ditambah sampai barang benar-benar diterima */
    public function storePO(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'vendors' => 'required|array',
        ]);

        $createdCount = 0;

        DB::transaction(function () use ($validated, &$createdCount) {
            foreach ($validated['vendors'] as $vendorId => $group) {
                $selectedItems = collect($group['items'] ?? [])
                    ->filter(fn ($item) => !empty($item['checked']) && (int) ($item['qty'] ?? 0) > 0);

                if ($selectedItems->isEmpty()) {
                    continue;
                }

                $purchase = Purchase::create([
                    'branch_id' => $validated['branch_id'],
                    'supplier_id' => $vendorId,
                    'invoice_number' => null,
                    'purchase_date' => now()->toDateString(),
                    'total_amount' => 0,
                    'status' => 'pending',
                    'created_by' => auth()->id(),
                ]);

                $total = 0;
                foreach ($selectedItems as $item) {
                    $product = Product::find($item['product_id']);
                    $qty = (int) $item['qty'];
                    $pricePerUnit = isset($item['price']) && $item['price'] !== '' ? (float) $item['price'] : ($product->harga_modal ?? 0);
                    $subtotal = $qty * $pricePerUnit;
                    $total += $subtotal;

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $qty,
                        'price_per_unit' => $pricePerUnit,
                        'subtotal' => $subtotal,
                    ]);
                }

                $purchase->update(['total_amount' => $total]);
                $createdCount++;
            }
        });

        if ($createdCount === 0) {
            return back()->with('error', 'Tidak ada item yang dipilih untuk dibuatkan PO.');
        }

        return redirect()->route('purchases.index')->with('success', "{$createdCount} PO berhasil dibuat, menunggu barang diterima.");
    }

    /** Form untuk terima barang (isi invoice_number asli dari vendor), baru stock bertambah */
    public function showReceive(Purchase $purchase)
    {
        abort_if($purchase->status !== 'pending', 404);
        $purchase->load('items.product', 'supplier');
        return view('inventory.purchases.receive', compact('purchase'));
    }

    public function receive(Request $request, Purchase $purchase)
    {
        abort_if($purchase->status !== 'pending', 404);

        $validated = $request->validate([
            'invoice_number' => 'required|string|max:60',
            'prices' => 'nullable|array',
        ]);

        DB::transaction(function () use ($purchase, $validated) {
            $prices = $validated['prices'] ?? [];
            $newTotal = 0;

            foreach ($purchase->items as $item) {
                if (isset($prices[$item->id]) && $prices[$item->id] !== '') {
                    $newPrice = (float) $prices[$item->id];
                    $newSubtotal = $newPrice * $item->quantity;

                    $item->update([
                        'price_per_unit' => $newPrice,
                        'subtotal' => $newSubtotal,
                    ]);

                    // Harga Modal produk ikut ter-update ke harga terbaru dari vendor
                    $item->product->update(['harga_modal' => $newPrice]);
                }

                $newTotal += $item->subtotal;

                StockMovement::create([
                    'branch_id' => $purchase->branch_id,
                    'product_id' => $item->product_id,
                    'type' => 'in',
                    'quantity' => $item->quantity,
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'notes' => 'Pembelian dari supplier #' . $validated['invoice_number'],
                ]);
            }

            $purchase->update([
                'invoice_number' => $validated['invoice_number'],
                'status' => 'completed',
                'total_amount' => $newTotal,
            ]);
        });

        return redirect()->route('purchases.index')->with('success', 'Barang diterima, stock dan Harga Modal produk otomatis ter-update.');
    }
}