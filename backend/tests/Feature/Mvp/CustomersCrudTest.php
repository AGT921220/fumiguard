<?php

namespace Tests\Feature\Mvp;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomersCrudTest extends TestCase
{
    use RefreshDatabase;

    private function loginToken(): string
    {
        $this->seed();

        $login = $this->postJson('/api/v1/login', [
            'email' => 'admin@demo.test',
            'password' => 'password',
        ])->assertOk();

        return (string) $login->json('token');
    }

    public function test_customers_crud(): void
    {
        $token = $this->loginToken();

        $created = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/customers', [
                'name' => 'ACME',
                'email' => 'ops@acme.test',
            ])
            ->assertStatus(201);

        $customerId = (int) $created->json('id');
        $this->assertGreaterThan(0, $customerId);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/customers')
            ->assertOk()
            ->assertJsonFragment(['id' => $customerId, 'name' => 'ACME']);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/customers/'.$customerId, [
                'phone' => '+52 55 0000 0000',
            ])
            ->assertOk()
            ->assertJsonFragment(['phone' => '+52 55 0000 0000']);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/customers/'.$customerId)
            ->assertOk()
            ->assertJson(['ok' => true]);
    }
}

