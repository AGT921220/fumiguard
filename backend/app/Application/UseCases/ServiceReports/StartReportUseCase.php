<?php

namespace App\Application\UseCases\ServiceReports;

use App\Application\Ports\ServiceReportRepository;
use App\Application\Ports\WorkOrderRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class StartReportUseCase
{
    public function __construct(
        private ServiceReportRepository $reports,
        private WorkOrderRepository $workOrders,
    ) {
    }

    public function execute(int $workOrderId): array
    {
        if ($this->workOrders->get($workOrderId) === null) {
            throw new NotFoundHttpException('WorkOrder no encontrado.');
        }

        $existing = $this->reports->getByWorkOrderId($workOrderId);
        if ($existing !== null) {
            return $existing;
        }

        return $this->reports->createForWorkOrder($workOrderId);
    }
}

