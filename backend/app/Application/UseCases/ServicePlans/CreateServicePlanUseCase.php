<?php

namespace App\Application\UseCases\ServicePlans;

use App\Application\Ports\ServicePlanRepository;

final readonly class CreateServicePlanUseCase
{
    public function __construct(private ServicePlanRepository $plans)
    {
    }

    public function execute(array $data): array
    {
        return $this->plans->create($data);
    }
}

