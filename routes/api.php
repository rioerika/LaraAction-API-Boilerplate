<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\PermissionController;
use App\Http\Controllers\Api\V1\ReadinessController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\RolePermissionController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', HealthController::class);
    Route::get('/readiness', ReadinessController::class);

    Route::prefix('auth')->group(function (): void {
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth.login');

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::apiResource('users', UserController::class);
        Route::post('users/{user}/roles', [UserRoleController::class, 'assign']);
        Route::delete('users/{user}/roles', [UserRoleController::class, 'revoke']);
        Route::put('users/{user}/roles', [UserRoleController::class, 'sync']);

        Route::apiResource('roles', RoleController::class);
        Route::post('roles/{role}/permissions', [RolePermissionController::class, 'assign']);
        Route::delete('roles/{role}/permissions', [RolePermissionController::class, 'revoke']);
        Route::put('roles/{role}/permissions', [RolePermissionController::class, 'sync']);

        Route::apiResource('permissions', PermissionController::class);
    });
});
