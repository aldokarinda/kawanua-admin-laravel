<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename parent menus
        DB::table('menus')->where('slug', 'system')->update(['name' => 'Administration']);
        DB::table('menus')->where('slug', 'reports')->update(['name' => 'Reports']);

        // 2. Rename child menus (English translation)
        DB::table('menus')->where('slug', 'categories')->update(['name' => 'Categories']);
        DB::table('menus')->where('slug', 'tags')->update(['name' => 'Tags']);
        DB::table('menus')->where('slug', 'regions')->update(['name' => 'Regions']);
        DB::table('menus')->where('slug', 'visitor-stats')->update(['name' => 'Visitor Statistics']);
        DB::table('menus')->where('slug', 'transactions')->update(['name' => 'Transaction Reports']);
        DB::table('menus')->where('slug', 'user-management')->update(['name' => 'Users']);
        
        // 3. Update App Settings menu details to point to the new route
        DB::table('menus')->where('slug', 'app-settings')->update([
            'name' => 'App Settings',
            'route_name' => 'admin.app_settings.index',
        ]);

        // 4. Remove duplicate audit-logs menu under system
        DB::table('menus')->where('slug', 'audit-logs')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore parent menus
        DB::table('menus')->where('slug', 'system')->update(['name' => 'Sistem Admin']);
        DB::table('menus')->where('slug', 'reports')->update(['name' => 'Laporan']);

        // Restore child menus
        DB::table('menus')->where('slug', 'categories')->update(['name' => 'Kategori']);
        DB::table('menus')->where('slug', 'tags')->update(['name' => 'Label / Tags']);
        DB::table('menus')->where('slug', 'regions')->update(['name' => 'Data Wilayah']);
        DB::table('menus')->where('slug', 'visitor-stats')->update(['name' => 'Statistik Pengunjung']);
        DB::table('menus')->where('slug', 'transactions')->update(['name' => 'Laporan Transaksi']);
        DB::table('menus')->where('slug', 'user-management')->update(['name' => 'User Management']);

        // Restore App Settings menu
        DB::table('menus')->where('slug', 'app-settings')->update([
            'name' => 'Pengaturan Aplikasi',
            'route_name' => 'admin.security.settings',
        ]);

        // Re-insert audit-logs menu (re-fetching parent ID)
        $parent = DB::table('menus')->where('slug', 'system')->first();
        if ($parent) {
            DB::table('menus')->insert([
                'parent_id' => $parent->id,
                'slug' => 'audit-logs',
                'name' => 'Audit Logs',
                'icon' => 'bi bi-journal-text text-rose-400',
                'route_name' => 'admin.audit_logs.index',
                'permission_name' => 'audit.view',
                'order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
