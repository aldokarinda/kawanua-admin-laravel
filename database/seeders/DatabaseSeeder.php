<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Menu;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define standard permissions grouped by module
        $permissions = [
            'dashboard.view',
            'user.view', 'user.create', 'user.edit', 'user.delete',
            'role.view', 'role.create', 'role.edit', 'role.delete',
            'permission.view', 'permission.create', 'permission.edit', 'permission.delete',
            'menu.view', 'menu.create', 'menu.edit', 'menu.delete',
            'audit.view'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // 3. Create Roles and assign created permissions
        $superAdminRole = Role::create([
            'name' => 'Super Admin',
            'description' => 'Has all privileges across the entire application.'
        ]);
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole = Role::create([
            'name' => 'Admin',
            'description' => 'Has privileges to manage users and view basic reports.'
        ]);
        $adminRole->givePermissionTo(['dashboard.view', 'user.view', 'user.create', 'user.edit']);

        // 4. Create the default Administrator User
        $user = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        // Assign Role
        $user->assignRole($superAdminRole);

        // 5. Seed Menus with Bootstrap Icons and RBAC protections
        // Dashboard Menu
        Menu::create([
            'name' => 'Dashboard',
            'slug' => 'dashboard',
            'icon' => 'bi bi-grid-fill text-blue-500',
            'route_name' => 'dashboard',
            'permission_name' => 'dashboard.view',
            'order' => 1
        ]);

        // Parent Menu (System Admin)
        $systemMenu = Menu::create([
            'name' => 'Sistem Admin',
            'slug' => 'system',
            'icon' => 'bi bi-gear-fill text-slate-400',
            'order' => 99
        ]);

        // Children of System Admin
        $systemMenu->children()->createMany([
            [
                'name' => 'User Management',
                'slug' => 'user-management',
                'route_name' => 'admin.users.index',
                'permission_name' => 'user.view',
                'order' => 1
            ],
            [
                'name' => 'Roles',
                'slug' => 'roles',
                'route_name' => 'admin.roles.index',
                'permission_name' => 'role.view',
                'order' => 2
            ],
            [
                'name' => 'Permissions',
                'slug' => 'permissions',
                'route_name' => 'admin.permissions.index',
                'permission_name' => 'permission.view',
                'order' => 3
            ],
            [
                'name' => 'Menu Builder',
                'slug' => 'menus',
                'route_name' => 'admin.menus.index',
                'permission_name' => 'menu.view',
                'order' => 4
            ],
            [
                'name' => 'Audit Logs',
                'slug' => 'audit-logs',
                'route_name' => 'admin.audit_logs.index',
                'permission_name' => 'audit.view',
                'order' => 5
            ]
        ]);
    }
}
