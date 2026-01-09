<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(\App\Http\Middleware\TraceId::class);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'tenant' => \App\Http\Middleware\SetTenantFromAuthenticatedUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $traceId = (string) ($request->attributes->get('trace_id') ?? app('trace_id') ?? '');

            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                    'trace_id' => $traceId,
                ], $e->status);
            }

            if ($e instanceof HttpExceptionInterface) {
                return response()->json([
                    'message' => $e->getMessage() !== '' ? $e->getMessage() : 'Error.',
                    'errors' => (object) [],
                    'trace_id' => $traceId,
                ], $e->getStatusCode());
            }

            return response()->json([
                'message' => 'Error interno.',
                'errors' => (object) [],
                'trace_id' => $traceId,
            ], 500);
        });
    })->create();
