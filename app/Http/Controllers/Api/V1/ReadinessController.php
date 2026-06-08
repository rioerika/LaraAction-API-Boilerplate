<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\System\ReadinessCheckAction;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

final class ReadinessController extends ApiController
{
    #[OA\Get(
        path: '/readiness',
        tags: ['System'],
        summary: 'Check database and cache readiness.',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Readiness check completed successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/ReadinessResponse'),
            ),
            new OA\Response(
                response: 503,
                description: 'Readiness check failed.',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'),
            ),
        ],
    )]
    public function __invoke(ReadinessCheckAction $action): JsonResponse
    {
        $result = $action->handle();

        if ($result['healthy']) {
            return $this->successResponse(
                message: 'Readiness check completed successfully.',
                data: [
                    'components' => $result['components'],
                    'timestamp' => $result['timestamp'],
                ],
            );
        }

        return $this->errorResponse(
            message: 'Readiness check failed.',
            errors: $result['failures'],
            status: Response::HTTP_SERVICE_UNAVAILABLE,
        );
    }
}
