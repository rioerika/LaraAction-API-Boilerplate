<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    private array $permissions = [
        'view users',
        'create users',
        'update users',
        'delete users',
        'assign roles',
        'view roles',
        'create roles',
        'update roles',
        'delete roles',
        'assign permissions',
        'view permissions',
        'create permissions',
        'update permissions',
        'delete permissions',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->permissions as $permissionName) {
            Permission::query()->firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'sanctum',
            ]);
        }

        $superAdmin = Role::query()->firstOrCreate([
            'name' => 'SuperAdmin',
            'guard_name' => 'sanctum',
        ]);
        $manager = Role::query()->firstOrCreate([
            'name' => 'Manager',
            'guard_name' => 'sanctum',
        ]);
        Role::query()->firstOrCreate([
            'name' => 'User',
            'guard_name' => 'sanctum',
        ]);

        $superAdmin->syncPermissions($this->permissions);
        $manager->syncPermissions([
            'view users',
            'create users',
            'update users',
            'view roles',
            'view permissions',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
