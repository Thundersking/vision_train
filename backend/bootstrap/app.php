<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->group('tenant', [
            \Spatie\Multitenancy\Http\Middleware\NeedsTenant::class,
        ]);

        $middleware->alias([
            'jwt.auth' => \App\Domain\Auth\Middleware\JWTAuth::class,
        ]);

        $middleware->redirectGuestsTo(function () {
            return response()->json([
                'message' => 'Требуется авторизация.',
                'code' => 401
            ], 401);
        });

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 405 Method Not Allowed
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Метод не поддерживается.',
                    'code' => 405,
                    'method' => $request->method(),
                    'path' => $request->path()
                ], 405);
            }
        });

        // 422 Validation
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Ошибка валидации',
                    'code' => 422,
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // 401 Unauthenticated (без редиректа на login)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Требуется авторизация.',
                    'code' => 401
                ], 401);
            }
        });

        // 403 Forbidden
        $exceptions->render(function (AuthorizationException|AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Доступ запрещен.',
                    'code' => 403
                ], 403);
            }
        });

        // 404 Not Found
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Запись не найдена',
                    'code' => 404
                ], 404);
            }
        });

        $exceptions->render(function (RouteNotFoundException|NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Маршрут не найден.',
                    'code' => 404
                ], 404);
            }
        });

        // 500 Internal Server Error
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                report($e);

                return response()->json([
                    'message' => 'Внутренняя ошибка сервера.',
                    'code' => 500,
                ], 500);
            }
        });

        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })->create();
