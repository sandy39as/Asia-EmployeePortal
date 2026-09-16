<?php

use App\Http\Middleware\EnsureHrd;
use App\Http\Middleware\EnsureKabag;
use App\Http\Middleware\EnsureLoginIdentityChanged;
use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Middleware\EnsurePortalMasterAdmin;
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
                EnsureLoginIdentityChanged::class,
                EnsurePasswordChanged::class,
            ]
        );

        $middleware->alias([
            'hrd' => EnsureHrd::class,
            'kabag' => EnsureKabag::class,
            'facelog.token' => VerifyFaceLogToken::class,
            'portal.master-admin' => EnsurePortalMasterAdmin::class,

            'login-id.changed' => EnsureLoginIdentityChanged::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        
    })
    ->create();
