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
        abort_unless(auth()->user()->can('delete_vendor'), 403, 'Anda tidak punya izin menghapus vendor.');
        $supplier->delete();
        return back()->with('success', 'Supplier berhasil dihapus.');
    }
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
        ]);

        $supplier = Supplier::create(['nama' => $validated['nama']]);

        return response()->json(['id' => $supplier->id, 'nama' => $supplier->nama]);
    }
}