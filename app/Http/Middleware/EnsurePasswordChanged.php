<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        /*
        |--------------------------------------------------------------------------
        | BELUM LOGIN
        |--------------------------------------------------------------------------
        */
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | ROUTE FIRST LOGIN YANG HARUS TETAP BISA DIAKSES
        |--------------------------------------------------------------------------
        |
        | login-id.first.* WAJIB dikecualikan.
        |
        | Kalau tidak, saat user masih:
        | must_change_username = true
        | must_change_password = true
        |
        | bisa terjadi redirect loop:
        |
        | login-id -> password -> login-id -> password ...
        |
        */
        if (
            $request->routeIs('login-id.first.*')
            ||
            $request->routeIs('password.first.*')
            ||
            $request->routeIs('logout')
        ) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | PASSWORD BELUM DIGANTI
        |--------------------------------------------------------------------------
        |
        | Middleware ID Login berjalan lebih dulu secara global.
        | Jadi kalau username juga masih wajib diganti, user sudah diarahkan
        | ke halaman ganti ID Login sebelum sampai ke bagian ini.
        |
        */
        if (
            (bool) $user->must_change_password
        ) {
            return redirect()
                ->route(
                    'password.first.edit'
                );
        }

        return $next($request);
    }
}
