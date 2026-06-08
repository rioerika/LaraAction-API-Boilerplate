<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Actions\Audit\RecordAuditLogAction;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class CreateRoleAction
{
    public function __construct(
        private readonly RecordAuditLogAction $recordAuditLogAction,
    ) {}

    /**
     * @param  array{name:string,guard_name?:string,permissions?:list<string>}  $data
     */
    public function handle(array $data): Role
    {
        /** @var Role $role */
        $role = DB::transaction(function () use ($data): Role {
            /** @var Role $role */
            $role = Role::query()->create([
                'name' => $data['name'],
                'guard_name' => $data['guard_name'] ?? 'sanctum',
            ]);

            if (isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            $role->load('permissions:id,name,guard_name');

            $this->recordAuditLogAction->handle(
                event: 'role.created',
                subject: $role,
                newValues: $this->snapshot($role),
                metadata: [
                    'requested_permissions' => $data['permissions'] ?? [],
                ],
            );

            return $role;
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $role->load('permissions:id,name,guard_name');
    }

    /**
     * @return array{name:string,guard_name:string,permissions:list<string>}
     */
    private function snapshot(Role $role): array
    {
        return [
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'permissions' => $role->permissions->pluck('name')->map(static fn (mixed $permission): string => (string) $permission)->values()->all(),
        ];
    }
}
