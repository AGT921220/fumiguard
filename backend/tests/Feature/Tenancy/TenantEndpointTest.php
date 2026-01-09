<?php

namespace Tests\Feature\Tenancy;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_endpoint_returns_current_tenant(): void
    {
        $this->seed();

        $login = $this->postJson('/api/v1/login', [
            'email' => 'admin@demo.test',
            'password' => 'password',
        ])->assertOk();

        $token = $login->json('token');
        $this->assertIsString($token);

        $tenant = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/tenant');

        $tenant->assertOk();
        $tenant->assertJsonStructure(['id', 'name', 'slug']);
        $tenant->assertJson(['slug' => 'fumiguard-demo']);
    }
}

