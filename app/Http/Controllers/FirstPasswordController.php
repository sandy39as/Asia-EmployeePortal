<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class FirstPasswordController extends Controller
{
    /**
     * Tampilkan form pembuatan password pertama.
     */
    public function edit(): View
    {
        return view('auth.first-password');
    }

    /**
     * Simpan password baru.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'password' => [
                    'required',
                    'string',
                    'min:6',
                    'confirmed',
                ],
            ],
            [
                'password.required' =>
                    'Password baru wajib diisi.',

                'password.min' =>
                    'Password minimal 6 karakter.',

                'password.confirmed' =>
                    'Konfirmasi password tidak sama.',
            ]
        );

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | UPDATE PASSWORD
        |--------------------------------------------------------------------------
        */
        $user->update([
            'password' =>
                Hash::make(
                    $validated['password']
                ),

            'must_change_password' =>
                false,
        ]);

        /*
         * Regenerate session ID setelah
         * password berhasil diganti.
         */
        $request->session()->regenerate();

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
            return redirect()
                ->route('hrd.dashboard')
                ->with(
                    'success',
                    'Password berhasil dibuat. Selamat datang di Employee Portal.'
                );
        }

        /*
         * Karyawan biasa.
         */
        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Password berhasil dibuat. Selamat datang di Employee Portal.'
            );
    }
}
