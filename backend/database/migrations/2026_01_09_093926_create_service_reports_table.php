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
        Schema::create('service_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('work_order_id')->constrained('work_orders');
            $table->string('status')->default('DRAFT');
            $table->boolean('locked')->default(false);
            $table->dateTime('started_at');
            $table->dateTime('finalized_at')->nullable();
            $table->json('checklist')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['work_order_id']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'locked']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_reports');
    }
};
