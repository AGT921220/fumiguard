<?php

namespace App\Application\UseCases\ServiceReports;

use App\Application\Ports\ServiceReportRepository;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class FinalizeReportUseCase
{
    public function __construct(private ServiceReportRepository $reports)
    {
    }

    public function execute(int $workOrderId): array
    {
        $report = $this->reports->getByWorkOrderId($workOrderId);
        if ($report === null) {
            throw new NotFoundHttpException('ServiceReport no encontrado.');
        }

        if ($report['locked']) {
            throw new BadRequestHttpException('ServiceReport ya está finalizado.');
        }

        $now = CarbonImmutable::now();

        $updated = $this->reports->update($report['id'], [
            'locked' => true,
            'status' => 'FINAL',
            'finalized_at' => $now->toISOString(),
        ]);

        if ($updated === null) {
            throw new NotFoundHttpException('ServiceReport no encontrado.');
        }

        return $updated;
    }
}

