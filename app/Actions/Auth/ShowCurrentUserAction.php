<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;

final class ShowCurrentUserAction
{
    public function handle(User $user): User
    {
        return $user->load(['roles:id,name,guard_name', 'permissions:id,name,guard_name']);
    }
}
