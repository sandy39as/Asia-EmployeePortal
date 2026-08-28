<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        /*
         * Route ini harus tetap bisa dibuka supaya
         * tidak terjadi redirect loop.
         */
        if (
            $request->routeIs('password.first.*') ||
            $request->routeIs('logout')
        ) {
            return $next($request);
        }

        if ($user->must_change_password) {
            return redirect()
                ->route('password.first.edit');
        }

        return $next($request);
    }
}
