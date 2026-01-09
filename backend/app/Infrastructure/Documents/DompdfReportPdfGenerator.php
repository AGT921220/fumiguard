<?php

namespace App\Infrastructure\Documents;

use App\Application\Ports\ReportPdfGenerator;
use Barryvdh\DomPDF\Facade\Pdf;

final class DompdfReportPdfGenerator implements ReportPdfGenerator
{
    public function generateServiceReport(array $payload): string
    {
        $html = view('pdf.service-report', $payload)->render();

        return Pdf::loadHTML($html)->setPaper('a4')->output();
    }

    public function generateCertificate(array $payload): string
    {
        $html = view('pdf.certificate', $payload)->render();

        return Pdf::loadHTML($html)->setPaper('a4')->output();
    }
}

