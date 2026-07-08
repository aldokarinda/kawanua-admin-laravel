<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add index on audit_logs module and action for faster dashboard/log filtering
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index('module');
            $table->index('action');
            $table->index('created_at');
        });

        // Add index on menus parent_id, is_active, and order for faster recursive sidebar assembly
        Schema::table('menus', function (Blueprint $table) {
            $table->index(['parent_id', 'is_active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['module']);
            $table->dropIndex(['action']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->dropIndex(['parent_id', 'is_active', 'order']);
        });
    }
};
