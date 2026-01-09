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
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('service_report_id')->constrained('service_reports');
            $table->string('signed_by_name');
            $table->string('signed_by_role')->nullable();
            $table->text('signature_data');
            $table->dateTime('signed_at');
            $table->timestamps();

            $table->index(['tenant_id', 'service_report_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
