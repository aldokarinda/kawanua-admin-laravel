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
            'icon' => 'bi bi-grid-fill text-blue-400',
            'route_name' => 'dashboard',
            'permission_name' => 'dashboard.view',
            'order' => 1
        ]);

        // 2. Parent Menu (Master Data)
        $masterMenu = Menu::updateOrCreate([
            'slug' => 'master-data'
        ], [
            'name' => 'Master Data',
            'icon' => 'bi bi-database-fill text-orange-400',
            'order' => 10
        ]);

        $masterChildren = [
            ['slug' => 'categories', 'name' => 'Kategori', 'icon' => 'bi bi-tags-fill text-yellow-400', 'route_name' => 'admin.categories.index', 'permission_name' => 'category.view', 'order' => 1],
            ['slug' => 'tags', 'name' => 'Label / Tags', 'icon' => 'bi bi-bookmark-fill text-orange-300', 'route_name' => 'admin.tags.index', 'permission_name' => 'tag.view', 'order' => 2],
            ['slug' => 'regions', 'name' => 'Data Wilayah', 'icon' => 'bi bi-geo-alt-fill text-lime-400', 'route_name' => 'admin.regions.index', 'permission_name' => 'region.view', 'order' => 3],
        ];

        foreach ($masterChildren as $child) {
            $masterMenu->children()->updateOrCreate(['slug' => $child['slug']], $child);
        }

        // 3. Parent Menu (Laporan)
        $reportMenu = Menu::updateOrCreate([
            'slug' => 'reports'
        ], [
            'name' => 'Laporan',
            'icon' => 'bi bi-bar-chart-line-fill text-indigo-400',
            'order' => 20
        ]);

        $reportChildren = [
            ['slug' => 'visitor-stats', 'name' => 'Statistik Pengunjung', 'icon' => 'bi bi-graph-up text-indigo-300', 'route_name' => 'admin.reports.visitors', 'permission_name' => 'report.view', 'order' => 1],
            ['slug' => 'transactions', 'name' => 'Laporan Transaksi', 'icon' => 'bi bi-receipt text-fuchsia-400', 'route_name' => 'admin.reports.transactions', 'permission_name' => 'report.view', 'order' => 2],
        ];

        foreach ($reportChildren as $child) {
            $reportMenu->children()->updateOrCreate(['slug' => $child['slug']], $child);
        }

        // 4. Parent Menu (System Admin)
        $systemMenu = Menu::updateOrCreate([
            'slug' => 'system'
        ], [
            'name' => 'Sistem Admin',
            'icon' => 'bi bi-gear-fill text-slate-400',
            'order' => 99
        ]);

        // 5. Children of System Admin
        $children = [
            [
                'slug' => 'user-management',
                'name' => 'User Management',
                'icon' => 'bi bi-people-fill text-emerald-400',
                'route_name' => 'admin.users.index',
                'permission_name' => 'user.view',
                'order' => 1
            ],
            [
                'slug' => 'roles',
                'name' => 'Roles',
                'icon' => 'bi bi-person-badge-fill text-violet-400',
                'route_name' => 'admin.roles.index',
                'permission_name' => 'role.view',
                'order' => 2
            ],
            [
                'slug' => 'permissions',
                'name' => 'Permissions',
                'icon' => 'bi bi-key-fill text-amber-400',
                'route_name' => 'admin.permissions.index',
                'permission_name' => 'permission.view',
                'order' => 3
            ],
            [
                'slug' => 'menus',
                'name' => 'Menus',
                'icon' => 'bi bi-list-nested text-cyan-400',
                'route_name' => 'admin.menus.index',
                'permission_name' => 'menu.view',
                'order' => 4
            ],
            [
                'slug' => 'audit-logs',
                'name' => 'Audit Logs',
                'icon' => 'bi bi-journal-text text-rose-400',
                'route_name' => 'admin.audit_logs.index',
                'permission_name' => 'audit.view',
                'order' => 5
            ],
            [
                'slug' => 'security',
                'name' => 'Security',
                'icon' => 'bi bi-shield-lock-fill text-red-400',
                'route_name' => 'admin.security.index',
                'permission_name' => 'security.view',
                'order' => 6
            ],
            [
                'slug' => 'app-settings',
                'name' => 'Pengaturan Aplikasi',
                'icon' => 'bi bi-sliders text-gray-400',
                'route_name' => 'admin.security.settings',
                'permission_name' => 'setting.view',
                'order' => 7
            ]
        ];

        foreach ($children as $child) {
            $systemMenu->children()->updateOrCreate([
                'slug' => $child['slug']
            ], [
                'name' => $child['name'],
                'icon' => $child['icon'] ?? null,
                'route_name' => $child['route_name'],
                'permission_name' => $child['permission_name'],
                'order' => $child['order']
            ]);
        }
    }
}
