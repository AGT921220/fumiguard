<?php

namespace App\Infrastructure\Billing;

use App\Application\Ports\PlanCatalog;
use App\Domain\Enums\PlanKey;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class EnvPlanCatalog implements PlanCatalog
{
    public function limitsFor(PlanKey $planKey): array
    {
        return match ($planKey) {
            PlanKey::BASIC => ['max_technicians' => 3, 'max_work_orders_per_month' => 100],
            PlanKey::PRO => ['max_technicians' => 10, 'max_work_orders_per_month' => 500],
            PlanKey::ENTERPRISE => ['max_technicians' => null, 'max_work_orders_per_month' => null],
        };
    }

    public function stripePriceIdFor(PlanKey $planKey): string
    {
        $key = match ($planKey) {
            PlanKey::BASIC => 'STRIPE_PRICE_BASIC',
            PlanKey::PRO => 'STRIPE_PRICE_PRO',
            PlanKey::ENTERPRISE => 'STRIPE_PRICE_ENTERPRISE',
        };

        $value = (string) env($key, '');
        if ($value === '') {
            throw new BadRequestHttpException("Falta configurar {$key}.");
        }

        return $value;
    }
}

