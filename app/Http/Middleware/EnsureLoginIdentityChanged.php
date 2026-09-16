<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLoginIdentityChanged
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | HANYA USER YANG DITANDAI
        |--------------------------------------------------------------------------
        */

        if (! (bool) $user->must_change_username) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | ROUTE YANG BOLEH DIAKSES SAAT WAJIB GANTI ID LOGIN
        |--------------------------------------------------------------------------
        */

        if (
            $request->routeIs(
                'login-id.first.edit',
                'login-id.first.update',
                'logout'
            )
        ) {
            return $next($request);
        }

        return redirect()
            ->route(
                'login-id.first.edit'
            );
    }
}
