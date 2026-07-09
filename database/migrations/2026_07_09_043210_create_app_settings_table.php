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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert defaults in English
        DB::table('app_settings')->insert([
            [
                'key' => 'app_name',
                'value' => 'Kawanua Panel',
                'type' => 'string',
                'description' => 'Application name displayed in sidebar and header.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_description',
                'value' => 'Integrated Management Information System',
                'type' => 'string',
                'description' => 'A brief description of the application.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_logo',
                'value' => null,
                'type' => 'string',
                'description' => 'Application brand logo image.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_theme',
                'value' => 'indigo',
                'type' => 'string',
                'description' => 'Primary sidebar theme color preset.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Cleanup security_settings of app settings
        DB::table('security_settings')->whereIn('key', ['app_name', 'app_description'])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-insert into security_settings (optional but nice)
        foreach (['app_name', 'app_description'] as $key) {
            $setting = DB::table('app_settings')->where('key', $key)->first();
            if ($setting) {
                DB::table('security_settings')->insert([
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'description' => $setting->description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::dropIfExists('app_settings');
    }
};
