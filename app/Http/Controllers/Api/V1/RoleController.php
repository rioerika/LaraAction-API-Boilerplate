<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Roles\CreateRoleAction;
use App\Actions\Roles\DeleteRoleAction;
use App\Actions\Roles\ListRolesAction;
use App\Actions\Roles\ShowRoleAction;
use App\Actions\Roles\UpdateRoleAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Roles\StoreRoleRequest;
use App\Http\Requests\Api\Roles\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

final class RoleController extends ApiController implements HasMiddleware
{
    /**
     * @return array<int, Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view roles', only: ['index', 'show']),
            new Middleware('permission:create roles', only: ['store']),
            new Middleware('permission:update roles', only: ['update']),
            new Middleware('permission:delete roles', only: ['destroy']),
        ];
    }

    #[OA\Get(
        path: '/roles',
        tags: ['Roles'],
        summary: 'List roles.',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Roles retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/RoleListResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function index(ListRolesAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Roles retrieved successfully.',
            data: $action->handle(),
        );
    }

    #[OA\Post(
        path: '/roles',
        tags: ['Roles'],
        summary: 'Create a role.',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/StoreRoleRequestBody')),
        responses: [
            new OA\Response(response: 201, description: 'Role created successfully.', content: new OA\JsonContent(ref: '#/components/schemas/RoleResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function store(StoreRoleRequest $request, CreateRoleAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Role created successfully.',
            data: $action->handle($request->validatedData()),
            status: Response::HTTP_CREATED,
        );
    }

    #[OA\Get(
        path: '/roles/{role}',
        tags: ['Roles'],
        summary: 'Show a single role.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'role', in: 'path', required: true, description: 'Role ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Role retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/RoleResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function show(Role $role, ShowRoleAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Role retrieved successfully.',
            data: $action->handle($role),
        );
    }

    #[OA\Put(
        path: '/roles/{role}',
        tags: ['Roles'],
        summary: 'Update a role.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'role', in: 'path', required: true, description: 'Role ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UpdateRoleRequestBody')),
        responses: [
            new OA\Response(response: 200, description: 'Role updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/RoleResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function update(UpdateRoleRequest $request, Role $role, UpdateRoleAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Role updated successfully.',
            data: $action->handle($role, $request->validatedData()),
        );
    }

    #[OA\Delete(
        path: '/roles/{role}',
        tags: ['Roles'],
        summary: 'Delete a role.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'role', in: 'path', required: true, description: 'Role ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Role deleted successfully.', content: new OA\JsonContent(ref: '#/components/schemas/EmptySuccessResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function destroy(Role $role, DeleteRoleAction $action): JsonResponse
    {
        $action->handle($role);

        return $this->successResponse(
            message: 'Role deleted successfully.',
            data: null,
        );
    }
}
