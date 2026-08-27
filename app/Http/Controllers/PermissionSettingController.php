<?php

namespace App\Http\Controllers;

use App\Support\MenuPermissions;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSettingController extends Controller
{
    public function edit()
    {
        $companyId = auth()->user()->company_id;
        app(PermissionRegistrar::class)->setPermissionsTeamId($companyId);

        $karyawanRole = Role::where('name', 'karyawan_toko')->where('company_id', $companyId)->first();
        $currentPermissions = $karyawanRole ? $karyawanRole->permissions->pluck('name')->toArray() : [];

        return view('settings.karyawan-access', [
            'menuList' => MenuPermissions::LIST,
            'currentPermissions' => $currentPermissions,
        ]);
    }

    public function update(Request $request)
    {
        $companyId = auth()->user()->company_id;
        app(PermissionRegistrar::class)->setPermissionsTeamId($companyId);

        $selected = array_intersect($request->input('permissions', []), array_keys(MenuPermissions::LIST));

        $karyawanRole = Role::where('name', 'karyawan_toko')->where('company_id', $companyId)->first();
        $karyawanRole?->syncPermissions($selected);

        return back()->with('success', 'Hak akses Karyawan Toko berhasil diperbarui.');
    }
}