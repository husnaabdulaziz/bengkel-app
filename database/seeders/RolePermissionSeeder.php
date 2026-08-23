<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'pos.access', 'pos.void',
            'product.view', 'product.manage',
            'customer.view', 'customer.manage',
            'inventory.manage', 'stock_opname.manage', 'stock_transfer.manage',
            'warranty.manage',
            'finance.report.view', 'finance.expense.manage',
            'employee.manage', 'settings.manage',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
    }
}