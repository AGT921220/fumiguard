<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\ServiceReports\AddChemicalUsageUseCase;
use App\Application\UseCases\ServiceReports\CaptureSignatureUseCase;
use App\Application\UseCases\ServiceReports\FinalizeReportUseCase;
use App\Application\UseCases\ServiceReports\SaveChecklistAndNotesUseCase;
use App\Application\UseCases\ServiceReports\StartReportUseCase;
use App\Application\UseCases\ServiceReports\UploadEvidenceUseCase;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateReportDocumentsJob;
use Illuminate\Http\Request;

class ServiceReportController extends Controller
{
    public function start(int $workOrderId, StartReportUseCase $useCase)
    {
        return response()->json($useCase->execute($workOrderId), 201);
    }

    public function saveChecklist(int $workOrderId, Request $request, SaveChecklistAndNotesUseCase $useCase)
    {
        $data = $request->validate([
            'checklist' => ['required', 'array'],
            'notes' => ['nullable', 'string'],
        ]);

        return response()->json($useCase->execute(
            workOrderId: $workOrderId,
            checklist: $data['checklist'],
            notes: $data['notes'] ?? null,
        ));
    }

    public function addChemical(int $workOrderId, Request $request, AddChemicalUsageUseCase $useCase)
    {
        $data = $request->validate([
            'chemical_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:32'],
        ]);

        return response()->json($useCase->execute($workOrderId, [
            'chemical_name' => $data['chemical_name'],
            'quantity' => (float) $data['quantity'],
            'unit' => $data['unit'],
        ]), 201);
    }

    public function uploadEvidence(int $workOrderId, Request $request, UploadEvidenceUseCase $useCase)
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        return response()->json($useCase->execute($workOrderId, $data['file']), 201);
    }

    public function captureSignature(int $workOrderId, Request $request, CaptureSignatureUseCase $useCase)
    {
        $data = $request->validate([
            'signed_by_name' => ['required', 'string', 'max:255'],
            'signed_by_role' => ['nullable', 'string', 'max:255'],
            'signature_data' => ['required', 'string'],
            'signed_at' => ['nullable', 'date'],
        ]);

        return response()->json($useCase->execute($workOrderId, $data), 201);
    }

    public function finalize(int $workOrderId, FinalizeReportUseCase $useCase)
    {
        $report = $useCase->execute($workOrderId);

        // En testing: queue=sync => se ejecuta inmediatamente.
        GenerateReportDocumentsJob::dispatch((int) $report['id']);

        return response()->json($report);
    }
}
