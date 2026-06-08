<?php

declare(strict_types=1);

namespace App\Actions\System;

final class HealthCheckAction
{
    /**
     * @return array<string, string>
     */
    public function handle(): array
    {
        return [
            'status' => 'ok',
            'app' => (string) config('app.name'),
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
