<?php

namespace App\Observers;

use App\Models\Company;
use App\Support\MenuPermissions;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class CompanyObserver
{
    public function created(Company $company): void
    {
        // Pastikan semua permission menu ada (global, dibuat sekali, dipakai semua company)
        foreach (array_keys(MenuPermissions::LIST) as $key) {
            Permission::firstOrCreate(['name' => $key, 'guard_name' => 'web']);
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($company->id);

        $adminToko = Role::firstOrCreate(['name' => 'admin_toko', 'guard_name' => 'web', 'company_id' => $company->id]);
        $karyawan  = Role::firstOrCreate(['name' => 'karyawan_toko', 'guard_name' => 'web', 'company_id' => $company->id]);
        $teknisi   = Role::firstOrCreate(['name' => 'teknisi', 'guard_name' => 'web', 'company_id' => $company->id]);

        // Admin Toko dapat semua akses menu
        $adminToko->syncPermissions(array_keys(MenuPermissions::LIST));

        // Karyawan Toko & Teknisi dapat akses default (Admin Toko bisa ubah nanti lewat "Hak Akses Karyawan")
        $karyawan->syncPermissions(MenuPermissions::DEFAULT_KARYAWAN);
        $teknisi->syncPermissions(['access_dashboard', 'access_pos', 'access_produk']);
    }
}