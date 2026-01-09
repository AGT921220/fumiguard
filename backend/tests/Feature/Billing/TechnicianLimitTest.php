<?php

namespace Tests\Feature\Billing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TechnicianLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_plan_limits_block_creating_technicians(): void
    {
        $this->seed();

        $login = $this->postJson('/api/v1/login', [
            'email' => 'admin@demo.test',
            'password' => 'password',
        ])->assertOk();

        $token = (string) $login->json('token');

        $tenantId = (int) DB::table('tenants')->value('id');
        DB::table('subscriptions')->where('tenant_id', $tenantId)->update([
            'status' => 'trialing',
            'limits_json' => json_encode(['max_technicians' => 0, 'max_work_orders_per_month' => 100]),
        ]);

        $res = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/users/technicians', [
                'name' => 'Tech 1',
                'email' => 'tech1@demo.test',
                'password' => 'password123',
            ]);

        $res->assertStatus(400);
        $res->assertJsonStructure(['message', 'errors', 'trace_id']);
    }
}

