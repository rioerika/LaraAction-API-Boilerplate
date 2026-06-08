<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class ReadinessCheckTest extends TestCase
{
    public function test_readiness_endpoint_returns_dependency_status_when_healthy(): void
    {
        $response = $this->getJson('/api/v1/readiness');

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Readiness check completed successfully.',
                'data' => [
                    'components' => [
                        'database' => [
                            'status' => 'up',
                        ],
                        'cache' => [
                            'status' => 'up',
                        ],
                    ],
                ],
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'components' => [
                        'database' => ['status', 'connection'],
                        'cache' => ['status', 'store'],
                    ],
                    'timestamp',
                ],
            ]);
    }

    public function test_readiness_endpoint_returns_service_unavailable_when_database_fails(): void
    {
        DB::shouldReceive('connection->select')
            ->once()
            ->andThrow(new RuntimeException('Database is unavailable.'));
        DB::shouldReceive('getDefaultConnection')
            ->atLeast()
            ->once()
            ->andReturn('sqlite');

        $this->getJson('/api/v1/readiness')
            ->assertStatus(503)
            ->assertExactJson([
                'success' => false,
                'message' => 'Readiness check failed.',
                'errors' => [
                    'database' => ['Database is unavailable.'],
                ],
            ]);
    }

    public function test_readiness_endpoint_returns_service_unavailable_when_cache_fails(): void
    {
        Cache::shouldReceive('put')
            ->once()
            ->andThrow(new RuntimeException('Cache store is unavailable.'));

        $this->getJson('/api/v1/readiness')
            ->assertStatus(503)
            ->assertExactJson([
                'success' => false,
                'message' => 'Readiness check failed.',
                'errors' => [
                    'cache' => ['Cache store is unavailable.'],
                ],
            ]);
    }
}
