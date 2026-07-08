<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define standard permissions grouped by module
        $permissions = [
            'dashboard.view',
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete',
            'menu.view',
            'menu.create',
            'menu.edit',
            'menu.delete',
            'audit.view',
            'security.view',
            'security.edit'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Create Roles and assign created permissions
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin'
        ], [
            'description' => 'Has all privileges across the entire application.'
        ]);
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole = Role::firstOrCreate([
            'name' => 'Admin'
        ], [
            'description' => 'Has privileges to manage users and view basic reports.'
        ]);
        $adminRole->givePermissionTo(['dashboard.view', 'user.view', 'user.create', 'user.edit']);
    }
}
