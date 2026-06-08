<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\System\HealthCheckAction;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;

final class HealthController extends ApiController
{
    public function __invoke(HealthCheckAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Health check completed successfully.',
            data: $action->handle(),
        );
    }
}
