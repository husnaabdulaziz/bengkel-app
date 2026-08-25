<?php

namespace App\Http\Controllers;

use App\Models\CashClosing;
use App\Models\WorkOrder;
use App\Models\Expense;
use App\Models\Branch;
use Illuminate\Http\Request;

class CashClosingController extends Controller
{
    /** Halaman kerja hari ini (buka kas / lihat status / tutup kas) */
    public function today(Request $request)
    {
        $branchId = $request->get('branch_id') ?? auth()->user()->branches()->value('branches.id');
        $branches = auth()->user()->isSuperAdmin() ? Branch::all() : auth()->user()->branches;

        $closing = CashClosing::where('branch_id', $branchId)
            ->where('closing_date', now()->toDateString())
            ->first();

        if ($closing) {
            $this->refreshComputedFields($closing);
        }

        return view('cash-closings.today', compact('closing', 'branches', 'branchId'));
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
            'actual_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $this->refreshComputedFields($cashClosing);

        $difference = $validated['actual_balance'] - $cashClosing->expected_balance;

        $cashClosing->update([
            'actual_balance' => $validated['actual_balance'],
            'difference' => $difference,
            'notes' => $validated['notes'] ?? null,
            'status' => 'closed',
            'closed_by' => auth()->id(),
            'closed_at' => now(),
        ]);

        return redirect()->route('cash-closings.today', ['branch_id' => $cashClosing->branch_id])
            ->with('success', 'Kas hari ini berhasil ditutup.');
    }

    /** Riwayat penutupan kas */
    public function index(Request $request)
    {
        $query = CashClosing::with('branch')->latest('closing_date');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $closings = $query->paginate(20)->withQueryString();
        $branches = auth()->user()->isSuperAdmin() ? Branch::all() : auth()->user()->branches;

        return view('cash-closings.index', compact('closings', 'branches'));
    }

    /** Hitung ulang cash_sales, cash_expenses, expected_balance berdasarkan data real-time */
    private function refreshComputedFields(CashClosing $closing): void
    {
        $cashSales = WorkOrder::where('branch_id', $closing->branch_id)
            ->where('stage', 'completed')
            ->where('payment_method', 'tunai')
            ->whereDate('paid_at', $closing->closing_date)
            ->sum('total_amount');

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