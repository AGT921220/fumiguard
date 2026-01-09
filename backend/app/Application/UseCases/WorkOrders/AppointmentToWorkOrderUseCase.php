<?php

namespace App\Application\UseCases\WorkOrders;

use App\Application\Auth\UserContext;
use App\Application\UseCases\Billing\EnforcePlanLimits;
use App\Application\Ports\AppointmentRepository;
use App\Application\Ports\WorkOrderRepository;
use App\Domain\Enums\UserRole;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class AppointmentToWorkOrderUseCase
{
    public function __construct(
        private AppointmentRepository $appointments,
        private WorkOrderRepository $workOrders,
        private UserContext $userContext,
        private EnforcePlanLimits $limits,
    ) {
    }

    public function execute(int $appointmentId): array
    {
        if ($this->appointments->get($appointmentId) === null) {
            throw new NotFoundHttpException('Appointment no encontrado.');
        }

        $existing = $this->workOrders->findByAppointmentId($appointmentId);
        if ($existing !== null) {
            $this->authorizeTechnician($existing);
            return $existing;
        }

        $this->assertMonthlyWorkOrderLimit();

        $assigned = null;
        if ($this->userContext->requireRole() === UserRole::TECHNICIAN) {
            $assigned = $this->userContext->requireUserId();
        }

        $created = $this->workOrders->createFromAppointment($appointmentId, $assigned);
        $this->authorizeTechnician($created);

        return $created;
    }

    private function assertMonthlyWorkOrderLimit(): void
    {
        $tenantId = (int) request()->user()?->tenant_id;
        $limits = $this->limits->limitsForTenant($tenantId);
        $max = $limits['max_work_orders_per_month'];

        if ($max === null) {
            return;
        }

        $start = CarbonImmutable::now()->startOfMonth()->toDateTimeString();
        $end = CarbonImmutable::now()->endOfMonth()->toDateTimeString();
        $count = $this->workOrders->countCreatedBetween($start, $end);

        if ($count >= $max) {
            throw new BadRequestHttpException('Límite mensual de órdenes alcanzado.');
        }
    }

    private function authorizeTechnician(array $workOrder): void
    {
        if ($this->userContext->requireRole() !== UserRole::TECHNICIAN) {
            return;
        }

        $assigned = $workOrder['assigned_user_id'] ?? null;
        if ($assigned === null || (int) $assigned !== $this->userContext->requireUserId()) {
            throw new AccessDeniedHttpException('No autorizado.');
        }
    }
}

