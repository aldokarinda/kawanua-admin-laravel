<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default security settings
        $defaults = [
            ['key' => 'password_min_length', 'value' => '8', 'type' => 'integer', 'description' => 'Minimum password length'],
            ['key' => 'password_require_uppercase', 'value' => 'true', 'type' => 'boolean', 'description' => 'Require uppercase letters'],
            ['key' => 'password_require_numeric', 'value' => 'true', 'type' => 'boolean', 'description' => 'Require numbers'],
            ['key' => 'password_require_special', 'value' => 'true', 'type' => 'boolean', 'description' => 'Require special characters'],
            ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'integer', 'description' => 'Maximum failed login attempts before lockout'],
            ['key' => 'lockout_duration_minutes', 'value' => '15', 'type' => 'integer', 'description' => 'Account lockout duration in minutes'],
            ['key' => 'session_lifetime_hours', 'value' => '24', 'type' => 'integer', 'description' => 'Session lifetime in hours'],
            ['key' => 'enforce_2fa', 'value' => 'false', 'type' => 'boolean', 'description' => 'Enforce 2FA for all users'],
            ['key' => 'ip_whitelist_enabled', 'value' => 'false', 'type' => 'boolean', 'description' => 'Enable IP whitelist'],
            ['key' => 'auto_logout_inactive_minutes', 'value' => '30', 'type' => 'integer', 'description' => 'Auto logout after inactivity'],
            ['key' => 'notify_failed_login', 'value' => 'true', 'type' => 'boolean', 'description' => 'Send notification on failed login'],
            ['key' => 'notify_new_device', 'value' => 'true', 'type' => 'boolean', 'description' => 'Send notification on new device login'],
        ];

        foreach ($defaults as $setting) {
            DB::table('security_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('security_settings');
    }
};
