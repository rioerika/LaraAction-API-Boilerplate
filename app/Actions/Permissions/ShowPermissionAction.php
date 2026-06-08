<?php

declare(strict_types=1);

namespace App\Actions\Permissions;

use App\Models\Permission;

final class ShowPermissionAction
{
    public function handle(Permission $permission): Permission
    {
        return $permission->load('roles:id,name,guard_name');
    }
}
