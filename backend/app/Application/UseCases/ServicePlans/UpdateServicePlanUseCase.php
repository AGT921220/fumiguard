<?php

namespace App\Application\UseCases\ServicePlans;

use App\Application\Ports\ServicePlanRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class UpdateServicePlanUseCase
{
    public function __construct(private ServicePlanRepository $plans)
    {
    }

    public function execute(int $id, array $data): array
    {
        $updated = $this->plans->update($id, $data);

        if ($updated === null) {
            throw new NotFoundHttpException('ServicePlan no encontrado.');
        }

        return $updated;
    }
}

