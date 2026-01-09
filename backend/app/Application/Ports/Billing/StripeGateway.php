<?php

namespace App\Application\Ports\Billing;

interface StripeGateway
{
    /**
     * @param array<string,mixed> $params
     * @return array{id:string,url:string}
     */
    public function createCheckoutSession(array $params): array;

    /**
     * @param array<string,mixed> $params
     * @return array{id:string,url:string}
     */
    public function createBillingPortalSession(array $params): array;

    /**
     * @return array{type:string,data:array<string,mixed>}
     */
    public function constructWebhookEvent(string $payload, string $sigHeader, string $secret): array;
}

