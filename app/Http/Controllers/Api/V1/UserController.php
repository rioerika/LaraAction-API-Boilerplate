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

    public function index(ListUsersAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'Users retrieved successfully.',
            data: $action->handle(),
        );
    }

    public function store(StoreUserRequest $request, CreateUserAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'User created successfully.',
            data: $action->handle($request->validatedData()),
            status: Response::HTTP_CREATED,
        );
    }

    public function show(User $user, ShowUserAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'User retrieved successfully.',
            data: $action->handle($user),
        );
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): JsonResponse
    {
        return $this->successResponse(
            message: 'User updated successfully.',
            data: $action->handle($user, $request->validatedData()),
        );
    }

    public function destroy(User $user, DeleteUserAction $action): JsonResponse
    {
        $action->handle($user);

        return $this->successResponse(
            message: 'User deleted successfully.',
            data: null,
        );
    }
}
