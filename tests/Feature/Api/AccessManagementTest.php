<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccessManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_and_permission_seeder_is_idempotent(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(RoleAndPermissionSeeder::class);

        $this->assertDatabaseCount('roles', 3);
        $this->assertDatabaseCount('permissions', 14);
    }

    public function test_user_without_permission_gets_forbidden_response(): void
    {
        $this->seed();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/users')
            ->assertForbidden()
            ->assertExactJson([
                'success' => false,
                'message' => 'This action is unauthorized.',
                'errors' => [],
            ]);
    }

    public function test_super_admin_can_manage_users_roles_and_permissions(): void
    {
        $this->seed();

        $superAdmin = User::query()->where('email', 'superadmin@example.com')->firstOrFail();
        Sanctum::actingAs($superAdmin);

        $createPermission = $this->postJson('/api/v1/permissions', [
            'name' => 'export reports',
        ]);
        $createPermission->assertCreated()->assertJsonPath('data.name', 'export reports');

        $createRole = $this->postJson('/api/v1/roles', [
            'name' => 'Auditor',
            'permissions' => ['export reports'],
        ]);
        $createRole->assertCreated()->assertJsonPath('data.permissions.0.name', 'export reports');

        $createUser = $this->postJson('/api/v1/users', [
            'name' => 'Auditor User',
            'email' => 'auditor@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $createUser->assertCreated()->assertJsonPath('data.email', 'auditor@example.com');

        $userId = (int) $createUser->json('data.id');
        $roleId = (int) $createRole->json('data.id');
        $permissionId = (int) $createPermission->json('data.id');

        $this->putJson("/api/v1/users/{$userId}/roles", [
            'roles' => ['Auditor'],
        ])
            ->assertOk()
            ->assertJsonPath('data.roles.0.name', 'Auditor');

        $this->putJson("/api/v1/roles/{$roleId}/permissions", [
            'permissions' => ['export reports'],
        ])
            ->assertOk()
            ->assertJsonPath('data.permissions.0.name', 'export reports');

        $this->getJson("/api/v1/users/{$userId}")
            ->assertOk()
            ->assertJsonPath('data.roles.0.name', 'Auditor');

        $this->getJson("/api/v1/roles/{$roleId}")
            ->assertOk()
            ->assertJsonPath('data.permissions.0.name', 'export reports');

        $this->getJson("/api/v1/permissions/{$permissionId}")
            ->assertOk()
            ->assertJsonPath('data.name', 'export reports');

        $this->putJson("/api/v1/users/{$userId}", [
            'name' => 'Auditor User Updated',
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Auditor User Updated');

        $this->putJson("/api/v1/roles/{$roleId}", [
            'name' => 'Auditor Updated',
            'permissions' => ['export reports'],
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Auditor Updated');

        $this->putJson("/api/v1/permissions/{$permissionId}", [
            'name' => 'export analytics',
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'export analytics');

        $this->deleteJson("/api/v1/users/{$userId}")->assertOk();
        $this->deleteJson("/api/v1/roles/{$roleId}")->assertOk();
        $this->deleteJson("/api/v1/permissions/{$permissionId}")->assertOk();
    }

    public function test_user_creation_validation_failure_uses_standard_structure(): void
    {
        $this->seed();

        $superAdmin = User::query()->where('email', 'superadmin@example.com')->firstOrFail();
        Sanctum::actingAs($superAdmin);

        $this->postJson('/api/v1/users', [
            'name' => '',
        ])
            ->assertUnprocessable()
            ->assertJson([
                'success' => false,
                'message' => 'The given data was invalid.',
            ])
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
