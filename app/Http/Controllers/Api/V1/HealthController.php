<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\System\HealthCheckAction;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

final class HealthController extends ApiController
{
    #[OA\Get(
        path: '/health',
        tags: ['System'],
        summary: 'Check application liveness.',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Health check completed successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/HealthResponse'),
            ),
        ],
    )]
    public function __invoke(HealthCheckAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Health check completed successfully.',
            data: $action->handle(),
        );
    }
}
