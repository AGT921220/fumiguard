<?php

namespace App\Application\Ports;

use App\Domain\Enums\PlanKey;

interface PlanCatalog
{
    /**
     * @return array{max_technicians:int|null,max_work_orders_per_month:int|null}
     */
    public function limitsFor(PlanKey $planKey): array;

    public function stripePriceIdFor(PlanKey $planKey): string;
}

