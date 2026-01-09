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
        Schema::create('recurrence_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('frequency'); // MONTHLY | QUARTERLY
            $table->unsignedTinyInteger('day_of_month')->default(1);
            $table->unsignedTinyInteger('interval_months')->default(1); // 1 (mensual) / 3 (trimestral)
            $table->date('starts_on')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'frequency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurrence_rules');
    }
};
