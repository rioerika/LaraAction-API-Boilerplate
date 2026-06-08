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

    public function assign(RolePermissionsSyncRequest $request, Role $role, AssignPermissionsToRoleAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Permissions assigned successfully.',
            data: $action->handle($role, $request->validatedData()['permissions']),
        );
    }

    public function revoke(RolePermissionsSyncRequest $request, Role $role, RevokePermissionsFromRoleAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Permissions revoked successfully.',
            data: $action->handle($role, $request->validatedData()['permissions']),
        );
    }

    public function sync(RolePermissionsSyncRequest $request, Role $role, SyncRolePermissionsAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Permissions synchronized successfully.',
            data: $action->handle($role, $request->validatedData()['permissions']),
        );
    }
}
