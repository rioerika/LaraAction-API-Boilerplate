<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class CreateRoleAction
{
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

            return $role;
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $role->load('permissions:id,name,guard_name');
    }
}
