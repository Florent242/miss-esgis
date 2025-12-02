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
        $middleware->alias([
            'supermod' => \App\Http\Middleware\SuperModMiddleware::class,
            'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
            'restricted.admin' => \App\Http\Middleware\RestrictedAdminMiddleware::class,
        ]);
        
        $middleware->validateCsrfTokens(except: [
            'api/webhook/sms',
            'api/fedapay/*',
            'api/transactions',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
