<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
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
        $this->assertNotNull($response->json('data.expires_at'));
        $this->assertNotNull($response->json('data.token'));
    }

    public function test_login_requires_device_name(): void
    {
        $this->seed();

        $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@example.com',
            'password' => 'password',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['device_name']);
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        $this->seed();

        $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@example.com',
            'password' => 'invalid-password',
            'device_name' => 'phpunit',
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
            'device_name' => 'phpunit',
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
            'device_name' => 'phpunit',
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

    public function test_login_replaces_existing_token_for_same_device(): void
    {
        $this->seed();

        $firstResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@example.com',
            'password' => 'password',
            'device_name' => 'mobile-app',
        ]);

        $firstResponse->assertOk();
        $firstToken = (string) $firstResponse->json('data.token');

        $secondResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@example.com',
            'password' => 'password',
            'device_name' => 'mobile-app',
        ]);

        $secondResponse->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->assertNotSame($firstToken, (string) $secondResponse->json('data.token'));
    }

    public function test_login_enforces_maximum_active_tokens_per_user(): void
    {
        $this->seed();
        Config::set('sanctum.max_tokens_per_user', 2);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@example.com',
            'password' => 'password',
            'device_name' => 'device-1',
        ])->assertOk();

        $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@example.com',
            'password' => 'password',
            'device_name' => 'device-2',
        ])->assertOk();

        $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@example.com',
            'password' => 'password',
            'device_name' => 'device-3',
        ])->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 2);
        $this->assertDatabaseMissing('personal_access_tokens', ['name' => 'device-1']);
    }

    public function test_login_is_rate_limited(): void
    {
        $this->seed();
        Config::set('sanctum.login_rate_limit', 2);

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'superadmin@example.com',
                'password' => 'invalid-password',
                'device_name' => 'phpunit',
            ])->assertUnprocessable();
        }

        $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@example.com',
            'password' => 'invalid-password',
            'device_name' => 'phpunit',
        ])
            ->assertStatus(429)
            ->assertJson([
                'success' => false,
                'message' => 'Too many login attempts. Please try again later.',
            ])
            ->assertJsonPath('errors.throttle.0', 'Retry after 60 seconds.');
    }
}
