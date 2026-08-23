<?php

namespace App\Observers;

use App\Models\Company;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class CompanyObserver
{
    public function created(Company $company): void
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($company->id);

        $adminToko = Role::firstOrCreate(['name' => 'admin_toko', 'guard_name' => 'web', 'company_id' => $company->id]);
        $karyawan  = Role::firstOrCreate(['name' => 'karyawan_toko', 'guard_name' => 'web', 'company_id' => $company->id]);
        $teknisi   = Role::firstOrCreate(['name' => 'teknisi', 'guard_name' => 'web', 'company_id' => $company->id]);

        $adminToko->givePermissionTo(Permission::all());
        $karyawan->givePermissionTo(['pos.access', 'product.view', 'customer.view']);
        $teknisi->givePermissionTo(['pos.access', 'product.view']);
    }
}