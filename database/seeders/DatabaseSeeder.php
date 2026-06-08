<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
        ]);

        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        /** @var User $user */
        $user = User::query()->firstOrCreate(
            ['email' => env('DEFAULT_SUPER_ADMIN_EMAIL', 'superadmin@example.com')],
            [
                'name' => env('DEFAULT_SUPER_ADMIN_NAME', 'Super Admin'),
                'password' => env('DEFAULT_SUPER_ADMIN_PASSWORD', 'password'),
            ],
        );

        if (! $user->hasRole('SuperAdmin')) {
            $user->assignRole('SuperAdmin');
        }
    }
}
