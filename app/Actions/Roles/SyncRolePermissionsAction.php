<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Actions\Audit\RecordAuditLogAction;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class SyncRolePermissionsAction
{
    public function __construct(
        private readonly RecordAuditLogAction $recordAuditLogAction,
    ) {}

    /**
     * @param  list<string>  $permissions
     */
    public function handle(Role $role, array $permissions): Role
    {
        $previousPermissions = $this->permissionNames($role);

        /** @var Role $updatedRole */
        $updatedRole = DB::transaction(function () use ($role, $permissions, $previousPermissions): Role {
            $role->syncPermissions($permissions);

            $updatedRole = $role->load('permissions:id,name,guard_name');
            $currentPermissions = $this->permissionNames($updatedRole);

            $this->recordAuditLogAction->handle(
                event: 'role.permissions.synced',
                subject: $role,
                oldValues: ['permissions' => $previousPermissions],
                newValues: ['permissions' => $currentPermissions],
                metadata: [
                    'requested_permissions' => $permissions,
                    'added_permissions' => array_values(array_diff($currentPermissions, $previousPermissions)),
                    'removed_permissions' => array_values(array_diff($previousPermissions, $currentPermissions)),
                ],
            );

            return $updatedRole;
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $updatedRole;
    }

    /**
     * @return list<string>
     */
    private function permissionNames(Role $role): array
    {
        return $role->permissions()->pluck('name')->map(static fn (mixed $permission): string => (string) $permission)->values()->all();
    }
}
