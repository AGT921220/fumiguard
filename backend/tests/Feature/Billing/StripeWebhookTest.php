<?php

namespace Tests\Feature\Billing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function stripeSignature(string $payload, string $secret, int $timestamp): string
    {
        $signedPayload = $timestamp.'.'.$payload;
        $sig = hash_hmac('sha256', $signedPayload, $secret);

        return "t={$timestamp},v1={$sig}";
    }

    public function test_webhook_updates_subscription_from_stripe_event(): void
    {
        $this->seed();

        $tenantId = (int) DB::table('tenants')->value('id');

        $secret = 'whsec_test_123';
        config()->set('billing.stripe.webhook_secret', $secret);

        $payload = json_encode([
            'id' => 'evt_test_1',
            'object' => 'event',
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'id' => 'sub_test_1',
                    'object' => 'subscription',
                    'status' => 'active',
                    'customer' => 'cus_test_1',
                    'current_period_end' => time() + 3600,
                    'metadata' => [
                        'tenant_id' => (string) $tenantId,
                        'plan_key' => 'PRO',
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $sig = $this->stripeSignature($payload, $secret, time());

        $this->call(
            'POST',
            '/api/v1/billing/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => $sig,
            ],
            $payload
        )->assertOk();

        $row = DB::table('subscriptions')->where('tenant_id', $tenantId)->first();
        $this->assertNotNull($row);
        $this->assertSame('PRO', $row->plan_key);
        $this->assertSame('active', $row->status);
        $this->assertSame('cus_test_1', $row->stripe_customer_id);
        $this->assertSame('sub_test_1', $row->stripe_subscription_id);
    }
}

