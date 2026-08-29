<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanySwitchController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->is_super_admin, 403);
        $companies = Company::orderBy('nama_toko')->get();
        return view('super-admin.switch-company', compact('companies'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->is_super_admin, 403);
        $validated = $request->validate(['company_id' => 'required|exists:companies,id']);

        session(['acting_company_id' => $validated['company_id']]);

        return redirect()->route('dashboard')->with('success', 'Sekarang bertindak sebagai toko: ' . Company::find($validated['company_id'])->nama_toko);
    }

    public function clear()
    {
        session()->forget('acting_company_id');
        return redirect()->route('super-admin.users.index')->with('success', 'Keluar dari mode "Bertindak Sebagai Toko".');
    }
}