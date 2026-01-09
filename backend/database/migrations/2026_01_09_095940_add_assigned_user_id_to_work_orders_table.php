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
        Schema::table('work_orders', function (Blueprint $table) {
            $table->foreignId('assigned_user_id')->nullable()->after('appointment_id')->constrained('users');
            $table->index(['tenant_id', 'assigned_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'assigned_user_id']);
            $table->dropConstrainedForeignId('assigned_user_id');
        });
    }
};
