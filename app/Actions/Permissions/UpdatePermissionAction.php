<?php

declare(strict_types=1);

namespace App\Actions\Permissions;

use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class UpdatePermissionAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(Permission $permission, array $data): Permission
    {
        DB::transaction(function () use ($permission, $data): void {
            $permission->fill([
                'name' => $data['name'] ?? $permission->name,
                'guard_name' => $data['guard_name'] ?? $permission->guard_name,
            ]);
            $permission->save();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $permission->load('roles:id,name,guard_name');
    }
}
