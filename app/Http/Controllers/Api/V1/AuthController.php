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
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

final class AuthController extends ApiController
{
    #[OA\Post(
        path: '/auth/login',
        tags: ['Auth'],
        summary: 'Authenticate a user and issue a Sanctum token.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoginRequestBody'),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Authenticated successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/LoginResponse'),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed or credentials are invalid.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
            new OA\Response(
                response: 429,
                description: 'Too many login attempts.',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'),
            ),
        ],
    )]
    public function login(LoginRequest $request, LoginAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Login completed successfully.',
            data: $action->handle($request->validatedData()),
            status: Response::HTTP_OK,
        );
    }

    #[OA\Post(
        path: '/auth/logout',
        tags: ['Auth'],
        summary: 'Revoke the current authenticated Sanctum token.',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logged out successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/EmptySuccessResponse'),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated.',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'),
            ),
        ],
    )]
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

    #[OA\Get(
        path: '/auth/me',
        tags: ['Auth'],
        summary: 'Get the current authenticated user.',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Authenticated user retrieved successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/UserResponse'),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated.',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'),
            ),
        ],
    )]
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
