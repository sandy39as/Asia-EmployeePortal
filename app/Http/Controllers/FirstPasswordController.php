<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FirstPasswordController extends Controller
{
    public function edit()
    {
        return view('auth.first-password');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make(
                $validated['password']
            ),

            'must_change_password' => false,
        ]);

        /*
         * Ganti session ID setelah password berubah.
         */
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Password berhasil dibuat. Selamat datang di Employee Portal.'
            );
    }
}
