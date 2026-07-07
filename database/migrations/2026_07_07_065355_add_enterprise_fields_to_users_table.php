<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->nullable();
            $table->string('phone_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('avatar')->nullable();
            $table->timestamp('last_login_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['department', 'phone_number', 'is_active', 'avatar', 'last_login_at']);
        });
    }
};
