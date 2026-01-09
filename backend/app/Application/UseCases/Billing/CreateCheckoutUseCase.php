<?php

namespace App\Application\UseCases\Billing;

use App\Application\Ports\Billing\StripeGateway;
use App\Application\Ports\PlanCatalog;
use App\Application\Ports\SubscriptionRepository;
use App\Application\Tenancy\TenantContext;
use App\Domain\Enums\PlanKey;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class CreateCheckoutUseCase
{
    public function __construct(
        private StripeGateway $stripe,
        private SubscriptionRepository $subscriptions,
        private PlanCatalog $plans,
        private TenantContext $tenantContext,
    ) {
    }

    /**
     * @return array{url:string,id:string}
     */
    public function execute(string $planKey): array
    {
        $tenantId = $this->tenantContext->requireTenantId();

        $plan = PlanKey::from($planKey);
        $priceId = $this->plans->stripePriceIdFor($plan);

        $existing = $this->subscriptions->getForTenant($tenantId);
        $customerId = $existing['stripe_customer_id'] ?? null;

        if ($customerId === null) {
            // Stripe Checkout can create customer automatically if no customer is provided.
            // We still keep a local placeholder subscription row to remember plan intent.
            $this->subscriptions->upsertForTenant([
                'tenant_id' => $tenantId,
                'plan_key' => $plan->value,
                'status' => 'incomplete',
                'stripe_customer_id' => null,
                'stripe_subscription_id' => null,
                'current_period_end' => null,
                'limits_json' => $this->plans->limitsFor($plan),
            ]);
        }

        $successUrl = (string) config('billing.stripe.success_url');
        $cancelUrl = (string) config('billing.stripe.cancel_url');
        if ($successUrl === '' || $cancelUrl === '') {
            throw new BadRequestHttpException('Faltan URLs de checkout.');
        }

        $params = [
            'mode' => 'subscription',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'line_items' => [
                ['price' => $priceId, 'quantity' => 1],
            ],
            'subscription_data' => [
                'metadata' => [
                    'tenant_id' => (string) $tenantId,
                    'plan_key' => $plan->value,
                ],
            ],
            'metadata' => [
                'tenant_id' => (string) $tenantId,
                'plan_key' => $plan->value,
            ],
        ];

        if ($customerId !== null) {
            $params['customer'] = $customerId;
        } else {
            $params['customer_creation'] = 'always';
        }

        return $this->stripe->createCheckoutSession($params);
    }
}

