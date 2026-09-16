<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'username' => [
                'required',
                'string',
                'min:4',
                'max:100',
                'regex:/^[A-Za-z0-9._@+\-]+$/',
                Rule::unique(
                    User::class,
                    'username'
                )->ignore(
                    $this->user()->id
                ),
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(
                    User::class,
                    'email'
                )->ignore(
                    $this->user()->id
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
                'Nama wajib diisi.',

            'username.required' =>
                'ID Login wajib diisi.',

            'username.min' =>
                'ID Login minimal 4 karakter.',

            'username.max' =>
                'ID Login maksimal 100 karakter.',

            'username.regex' =>
                'ID Login hanya boleh berisi huruf, angka, titik, underscore, @, +, atau tanda minus.',

            'username.unique' =>
                'ID Login sudah digunakan akun lain.',

            'email.required' =>
                'Email wajib diisi.',

            'email.email' =>
                'Format email tidak valid.',

            'email.unique' =>
                'Email sudah digunakan akun lain.',
        ];
    }
}
