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

    public function index(ListPermissionsAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Permissions retrieved successfully.',
            data: $action->handle(),
        );
    }

    public function store(StorePermissionRequest $request, CreatePermissionAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Permission created successfully.',
            data: $action->handle($request->validatedData()),
            status: Response::HTTP_CREATED,
        );
    }

    public function show(Permission $permission, ShowPermissionAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Permission retrieved successfully.',
            data: $action->handle($permission),
        );
    }

    public function update(UpdatePermissionRequest $request, Permission $permission, UpdatePermissionAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Permission updated successfully.',
            data: $action->handle($permission, $request->validatedData()),
        );
    }

    public function destroy(Permission $permission, DeletePermissionAction $action): JsonResponse
    {
        $action->handle($permission);

        return $this->successResponse(
            message: 'Permission deleted successfully.',
            data: null,
        );
    }
}
