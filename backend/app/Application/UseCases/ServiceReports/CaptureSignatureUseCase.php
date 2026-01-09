<?php

namespace App\Application\UseCases\ServiceReports;

use App\Application\Auth\UserContext;
use App\Application\Ports\ServiceReportRepository;
use App\Application\Ports\SignatureRepository;
use App\Application\Ports\WorkOrderRepository;
use App\Domain\Enums\UserRole;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class CaptureSignatureUseCase
{
    public function __construct(
        private ServiceReportRepository $reports,
        private SignatureRepository $signatures,
        private WorkOrderRepository $workOrders,
        private UserContext $userContext,
    ) {
    }

    public function execute(int $workOrderId, array $data): array
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

        $signedAt = isset($data['signed_at']) ? CarbonImmutable::parse($data['signed_at']) : CarbonImmutable::now();

        return $this->signatures->create([
            'service_report_id' => $report['id'],
            'signed_by_name' => $data['signed_by_name'],
            'signed_by_role' => $data['signed_by_role'] ?? null,
            'signature_data' => $data['signature_data'],
            'signed_at' => $signedAt->toISOString(),
        ]);
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

