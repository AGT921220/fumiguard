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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('site_id')->constrained('sites');
            $table->foreignId('service_plan_id')->nullable()->constrained('service_plans');
            $table->foreignId('recurrence_rule_id')->nullable()->constrained('recurrence_rules');
            $table->dateTime('scheduled_at');
            $table->string('status')->default('SCHEDULED');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'scheduled_at']);
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
