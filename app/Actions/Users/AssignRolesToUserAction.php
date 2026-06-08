<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;

final class AssignRolesToUserAction
{
    /**
     * @param  list<string>  $roles
     */
    public function handle(User $user, array $roles): User
    {
        $user->assignRole($roles);

        return $user->load(['roles:id,name,guard_name', 'permissions:id,name,guard_name']);
    }
}
