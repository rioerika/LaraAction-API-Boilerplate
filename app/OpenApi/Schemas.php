<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Components(
    schemas: [
        new OA\Schema(
            schema: 'ErrorResponse',
            required: ['success', 'message', 'errors'],
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'This action is unauthorized.'),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    additionalProperties: new OA\AdditionalProperties(
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                    ),
                    example: [],
                ),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'ValidationErrorResponse',
            required: ['success', 'message', 'errors'],
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    additionalProperties: new OA\AdditionalProperties(
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                    ),
                    example: ['email' => ['The provided credentials are incorrect.']],
                ),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'EmptySuccessResponse',
            required: ['success', 'message', 'data'],
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Operation completed successfully.'),
                new OA\Property(property: 'data', nullable: true, example: null),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'RoleSummary',
            required: ['id', 'name', 'guard_name', 'created_at', 'updated_at'],
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'SuperAdmin'),
                new OA\Property(property: 'guard_name', type: 'string', example: 'sanctum'),
                new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'PermissionSummary',
            required: ['id', 'name', 'guard_name', 'created_at', 'updated_at'],
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'view users'),
                new OA\Property(property: 'guard_name', type: 'string', example: 'sanctum'),
                new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'UserResource',
            required: ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at', 'roles', 'permissions'],
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'Super Admin'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'superadmin@example.com'),
                new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', nullable: true),
                new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(ref: '#/components/schemas/RoleSummary')),
                new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(ref: '#/components/schemas/PermissionSummary')),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'RoleResource',
            required: ['id', 'name', 'guard_name', 'created_at', 'updated_at', 'permissions'],
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'SuperAdmin'),
                new OA\Property(property: 'guard_name', type: 'string', example: 'sanctum'),
                new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(ref: '#/components/schemas/PermissionSummary')),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'PermissionResource',
            required: ['id', 'name', 'guard_name', 'created_at', 'updated_at', 'roles'],
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'view users'),
                new OA\Property(property: 'guard_name', type: 'string', example: 'sanctum'),
                new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(ref: '#/components/schemas/RoleSummary')),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'PaginationLink',
            required: ['url', 'label', 'active'],
            properties: [
                new OA\Property(property: 'url', type: 'string', nullable: true, example: 'http://localhost/api/v1/users?page=1'),
                new OA\Property(property: 'label', type: 'string', example: '&laquo; Previous'),
                new OA\Property(property: 'active', type: 'boolean', example: false),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'UserPagination',
            required: ['current_page', 'data', 'first_page_url', 'from', 'last_page', 'last_page_url', 'links', 'path', 'per_page', 'to', 'total'],
            properties: [
                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/UserResource')),
                new OA\Property(property: 'first_page_url', type: 'string', example: 'http://localhost/api/v1/users?page=1'),
                new OA\Property(property: 'from', type: 'integer', nullable: true, example: 1),
                new OA\Property(property: 'last_page', type: 'integer', example: 1),
                new OA\Property(property: 'last_page_url', type: 'string', example: 'http://localhost/api/v1/users?page=1'),
                new OA\Property(property: 'links', type: 'array', items: new OA\Items(ref: '#/components/schemas/PaginationLink')),
                new OA\Property(property: 'path', type: 'string', example: 'http://localhost/api/v1/users'),
                new OA\Property(property: 'per_page', type: 'integer', example: 15),
                new OA\Property(property: 'to', type: 'integer', nullable: true, example: 1),
                new OA\Property(property: 'total', type: 'integer', example: 1),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'RolePagination',
            required: ['current_page', 'data', 'first_page_url', 'from', 'last_page', 'last_page_url', 'links', 'path', 'per_page', 'to', 'total'],
            properties: [
                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/RoleResource')),
                new OA\Property(property: 'first_page_url', type: 'string', example: 'http://localhost/api/v1/roles?page=1'),
                new OA\Property(property: 'from', type: 'integer', nullable: true, example: 1),
                new OA\Property(property: 'last_page', type: 'integer', example: 1),
                new OA\Property(property: 'last_page_url', type: 'string', example: 'http://localhost/api/v1/roles?page=1'),
                new OA\Property(property: 'links', type: 'array', items: new OA\Items(ref: '#/components/schemas/PaginationLink')),
                new OA\Property(property: 'path', type: 'string', example: 'http://localhost/api/v1/roles'),
                new OA\Property(property: 'per_page', type: 'integer', example: 15),
                new OA\Property(property: 'to', type: 'integer', nullable: true, example: 1),
                new OA\Property(property: 'total', type: 'integer', example: 1),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'PermissionPagination',
            required: ['current_page', 'data', 'first_page_url', 'from', 'last_page', 'last_page_url', 'links', 'path', 'per_page', 'to', 'total'],
            properties: [
                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/PermissionResource')),
                new OA\Property(property: 'first_page_url', type: 'string', example: 'http://localhost/api/v1/permissions?page=1'),
                new OA\Property(property: 'from', type: 'integer', nullable: true, example: 1),
                new OA\Property(property: 'last_page', type: 'integer', example: 1),
                new OA\Property(property: 'last_page_url', type: 'string', example: 'http://localhost/api/v1/permissions?page=1'),
                new OA\Property(property: 'links', type: 'array', items: new OA\Items(ref: '#/components/schemas/PaginationLink')),
                new OA\Property(property: 'path', type: 'string', example: 'http://localhost/api/v1/permissions'),
                new OA\Property(property: 'per_page', type: 'integer', example: 15),
                new OA\Property(property: 'to', type: 'integer', nullable: true, example: 1),
                new OA\Property(property: 'total', type: 'integer', example: 1),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'HealthData',
            required: ['status', 'app', 'timestamp'],
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'ok'),
                new OA\Property(property: 'app', type: 'string', example: 'Laravel API Boilerplate'),
                new OA\Property(property: 'timestamp', type: 'string', format: 'date-time'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'ReadinessComponent',
            required: ['status'],
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'up'),
                new OA\Property(property: 'connection', type: 'string', nullable: true, example: 'mysql'),
                new OA\Property(property: 'store', type: 'string', nullable: true, example: 'file'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'ReadinessData',
            required: ['components', 'timestamp'],
            properties: [
                new OA\Property(
                    property: 'components',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'database', ref: '#/components/schemas/ReadinessComponent'),
                        new OA\Property(property: 'cache', ref: '#/components/schemas/ReadinessComponent'),
                    ],
                ),
                new OA\Property(property: 'timestamp', type: 'string', format: 'date-time'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'LoginData',
            required: ['token', 'token_type', 'expires_at', 'user'],
            properties: [
                new OA\Property(property: 'token', type: 'string'),
                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', nullable: true),
                new OA\Property(property: 'user', ref: '#/components/schemas/UserResource'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'HealthResponse',
            required: ['success', 'message', 'data'],
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Health check completed successfully.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/HealthData'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'ReadinessResponse',
            required: ['success', 'message', 'data'],
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Readiness check completed successfully.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/ReadinessData'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'LoginResponse',
            required: ['success', 'message', 'data'],
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Login completed successfully.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/LoginData'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'UserResponse',
            required: ['success', 'message', 'data'],
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'User retrieved successfully.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/UserResource'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'UserListResponse',
            required: ['success', 'message', 'data'],
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Users retrieved successfully.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/UserPagination'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'RoleResponse',
            required: ['success', 'message', 'data'],
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Role retrieved successfully.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/RoleResource'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'RoleListResponse',
            required: ['success', 'message', 'data'],
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Roles retrieved successfully.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/RolePagination'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'PermissionResponse',
            required: ['success', 'message', 'data'],
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Permission retrieved successfully.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/PermissionResource'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'PermissionListResponse',
            required: ['success', 'message', 'data'],
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Permissions retrieved successfully.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/PermissionPagination'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'LoginRequestBody',
            required: ['email', 'password', 'device_name'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'superadmin@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
                new OA\Property(property: 'device_name', type: 'string', example: 'postman'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'StoreUserRequestBody',
            required: ['name', 'email', 'password', 'password_confirmation'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Jane Doe'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jane@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
                new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'UpdateUserRequestBody',
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Jane Doe Updated'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jane.updated@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
                new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'UserRolesSyncRequestBody',
            required: ['roles'],
            properties: [
                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string'), example: ['Manager']),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'StoreRoleRequestBody',
            required: ['name'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Auditor'),
                new OA\Property(property: 'guard_name', type: 'string', nullable: true, example: 'sanctum'),
                new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(type: 'string'), nullable: true, example: ['view users']),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'UpdateRoleRequestBody',
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Auditor Updated'),
                new OA\Property(property: 'guard_name', type: 'string', nullable: true, example: 'sanctum'),
                new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(type: 'string'), nullable: true, example: ['view users']),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'RolePermissionsSyncRequestBody',
            required: ['permissions'],
            properties: [
                new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(type: 'string'), example: ['view users']),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'StorePermissionRequestBody',
            required: ['name'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'export reports'),
                new OA\Property(property: 'guard_name', type: 'string', nullable: true, example: 'sanctum'),
            ],
            type: 'object',
        ),
        new OA\Schema(
            schema: 'UpdatePermissionRequestBody',
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'export analytics'),
                new OA\Property(property: 'guard_name', type: 'string', nullable: true, example: 'sanctum'),
            ],
            type: 'object',
        ),
    ],
)]
final class Schemas {}
