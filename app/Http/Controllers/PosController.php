<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\WorkOrderItemTechnician;
use App\Models\ProductFee;
use App\Models\Warranty;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /** Tahap 1: form baru */
    public function create()
    {
        $branchId = session('active_branch_id') ?? auth()->user()->branches()->value('branches.id');
        $technicians = \App\Models\User::role('teknisi')->orderBy('name')->get(['id', 'name', 'inisial']);
        $drafts = $this->getOpenOrders($branchId);

        return view('pos.create', compact('branchId', 'technicians', 'drafts'));
    }

    /** AJAX: cari pelanggan by nama/telpon/plat */
    public function searchCustomer(Request $request)
    {
        $q = $request->get('q', '');

        $customers = Customer::where(function ($query) use ($q) {
                $query->where('nama', 'like', "%{$q}%")
                      ->orWhere('telpon', 'like', "%{$q}%")
                      ->orWhere('plat_nomor', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'nama', 'telpon', 'plat_nomor', 'alamat', 'jenis_kendaraan', 'merk_kendaraan', 'model_kendaraan']);

        return response()->json($customers);
    }

    /** AJAX: cari produk by nama untuk ditambahkan ke item */
    public function searchProduct(Request $request)
    {
        $q = $request->get('q', '');
        $branchId = $request->get('branch_id');

        $products = Product::where('nama', 'like', "%{$q}%")
            ->where('status', 'active')
            ->limit(10)
            ->get(['id', 'nama', 'satuan', 'harga_jual', 'harga_jual_jasa', 'harga_online', 'harga_ojol', 'is_jasa']);

        $result = $products->map(function ($p) use ($branchId) {
            $stock = ($p->is_jasa || !$branchId) ? null : $p->stockAtBranch((int) $branchId);
            return [
                'id' => $p->id,
                'nama' => $p->nama,
                'satuan' => $p->satuan,
                'harga_jual' => $p->harga_jual,
                'harga_jual_jasa' => $p->harga_jual_jasa,
                'harga_online' => $p->harga_online,
                'harga_ojol' => $p->harga_ojol,
                'is_jasa' => $p->is_jasa,
                'stock' => $stock,
                'out_of_stock' => !$p->is_jasa && $stock !== null && $stock <= 0,
            ];
        });

        return response()->json($result);
    }

    /** Simpan sebagai draft (tahap 1 -> stage draft) */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_id' => 'nullable|exists:customers,id',
            'new_customer.nama' => 'required_without:customer_id|string|max:150',
            'new_customer.telpon' => 'nullable|string|max:30',
            'new_customer.plat_nomor' => 'nullable|string|max:20',
            'new_customer.alamat' => 'nullable|string',
            'new_customer.jenis_kendaraan' => 'nullable|string|max:60',
            'new_customer.merk_kendaraan' => 'nullable|string|max:60',
            'new_customer.model_kendaraan' => 'nullable|string|max:60',
            'customer_price_tier' => 'required|in:harga_jual,harga_jual_jasa,harga_online,harga_ojol,custom',
            'items' => 'nullable|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'technician_ids' => 'nullable|array',
            'technician_ids.*' => 'exists:users,id',
            'manual_fee' => 'nullable',
        ]);

        $workOrder = DB::transaction(function () use ($validated) {
            $customerId = $validated['customer_id'] ?? null;

            if (!$customerId) {
                $customer = Customer::create([
                    'branch_id' => $validated['branch_id'],
                    'nama' => $validated['new_customer']['nama'],
                    'telpon' => $validated['new_customer']['telpon'] ?? null,
                    'plat_nomor' => $validated['new_customer']['plat_nomor'] ?? null,
                    'alamat' => $validated['new_customer']['alamat'] ?? null,
                    'jenis_kendaraan' => $validated['new_customer']['jenis_kendaraan'] ?? null,
                    'merk_kendaraan' => $validated['new_customer']['merk_kendaraan'] ?? null,
                    'model_kendaraan' => $validated['new_customer']['model_kendaraan'] ?? null,
                ]);
                $customerId = $customer->id;
            }

            $workOrder = WorkOrder::create([
                'branch_id' => $validated['branch_id'],
                'customer_id' => $customerId,
                'stage' => 'draft',
                'customer_price_tier' => $validated['customer_price_tier'],
                'created_by' => auth()->id(),
            ]);

            $isManualFee = !empty($validated['manual_fee']) && $validated['manual_fee'] != '0';
            $technicianIds = $validated['technician_ids'] ?? [];

            foreach ($validated['items'] ?? [] as $item) {
                $product = Product::find($item['product_id']);
                $woItem = WorkOrderItem::create([
                    'work_order_id' => $workOrder->id,
                    'product_id' => $product->id,
                    'item_name' => $product->nama,
                    'price_tier_used' => $validated['customer_price_tier'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['unit_price'] * $item['quantity'],
                    'manual_fee' => $isManualFee,
                ]);

                foreach ($technicianIds as $techId) {
                    WorkOrderItemTechnician::create([
                        'work_order_item_id' => $woItem->id,
                        'user_id' => $techId,
                        'fee_amount' => 0,
                    ]);
                }
            }

            $workOrder->recalculateTotal();

            return $workOrder;
        });

        return redirect()->route('pos.queue.show', $workOrder)->with('success', 'Draft servis berhasil disimpan.');
    }

    /** Helper: ambil daftar transaksi belum selesai (draft/queue), dipakai di sidebar */
    private function getOpenOrders(int $branchId, ?int $excludeId = null)
    {
        return WorkOrder::with('customer', 'items.technicians.technician')
            ->where('branch_id', $branchId)
            ->whereIn('stage', ['draft', 'queue'])
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->latest()
            ->get();
    }

    /** Tahap 2: halaman shell saja, data list di-load via JSON oleh Alpine */
    public function queue()
    {
        $branches = auth()->user()->isSuperAdmin() ? Branch::all() : auth()->user()->branches;
        return view('pos.queue', compact('branches'));
    }

    /** JSON: data list + counter, dipanggil Alpine via fetch */
    public function queueData(Request $request)
    {
        $branchIds = auth()->user()->isSuperAdmin()
            ? Branch::pluck('id')
            : auth()->user()->branches()->pluck('branches.id');

        $counts = [
            'all'       => WorkOrder::whereIn('branch_id', $branchIds)->count(),
            'draft'     => WorkOrder::whereIn('branch_id', $branchIds)->where('stage', 'draft')->count(),
            'queue'     => WorkOrder::whereIn('branch_id', $branchIds)->where('stage', 'queue')->count(),
            'completed' => WorkOrder::whereIn('branch_id', $branchIds)->where('stage', 'completed')->count(),
        ];

        $status = $request->get('status', 'all');
        $query = WorkOrder::whereIn('branch_id', $branchIds)->with('customer');

        if ($status !== 'all') {
            $query->where('stage', $status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('nama', 'like', "%{$search}%")
                         ->orWhere('plat_nomor', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = in_array((int) $request->get('per_page'), [10, 20, 50, 100]) ? (int) $request->get('per_page') : 10;
        $workOrders = $query->latest()->paginate($perPage)->withQueryString();

        $items = $workOrders->getCollection()->map(function ($wo) {
            return [
                'id' => $wo->id,
                'invoice_number' => $wo->invoice_number,
                'stage' => $wo->stage,
                'total_amount' => (float) $wo->total_amount,
                'customer_nama' => $wo->customer->nama,
                'plat_nomor' => $wo->customer->plat_nomor,
                'created_at' => $wo->created_at->format('d M Y'),
                'show_url' => route('pos.queue.show', $wo),
                'can_delete' => in_array($wo->stage, ['draft', 'queue']) || auth()->user()->can('delete_transaksi'),
                'delete_url' => route('pos.queue.destroy', $wo),
            ];
        });

        return response()->json([
            'counts' => $counts,
            'items' => $items,
            'current_page' => $workOrders->currentPage(),
            'last_page' => $workOrders->lastPage(),
            'total' => $workOrders->total(),
        ]);
    }

    /** Hapus transaksi yang belum jadi (draft/queue). Pelanggan ikut dihapus kalau tidak dipakai transaksi lain */
    public function destroy(WorkOrder $workOrder)
    {
        if (!in_array($workOrder->stage, ['draft', 'queue']) && !auth()->user()->can('delete_transaksi')) {
            return response()->json(['message' => 'Tidak bisa menghapus transaksi yang sudah selesai. Anda tidak punya izin.'], 422);
        }

        DB::transaction(function () use ($workOrder) {
            $customer = $workOrder->customer;
            $workOrder->forceDelete();

            if ($customer) {
                $stillUsed = WorkOrder::where('customer_id', $customer->id)->exists();
                if (!$stillUsed) {
                    $customer->forceDelete();
                }
            }
        });

        return response()->json(['message' => 'Transaksi dihapus. Catatan: stock yang sudah terpotong dari transaksi ini TIDAK otomatis dikembalikan — sesuaikan manual lewat Stock Opname kalau perlu.']);
    }

    /** Tahap 2: detail 1 work order, bisa tambah item */
    public function showQueue(WorkOrder $workOrder)
    {
        $workOrder->load('items.product', 'items.technicians.technician', 'customer');
        $assignedTechnicians = $workOrder->assignedTechnicians();
        $assignedTechnicianIds = $assignedTechnicians->pluck('id')->toArray();
        $currentManualFee = (bool) $workOrder->items->first()?->manual_fee;
        $technicians = \App\Models\User::role('teknisi')->orderBy('name')->get(['id', 'name', 'inisial']);
        $otherOrders = $this->getOpenOrders($workOrder->branch_id, $workOrder->id);

        return view('pos.queue-detail', compact('workOrder', 'assignedTechnicians', 'assignedTechnicianIds', 'currentManualFee', 'technicians', 'otherOrders'));
    }

    /** AJAX-style: tambah item ke work order yang sudah ada (masih di tahap draft/queue) */
    public function addItem(Request $request, WorkOrder $workOrder)
    {
        if (!in_array($workOrder->stage, ['draft', 'queue'])) {
            return back()->with('error', 'Item tidak bisa ditambahkan lagi pada tahap ini.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $product = Product::find($validated['product_id']);

        WorkOrderItem::create([
            'work_order_id' => $workOrder->id,
            'product_id' => $product->id,
            'item_name' => $product->nama,
            'price_tier_used' => $workOrder->customer_price_tier,
            'unit_price' => $validated['unit_price'],
            'quantity' => $validated['quantity'],
            'subtotal' => $validated['unit_price'] * $validated['quantity'],
        ]);

        $workOrder->recalculateTotal();

        return back()->with('success', 'Item ditambahkan.');
    }

    public function removeItem(WorkOrder $workOrder, WorkOrderItem $item)
    {
        if (!in_array($workOrder->stage, ['draft', 'queue'])) {
            return back()->with('error', 'Item tidak bisa dihapus pada tahap ini.');
        }

        $item->delete();
        $workOrder->recalculateTotal();

        return back()->with('success', 'Item dihapus.');
    }

    /** Update pilihan mekanik untuk seluruh item di work order ini (masih bisa diubah selama draft/queue) */
    public function updateTechnicians(Request $request, WorkOrder $workOrder)
    {
        if (!in_array($workOrder->stage, ['draft', 'queue'])) {
            return back()->with('error', 'Mekanik tidak bisa diubah lagi pada tahap ini.');
        }

        $validated = $request->validate([
            'technician_ids' => 'nullable|array',
            'technician_ids.*' => 'exists:users,id',
            'manual_fee' => 'nullable',
        ]);

        $isManualFee = !empty($validated['manual_fee']) && $validated['manual_fee'] != '0';
        $technicianIds = $validated['technician_ids'] ?? [];

        foreach ($workOrder->items as $item) {
            WorkOrderItemTechnician::where('work_order_item_id', $item->id)->delete();

            foreach ($technicianIds as $techId) {
                WorkOrderItemTechnician::create([
                    'work_order_item_id' => $item->id,
                    'user_id' => $techId,
                    'fee_amount' => 0,
                ]);
            }

            $item->update(['manual_fee' => $isManualFee]);
        }

        return back()->with('success', 'Mekanik berhasil diperbarui.');
    }

    /** Pindah dari draft -> queue (mulai diproses), sekaligus terima update mekanik terakhir */
    public function process(Request $request, WorkOrder $workOrder)
    {
        if ($workOrder->stage !== 'draft') {
            return back()->with('error', 'Work order ini bukan status draft.');
        }

        $validated = $request->validate([
            'technician_ids' => 'nullable|array',
            'technician_ids.*' => 'exists:users,id',
            'manual_fee' => 'nullable',
        ]);

        $isManualFee = !empty($validated['manual_fee']) && $validated['manual_fee'] != '0';
        $technicianIds = $validated['technician_ids'] ?? [];

        foreach ($workOrder->items as $item) {
            WorkOrderItemTechnician::where('work_order_item_id', $item->id)->delete();

            foreach ($technicianIds as $techId) {
                WorkOrderItemTechnician::create([
                    'work_order_item_id' => $item->id,
                    'user_id' => $techId,
                    'fee_amount' => 0,
                ]);
            }

            $item->update(['manual_fee' => $isManualFee]);
        }

        $workOrder->update(['stage' => 'queue']);

        return redirect()->route('pos.payment', $workOrder)->with('success', 'Servis mulai diproses, lanjut ke pembayaran.');
    }

    /** Tahap 3: form pembayaran */
    public function paymentForm(WorkOrder $workOrder)
    {
        if ($workOrder->stage !== 'queue') {
            return redirect()->route('pos.queue')->with('error', 'Work order ini belum siap dibayar.');
        }

        $workOrder->load('items', 'items.technicians.technician', 'customer');
        $assignedTechnicians = $workOrder->assignedTechnicians();
        $otherOrders = $this->getOpenOrders($workOrder->branch_id, $workOrder->id);

        return view('pos.payment', compact('workOrder', 'assignedTechnicians', 'otherOrders'));
    }

    /** Tahap 3: proses pembayaran, generate invoice, kurangi stock, buat garansi otomatis */
    public function confirmPayment(Request $request, WorkOrder $workOrder)
    {
        if ($workOrder->stage !== 'queue') {
            return back()->with('error', 'Work order ini belum siap dibayar.');
        }

        $validated = $request->validate([
            'discount_type' => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'payments' => 'required|array|min:1',
            'payments.*.method' => 'required|in:tunai,transfer,debit',
            'payments.*.amount' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($validated, $workOrder) {
            $workOrder->update([
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? 0,
            ]);
            $workOrder->recalculateTotal();
            $totalPayments = collect($validated['payments'])->sum('amount');
            if (round($totalPayments, 2) !== round($workOrder->total_amount, 2)) {
                throw new \Exception('Total pembayaran (Rp ' . number_format($totalPayments, 0, ',', '.') . ') tidak sama dengan tagihan (Rp ' . number_format($workOrder->total_amount, 0, ',', '.') . ').');
            }
            foreach ($workOrder->items as $item) {
                if ($item->product && !$item->product->is_jasa) {
                    StockMovement::create([
                        'branch_id' => $workOrder->branch_id,
                        'product_id' => $item->product_id,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'reference_type' => 'sale',
                        'reference_id' => $workOrder->id,
                        'notes' => 'Penjualan via POS',
                    ]);
                }

                // Auto-buat garansi kalau produk ini punya garansi aktif
                if ($item->product && $item->product->garansi_aktif && $item->product->garansi_durasi_hari) {
                    Warranty::create([
                        'branch_id' => $workOrder->branch_id,
                        'work_order_item_id' => $item->id,
                        'product_id' => $item->product_id,
                        'customer_id' => $workOrder->customer_id,
                        'kode_garansi' => 'GRS-' . now()->format('YmdHis') . '-' . $item->id,
                        'warranty_start_date' => now()->toDateString(),
                        'warranty_end_date' => now()->addDays($item->product->garansi_durasi_hari)->toDateString(),
                        'duration_days' => $item->product->garansi_durasi_hari,
                        'status' => 'active',
                    ]);
                }

                // Fee otomatis HANYA kalau: bukan manual_fee DAN cuma 1 teknisi ditugaskan
                $technicianAssignments = WorkOrderItemTechnician::where('work_order_item_id', $item->id)->get();

                if (!$item->manual_fee && $technicianAssignments->count() === 1) {
                    $productFee = ProductFee::where('product_id', $item->product_id)->first();
                    $feeAmount = $productFee ? $productFee->calculateFee($item->subtotal, $item->quantity) : 0;
                    $technicianAssignments->first()->update(['fee_amount' => $feeAmount]);
                }
            }

                $invoiceNumber = 'INV-' . $workOrder->branch_id . '-' . now()->format('Ymd') . '-' . str_pad($workOrder->id, 4, '0', STR_PAD_LEFT);

                // Rangkum jadi 1 label sederhana untuk kolom lama (dipakai badge/laporan ringkas)
                $methodsUsed = collect($validated['payments'])->pluck('method')->unique();
                $summaryMethod = $methodsUsed->count() > 1 ? 'campuran' : $methodsUsed->first();

                $workOrder->update([
                    'invoice_number' => $invoiceNumber,
                    'stage' => 'completed',
                    'payment_method' => $summaryMethod,
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                ]);

                foreach ($validated['payments'] as $payment) {
                    \App\Models\WorkOrderPayment::create([
                        'work_order_id' => $workOrder->id,
                        'payment_method' => $payment['method'],
                        'amount' => $payment['amount'],
                    ]);
                }

            $workOrder->customer->update(['last_visit_at' => now()]);
            \App\Models\ActivityLog::create([
            'company_id' => auth()->user()->company_id,
            'branch_id' => $workOrder->branch_id,
            'user_id' => auth()->id(),
            'action' => 'pos_payment',
            'model_type' => \App\Models\WorkOrder::class,
            'model_id' => $workOrder->id,
            'description' => "Transaksi {$workOrder->invoice_number} senilai Rp " . number_format($workOrder->total_amount, 0, ',', '.') . ' dibayar ' . $workOrder->payment_method,
            'ip_address' => request()->ip(),
        ]);
        });

        return redirect()->route('pos.queue')->with('success', 'Pembayaran berhasil.')->with('print_invoice_id', $workOrder->id);
    }

    /** Halaman invoice untuk dicetak */
    public function invoice(WorkOrder $workOrder)
    {
        $workOrder->load('items', 'customer', 'branch.company');
        return view('pos.invoice', compact('workOrder'));
    }
}