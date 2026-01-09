<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_and_user(): void
    {
        $this->seed();

        $response = $this->postJson('/api/v1/login', [
            'email' => 'admin@demo.test',
            'password' => 'password',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email', 'tenant_id', 'role'],
        ]);
    }

    public function test_invalid_login_returns_error_shape_with_trace_id(): void
    {
        $this->seed();

        $response = $this->postJson('/api/v1/login', [
            'email' => 'admin@demo.test',
            'password' => 'wrong',
        ]);

        $response->assertStatus(401);
        $response->assertJsonStructure(['message', 'errors', 'trace_id']);
    }
}

