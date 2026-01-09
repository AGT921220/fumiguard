<?php

namespace App\Application\UseCases\ServicePlans;

use App\Application\Ports\ServicePlanRepository;

final readonly class ListServicePlansUseCase
{
    public function __construct(private ServicePlanRepository $plans)
    {
    }

    public function execute(): array
    {
        return $this->plans->list();
    }
}

