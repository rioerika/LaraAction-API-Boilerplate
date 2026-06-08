<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\LoginAction;
use App\Actions\Auth\LogoutAction;
use App\Actions\Auth\ShowCurrentUserAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class AuthController extends ApiController
{
    public function login(LoginRequest $request, LoginAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Login completed successfully.',
            data: $action->handle($request->validatedData()),
            status: Response::HTTP_OK,
        );
    }

    public function logout(LogoutAction $action): JsonResponse
    {
        /** @var User $user */
        $user = request()->user();

        $action->handle($user);

        return $this->successResponse(
            message: 'Logout completed successfully.',
            data: null,
        );
    }

    public function me(ShowCurrentUserAction $action): JsonResponse
    {
        /** @var User $user */
        $user = request()->user();

        return $this->successResponse(
            message: 'Authenticated user retrieved successfully.',
            data: $action->handle($user),
        );
    }
}
