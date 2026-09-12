<?php

use App\Http\Middleware\EnsureHrd;
use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Middleware\VerifyFaceLogToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->web(
            append: [
                EnsurePasswordChanged::class,
            ]
        );

        $middleware->alias([
            'hrd' => EnsureHrd::class,
            'facelog.token' => VerifyFaceLogToken::class,
            'portal.master-admin' => EnsurePortalMasterAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
