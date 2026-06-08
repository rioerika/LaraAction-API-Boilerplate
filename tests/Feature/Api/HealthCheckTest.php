<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_endpoint_returns_standard_success_payload(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Health check completed successfully.',
                'data' => [
                    'status' => 'ok',
                    'app' => 'Laravel API Boilerplate',
                ],
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['status', 'app', 'timestamp'],
            ]);
    }
}
