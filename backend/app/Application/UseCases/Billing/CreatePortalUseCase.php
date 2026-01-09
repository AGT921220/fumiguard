<?php

namespace App\Application\UseCases\Billing;

use App\Application\Ports\Billing\StripeGateway;
use App\Application\Ports\SubscriptionRepository;
use App\Application\Tenancy\TenantContext;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class CreatePortalUseCase
{
    public function __construct(
        private StripeGateway $stripe,
        private SubscriptionRepository $subscriptions,
        private TenantContext $tenantContext,
    ) {
    }

    /**
     * @return array{url:string,id:string}
     */
    public function execute(): array
    {
        $tenantId = $this->tenantContext->requireTenantId();
        $sub = $this->subscriptions->getForTenant($tenantId);

        $customerId = $sub['stripe_customer_id'] ?? null;
        if ($customerId === null) {
            throw new BadRequestHttpException('Tenant sin customer en Stripe.');
        }

        $returnUrl = (string) config('billing.stripe.portal_return_url');
        if ($returnUrl === '') {
            throw new BadRequestHttpException('Falta STRIPE_PORTAL_RETURN_URL.');
        }

        return $this->stripe->createBillingPortalSession([
            'customer' => $customerId,
            'return_url' => $returnUrl,
        ]);
    }
}

