<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\DeleteUserAction;
use App\Actions\Users\ListUsersAction;
use App\Actions\Users\ShowUserAction;
use App\Actions\Users\UpdateUserAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Users\StoreUserRequest;
use App\Http\Requests\Api\Users\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

final class UserController extends ApiController implements HasMiddleware
{
    /**
     * @return array<int, Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view users', only: ['index', 'show']),
            new Middleware('permission:create users', only: ['store']),
            new Middleware('permission:update users', only: ['update']),
            new Middleware('permission:delete users', only: ['destroy']),
        ];
    }

    #[OA\Get(
        path: '/users',
        tags: ['Users'],
        summary: 'List users.',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Users retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/UserListResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function index(ListUsersAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Users retrieved successfully.',
            data: $action->handle(),
        );
    }

    #[OA\Post(
        path: '/users',
        tags: ['Users'],
        summary: 'Create a new user.',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/StoreUserRequestBody')),
        responses: [
            new OA\Response(response: 201, description: 'User created successfully.', content: new OA\JsonContent(ref: '#/components/schemas/UserResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function store(StoreUserRequest $request, CreateUserAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'User created successfully.',
            data: $action->handle($request->validatedData()),
            status: Response::HTTP_CREATED,
        );
    }

    #[OA\Get(
        path: '/users/{user}',
        tags: ['Users'],
        summary: 'Show a single user.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'User ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'User retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/UserResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function show(User $user, ShowUserAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'User retrieved successfully.',
            data: $action->handle($user),
        );
    }

    #[OA\Put(
        path: '/users/{user}',
        tags: ['Users'],
        summary: 'Update an existing user.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'User ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UpdateUserRequestBody')),
        responses: [
            new OA\Response(response: 200, description: 'User updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/UserResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'User updated successfully.',
            data: $action->handle($user, $request->validatedData()),
        );
    }

    #[OA\Delete(
        path: '/users/{user}',
        tags: ['Users'],
        summary: 'Delete a user.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'User ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'User deleted successfully.', content: new OA\JsonContent(ref: '#/components/schemas/EmptySuccessResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function destroy(User $user, DeleteUserAction $action): JsonResponse
    {
        $action->handle($user);

        return $this->successResponse(
            message: 'User deleted successfully.',
            data: null,
        );
    }
}
