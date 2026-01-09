<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Ports\SubscriptionRepository;
use App\Infrastructure\Persistence\Eloquent\Models\Subscription;

final class EloquentSubscriptionRepository implements SubscriptionRepository
{
    public function getForTenant(int $tenantId): ?array
    {
        $s = Subscription::query()->where('tenant_id', $tenantId)->first();

        return $s ? $this->toArray($s) : null;
    }

    public function upsertForTenant(array $data): array
    {
        $s = Subscription::query()->updateOrCreate(
            ['tenant_id' => $data['tenant_id']],
            [
                'plan_key' => $data['plan_key'],
                'status' => $data['status'],
                'stripe_customer_id' => $data['stripe_customer_id'] ?? null,
                'stripe_subscription_id' => $data['stripe_subscription_id'] ?? null,
                'current_period_end' => $data['current_period_end'] ?? null,
                'limits_json' => $data['limits_json'] ?? null,
            ]
        );

        return [
            'id' => (int) $s->id,
            'tenant_id' => (int) $s->tenant_id,
            'plan_key' => (string) $s->plan_key,
            'status' => (string) $s->status,
        ];
    }

    public function setStripeCustomerId(int $tenantId, string $stripeCustomerId): void
    {
        Subscription::query()->updateOrCreate(
            ['tenant_id' => $tenantId],
            [
                'plan_key' => 'BASIC',
                'status' => 'incomplete',
                'stripe_customer_id' => $stripeCustomerId,
            ]
        );
    }

    private function toArray(Subscription $s): array
    {
        return [
            'id' => (int) $s->id,
            'tenant_id' => (int) $s->tenant_id,
            'plan_key' => (string) $s->plan_key,
            'status' => (string) $s->status,
            'stripe_customer_id' => $s->stripe_customer_id !== null ? (string) $s->stripe_customer_id : null,
            'stripe_subscription_id' => $s->stripe_subscription_id !== null ? (string) $s->stripe_subscription_id : null,
            'current_period_end' => $s->current_period_end?->toISOString(),
            'limits_json' => $s->limits_json,
        ];
    }
}

