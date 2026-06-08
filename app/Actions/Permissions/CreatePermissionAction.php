<?php

declare(strict_types=1);

namespace App\Actions\Permissions;

use App\Actions\Audit\RecordAuditLogAction;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class CreatePermissionAction
{
    public function __construct(
        private readonly RecordAuditLogAction $recordAuditLogAction,
    ) {}

    /**
     * @param  array{name:string,guard_name?:string}  $data
     */
    public function handle(array $data): Permission
    {
        /** @var Permission $permission */
        $permission = DB::transaction(function () use ($data): Permission {
            /** @var Permission $permission */
            $permission = Permission::query()->create([
                'name' => $data['name'],
                'guard_name' => $data['guard_name'] ?? 'sanctum',
            ]);

            $this->recordAuditLogAction->handle(
                event: 'permission.created',
                subject: $permission,
                newValues: $this->snapshot($permission),
            );

            return $permission;
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
