<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // It forces JSON in API
        $exceptions->shouldRenderJsonWhen(function ($request, $e) {
            return $request->is('api/*') || $request->expectsJson();
        });
        
        // For Validation errors - 422 (Unprocessable Entity)
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            return response()->json(['errors' => $e->errors()], 422);
        });
        
         // 3) 404 - Recurso no encontrado (ruta o modelo). It forces JSON in API
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Resource not found'], 404);
            }
        });
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Resource not found'], 404);
            }
        });

        // 4) 403 - Prohibido. It forces JSON in API
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Forbidden'], 403);
            }
        });

        // 5) 401 - No autenticado (útil con Sanctum/API tokens). It forces JSON in API
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
        });

        // 6) (Opcional) Fallback 500 para API. It forces JSON in API
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'Server error',
                    // 'debug' => $e->getMessage(), // habilítalo solo en local si quieres
                ], 500);
            }
        });
    })->create();
