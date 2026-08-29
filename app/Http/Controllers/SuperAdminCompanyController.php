<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuperAdminCompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount(['users', 'branches'])->orderBy('nama_toko')->get();
        return view('super-admin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('super-admin.companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_toko' => 'required|string|max:150',
            'alamat_toko' => 'nullable|string',
            'telpon' => 'nullable|string|max:30',
            'email' => 'required|email|max:150',
            'logo' => 'nullable|image|max:2048',
            'nama_cabang_utama' => 'required|string|max:100',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $company = Company::create([
            'nama_toko' => $validated['nama_toko'],
            'alamat_toko' => $validated['alamat_toko'] ?? null,
            'telpon' => $validated['telpon'] ?? null,
            'email' => $validated['email'],
            'logo_path' => $validated['logo_path'] ?? null,
        ]);

        // Setiap toko baru otomatis dapat 1 cabang utama, supaya langsung bisa dipakai operasional
        Branch::create([
            'company_id' => $company->id,
            'nama_cabang' => $validated['nama_cabang_utama'],
            'is_main' => true,
        ]);

        return redirect()->route('super-admin.companies.index')->with('success', 'Toko baru berhasil dibuat beserta cabang utamanya.');
    }

    public function edit(Company $company)
    {
        return view('super-admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'nama_toko' => 'required|string|max:150',
            'alamat_toko' => 'nullable|string',
            'telpon' => 'nullable|string|max:30',
            'email' => 'required|email|max:150',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($validated);

        return redirect()->route('super-admin.companies.index')->with('success', 'Data toko berhasil diperbarui.');
    }
}