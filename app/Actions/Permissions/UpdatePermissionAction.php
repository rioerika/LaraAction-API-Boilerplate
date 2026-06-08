<?php

declare(strict_types=1);

namespace App\Actions\Permissions;

use App\Actions\Audit\RecordAuditLogAction;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class UpdatePermissionAction
{
    public function __construct(
        private readonly RecordAuditLogAction $recordAuditLogAction,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(Permission $permission, array $data): Permission
    {
        $previousSnapshot = $this->snapshot($permission);

        DB::transaction(function () use ($permission, $data, $previousSnapshot): void {
            $permission->fill([
                'name' => $data['name'] ?? $permission->name,
                'guard_name' => $data['guard_name'] ?? $permission->guard_name,
            ]);
            $permission->save();

            $this->recordAuditLogAction->handle(
                event: 'permission.updated',
                subject: $permission,
                oldValues: $previousSnapshot,
                newValues: $this->snapshot($permission),
            );
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $permission->load('roles:id,name,guard_name');
    }

    /**
     * @return array{name:string,guard_name:string}
     */
    private function snapshot(Permission $permission): array
    {
        return [
            'name' => $permission->name,
            'guard_name' => $permission->guard_name,
        ];
    }
}
