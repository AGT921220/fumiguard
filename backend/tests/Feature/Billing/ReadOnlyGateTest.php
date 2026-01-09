<?php

namespace Tests\Feature\Billing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReadOnlyGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_subscription_blocks_mutations(): void
    {
        $this->seed();

        $login = $this->postJson('/api/v1/login', [
            'email' => 'admin@demo.test',
            'password' => 'password',
        ])->assertOk();

        $token = (string) $login->json('token');

        $tenantId = (int) DB::table('tenants')->value('id');
        DB::table('subscriptions')->where('tenant_id', $tenantId)->update(['status' => 'past_due']);

        $res = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/customers', ['name' => 'Blocked']);

        $res->assertStatus(403);
        $res->assertJsonStructure(['message', 'errors', 'trace_id']);
    }
}

