<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FirstLoginIdController extends Controller
{
    public function edit(
        Request $request
    ): View|RedirectResponse {
        $user = $request->user();

        if (! $user->must_change_username) {
            return $this->nextDestination(
                $request
            );
        }

        return view(
            'auth.first-login-id'
        );
    }

    public function update(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        if (! $user->must_change_username) {
            return $this->nextDestination(
                $request
            );
        }

        $validated = $request->validate(
            [
                'username' => [
                    'required',
                    'string',
                    'min:4',
                    'max:100',

                    /*
                    |--------------------------------------------------------------------------
                    | BOLEH USERNAME ATAU EMAIL
                    |--------------------------------------------------------------------------
                    |
                    | Contoh valid:
                    | sandyaditya
                    | sandi.aditya
                    | sandi_266
                    | sandiaditya@gmail.com
                    |
                    | Spasi tidak diperbolehkan.
                    |
                    */
                    'regex:/^[A-Za-z0-9._@+\-]+$/',

                    Rule::unique(
                        'users',
                        'username'
                    )->ignore(
                        $user->id
                    ),
                ],
            ],
            [
                'username.required' =>
                    'ID Login baru wajib diisi.',

                'username.min' =>
                    'ID Login minimal 4 karakter.',

                'username.max' =>
                    'ID Login maksimal 100 karakter.',

                'username.regex' =>
                    'ID Login hanya boleh berisi huruf, angka, titik, underscore, @, +, atau tanda minus.',

                'username.unique' =>
                    'ID Login tersebut sudah digunakan akun lain.',
            ]
        );

        $newUsername =
            trim(
                $validated[
                    'username'
                ]
            );

        if (
            strcasecmp(
                $newUsername,
                (string) $user->username
            )
            === 0
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'username' =>
                        'ID Login baru harus berbeda dari ID Login awal.',
                ]);
        }

        $user->forceFill([
            'username' =>
                $newUsername,

            'must_change_username' =>
                false,
        ])->save();

        /*
        |--------------------------------------------------------------------------
        | SETELAH ID LOGIN → LANJUT GANTI PASSWORD
        |--------------------------------------------------------------------------
        */

        if (
            (bool) $user->must_change_password
        ) {
            return redirect()
                ->route(
                    'password.first.edit'
                )
                ->with(
                    'success',
                    'ID Login berhasil dibuat. Sekarang buat password baru.'
                );
        }

        return $this->nextDestination(
            $request
        );
    }

    private function nextDestination(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        if (
            (bool) $user->must_change_password
        ) {
            return redirect()
                ->route(
                    'password.first.edit'
                );
        }

        return match (
            $user->role
        ) {
            'hrd',
            'admin',
            'superadmin' =>
                redirect()
                    ->route(
                        'hrd.leave-requests.index'
                    ),

            'kabag' =>
                redirect()
                    ->route(
                        'kabag.leave-requests.index'
                    ),

            default =>
                redirect()
                    ->route(
                        'leave-requests.index'
                    ),
        };
    }
}
