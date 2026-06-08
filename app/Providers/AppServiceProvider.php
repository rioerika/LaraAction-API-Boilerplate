<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\ApiResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('auth.login', function (Request $request): Limit {
            $email = strtolower((string) $request->input('email', 'guest'));
            $key = sprintf('%s|%s', $email, $request->ip());

            return Limit::perMinute((int) config('sanctum.login_rate_limit', 5))
                ->by($key)
                ->response(static function (Request $request, array $headers) {
                    $retryAfter = (string) ($headers['Retry-After'] ?? '60');

                    return ApiResponse::error(
                        message: 'Too many login attempts. Please try again later.',
                        errors: [
                            'throttle' => ["Retry after {$retryAfter} seconds."],
                        ],
                        status: Response::HTTP_TOO_MANY_REQUESTS,
                    )->withHeaders($headers);
                });
        });
    }
}
