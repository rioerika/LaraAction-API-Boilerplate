<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class DeleteRoleAction
{
    public function handle(Role $role): void
    {
        DB::transaction(static function () use ($role): void {
            $role->delete();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
