<?php

namespace App\Application\Ports;

interface SubscriptionRepository
{
    /**
     * @return array{
     *   id:int,
     *   tenant_id:int,
     *   plan_key:string,
     *   status:string,
     *   stripe_customer_id:?string,
     *   stripe_subscription_id:?string,
     *   current_period_end:?string,
     *   limits_json:mixed
     * }|null
     */
    public function getForTenant(int $tenantId): ?array;

    /**
     * @param array{
     *   tenant_id:int,
     *   plan_key:string,
     *   status:string,
     *   stripe_customer_id:?string,
     *   stripe_subscription_id:?string,
     *   current_period_end:?string,
     *   limits_json:mixed
     * } $data
     * @return array{id:int,tenant_id:int,plan_key:string,status:string}
     */
    public function upsertForTenant(array $data): array;

    public function setStripeCustomerId(int $tenantId, string $stripeCustomerId): void;
}

