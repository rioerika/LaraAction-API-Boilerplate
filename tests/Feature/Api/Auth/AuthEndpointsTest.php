<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_and_user_payload(): void
    {
        $this->seed();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@example.com',
            'password' => 'password',
            'device_name' => 'phpunit',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Login completed successfully.',
                'data' => [
                    'token_type' => 'Bearer',
                    'user' => [
                        'email' => 'superadmin@example.com',
                    ],
                ],
            ])
            ->assertJsonPath('data.user.roles.0.name', 'SuperAdmin');

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        $this->seed();

        $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@example.com',
            'password' => 'invalid-password',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_me_returns_authenticated_user(): void
    {
        $this->seed();

        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@example.com',
            'password' => 'password',
        ]);

        $token = (string) $loginResponse->json('data.token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'superadmin@example.com');
    }

    public function test_logout_revokes_current_token(): void
    {
        $this->seed();

        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@example.com',
            'password' => 'password',
        ]);

        $token = (string) $loginResponse->json('data.token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertExactJson([
                'success' => true,
                'message' => 'Logout completed successfully.',
                'data' => null,
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
