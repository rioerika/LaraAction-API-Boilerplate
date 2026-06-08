<?php

declare(strict_types=1);

namespace App\Actions\Permissions;

use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class DeletePermissionAction
{
    public function handle(Permission $permission): void
    {
        DB::transaction(static function () use ($permission): void {
            $permission->delete();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
