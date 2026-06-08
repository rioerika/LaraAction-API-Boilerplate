<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'LaraAction API',
    description: 'OpenAPI documentation for the Laravel API boilerplate.',
)]
#[OA\Server(
    url: '/api/v1',
    description: 'Version 1 API',
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token',
    description: 'Use a Laravel Sanctum personal access token in the Authorization header as Bearer {token}.',
)]
#[OA\Tag(
    name: 'System',
    description: 'System health and readiness endpoints.',
)]
#[OA\Tag(
    name: 'Auth',
    description: 'Authentication endpoints.',
)]
#[OA\Tag(
    name: 'Users',
    description: 'User management endpoints.',
)]
#[OA\Tag(
    name: 'Roles',
    description: 'Role management endpoints.',
)]
#[OA\Tag(
    name: 'Permissions',
    description: 'Permission management endpoints.',
)]
final class OpenApiSpec {}
