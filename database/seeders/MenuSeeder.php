<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dashboard Menu
        Menu::updateOrCreate([
            'slug' => 'dashboard'
        ], [
            'name' => 'Dashboard',
            'icon' => 'bi bi-grid-fill text-blue-500',
            'route_name' => 'dashboard',
            'permission_name' => 'dashboard.view',
            'order' => 1
        ]);

        // 2. Parent Menu (System Admin)
        $systemMenu = Menu::updateOrCreate([
            'slug' => 'system'
        ], [
            'name' => 'Sistem Admin',
            'icon' => 'bi bi-gear-fill text-slate-400',
            'order' => 99
        ]);

        // 3. Children of System Admin
        $children = [
            [
                'slug' => 'user-management',
                'name' => 'User Management',
                'route_name' => 'admin.users.index',
                'permission_name' => 'user.view',
                'order' => 1
            ],
            [
                'slug' => 'roles',
                'name' => 'Roles',
                'route_name' => 'admin.roles.index',
                'permission_name' => 'role.view',
                'order' => 2
            ],
            [
                'slug' => 'permissions',
                'name' => 'Permissions',
                'route_name' => 'admin.permissions.index',
                'permission_name' => 'permission.view',
                'order' => 3
            ],
            [
                'slug' => 'menus',
                'name' => 'Menus',
                'route_name' => 'admin.menus.index',
                'permission_name' => 'menu.view',
                'order' => 4
            ],
            [
                'slug' => 'audit-logs',
                'name' => 'Audit Logs',
                'route_name' => 'admin.audit_logs.index',
                'permission_name' => 'audit.view',
                'order' => 5
            ],
            [
                'slug' => 'security',
                'name' => 'Security',
                'route_name' => 'admin.security.index',
                'permission_name' => 'security.view',
                'order' => 6
            ]
        ];

        foreach ($children as $child) {
            $systemMenu->children()->updateOrCreate([
                'slug' => $child['slug']
            ], [
                'name' => $child['name'],
                'route_name' => $child['route_name'],
                'permission_name' => $child['permission_name'],
                'order' => $child['order']
            ]);
        }
    }
}
