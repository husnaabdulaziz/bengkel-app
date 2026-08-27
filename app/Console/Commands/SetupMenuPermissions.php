<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Support\MenuPermissions;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SetupMenuPermissions extends Command
{
    protected $signature = 'app:setup-menu-permissions';
    protected $description = 'Backfill permission menu untuk semua company yang sudah ada';

    public function handle(): void
    {
        foreach (array_keys(MenuPermissions::LIST) as $key) {
            Permission::firstOrCreate(['name' => $key, 'guard_name' => 'web']);
        }
        $this->info('Permission menu dipastikan ada.');

        foreach (Company::all() as $company) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($company->id);

            $adminToko = Role::firstOrCreate(['name' => 'admin_toko', 'guard_name' => 'web', 'company_id' => $company->id]);
            $karyawan  = Role::firstOrCreate(['name' => 'karyawan_toko', 'guard_name' => 'web', 'company_id' => $company->id]);
            $teknisi   = Role::firstOrCreate(['name' => 'teknisi', 'guard_name' => 'web', 'company_id' => $company->id]);

            $adminToko->syncPermissions(array_keys(MenuPermissions::LIST));

            // Kalau karyawan_toko/teknisi sudah pernah punya permission (misal sudah diatur manual), jangan timpa
            if ($karyawan->permissions()->count() === 0) {
                $karyawan->syncPermissions(MenuPermissions::DEFAULT_KARYAWAN);
            }
            if ($teknisi->permissions()->count() === 0) {
                $teknisi->syncPermissions(['access_dashboard', 'access_pos', 'access_produk']);
            }

            $this->info("Company #{$company->id} ({$company->nama_toko}) selesai.");
        }

        $this->info('Selesai.');
    }
}