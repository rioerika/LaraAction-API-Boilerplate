<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Actions\Audit\RecordAuditLogAction;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class UpdateRoleAction
{
    public function __construct(
        private readonly RecordAuditLogAction $recordAuditLogAction,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(Role $role, array $data): Role
    {
        $role->load('permissions:id,name,guard_name');
        $previousSnapshot = $this->snapshot($role);

        DB::transaction(function () use ($role, $data, $previousSnapshot): void {
            $role->fill([
                'name' => $data['name'] ?? $role->name,
                'guard_name' => $data['guard_name'] ?? $role->guard_name,
            ]);
            $role->save();

            if (array_key_exists('permissions', $data)) {
                $role->syncPermissions($data['permissions']);
            }

            $role->load('permissions:id,name,guard_name');

            $this->recordAuditLogAction->handle(
                event: 'role.updated',
                subject: $role,
                oldValues: $previousSnapshot,
                newValues: $this->snapshot($role),
                metadata: isset($data['permissions']) && is_array($data['permissions'])
                    ? ['requested_permissions' => $data['permissions']]
                    : [],
            );
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
