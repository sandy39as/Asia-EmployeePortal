<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'string',
            ],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = [
            'username' => strtoupper(
                trim((string) $this->username)
            ),

            'password' => $this->password,

            'is_active' => true,
        ];

        if (! Auth::attempt(
            $credentials,
            $this->boolean('remember')
        )) {
            RateLimiter::hit(
                $this->throttleKey()
            );

            throw ValidationException::withMessages([
                'username' => 'ID Karyawan atau password tidak sesuai.',
            ]);
        }

        RateLimiter::clear(
            $this->throttleKey()
        );
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts(
            $this->throttleKey(),
            5
        )) {
            return;
        }

        event(
            new Lockout($this)
        );

        $seconds = RateLimiter::availableIn(
            $this->throttleKey()
        );

        throw ValidationException::withMessages([
            'username' =>
                'Terlalu banyak percobaan login. Coba lagi dalam '
                . $seconds
                . ' detik.',
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower(
                (string) $this->username
            )
            . '|'
            . $this->ip()
        );
    }
}
