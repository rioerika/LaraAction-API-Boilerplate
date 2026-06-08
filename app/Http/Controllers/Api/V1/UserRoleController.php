<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Users\AssignRolesToUserAction;
use App\Actions\Users\RevokeRolesFromUserAction;
use App\Actions\Users\SyncUserRolesAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Users\UserRolesSyncRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use OpenApi\Attributes as OA;

final class UserRoleController extends ApiController implements HasMiddleware
{
    /**
     * @return array<int, Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:assign roles'),
        ];
    }

    #[OA\Post(
        path: '/users/{user}/roles',
        tags: ['Users'],
        summary: 'Assign roles to a user.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'User ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UserRolesSyncRequestBody')),
        responses: [
            new OA\Response(response: 200, description: 'Roles assigned successfully.', content: new OA\JsonContent(ref: '#/components/schemas/UserResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function assign(UserRolesSyncRequest $request, User $user, AssignRolesToUserAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Roles assigned successfully.',
            data: $action->handle($user, $request->validatedData()['roles']),
        );
    }

    #[OA\Delete(
        path: '/users/{user}/roles',
        tags: ['Users'],
        summary: 'Revoke roles from a user.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'User ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UserRolesSyncRequestBody')),
        responses: [
            new OA\Response(response: 200, description: 'Roles revoked successfully.', content: new OA\JsonContent(ref: '#/components/schemas/UserResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function revoke(UserRolesSyncRequest $request, User $user, RevokeRolesFromUserAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Roles revoked successfully.',
            data: $action->handle($user, $request->validatedData()['roles']),
        );
    }

    #[OA\Put(
        path: '/users/{user}/roles',
        tags: ['Users'],
        summary: 'Synchronize a user role set.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'User ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UserRolesSyncRequestBody')),
        responses: [
            new OA\Response(response: 200, description: 'Roles synchronized successfully.', content: new OA\JsonContent(ref: '#/components/schemas/UserResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function sync(UserRolesSyncRequest $request, User $user, SyncUserRolesAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Roles synchronized successfully.',
            data: $action->handle($user, $request->validatedData()['roles']),
        );
    }
}
