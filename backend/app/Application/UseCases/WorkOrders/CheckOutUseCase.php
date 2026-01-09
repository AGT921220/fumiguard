<?php

namespace App\Application\UseCases\WorkOrders;

use App\Application\Auth\UserContext;
use App\Application\Ports\WorkOrderRepository;
use App\Domain\Enums\UserRole;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class CheckOutUseCase
{
    public function __construct(
        private WorkOrderRepository $workOrders,
        private UserContext $userContext,
    )
    {
    }

    public function execute(int $workOrderId, float $lat, float $lng, ?string $atIso = null): array
    {
        $wo = $this->workOrders->get($workOrderId);

        if ($wo === null) {
            throw new NotFoundHttpException('WorkOrder no encontrado.');
        }

        $this->authorizeEdit($wo);

        if ($wo['check_in_at'] === null) {
            throw new BadRequestHttpException('WorkOrder requiere check-in antes de check-out.');
        }

        if ($wo['check_out_at'] !== null) {
            throw new BadRequestHttpException('WorkOrder ya tiene check-out.');
        }

        $at = $atIso ? CarbonImmutable::parse($atIso) : CarbonImmutable::now();

        $updated = $this->workOrders->update($workOrderId, [
            'check_out_at' => $at->toISOString(),
            'check_out_lat' => $lat,
            'check_out_lng' => $lng,
            'status' => 'COMPLETED',
        ]);

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

