<?php

namespace App\Application\UseCases\ServicePlans;

use App\Application\Ports\ServicePlanRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class GetServicePlanUseCase
{
    public function __construct(private ServicePlanRepository $plans)
    {
    }

    public function execute(int $id): array
    {
        $plan = $this->plans->get($id);

        if ($plan === null) {
            throw new NotFoundHttpException('ServicePlan no encontrado.');
        }

        return $plan;
    }
}

