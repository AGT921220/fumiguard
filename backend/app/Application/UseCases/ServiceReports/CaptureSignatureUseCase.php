<?php

namespace App\Application\UseCases\ServiceReports;

use App\Application\Ports\ServiceReportRepository;
use App\Application\Ports\SignatureRepository;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class CaptureSignatureUseCase
{
    public function __construct(
        private ServiceReportRepository $reports,
        private SignatureRepository $signatures,
    ) {
    }

    public function execute(int $workOrderId, array $data): array
    {
        $report = $this->reports->getByWorkOrderId($workOrderId);
        if ($report === null) {
            throw new NotFoundHttpException('ServiceReport no encontrado.');
        }

        if ($report['locked']) {
            throw new BadRequestHttpException('ServiceReport está finalizado y bloqueado.');
        }

        $signedAt = isset($data['signed_at']) ? CarbonImmutable::parse($data['signed_at']) : CarbonImmutable::now();

        return $this->signatures->create([
            'service_report_id' => $report['id'],
            'signed_by_name' => $data['signed_by_name'],
            'signed_by_role' => $data['signed_by_role'] ?? null,
            'signature_data' => $data['signature_data'],
            'signed_at' => $signedAt->toISOString(),
        ]);
    }
}

