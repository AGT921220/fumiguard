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
        Schema::table('service_reports', function (Blueprint $table) {
            $table->string('certificate_folio')->nullable()->after('work_order_id');
            $table->string('report_pdf_path')->nullable()->after('notes');
            $table->string('certificate_pdf_path')->nullable()->after('report_pdf_path');

            $table->index(['tenant_id', 'certificate_folio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_reports', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'certificate_folio']);
            $table->dropColumn(['certificate_folio', 'report_pdf_path', 'certificate_pdf_path']);
        });
    }
};
