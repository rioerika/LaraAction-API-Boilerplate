<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Models\Role;

final class ShowRoleAction
{
    public function handle(Role $role): Role
    {
        return $role->load('permissions:id,name,guard_name');
    }
}
