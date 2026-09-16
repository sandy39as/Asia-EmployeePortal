<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(
        Request $request
    ): View {
        return view(
            'profile.edit',
            [
                'user' =>
                    $request->user(),
            ]
        );
    }

    public function checkUsername(
        Request $request
    ): JsonResponse {
        $user =
            $request->user();

        $validated =
            $request->validate(
                [
                    'username' => [
                        'required',
                        'string',
                        'min:4',
                        'max:100',
                        'regex:/^[A-Za-z0-9._@+\-]+$/',
                    ],
                ],
                [
                    'username.required' =>
                        'ID Login wajib diisi.',

                    'username.min' =>
                        'ID Login minimal 4 karakter.',

                    'username.max' =>
                        'ID Login maksimal 100 karakter.',

                    'username.regex' =>
                        'ID Login hanya boleh berisi huruf, angka, titik, underscore, @, +, atau tanda minus.',
                ]
            );

        $username =
            trim(
                $validated['username']
            );


        if (
            strcasecmp(
                $username,
                (string) $user->username
            )
            === 0
        ) {
            return response()->json([
                'available' =>
                    true,

                'same_as_current' =>
                    true,

                'message' =>
                    'Ini adalah ID Login Anda saat ini.',
            ]);
        }


        $exists =
            User::query()
                ->where(
                    'username',
                    $username
                )
                ->where(
                    'id',
                    '!=',
                    $user->id
                )
                ->exists();


        if (
            $exists
        ) {
            return response()->json([
                'available' =>
                    false,

                'same_as_current' =>
                    false,

                'message' =>
                    'ID Login sudah digunakan akun lain.',
            ]);
        }


        return response()->json([
            'available' =>
                true,

            'same_as_current' =>
                false,

            'message' =>
                'ID Login tersedia dan dapat digunakan.',
        ]);
    }

    public function update(
        ProfileUpdateRequest $request
    ): RedirectResponse {
        $user =
            $request->user();

        $validated =
            $request->validated();

        $user->username =
            $validated['username'];

        $user->email =
            $validated['email'];


        if (
            $user->isDirty(
                'email'
            )
        ) {
            $user->email_verified_at =
                null;
        }


        if (
            $user->isDirty(
                'username'
            )
        ) {
            $user->must_change_username =
                false;
        }


        $user->save();


        return Redirect::route(
            'profile.edit'
        )->with(
            'status',
            'profile-updated'
        );
    }

    public function destroy(
        Request $request
    ): RedirectResponse {
        $request->validateWithBag(
            'userDeletion',
            [
                'password' => [
                    'required',
                    'current_password',
                ],
            ]
        );

        $user =
            $request->user();

        Auth::logout();

        $user->delete();

        $request
            ->session()
            ->invalidate();

        $request
            ->session()
            ->regenerateToken();

        return Redirect::to(
            '/'
        );
    }
}
