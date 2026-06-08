<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Permissions\CreatePermissionAction;
use App\Actions\Permissions\DeletePermissionAction;
use App\Actions\Permissions\ListPermissionsAction;
use App\Actions\Permissions\ShowPermissionAction;
use App\Actions\Permissions\UpdatePermissionAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Permissions\StorePermissionRequest;
use App\Http\Requests\Api\Permissions\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

final class PermissionController extends ApiController implements HasMiddleware
{
    /**
     * @return array<int, Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view permissions', only: ['index', 'show']),
            new Middleware('permission:create permissions', only: ['store']),
            new Middleware('permission:update permissions', only: ['update']),
            new Middleware('permission:delete permissions', only: ['destroy']),
        ];
    }

    #[OA\Get(
        path: '/permissions',
        tags: ['Permissions'],
        summary: 'List permissions.',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Permissions retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/PermissionListResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function index(ListPermissionsAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Permissions retrieved successfully.',
            data: $action->handle(),
        );
    }

    #[OA\Post(
        path: '/permissions',
        tags: ['Permissions'],
        summary: 'Create a permission.',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/StorePermissionRequestBody')),
        responses: [
            new OA\Response(response: 201, description: 'Permission created successfully.', content: new OA\JsonContent(ref: '#/components/schemas/PermissionResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function store(StorePermissionRequest $request, CreatePermissionAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Permission created successfully.',
            data: $action->handle($request->validatedData()),
            status: Response::HTTP_CREATED,
        );
    }

    #[OA\Get(
        path: '/permissions/{permission}',
        tags: ['Permissions'],
        summary: 'Show a single permission.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'permission', in: 'path', required: true, description: 'Permission ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Permission retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/PermissionResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function show(Permission $permission, ShowPermissionAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Permission retrieved successfully.',
            data: $action->handle($permission),
        );
    }

    #[OA\Put(
        path: '/permissions/{permission}',
        tags: ['Permissions'],
        summary: 'Update a permission.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'permission', in: 'path', required: true, description: 'Permission ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UpdatePermissionRequestBody')),
        responses: [
            new OA\Response(response: 200, description: 'Permission updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/PermissionResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function update(UpdatePermissionRequest $request, Permission $permission, UpdatePermissionAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Permission updated successfully.',
            data: $action->handle($permission, $request->validatedData()),
        );
    }

    #[OA\Delete(
        path: '/permissions/{permission}',
        tags: ['Permissions'],
        summary: 'Delete a permission.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'permission', in: 'path', required: true, description: 'Permission ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Permission deleted successfully.', content: new OA\JsonContent(ref: '#/components/schemas/EmptySuccessResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Forbidden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Resource not found.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function destroy(Permission $permission, DeletePermissionAction $action): JsonResponse
    {
        $action->handle($permission);

        return $this->successResponse(
            message: 'Permission deleted successfully.',
            data: null,
        );
    }
}
