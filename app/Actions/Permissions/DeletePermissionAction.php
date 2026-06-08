<?php

declare(strict_types=1);

namespace App\Actions\Permissions;

use App\Actions\Audit\RecordAuditLogAction;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class DeletePermissionAction
{
    public function __construct(
        private readonly RecordAuditLogAction $recordAuditLogAction,
    ) {}

    public function handle(Permission $permission): void
    {
        $previousSnapshot = $this->snapshot($permission);

        DB::transaction(function () use ($permission, $previousSnapshot): void {
            $this->recordAuditLogAction->handle(
                event: 'permission.deleted',
                subject: $permission,
                oldValues: $previousSnapshot,
            );

            $permission->delete();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
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
