<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Ports\DocumentStorage;
use App\Application\UseCases\ServiceReports\EnsureReportDocumentsUseCase;
use App\Http\Controllers\Controller;

class ReportDocumentController extends Controller
{
    public function serviceReport(int $id, EnsureReportDocumentsUseCase $ensure, DocumentStorage $storage)
    {
        $docs = $ensure->execute($id);
        $bytes = $storage->get($docs['report_pdf_path']);

        return response()->streamDownload(function () use ($bytes) {
            echo $bytes;
        }, 'service-report-'.$id.'.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function certificate(int $id, EnsureReportDocumentsUseCase $ensure, DocumentStorage $storage)
    {
        $docs = $ensure->execute($id);
        $bytes = $storage->get($docs['certificate_pdf_path']);

        return response()->streamDownload(function () use ($bytes) {
            echo $bytes;
        }, 'certificate-'.$docs['certificate_folio'].'.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
