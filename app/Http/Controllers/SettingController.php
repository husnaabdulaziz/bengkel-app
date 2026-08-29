<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    private array $colorDefaults = [
    'primary_color'      => '#007bff',
    'link_color'         => '#007bff',
    'active_menu_color'  => '#ffc107',
    'sidebar_color'      => '#343a40',
    'success_color'      => '#28a745',
    'danger_color'       => '#dc3545',
    'warning_color'      => '#ffc107',
    'hover_color'        => '#f4f4f4',
    'brand_bg_color'     => '#ffc107',
    'brand_text_color'   => '#1f2d3d',
];

    public function edit()
    {
        if (!auth()->user()->company_id) {
        return redirect()->route('super-admin.users.index')->with('error', 'Super Admin tidak terikat ke toko manapun, tidak bisa mengakses Pengaturan Toko.');
        }
        $company = auth()->user()->company;

        $printerPaperSize = StoreSetting::where('company_id', $company->id)
            ->whereNull('branch_id')
            ->where('setting_key', 'printer_paper_size')
            ->value('setting_value') ?? '80mm';

        $savedColors = StoreSetting::where('company_id', $company->id)
            ->whereNull('branch_id')
            ->whereIn('setting_key', array_keys($this->colorDefaults))
            ->pluck('setting_value', 'setting_key');

        $colors = array_merge($this->colorDefaults, $savedColors->toArray());

        return view('settings.edit', compact('company', 'printerPaperSize', 'colors'));
    }

    public function update(Request $request)
    {
        if (!auth()->user()->company_id) {
            return redirect()->route('super-admin.users.index')->with('error', 'Super Admin tidak terikat ke toko manapun, tidak bisa mengakses Pengaturan Toko.');
        }
        $company = auth()->user()->company;

        $validated = $request->validate([
            'nama_toko' => 'required|string|max:150',
            'alamat_toko' => 'nullable|string',
            'telpon' => 'nullable|string|max:30',
            'email' => 'required|email|max:150',
            'logo' => 'nullable|image|max:2048',
            'printer_paper_size' => 'required|in:58mm,80mm,A4',
            'primary_color' => 'required|string|max:7',
            'link_color' => 'required|string|max:7',
            'active_menu_color' => 'required|string|max:7',
            'sidebar_color' => 'required|string|max:7',
            'success_color' => 'required|string|max:7',
            'danger_color' => 'required|string|max:7',
            'warning_color' => 'required|string|max:7',
            'hover_color' => 'required|string|max:7',
            'brand_bg_color' => 'required|string|max:7',
            'brand_text_color' => 'required|string|max:7',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        $company->update([
            'nama_toko' => $validated['nama_toko'],
            'alamat_toko' => $validated['alamat_toko'] ?? null,
            'telpon' => $validated['telpon'] ?? null,
            'email' => $validated['email'],
            'logo_path' => $validated['logo_path'] ?? $company->logo_path,
        ]);

        StoreSetting::updateOrCreate(
            ['company_id' => $company->id, 'branch_id' => null, 'setting_key' => 'printer_paper_size'],
            ['setting_value' => $validated['printer_paper_size']]
        );

        foreach (array_keys($this->colorDefaults) as $key) {
            StoreSetting::updateOrCreate(
                ['company_id' => $company->id, 'branch_id' => null, 'setting_key' => $key],
                ['setting_value' => $validated[$key]]
            );
        }

        return back()->with('success', 'Pengaturan toko berhasil disimpan.');
    }
}