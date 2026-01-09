<?php

namespace App\Application\UseCases\ServiceReports;

use App\Application\Ports\ServiceReportRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class SaveChecklistAndNotesUseCase
{
    public function __construct(private ServiceReportRepository $reports)
    {
    }

    public function execute(int $workOrderId, array $checklist, ?string $notes): array
    {
        $report = $this->reports->getByWorkOrderId($workOrderId);
        if ($report === null) {
            throw new NotFoundHttpException('ServiceReport no encontrado.');
        }

        if ($report['locked']) {
            throw new BadRequestHttpException('ServiceReport está finalizado y bloqueado.');
        }

        $updated = $this->reports->update($report['id'], [
            'checklist' => $checklist,
            'notes' => $notes,
        ]);

        if ($updated === null) {
            throw new NotFoundHttpException('ServiceReport no encontrado.');
        }

        return $updated;
    }
}

