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

    public function assign(UserRolesSyncRequest $request, User $user, AssignRolesToUserAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Roles assigned successfully.',
            data: $action->handle($user, $request->validatedData()['roles']),
        );
    }

    public function revoke(UserRolesSyncRequest $request, User $user, RevokeRolesFromUserAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Roles revoked successfully.',
            data: $action->handle($user, $request->validatedData()['roles']),
        );
    }

    public function sync(UserRolesSyncRequest $request, User $user, SyncUserRolesAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Roles synchronized successfully.',
            data: $action->handle($user, $request->validatedData()['roles']),
        );
    }
}
