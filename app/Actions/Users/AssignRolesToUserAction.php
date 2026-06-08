<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Actions\Audit\RecordAuditLogAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class AssignRolesToUserAction
{
    public function __construct(
        private readonly RecordAuditLogAction $recordAuditLogAction,
    ) {}

    /**
     * @param  list<string>  $roles
     */
    public function handle(User $user, array $roles): User
    {
        $previousRoles = $this->roleNames($user);

        /** @var User $updatedUser */
        $updatedUser = DB::transaction(function () use ($user, $roles, $previousRoles): User {
            $user->assignRole($roles);

            $updatedUser = $user->load(['roles:id,name,guard_name', 'permissions:id,name,guard_name']);
            $currentRoles = $this->roleNames($updatedUser);

            $this->recordAuditLogAction->handle(
                event: 'user.roles.assigned',
                subject: $user,
                oldValues: ['roles' => $previousRoles],
                newValues: ['roles' => $currentRoles],
                metadata: [
                    'requested_roles' => $roles,
                    'added_roles' => array_values(array_diff($currentRoles, $previousRoles)),
                ],
            );

            return $updatedUser;
        });

        return $updatedUser;
    }

    /**
     * @return list<string>
     */
    private function roleNames(User $user): array
    {
        return $user->roles()->pluck('name')->map(static fn (mixed $role): string => (string) $role)->values()->all();
    }
}
