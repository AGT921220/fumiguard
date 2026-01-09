<?php

namespace App\Application\UseCases\WorkOrders;

use App\Application\Ports\WorkOrderRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class UpdateStatusUseCase
{
    private const ALLOWED = ['OPEN', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'];

    public function __construct(private WorkOrderRepository $workOrders)
    {
    }

    public function execute(int $workOrderId, string $status): array
    {
        if (! in_array($status, self::ALLOWED, true)) {
            throw new BadRequestHttpException('Estado inválido.');
        }

        $updated = $this->workOrders->update($workOrderId, ['status' => $status]);

        if ($updated === null) {
            throw new NotFoundHttpException('WorkOrder no encontrado.');
        }

        return $updated;
    }
}

