<?php

namespace App\Application\UseCases\Billing;

use App\Application\Ports\Billing\StripeGateway;
use App\Application\Ports\PlanCatalog;
use App\Application\Ports\SubscriptionRepository;
use App\Domain\Enums\PlanKey;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class HandleStripeWebhookUseCase
{
    public function __construct(
        private StripeGateway $stripe,
        private SubscriptionRepository $subscriptions,
        private PlanCatalog $plans,
    ) {
    }

    public function execute(string $payload, string $sigHeader): void
    {
        $secret = (string) config('billing.stripe.webhook_secret');
        if ($secret === '') {
            throw new BadRequestHttpException('Falta STRIPE_WEBHOOK_SECRET.');
        }

        $event = $this->stripe->constructWebhookEvent($payload, $sigHeader, $secret);
        $type = $event['type'];
        $obj = $event['data'];

        match ($type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($obj),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($obj),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($obj),
            'invoice.payment_failed' => $this->handleInvoicePaymentFailed($obj),
            default => null,
        };
    }

    private function handleCheckoutCompleted(array $session): void
    {
        // We rely on subscription.updated for full truth; here we only persist customer id quickly.
        $metadata = (array) ($session['metadata'] ?? []);
        $tenantId = (int) ($metadata['tenant_id'] ?? 0);

        $customerId = $session['customer'] ?? null;
        if ($tenantId > 0 && is_string($customerId) && $customerId !== '') {
            $this->subscriptions->setStripeCustomerId($tenantId, $customerId);
        }
    }

    private function handleSubscriptionUpdated(array $subscription): void
    {
        $metadata = (array) ($subscription['metadata'] ?? []);
        $tenantId = (int) ($metadata['tenant_id'] ?? 0);
        $planKey = (string) ($metadata['plan_key'] ?? null);

        if ($tenantId <= 0 || $planKey === null || $planKey === '') {
            // Fallback: cannot map to tenant without metadata (source of truth requirement implies we should set it).
            return;
        }

        $status = (string) ($subscription['status'] ?? 'incomplete');
        $customerId = is_string($subscription['customer'] ?? null) ? (string) $subscription['customer'] : null;
        $subId = is_string($subscription['id'] ?? null) ? (string) $subscription['id'] : null;
        $periodEnd = isset($subscription['current_period_end']) ? (int) $subscription['current_period_end'] : null;

        $plan = PlanKey::from($planKey);
        $limits = $this->plans->limitsFor($plan);

        $this->subscriptions->upsertForTenant([
            'tenant_id' => $tenantId,
            'plan_key' => $plan->value,
            'status' => $status,
            'stripe_customer_id' => $customerId,
            'stripe_subscription_id' => $subId,
            'current_period_end' => $periodEnd ? gmdate('Y-m-d H:i:s', $periodEnd) : null,
            'limits_json' => $limits,
        ]);
    }

    private function handleSubscriptionDeleted(array $subscription): void
    {
        // Treat as non-active; keep last known plan/limits.
        $metadata = (array) ($subscription['metadata'] ?? []);
        $tenantId = (int) ($metadata['tenant_id'] ?? 0);
        if ($tenantId <= 0) {
            return;
        }

        $current = $this->subscriptions->getForTenant($tenantId);
        $planKey = (string) ($metadata['plan_key'] ?? ($current['plan_key'] ?? 'BASIC'));

        $this->subscriptions->upsertForTenant([
            'tenant_id' => $tenantId,
            'plan_key' => $planKey,
            'status' => 'canceled',
            'stripe_customer_id' => $current['stripe_customer_id'] ?? null,
            'stripe_subscription_id' => is_string($subscription['id'] ?? null) ? (string) $subscription['id'] : ($current['stripe_subscription_id'] ?? null),
            'current_period_end' => $current['current_period_end'] ?? null,
            'limits_json' => $current['limits_json'] ?? $this->plans->limitsFor(PlanKey::from($planKey)),
        ]);
    }

    private function handleInvoicePaymentFailed(array $invoice): void
    {
        // Stripe is source of truth; this event indicates an issue, but we still store a non-active-ish status.
        // We may not have tenant metadata on invoice; ignore if missing.
        $subscriptionId = is_string($invoice['subscription'] ?? null) ? (string) $invoice['subscription'] : null;
        if ($subscriptionId === null) {
            return;
        }

        // Best-effort: no tenant mapping; rely on customer.subscription.updated/delete for tenant-bound updates.
    }
}

