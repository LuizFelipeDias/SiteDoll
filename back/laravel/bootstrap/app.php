<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

// >>> adicione estes dois:
use App\Middleware\PermissaoMiddleware;
use App\Middleware\AuthMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Aliases dos seus middlewares (agora usando imports)
        $middleware->alias([
            'permissao'   => PermissaoMiddleware::class,
            'auth.custom' => AuthMiddleware::class,
        ]);

        // Grupo 'api' com Sanctum stateful + bindings (fluxo SPA)
        $middleware->group('api', [
            EnsureFrontendRequestsAreStateful::class,
            SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {
        //
    })->create();
