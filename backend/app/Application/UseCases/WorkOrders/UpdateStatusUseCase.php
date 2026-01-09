<?php

namespace App\Application\UseCases\WorkOrders;

use App\Application\Auth\UserContext;
use App\Application\Ports\WorkOrderRepository;
use App\Domain\Enums\UserRole;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class UpdateStatusUseCase
{
    private const ALLOWED = ['OPEN', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'];

    public function __construct(
        private WorkOrderRepository $workOrders,
        private UserContext $userContext,
    )
    {
    }

    public function execute(int $workOrderId, string $status): array
    {
        if (! in_array($status, self::ALLOWED, true)) {
            throw new BadRequestHttpException('Estado inválido.');
        }

        $wo = $this->workOrders->get($workOrderId);
        if ($wo === null) {
            throw new NotFoundHttpException('WorkOrder no encontrado.');
        }

        $this->authorizeEdit($wo);

        $updated = $this->workOrders->update($workOrderId, ['status' => $status]);

        if ($updated === null) {
            throw new NotFoundHttpException('WorkOrder no encontrado.');
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

