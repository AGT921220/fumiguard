<?php

namespace App\Infrastructure\Billing;

use App\Application\Ports\Billing\StripeGateway;
use Stripe\BillingPortal\Session as BillingPortalSession;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Event;
use Stripe\StripeClient;
use Stripe\Webhook;

final class StripePhpGateway implements StripeGateway
{
    private ?StripeClient $client = null;

    private function client(): StripeClient
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $secret = (string) config('billing.stripe.secret');
        if ($secret === '') {
            throw new \InvalidArgumentException('STRIPE_SECRET_KEY no puede estar vacío.');
        }

        $this->client = new StripeClient($secret);

        return $this->client;
    }

    public function createCheckoutSession(array $params): array
    {
        /** @var CheckoutSession $session */
        $session = $this->client()->checkout->sessions->create($params);

        return [
            'id' => (string) $session->id,
            'url' => (string) $session->url,
        ];
    }

    public function createBillingPortalSession(array $params): array
    {
        /** @var BillingPortalSession $session */
        $session = $this->client()->billingPortal->sessions->create($params);

        return [
            'id' => (string) $session->id,
            'url' => (string) $session->url,
        ];
    }

    public function constructWebhookEvent(string $payload, string $sigHeader, string $secret): array
    {
        /** @var Event $event */
        $event = Webhook::constructEvent($payload, $sigHeader, $secret);

        $decoded = json_decode($event->toJSON(), true);
        $obj = is_array($decoded) ? ($decoded['data']['object'] ?? []) : [];

        return [
            'type' => (string) $event->type,
            'data' => is_array($obj) ? $obj : [],
        ];
    }
}

