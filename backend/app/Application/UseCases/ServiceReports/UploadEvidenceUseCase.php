<?php

namespace App\Application\UseCases\ServiceReports;

use App\Application\Ports\EvidenceRepository;
use App\Application\Ports\EvidenceStorage;
use App\Application\Ports\ServiceReportRepository;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class UploadEvidenceUseCase
{
    public function __construct(
        private ServiceReportRepository $reports,
        private EvidenceStorage $storage,
        private EvidenceRepository $evidences,
    ) {
    }

    public function execute(int $workOrderId, UploadedFile $file): array
    {
        $report = $this->reports->getByWorkOrderId($workOrderId);
        if ($report === null) {
            throw new NotFoundHttpException('ServiceReport no encontrado.');
        }

        if ($report['locked']) {
            throw new BadRequestHttpException('ServiceReport está finalizado y bloqueado.');
        }

        $stored = $this->storage->storeEvidence($file);

        return $this->evidences->create([
            'service_report_id' => $report['id'],
            'path' => $stored['path'],
            'mime' => $stored['mime'],
        ]);
    }
}

