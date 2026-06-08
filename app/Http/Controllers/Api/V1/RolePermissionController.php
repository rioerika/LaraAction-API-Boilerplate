<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Roles\AssignPermissionsToRoleAction;
use App\Actions\Roles\RevokePermissionsFromRoleAction;
use App\Actions\Roles\SyncRolePermissionsAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Roles\RolePermissionsSyncRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use OpenApi\Attributes as OA;

final class RolePermissionController extends ApiController implements HasMiddleware
{
    /**
     * @return array<int, Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:assign permissions'),
        ];
    }

    #[OA\Post(
        path: '/roles/{role}/permissions',
        tags: ['Roles'],
        summary: 'Assign permissions to a role.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'role', in: 'path', required: true, description: 'Role ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RolePermissionsSyncRequestBody')),
        responses: [
            new OA\Response(response: 200, description: 'Permissions assigned successfully.', content: new OA\JsonContent(ref: '#/components/schemas/RoleResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function assign(RolePermissionsSyncRequest $request, Role $role, AssignPermissionsToRoleAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Permissions assigned successfully.',
            data: $action->handle($role, $request->validatedData()['permissions']),
        );
    }

    #[OA\Delete(
        path: '/roles/{role}/permissions',
        tags: ['Roles'],
        summary: 'Revoke permissions from a role.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'role', in: 'path', required: true, description: 'Role ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RolePermissionsSyncRequestBody')),
        responses: [
            new OA\Response(response: 200, description: 'Permissions revoked successfully.', content: new OA\JsonContent(ref: '#/components/schemas/RoleResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function revoke(RolePermissionsSyncRequest $request, Role $role, RevokePermissionsFromRoleAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Permissions revoked successfully.',
            data: $action->handle($role, $request->validatedData()['permissions']),
        );
    }

    #[OA\Put(
        path: '/roles/{role}/permissions',
        tags: ['Roles'],
        summary: 'Synchronize a role permission set.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'role', in: 'path', required: true, description: 'Role ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RolePermissionsSyncRequestBody')),
        responses: [
            new OA\Response(response: 200, description: 'Permissions synchronized successfully.', content: new OA\JsonContent(ref: '#/components/schemas/RoleResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function sync(RolePermissionsSyncRequest $request, Role $role, SyncRolePermissionsAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Permissions synchronized successfully.',
            data: $action->handle($role, $request->validatedData()['permissions']),
        );
    }
}
