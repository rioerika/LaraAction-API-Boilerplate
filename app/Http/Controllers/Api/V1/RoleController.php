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

    public function index(ListRolesAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Roles retrieved successfully.',
            data: $action->handle(),
        );
    }

    public function store(StoreRoleRequest $request, CreateRoleAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Role created successfully.',
            data: $action->handle($request->validatedData()),
            status: Response::HTTP_CREATED,
        );
    }

    public function show(Role $role, ShowRoleAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Role retrieved successfully.',
            data: $action->handle($role),
        );
    }

    public function update(UpdateRoleRequest $request, Role $role, UpdateRoleAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Role updated successfully.',
            data: $action->handle($role, $request->validatedData()),
        );
    }

    public function destroy(Role $role, DeleteRoleAction $action): JsonResponse
    {
        $action->handle($role);

        return $this->successResponse(
            message: 'Role deleted successfully.',
            data: null,
        );
    }
}
