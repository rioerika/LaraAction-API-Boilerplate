<?php

declare(strict_types=1);

namespace App\Actions\Permissions;

use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

final class CreatePermissionAction
{
    /**
     * @param  array{name:string,guard_name?:string}  $data
     */
    public function handle(array $data): Permission
    {
        /** @var Permission $permission */
        $permission = DB::transaction(static fn (): Permission => Permission::query()->create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'sanctum',
        ]));

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $permission->load('roles:id,name,guard_name');
    }
}
