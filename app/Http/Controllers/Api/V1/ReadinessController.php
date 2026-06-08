<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\System\ReadinessCheckAction;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ReadinessController extends ApiController
{
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
