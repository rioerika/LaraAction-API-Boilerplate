<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class UpdateRoleAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(Role $role, array $data): Role
    {
        DB::transaction(function () use ($role, $data): void {
            $role->fill([
                'name' => $data['name'] ?? $role->name,
                'guard_name' => $data['guard_name'] ?? $role->guard_name,
            ]);
            $role->save();

            if (array_key_exists('permissions', $data)) {
                $role->syncPermissions($data['permissions']);
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $role->load('permissions:id,name,guard_name');
    }
}
