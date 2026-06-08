<?php

declare(strict_types=1);

namespace App\Actions\System;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

final class ReadinessCheckAction
{
    /**
     * @return array{
     *     healthy: bool,
     *     components: array<string, array<string, string>>,
     *     failures: array<string, list<string>>,
     *     timestamp: string
     * }
     */
    public function handle(): array
    {
        $components = [];
        $failures = [];

        try {
            DB::connection()->select('SELECT 1');

            $components['database'] = [
                'status' => 'up',
                'connection' => DB::getDefaultConnection(),
            ];
        } catch (Throwable $throwable) {
            $components['database'] = [
                'status' => 'down',
                'connection' => DB::getDefaultConnection(),
            ];
            $failures['database'] = [$throwable->getMessage()];
        }

        $cacheStore = (string) config('cache.default');
        $cacheKey = sprintf('readiness:%s', bin2hex(random_bytes(8)));

        try {
            Cache::put($cacheKey, 'ok', 10);

            if (Cache::get($cacheKey) !== 'ok') {
                throw new RuntimeException('Cache round-trip verification failed.');
            }

            Cache::forget($cacheKey);

            $components['cache'] = [
                'status' => 'up',
                'store' => $cacheStore,
            ];
        } catch (Throwable $throwable) {
            $components['cache'] = [
                'status' => 'down',
                'store' => $cacheStore,
            ];
            $failures['cache'] = [$throwable->getMessage()];
        }

        return [
            'healthy' => $failures === [],
            'components' => $components,
            'failures' => $failures,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
