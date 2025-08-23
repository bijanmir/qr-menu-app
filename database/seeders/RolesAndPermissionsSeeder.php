<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Restaurant management
            'create restaurants',
            'update restaurants',
            'delete restaurants',
            'view restaurants',
            
            // Menu management
            'create menus',
            'update menus',
            'delete menus',
            'publish menus',
            'duplicate menus',
            
            // Item management
            'create items',
            'update items',
            'delete items',
            '86 items',
            
            // Order management
            'view orders',
            'update order status',
            'refund orders',
            
            // User management
            'create users',
            'update users',
            'delete users',
            'impersonate users',
            
            // Admin functions
            'view all tenants',
            'manage billing',
            'view audits',
            'manage feature flags',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $ownerRole = Role::firstOrCreate(['name' => 'owner']);
        $ownerRole->syncPermissions([
            'create restaurants',
            'update restaurants',
            'delete restaurants',
            'view restaurants',
            'create menus',
            'update menus',
            'delete menus',
            'publish menus',
            'duplicate menus',
            'create items',
            'update items',
            'delete items',
            '86 items',
            'view orders',
            'update order status',
            'refund orders',
            'create users',
            'update users',
            'view audits',
        ]);

        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->syncPermissions([
            'view restaurants',
            'update restaurants', // limited to assigned restaurants
            'create menus',
            'update menus',
            'publish menus',
            'duplicate menus',
            'create items',
            'update items',
            'delete items',
            '86 items',
            'view orders',
            'update order status',
        ]);

        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        // Customers don't need explicit permissions for now
    }
}
