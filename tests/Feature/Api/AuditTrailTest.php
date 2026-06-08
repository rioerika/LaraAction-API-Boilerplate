<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_and_permission_assignments_are_audited(): void
    {
        $this->seed();

        $superAdmin = User::query()->where('email', 'superadmin@example.com')->firstOrFail();
        Sanctum::actingAs($superAdmin);

        $this->withHeader('User-Agent', 'PHPUnit Audit Trail')
            ->postJson('/api/v1/permissions', [
                'name' => 'export reports',
            ])->assertCreated();

        $roleResponse = $this->withHeader('User-Agent', 'PHPUnit Audit Trail')
            ->postJson('/api/v1/roles', [
                'name' => 'Auditor',
            ]);
        $roleResponse->assertCreated();

        $userResponse = $this->withHeader('User-Agent', 'PHPUnit Audit Trail')
            ->postJson('/api/v1/users', [
                'name' => 'Auditor User',
                'email' => 'auditor@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);
        $userResponse->assertCreated();

        $role = Role::query()->where('name', 'Auditor')->firstOrFail();
        $user = User::query()->where('email', 'auditor@example.com')->firstOrFail();

        $this->withHeader('User-Agent', 'PHPUnit Audit Trail')
            ->putJson("/api/v1/users/{$user->id}/roles", [
                'roles' => ['Auditor'],
            ])
            ->assertOk();

        $this->withHeader('User-Agent', 'PHPUnit Audit Trail')
            ->putJson("/api/v1/roles/{$role->id}/permissions", [
                'permissions' => ['export reports'],
            ])
            ->assertOk();

        $userRoleAudit = AuditLog::query()
            ->where('event', 'user.roles.synced')
            ->where('subject_type', User::class)
            ->where('subject_id', (string) $user->id)
            ->firstOrFail();

        $this->assertSame($superAdmin->id, $userRoleAudit->actor_id);
        $this->assertSame('Auditor User', $userRoleAudit->subject_name);
        $this->assertSame(['roles' => []], $userRoleAudit->old_values);
        $this->assertSame(['roles' => ['Auditor']], $userRoleAudit->new_values);
        $this->assertSame([
            'requested_roles' => ['Auditor'],
            'added_roles' => ['Auditor'],
            'removed_roles' => [],
        ], $userRoleAudit->metadata);
        $this->assertSame('127.0.0.1', $userRoleAudit->ip_address);
        $this->assertSame('PHPUnit Audit Trail', $userRoleAudit->user_agent);

        $rolePermissionAudit = AuditLog::query()
            ->where('event', 'role.permissions.synced')
            ->where('subject_type', Role::class)
            ->where('subject_id', (string) $role->id)
            ->firstOrFail();

        $this->assertSame($superAdmin->id, $rolePermissionAudit->actor_id);
        $this->assertSame('Auditor', $rolePermissionAudit->subject_name);
        $this->assertSame(['permissions' => []], $rolePermissionAudit->old_values);
        $this->assertSame(['permissions' => ['export reports']], $rolePermissionAudit->new_values);
        $this->assertSame([
            'requested_permissions' => ['export reports'],
            'added_permissions' => ['export reports'],
            'removed_permissions' => [],
        ], $rolePermissionAudit->metadata);
    }

    public function test_role_and_permission_lifecycle_changes_are_audited(): void
    {
        $this->seed();

        $superAdmin = User::query()->where('email', 'superadmin@example.com')->firstOrFail();
        Sanctum::actingAs($superAdmin);

        $permissionResponse = $this->postJson('/api/v1/permissions', [
            'name' => 'export reports',
        ]);
        $permissionResponse->assertCreated();

        $permissionId = (int) $permissionResponse->json('data.id');

        $roleResponse = $this->postJson('/api/v1/roles', [
            'name' => 'Auditor',
            'permissions' => ['export reports'],
        ]);
        $roleResponse->assertCreated();

        $roleId = (int) $roleResponse->json('data.id');

        $this->putJson("/api/v1/permissions/{$permissionId}", [
            'name' => 'export analytics',
        ])->assertOk();

        $this->putJson("/api/v1/roles/{$roleId}", [
            'name' => 'Auditor Updated',
            'permissions' => ['export analytics'],
        ])->assertOk();

        $this->deleteJson("/api/v1/roles/{$roleId}")->assertOk();
        $this->deleteJson("/api/v1/permissions/{$permissionId}")->assertOk();

        $permissionCreateAudit = AuditLog::query()
            ->where('event', 'permission.created')
            ->where('subject_type', Permission::class)
            ->where('subject_id', (string) $permissionId)
            ->firstOrFail();

        $this->assertNull($permissionCreateAudit->old_values);
        $this->assertSame([
            'name' => 'export reports',
            'guard_name' => 'sanctum',
        ], $permissionCreateAudit->new_values);

        $permissionUpdateAudit = AuditLog::query()
            ->where('event', 'permission.updated')
            ->where('subject_type', Permission::class)
            ->where('subject_id', (string) $permissionId)
            ->firstOrFail();

        $this->assertSame([
            'name' => 'export reports',
            'guard_name' => 'sanctum',
        ], $permissionUpdateAudit->old_values);
        $this->assertSame([
            'name' => 'export analytics',
            'guard_name' => 'sanctum',
        ], $permissionUpdateAudit->new_values);

        $roleCreateAudit = AuditLog::query()
            ->where('event', 'role.created')
            ->where('subject_type', Role::class)
            ->where('subject_id', (string) $roleId)
            ->firstOrFail();

        $this->assertSame([
            'name' => 'Auditor',
            'guard_name' => 'sanctum',
            'permissions' => ['export reports'],
        ], $roleCreateAudit->new_values);

        $roleUpdateAudit = AuditLog::query()
            ->where('event', 'role.updated')
            ->where('subject_type', Role::class)
            ->where('subject_id', (string) $roleId)
            ->firstOrFail();

        $this->assertSame([
            'name' => 'Auditor',
            'guard_name' => 'sanctum',
            'permissions' => ['export analytics'],
        ], $roleUpdateAudit->old_values);
        $this->assertSame([
            'name' => 'Auditor Updated',
            'guard_name' => 'sanctum',
            'permissions' => ['export analytics'],
        ], $roleUpdateAudit->new_values);

        $roleDeleteAudit = AuditLog::query()
            ->where('event', 'role.deleted')
            ->where('subject_type', Role::class)
            ->where('subject_id', (string) $roleId)
            ->firstOrFail();

        $this->assertSame([
            'name' => 'Auditor Updated',
            'guard_name' => 'sanctum',
            'permissions' => ['export analytics'],
        ], $roleDeleteAudit->old_values);

        $permissionDeleteAudit = AuditLog::query()
            ->where('event', 'permission.deleted')
            ->where('subject_type', Permission::class)
            ->where('subject_id', (string) $permissionId)
            ->firstOrFail();

        $this->assertSame([
            'name' => 'export analytics',
            'guard_name' => 'sanctum',
        ], $permissionDeleteAudit->old_values);
    }
}
