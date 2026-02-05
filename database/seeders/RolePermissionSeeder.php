<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // 1. Reset cache permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat Permission (Izin Akses Granular)
        $permissions = [
            // Dashboard
            'view_dashboard',

            // Inventory (Gudang)
            'view_inventory', 'create_product', 'edit_product', 'delete_product', 'stock_adjustment',

            // Sales (Penjualan)
            'view_sales', 'create_sales', 'edit_sales', 'delete_sales', 'approve_sales',

            // Purchase (Pembelian)
            'view_purchase', 'create_purchase', 'edit_purchase', 'delete_purchase', 'approve_purchase',

            // Finance (Keuangan)
            'view_finance', 'create_transaction', 'approve_payment', 'view_financial_reports',

            // User Management
            'view_users', 'create_users', 'edit_users', 'delete_users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // 3. Buat Role & Assign Permission

        // A. STAFF GUDANG (Warehouse)
        $role = Role::create(['name' => 'staff']); // Kita pakai nama 'staff' agar sesuai dengan kode lama Anda
        $role->givePermissionTo(['view_inventory', 'create_product', 'edit_product', 'stock_adjustment', 'view_dashboard']);

        // B. ADMIN (Operasional)
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(['view_inventory', 'create_product', 'edit_product', 'delete_product', 'stock_adjustment', 
                                 'view_sales', 'create_sales', 'edit_sales', 
                                 'view_purchase', 'create_purchase', 'edit_purchase', 
                                 'view_users', 'create_users', 'edit_users', 'view_dashboard']);

        // C. SUPER ADMIN (Pemilik / IT)
        $role = Role::create(['name' => 'super_admin']);
        // Super Admin tidak perlu assign manual, nanti kita setting biar bisa akses semuanya otomatis


        // 4. Update User yang sudah ada (Agar tidak error saat login)
        // User ID 1 (Anda) jadikan Super Admin
        $user = User::find(1);
        if($user) {
            $user->assignRole('super_admin');
        }
    }
}