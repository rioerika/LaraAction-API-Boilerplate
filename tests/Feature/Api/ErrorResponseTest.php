<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class ErrorResponseTest extends TestCase
{
    public function test_not_found_uses_standard_error_payload(): void
    {
        $this->getJson('/api/v1/missing-endpoint')
            ->assertNotFound()
            ->assertExactJson([
                'success' => false,
                'message' => 'Resource not found.',
                'errors' => [],
            ]);
    }

    public function test_unauthenticated_endpoint_uses_standard_error_payload(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertUnauthorized()
            ->assertExactJson([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => [],
            ]);
    }

    public function test_validation_errors_use_standard_error_payload(): void
    {
        $this->postJson('/api/v1/auth/login', [])
            ->assertUnprocessable()
            ->assertJson([
                'success' => false,
                'message' => 'The given data was invalid.',
            ])
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_unexpected_exceptions_use_standard_error_payload(): void
    {
        Route::get('/api/v1/test-error', static function (): void {
            throw new RuntimeException('Boom');
        });

        $this->getJson('/api/v1/test-error')
            ->assertStatus(500)
            ->assertExactJson([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'errors' => [],
            ]);
    }
}
