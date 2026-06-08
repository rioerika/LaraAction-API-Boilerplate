<?php

declare(strict_types=1);

namespace App\Traits;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function successResponse(string $message, mixed $data = null, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        return ApiResponse::success(
            message: $message,
            data: $data,
            status: $status,
        );
    }

    /**
     * @param  array<string, mixed>  $errors
     */
    protected function errorResponse(string $message, array $errors = [], int $status = JsonResponse::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
    {
        return ApiResponse::error(
            message: $message,
            errors: $errors,
            status: $status,
        );
    }
}
