<?php

namespace App\Http\Controllers;

use App\Models\EmployeeTempCredential;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class FirstPasswordController extends Controller
{
    public function edit(
        Request $request
    ): View|RedirectResponse {
        $user =
            $request->user();

        if (
            (bool) $user->must_change_username
        ) {
            return redirect()
                ->route(
                    'login-id.first.edit'
                );
        }

        if (
            ! (bool) $user->must_change_password
        ) {
            return $this->nextDestination(
                $user
            );
        }

        return view(
            'auth.first-password'
        );
    }


    public function update(
        Request $request
    ): RedirectResponse {
        $user =
            $request->user();

        if (
            (bool) $user->must_change_username
        ) {
            return redirect()
                ->route(
                    'login-id.first.edit'
                );
        }


        $validated =
            $request->validate(
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

        $user->forceFill([
            'password' =>
                Hash::make(
                    $validated['password']
                ),

            'must_change_password' =>
                false,
        ])->save();

        EmployeeTempCredential::query()
            ->where(
                'user_id',
                $user->id
            )
            ->delete();

        $request
            ->session()
            ->regenerate();


        return $this
            ->nextDestination(
                $user
            )
            ->with(
                'success',
                'Password berhasil dibuat. Selamat datang di Employee Portal.'
            );
    }

    private function nextDestination(
        $user
    ): RedirectResponse {
        return match (
            strtolower(
                trim(
                    (string) $user->role
                )
            )
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
