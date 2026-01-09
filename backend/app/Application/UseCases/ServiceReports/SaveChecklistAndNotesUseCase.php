<?php

namespace App\Application\UseCases\ServiceReports;

use App\Application\Auth\UserContext;
use App\Application\Ports\ServiceReportRepository;
use App\Application\Ports\WorkOrderRepository;
use App\Domain\Enums\UserRole;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class SaveChecklistAndNotesUseCase
{
    public function __construct(
        private ServiceReportRepository $reports,
        private WorkOrderRepository $workOrders,
        private UserContext $userContext,
    ) {
    }

    public function execute(int $workOrderId, array $checklist, ?string $notes): array
    {
        $report = $this->reports->getByWorkOrderId($workOrderId);
        if ($report === null) {
            throw new NotFoundHttpException('ServiceReport no encontrado.');
        }

        $wo = $this->workOrders->get($workOrderId);
        if ($wo === null) {
            throw new NotFoundHttpException('WorkOrder no encontrado.');
        }
        $this->authorizeEdit($wo);

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

