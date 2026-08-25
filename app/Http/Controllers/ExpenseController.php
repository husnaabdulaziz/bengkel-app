<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Branch;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('branch')->latest('expense_date');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        }

        $expenses = $query->paginate(20)->withQueryString();
        $branches = auth()->user()->isSuperAdmin() ? Branch::all() : auth()->user()->branches;

        return view('expenses.index', compact('expenses', 'branches'));
    }

    public function create()
    {
        $branches = auth()->user()->isSuperAdmin() ? Branch::all() : auth()->user()->branches;
        return view('expenses.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        $validated['created_by'] = auth()->id();

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Pengeluaran berhasil dihapus.');
    }
}