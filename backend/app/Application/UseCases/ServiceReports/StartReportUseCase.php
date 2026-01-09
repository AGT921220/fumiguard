<?php

namespace App\Application\UseCases\ServiceReports;

use App\Application\Auth\UserContext;
use App\Application\Ports\ServiceReportRepository;
use App\Application\Ports\WorkOrderRepository;
use App\Domain\Enums\UserRole;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class StartReportUseCase
{
    public function __construct(
        private ServiceReportRepository $reports,
        private WorkOrderRepository $workOrders,
        private UserContext $userContext,
    ) {
    }

    public function execute(int $workOrderId): array
    {
        $wo = $this->workOrders->get($workOrderId);
        if ($wo === null) {
            throw new NotFoundHttpException('WorkOrder no encontrado.');
        }

        $this->authorizeEdit($wo);

        $existing = $this->reports->getByWorkOrderId($workOrderId);
        if ($existing !== null) {
            return $existing;
        }

        return $this->reports->createForWorkOrder($workOrderId);
    }

    private function authorizeEdit(array $workOrder): void
    {
        $role = $this->userContext->requireRole();

        if ($role === UserRole::CLIENT_VIEWER) {
            throw new AccessDeniedHttpException('Solo lectura.');
        }

        if ($role === UserRole::TECHNICIAN) {
            $assigned = $workOrder['assigned_user_id'] ?? null;
            if ($assigned === null || (int) $assigned !== $this->userContext->requireUserId()) {
                throw new AccessDeniedHttpException('No autorizado.');
            }
        }
    }
}

