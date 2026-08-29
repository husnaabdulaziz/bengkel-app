<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount('users')->orderBy('nama_cabang')->get();
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_cabang' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'telpon' => 'nullable|string|max:30',
        ]);

        $validated['is_main'] = false;

        $branch = Branch::create($validated);

        // Tautkan otomatis semua user (Admin Toko & Karyawan Toko) di toko ini ke cabang baru,
        // supaya langsung terlihat di semua menu tanpa perlu diatur manual satu-satu.
        $companyUsers = \App\Models\User::where('company_id', auth()->user()->company_id)
            ->where('is_super_admin', false)
            ->get();

        foreach ($companyUsers as $user) {
            $user->branches()->syncWithoutDetaching([$branch->id]);
        }

        return redirect()->route('branches.index')->with('success', 'Cabang baru berhasil ditambahkan dan otomatis terhubung ke semua user toko ini.');
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'nama_cabang' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'telpon' => 'nullable|string|max:30',
        ]);

        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'Data cabang berhasil diperbarui.');
    }

    public function toggleActive(Branch $branch)
    {
        if ($branch->is_main) {
            return back()->with('error', 'Cabang utama tidak bisa dinonaktifkan.');
        }

        $branch->update(['is_active' => !$branch->is_active]);

        $status = $branch->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Cabang berhasil {$status}. Seluruh riwayat data tetap aman.");
    }

    public function confirmDelete(Branch $branch)
    {
        abort_unless(auth()->user()->is_super_admin, 403, 'Hapus permanen cabang hanya bisa dilakukan Super Admin.');

        if ($branch->is_main) {
            return back()->with('error', 'Cabang utama tidak bisa dihapus.');
        }

        return view('branches.confirm-delete', compact('branch'));
    }

    public function destroy(Request $request, Branch $branch)
    {
        abort_unless(auth()->user()->is_super_admin, 403, 'Hapus permanen cabang hanya bisa dilakukan Super Admin.');

        if ($branch->is_main) {
            return back()->with('error', 'Cabang utama tidak bisa dihapus.');
        }

        $request->validate([
            'confirmation' => 'required',
        ]);

        if ($request->confirmation !== $branch->nama_cabang) {
            return back()->with('error', 'Nama cabang yang diketik tidak cocok. Penghapusan dibatalkan.');
        }

        $branch->delete();

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil dihapus permanen beserta seluruh riwayat datanya.');
    }
}