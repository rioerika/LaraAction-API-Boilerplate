<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;

final class RevokeRolesFromUserAction
{
    /**
     * @param  list<string>  $roles
     */
    public function handle(User $user, array $roles): User
    {
        foreach ($roles as $role) {
            $user->removeRole($role);
        }

        return $user->load(['roles:id,name,guard_name', 'permissions:id,name,guard_name']);
    }
}
