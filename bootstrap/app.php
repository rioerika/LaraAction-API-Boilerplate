<?php

declare(strict_types=1);

use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException as SpatieUnauthorizedException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            static fn (Request $request): bool => $request->is('api/*'),
        );

        $exceptions->render(function (ValidationException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return ApiResponse::error(
                message: 'The given data was invalid.',
                errors: $exception->errors(),
                status: Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return ApiResponse::error(
                message: 'Unauthenticated.',
                errors: [],
                status: Response::HTTP_UNAUTHORIZED,
            );
        });

        $exceptions->render(function (AuthorizationException|SpatieUnauthorizedException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return ApiResponse::error(
                message: 'This action is unauthorized.',
                errors: [],
                status: Response::HTTP_FORBIDDEN,
            );
        });

        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return ApiResponse::error(
                message: 'Resource not found.',
                errors: [],
                status: Response::HTTP_NOT_FOUND,
            );
        });

        $exceptions->render(function (HttpResponseException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return $exception->getResponse();
        });

        $exceptions->render(function (Throwable $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $status = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return ApiResponse::error(
                message: $status >= Response::HTTP_INTERNAL_SERVER_ERROR
                    ? 'An unexpected error occurred.'
                    : ($exception->getMessage() !== '' ? $exception->getMessage() : Response::$statusTexts[$status]),
                errors: [],
                status: $status,
            );
        });
    })->create();
