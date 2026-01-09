<?php

namespace App\Application\UseCases\ServiceReports;

use App\Application\Auth\UserContext;
use App\Application\Ports\EvidenceRepository;
use App\Application\Ports\EvidenceStorage;
use App\Application\Ports\ServiceReportRepository;
use App\Application\Ports\WorkOrderRepository;
use App\Domain\Enums\UserRole;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class UploadEvidenceUseCase
{
    public function __construct(
        private ServiceReportRepository $reports,
        private EvidenceStorage $storage,
        private EvidenceRepository $evidences,
        private WorkOrderRepository $workOrders,
        private UserContext $userContext,
    ) {
    }

    public function execute(int $workOrderId, UploadedFile $file): array
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

        $stored = $this->storage->storeEvidence($file);

        return $this->evidences->create([
            'service_report_id' => $report['id'],
            'path' => $stored['path'],
            'mime' => $stored['mime'],
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

