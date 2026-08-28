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
        | AUTHENTICATE
        |--------------------------------------------------------------------------
        */
        $request->authenticate();

        /*
         * Regenerate session setelah login.
         */
        $request->session()->regenerate();

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | REDIRECT BERDASARKAN ROLE
        |--------------------------------------------------------------------------
        |
        | Jangan gunakan redirect()->intended()
        | karena bisa membawa HRD ke halaman employee
        | berdasarkan URL yang tersimpan sebelumnya.
        |
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
            return redirect()
                ->route('hrd.dashboard');
        }

        /*
         * Karyawan biasa.
         */
        return redirect()
            ->route('dashboard');
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
