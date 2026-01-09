<?php

namespace App\Application\UseCases\Billing;

use App\Application\Ports\PlanCatalog;
use App\Application\Ports\SubscriptionRepository;
use App\Domain\Enums\PlanKey;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class EnforcePlanLimits
{
    public function __construct(
        private SubscriptionRepository $subscriptions,
        private PlanCatalog $plans,
    ) {
    }

    /**
     * @return array{max_technicians:int|null,max_work_orders_per_month:int|null}
     */
    public function limitsForTenant(int $tenantId): array
    {
        $sub = $this->subscriptions->getForTenant($tenantId);
        if ($sub === null) {
            // Stripe is source of truth; no subscription means effectively blocked.
            throw new BadRequestHttpException('Tenant sin suscripción.');
        }

        $planKey = PlanKey::from((string) $sub['plan_key']);
        $limits = $sub['limits_json'];

        if (is_array($limits) && (array_key_exists('max_technicians', $limits) || array_key_exists('max_work_orders_per_month', $limits))) {
            return [
                'max_technicians' => $limits['max_technicians'] ?? $this->plans->limitsFor($planKey)['max_technicians'],
                'max_work_orders_per_month' => $limits['max_work_orders_per_month'] ?? $this->plans->limitsFor($planKey)['max_work_orders_per_month'],
            ];
        }

        return $this->plans->limitsFor($planKey);
    }
}

