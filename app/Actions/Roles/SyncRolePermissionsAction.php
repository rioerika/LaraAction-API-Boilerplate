<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class SyncRolePermissionsAction
{
    /**
     * @param  list<string>  $permissions
     */
    public function handle(Role $role, array $permissions): Role
    {
        $role->syncPermissions($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $role->load('permissions:id,name,guard_name');
    }
}
