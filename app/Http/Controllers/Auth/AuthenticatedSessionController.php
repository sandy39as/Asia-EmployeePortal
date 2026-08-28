<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHENTICATE DULU
        |--------------------------------------------------------------------------
        |
        | Ini WAJIB dilakukan sebelum mengambil $request->user().
        |
        */
        $request->authenticate();

        /*
         * Regenerate session setelah login berhasil.
         */
        $request->session()->regenerate();

        /*
         * Sekarang user sudah tersedia.
         */
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | REDIRECT BERDASARKAN ROLE
        |--------------------------------------------------------------------------
        */
        if (
            in_array(
                $user->role,
                [
                    'hrd',
                    'admin',
                    'superadmin',
                ],
                true
            )
        ) {
            return redirect()->intended(
                route(
                    'hrd.dashboard',
                    absolute: false
                )
            );
        }

        /*
         * Karyawan biasa.
         */
        return redirect()->intended(
            route(
                'dashboard',
                absolute: false
            )
        );
    }

    /**
     * Logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
