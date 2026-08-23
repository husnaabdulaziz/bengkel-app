<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('nama')->paginate(20);
        return view('master.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'telpon' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
        ]);

        Supplier::create($validated);

        return back()->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'telpon' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return back()->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return back()->with('success', 'Supplier berhasil dihapus.');
    }
}