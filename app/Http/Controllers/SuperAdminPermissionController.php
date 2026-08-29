<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Support\MenuPermissions;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminPermissionController extends Controller
{
    public function edit(Request $request)
    {
        $companies = Company::orderBy('nama_toko')->get();
        $companyId = $request->get('company_id', $companies->first()?->id);
        $roleName = $request->get('role', 'admin_toko');

        $currentPermissions = [];
        if ($companyId) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($companyId);
            $role = Role::where('name', $roleName)->where('company_id', $companyId)->first();
            $currentPermissions = $role ? $role->permissions->pluck('name')->toArray() : [];
        }

        return view('super-admin.permissions', [
            'companies' => $companies,
            'companyId' => $companyId,
            'roleName' => $roleName,
            'menuList' => MenuPermissions::LIST,
            'currentPermissions' => $currentPermissions,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'role' => 'required|in:admin_toko,karyawan_toko,teknisi',
            'permissions' => 'nullable|array',
        ]);

        app(PermissionRegistrar::class)->setPermissionsTeamId($validated['company_id']);
        $role = Role::where('name', $validated['role'])->where('company_id', $validated['company_id'])->first();

        $selected = array_intersect($validated['permissions'] ?? [], array_keys(MenuPermissions::LIST));
        $role?->syncPermissions($selected);

        return redirect()->route('super-admin.permissions.edit', ['company_id' => $validated['company_id'], 'role' => $validated['role']])
            ->with('success', 'Hak akses berhasil diperbarui.');
    }
}