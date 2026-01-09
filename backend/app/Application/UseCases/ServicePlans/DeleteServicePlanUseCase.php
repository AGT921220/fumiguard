<?php

namespace App\Application\UseCases\ServicePlans;

use App\Application\Ports\ServicePlanRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class DeleteServicePlanUseCase
{
    public function __construct(private ServicePlanRepository $plans)
    {
    }

    public function execute(int $id): void
    {
        if (! $this->plans->delete($id)) {
            throw new NotFoundHttpException('ServicePlan no encontrado.');
        }
    }
}

