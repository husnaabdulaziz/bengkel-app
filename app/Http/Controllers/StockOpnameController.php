<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductBrand;
use App\Models\StockMovement;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StockOpnameController extends Controller
{
    public function index()
    {
        $opnames = StockOpname::with('branch')
        ->where('branch_id', $this->activeBranchId())
        ->latest()->paginate(20);
        return view('inventory.opnames.index', compact('opnames'));
    }

    public function create()
    {
        $branches = auth()->user()->isSuperAdmin() ? Branch::all() : auth()->user()->branches;
        $categories = ProductCategory::orderBy('nama')->get();
        $brands = ProductBrand::with('categories')->orderBy('nama')->get();

        return view('inventory.opnames.create', compact('branches', 'categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:product_brands,id',
            'opname_date' => 'required|date',
        ]);

        $query = Product::query();
        if ($validated['category_id'] ?? null) $query->where('category_id', $validated['category_id']);
        if ($validated['brand_id'] ?? null) $query->where('brand_id', $validated['brand_id']);
        $products = $query->where('is_jasa', false)->get();

        if ($products->isEmpty()) {
            return back()->with('error', 'Tidak ada produk yang cocok dengan filter tersebut.');
        }

        $opname = DB::transaction(function () use ($validated, $products) {
            $opname = StockOpname::create([
                'branch_id' => $validated['branch_id'],
                'kode_opname' => 'SO-' . now()->format('YmdHis'),
                'opname_date' => $validated['opname_date'],
                'category_id' => $validated['category_id'] ?? null,
                'brand_id' => $validated['brand_id'] ?? null,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($products as $product) {
                $systemStock = $product->stockAtBranch($validated['branch_id']);
                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'product_id' => $product->id,
                    'system_stock' => $systemStock,
                    'real_stock' => $systemStock,
                ]);
            }

            return $opname;
        });

        return redirect()->route('stock-opnames.edit', $opname)->with('success', 'Draft opname dibuat, silakan input stock real.');
    }

    public function edit(StockOpname $stockOpname)
    {
        $stockOpname->load('items.product.category', 'items.product.subcategory', 'items.product.brand', 'branch');
        return view('inventory.opnames.edit', ['opname' => $stockOpname]);
    }

    public function update(Request $request, StockOpname $stockOpname)
    {
        $validated = $request->validate([
            'real_stock' => 'required|array',
            'real_stock.*' => 'required|integer|min:0',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string|max:255',
        ]);

        foreach ($validated['real_stock'] as $itemId => $realStock) {
            StockOpnameItem::where('id', $itemId)
                ->where('stock_opname_id', $stockOpname->id)
                ->update([
                    'real_stock' => $realStock,
                    'notes' => $validated['notes'][$itemId] ?? null,
                ]);
        }

        return back()->with('success', 'Stock real berhasil disimpan. Cek selisih sebelum menyesuaikan stock.');
    }

    public function adjust(StockOpname $stockOpname)
    {
        if ($stockOpname->is_adjusted) {
            return back()->with('error', 'Opname ini sudah pernah disesuaikan sebelumnya.');
        }

        DB::transaction(function () use ($stockOpname) {
            foreach ($stockOpname->items as $item) {
                $diff = $item->real_stock - $item->system_stock;

                if ($diff === 0) continue;

                StockMovement::create([
                    'branch_id' => $stockOpname->branch_id,
                    'product_id' => $item->product_id,
                    'type' => $diff > 0 ? 'adjustment_in' : 'adjustment_out',
                    'quantity' => abs($diff),
                    'reference_type' => 'opname',
                    'reference_id' => $stockOpname->id,
                    'notes' => 'Penyesuaian stock opname ' . $stockOpname->kode_opname,
                ]);
            }

            $stockOpname->update([
                'status' => 'completed',
                'is_adjusted' => true,
                'completed_at' => now(),
            ]);
        });

        return redirect()->route('stock-opnames.index')->with('success', 'Stock berhasil disesuaikan otomatis.');
    }
    public function pdf(StockOpname $stockOpname, Request $request)
    {
        $stockOpname->load('items.product.category', 'items.product.subcategory', 'items.product.brand', 'branch');

        $pdf = Pdf::loadView('inventory.opnames.pdf', [
            'opname' => $stockOpname,
            'showSku' => $request->boolean('sku'),
            'showKategori' => $request->boolean('kategori'),
            'showSubkategori' => $request->boolean('subkategori'),
            'showBrand' => $request->boolean('brand'),
            'showLokasi' => $request->boolean('lokasi'),
        ]);

        return $pdf->stream($stockOpname->kode_opname . '.pdf');
    }

    public function destroy(\App\Models\StockOpname $stockOpname)
    {
        abort_unless(auth()->user()->can('delete_stock_opname'), 403, 'Anda tidak punya izin menghapus riwayat stock opname.');

        $stockOpname->items()->delete();
        $stockOpname->delete();

        return redirect()->route('stock-opnames.index')->with('success', 'Riwayat stock opname berhasil dihapus.');
    }
}