<?php

use App\Helpers\ResponseBuilder;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //Sanctum eklemesi
        $middleware->group('api', [
            EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
        
        // CORS yapılandırması
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

    })
    
    ->withExceptions(function (Exceptions $exceptions): void {
        if (request()->expectsJson() || request()->is('api/*')) {
            $exceptions->render(function (Throwable $e) {
                $firstError = collect($e->errors())->flatten()->first();

                return response()->json([
                    'meta' => [
                        'status' => false,
                        'code' => 422,
                        'message' => 'VALIDATION_ERROR',
                    ],
                    'data' => null,
                    'error' => $firstError,
                ], 422);
            });
        }
        
    }) 
    ->create();
