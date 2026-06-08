<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Actions\Audit\RecordAuditLogAction;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class DeleteRoleAction
{
    public function __construct(
        private readonly RecordAuditLogAction $recordAuditLogAction,
    ) {}

    public function handle(Role $role): void
    {
        $role->load('permissions:id,name,guard_name');
        $previousSnapshot = $this->snapshot($role);

        DB::transaction(function () use ($role, $previousSnapshot): void {
            $this->recordAuditLogAction->handle(
                event: 'role.deleted',
                subject: $role,
                oldValues: $previousSnapshot,
            );

            $role->delete();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
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
