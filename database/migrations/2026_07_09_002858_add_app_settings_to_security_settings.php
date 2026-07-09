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
        DB::table('security_settings')->insert([
            [
                'key' => 'app_name',
                'value' => 'Kawanua Panel',
                'type' => 'string',
                'description' => 'Nama aplikasi yang ditampilkan di sidebar dan header.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_description',
                'value' => 'Sistem Informasi Manajemen Terpadu',
                'type' => 'string',
                'description' => 'Deskripsi singkat aplikasi.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('security_settings')->whereIn('key', ['app_name', 'app_description'])->delete();
    }
};
