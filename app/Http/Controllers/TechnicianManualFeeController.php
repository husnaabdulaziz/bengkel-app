<?php

namespace App\Http\Controllers;

use App\Models\TechnicianManualFee;
use App\Models\User;
use Illuminate\Http\Request;

class TechnicianManualFeeController extends Controller
{
    public function index(Request $request)
    {
        $query = TechnicianManualFee::with(['technician', 'product'])->latest('transaction_date');

        if ($request->filled('technician_id')) {
            $query->where('user_id', $request->technician_id);
        }

        $fees = $query->paginate(20)->withQueryString();
        $technicians = User::role('teknisi')->orderBy('name')->get();

        return view('pos.manual-fees.index', compact('fees', 'technicians'));
    }

    public function create()
    {
        $technicians = User::role('teknisi')->orderBy('name')->get();
        return view('pos.manual-fees.create', compact('technicians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'product_id' => 'nullable|exists:products,id',
            'fee_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        TechnicianManualFee::create($validated);

        return redirect()->route('technician-manual-fees.index')->with('success', 'Fee manual berhasil disimpan.');
    }
    public function destroy(\App\Models\TechnicianManualFee $technicianManualFee)
    {
        $technicianManualFee->delete();
        return back()->with('success', 'Data fee manual dihapus.');
    }
    public function edit(\App\Models\TechnicianManualFee $technicianManualFee)
    {
        $technicians = \App\Models\User::role('teknisi')->orderBy('name')->get();
        return view('pos.manual-fees.edit', ['fee' => $technicianManualFee, 'technicians' => $technicians]);
    }


}