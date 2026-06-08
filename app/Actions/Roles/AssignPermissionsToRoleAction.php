<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class AssignPermissionsToRoleAction
{
    /**
     * @param  list<string>  $permissions
     */
    public function handle(Role $role, array $permissions): Role
    {
        $role->givePermissionTo($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $role->load('permissions:id,name,guard_name');
    }
}
