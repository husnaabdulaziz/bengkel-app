<?php

namespace App\Http\Controllers;

use App\Models\CashClosing;
use App\Models\CashClosingDenomination;
use App\Models\WorkOrder;
use App\Models\Expense;
use App\Models\Branch;
use Illuminate\Http\Request;

class CashClosingController extends Controller
{
    /** Halaman kerja hari ini (buka kas / lihat status / tutup kas) */
    public function today(Request $request)
    {
        $branchId = $request->get('branch_id', $this->activeBranchId());
        $branches = auth()->user()->isSuperAdmin() ? Branch::all() : auth()->user()->branches;

        $closing = CashClosing::with('denominations')
            ->where('branch_id', $branchId)
            ->where('closing_date', now()->toDateString())
            ->first();

        if ($closing && $closing->status === 'open') {
            $this->refreshComputedFields($closing);
        }

        return view('cash-closings.today', compact('closing', 'branches', 'branchId'));
    }

    public function reopen(CashClosing $cashClosing)
    {
        if ($cashClosing->status !== 'closed') {
            return back()->with('error', 'Kas ini belum ditutup.');
        }

        $cashClosing->update([
            'status' => 'open',
            'actual_balance' => null,
            'difference' => null,
            'closed_by' => null,
            'closed_at' => null,
        ]);

        return redirect()->route('cash-closings.today', ['branch_id' => $cashClosing->branch_id])
            ->with('success', 'Kas berhasil dibuka kembali.');
    }

    /** Buka kas: input saldo awal / uang kecil */
    public function open(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        $existing = CashClosing::where('branch_id', $validated['branch_id'])
            ->where('closing_date', now()->toDateString())
            ->first();

        if ($existing) {
            return back()->with('error', 'Kas untuk hari ini sudah dibuka sebelumnya.');
        }

        CashClosing::create([
            'branch_id' => $validated['branch_id'],
            'closing_date' => now()->toDateString(),
            'opening_balance' => $validated['opening_balance'],
            'status' => 'open',
            'opened_by' => auth()->id(),
        ]);

        \App\Models\ActivityLog::create([
            'company_id' => auth()->user()->company_id,
            'branch_id' => $validated['branch_id'],
            'user_id' => auth()->id(),
            'action' => 'cash_open',
            'description' => 'Membuka kas dengan saldo awal Rp ' . number_format($validated['opening_balance'], 0, ',', '.'),
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('cash-closings.today', ['branch_id' => $validated['branch_id']])
            ->with('success', 'Kas hari ini berhasil dibuka.');
    }

    /** Tutup kas: input uang fisik hasil hitung, hitung selisih */
    public function close(Request $request, CashClosing $cashClosing)
    {
        if ($cashClosing->status === 'closed') {
            return back()->with('error', 'Kas ini sudah ditutup sebelumnya.');
        }

        $validated = $request->validate([
            'denominations' => 'required|array',
            'denominations.*' => 'nullable|integer|min:0',
            'reserved' => 'nullable|array',
            'reserved.*' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $reserved = $validated['reserved'] ?? [];

        // Lembar "untuk besok" tidak boleh melebihi jumlah lembar yang dihitung per pecahan
        foreach ($validated['denominations'] as $denom => $count) {
            $r = (int) ($reserved[$denom] ?? 0);
            if ($r > (int) ($count ?? 0)) {
                return back()->withInput()->with(
                    'error',
                    'Jumlah lembar "Untuk Besok" tidak boleh melebihi Jumlah Lembar (pecahan Rp ' . number_format($denom, 0, ',', '.') . ').'
                );
            }
        }

        $actualBalance = 0;
        foreach ($validated['denominations'] as $denom => $count) {
            $actualBalance += $denom * (int) ($count ?? 0);
        }

        $this->refreshComputedFields($cashClosing);

        $difference = $actualBalance - $cashClosing->expected_balance;

        $cashClosing->update([
            'actual_balance' => $actualBalance,
            'difference' => $difference,
            'notes' => $validated['notes'] ?? null,
            'status' => 'closed',
            'closed_by' => auth()->id(),
            'closed_at' => now(),
        ]);

        // Hapus rincian pecahan lama (kalau ini hasil reopen + tutup ulang) sebelum simpan yang baru
        CashClosingDenomination::where('cash_closing_id', $cashClosing->id)->delete();

        foreach ($validated['denominations'] as $denom => $count) {
            $c = (int) ($count ?? 0);
            $r = (int) ($reserved[$denom] ?? 0);
            if ($c > 0 || $r > 0) {
                CashClosingDenomination::create([
                    'cash_closing_id' => $cashClosing->id,
                    'denomination' => $denom,
                    'count' => $c,
                    'reserved_for_next_day' => $r,
                ]);
            }
        }

        return redirect()->route('cash-closings.today', ['branch_id' => $cashClosing->branch_id])
            ->with('success', 'Kas hari ini berhasil ditutup.');
    }

    /** Riwayat penutupan kas */
    public function index(Request $request)
    {
        $query = CashClosing::with(['branch', 'denominations'])->latest('closing_date');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $closings = $query->paginate(20)->withQueryString();
        $branches = auth()->user()->isSuperAdmin() ? Branch::all() : auth()->user()->branches;

        return view('cash-closings.index', compact('closings', 'branches'));
    }

    /** Rincian uang masuk kamar per pecahan, per tanggal, dengan filter range tanggal */
    public function kamarReport(Request $request)
    {
        $branches = auth()->user()->isSuperAdmin() ? Branch::all() : auth()->user()->branches;

        // Default: pakai cabang aktif yang sedang dipilih di sistem, bukan "Semua Cabang"
        $branchId = $request->get('branch_id', $this->activeBranchId());
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        $query = CashClosing::with(['branch', 'denominations'])
            ->whereNotNull('actual_balance')
            ->whereBetween('closing_date', [$dateFrom, $dateTo])
            ->orderBy('closing_date');

        // branch_id=all (dari opsi "Semua Cabang") berarti tidak difilter per cabang
        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        } elseif ($branchId === 'all' && !auth()->user()->isSuperAdmin()) {
            $query->whereIn('branch_id', auth()->user()->branches->pluck('id'));
        }

        $closings = $query->get();

        $denominations = CashClosingDenomination::DENOMINATIONS;

        // Susun baris per tanggal: [denom => jumlah lembar ke kamar] + total rupiah ke kamar
        $rows = $closings->map(function ($closing) use ($denominations) {
            $byDenom = $closing->denominations->keyBy('denomination');

            $columns = [];
            $totalKamar = 0;
            foreach ($denominations as $denom) {
                $d = $byDenom->get($denom);
                $count = $d ? $d->kamar_count : 0;
                $columns[$denom] = $count;
                $totalKamar += $count * $denom;
            }

            return [
                'closing_date' => $closing->closing_date,
                'branch' => $closing->branch->nama_cabang ?? '-',
                'columns' => $columns,
                'total' => $totalKamar,
            ];
        });

        $grandTotals = [];
        foreach ($denominations as $denom) {
            $grandTotals[$denom] = $rows->sum(fn($r) => $r['columns'][$denom]);
        }
        $grandTotalAmount = $rows->sum('total');

        return view('cash-closings.kamar-report', compact(
            'rows', 'denominations', 'grandTotals', 'grandTotalAmount',
            'branches', 'branchId', 'dateFrom', 'dateTo'
        ));
    }

    /** Hitung ulang cash_sales, cash_expenses, expected_balance berdasarkan data real-time */
    private function refreshComputedFields(CashClosing $closing): void
    {
        $cashSales = \App\Models\WorkOrderPayment::where('payment_method', 'tunai')
            ->whereHas('workOrder', function ($q) use ($closing) {
                $q->where('branch_id', $closing->branch_id)
                ->where('stage', 'completed')
                ->whereDate('paid_at', $closing->closing_date);
            })
            ->sum('amount');

        $cashExpenses = Expense::where('branch_id', $closing->branch_id)
            ->whereDate('expense_date', $closing->closing_date)
            ->sum('amount');

        $expectedBalance = $closing->opening_balance + $cashSales - $cashExpenses;

        $closing->update([
            'cash_sales' => $cashSales,
            'cash_expenses' => $cashExpenses,
            'expected_balance' => $expectedBalance,
        ]);

        $closing->refresh();
    }
}