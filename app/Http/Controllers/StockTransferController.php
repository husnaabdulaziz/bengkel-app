<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index()
    {
        $branchId = $this->activeBranchId();
        $transfers = StockTransfer::with(['fromBranch', 'toBranch'])
            ->where(function ($q) use ($branchId) {
                $q->where('from_branch_id', $branchId)->orWhere('to_branch_id', $branchId);
            })
            ->latest()->paginate(20);
        return view('inventory.transfers.index', compact('transfers'));
    }

    public function create()
    {
        $branches = Branch::all();
        $products = Product::where('is_jasa', false)->orderBy('nama')->get(['id', 'nama', 'satuan']);
        return view('inventory.transfers.create', compact('branches', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_branch_id' => 'required|exists:branches,id|different:to_branch_id',
            'to_branch_id' => 'required|exists:branches,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty_requested' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $transfer = StockTransfer::create([
                'from_branch_id' => $validated['from_branch_id'],
                'to_branch_id' => $validated['to_branch_id'],
                'kode_transfer' => 'TR-' . now()->format('YmdHis'),
                'status' => 'requested',
                'requested_by' => auth()->id(),
                'requested_at' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'qty_requested' => $item['qty_requested'],
                ]);
            }
        });

        return redirect()->route('stock-transfers.index')->with('success', 'Permintaan transfer stock berhasil dibuat.');
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load('items.product', 'fromBranch', 'toBranch');
        return view('inventory.transfers.show', ['transfer' => $stockTransfer]);
    }

    /** Tahap 2: approve — cabang asal menyetujui, isi qty_approved (bisa beda dari qty_requested) */
    public function approve(Request $request, StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'requested') {
            return back()->with('error', 'Transfer ini sudah bukan status "requested".');
        }

        $validated = $request->validate([
            'qty_approved' => 'required|array',
            'qty_approved.*' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $stockTransfer) {
            foreach ($validated['qty_approved'] as $itemId => $qty) {
                StockTransferItem::where('id', $itemId)->where('stock_transfer_id', $stockTransfer->id)
                    ->update(['qty_approved' => $qty]);
            }
            $stockTransfer->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        });

        return back()->with('success', 'Transfer disetujui.');
    }

    /** Tahap 3: shipped — stock dikurangi dari cabang asal saat ini */
    public function ship(StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'approved') {
            return back()->with('error', 'Transfer ini belum disetujui.');
        }

        DB::transaction(function () use ($stockTransfer) {
            foreach ($stockTransfer->items as $item) {
                $qty = $item->qty_approved ?? $item->qty_requested;

                StockMovement::create([
                    'branch_id' => $stockTransfer->from_branch_id,
                    'product_id' => $item->product_id,
                    'type' => 'transfer_out',
                    'quantity' => $qty,
                    'reference_type' => 'transfer',
                    'reference_id' => $stockTransfer->id,
                    'notes' => 'Kirim transfer ' . $stockTransfer->kode_transfer,
                ]);

                $item->update(['qty_shipped' => $qty]);
            }

            $stockTransfer->update(['status' => 'shipped', 'shipped_by' => auth()->id(), 'shipped_at' => now()]);
        });

        return back()->with('success', 'Barang telah dikirim, stock cabang asal berkurang.');
    }

    /** Tahap 4: received — stock ditambah ke cabang tujuan */
    public function receive(StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'shipped') {
            return back()->with('error', 'Transfer ini belum dikirim.');
        }

        DB::transaction(function () use ($stockTransfer) {
            foreach ($stockTransfer->items as $item) {
                $qty = $item->qty_shipped;

                StockMovement::create([
                    'branch_id' => $stockTransfer->to_branch_id,
                    'product_id' => $item->product_id,
                    'type' => 'transfer_in',
                    'quantity' => $qty,
                    'reference_type' => 'transfer',
                    'reference_id' => $stockTransfer->id,
                    'notes' => 'Terima transfer ' . $stockTransfer->kode_transfer,
                ]);

                $item->update(['qty_received' => $qty]);
            }

            $stockTransfer->update(['status' => 'received', 'received_by' => auth()->id(), 'received_at' => now()]);
        });

        return back()->with('success', 'Barang telah diterima, stock cabang tujuan bertambah.');
    }
}