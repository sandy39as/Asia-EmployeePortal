<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class KabagController extends Controller
{
    public function index(Request $request)
    {
        $search = trim(
            (string) $request->get(
                'search',
                ''
            )
        );

        $kabags = User::query()
            ->where('role', 'kabag')
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'email',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'username',
                            'like',
                            "%{$search}%"
                        );
                    });
                }
            )
            ->withCount('managedEmployees')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view(
            'master.kabag.index',
            compact(
                'kabags',
                'search'
            )
        );
    }

    public function create()
    {
        return view(
            'master.kabag.create'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'username' => [
                'nullable',
                'string',
                'max:100',
                'unique:users,username',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
            ],
        ]);

        User::create([
            'name' =>
                $validated['name'],

            'email' =>
                strtolower(
                    $validated['email']
                ),

            'username' =>
                $validated['username']
                    ?? null,

            'password' =>
                Hash::make(
                    $validated['password']
                ),

            'role' =>
                'kabag',

            'is_active' =>
                true,

            'must_change_password' =>
                true,

            'email_verified_at' =>
                now(),
        ]);

        return redirect()
            ->route('master.kabag.index')
            ->with(
                'success',
                'Akun Kabag berhasil ditambahkan.'
            );
    }

    public function edit(User $kabag)
    {
        abort_unless(
            $kabag->role === 'kabag',
            404
        );

        return view(
            'master.kabag.edit',
            compact('kabag')
        );
    }

    public function update(
        Request $request,
        User $kabag
    ) {
        abort_unless(
            $kabag->role === 'kabag',
            404
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',

                Rule::unique(
                    'users',
                    'email'
                )->ignore($kabag->id),
            ],

            'username' => [
                'nullable',
                'string',
                'max:100',

                Rule::unique(
                    'users',
                    'username'
                )->ignore($kabag->id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $data = [
            'name' =>
                $validated['name'],

            'email' =>
                strtolower(
                    $validated['email']
                ),

            'username' =>
                $validated['username']
                    ?? null,

            'is_active' =>
                $request->boolean(
                    'is_active'
                ),
        ];

        if (
            filled(
                $validated['password']
                ?? null
            )
        ) {
            $data['password'] =
                Hash::make(
                    $validated['password']
                );

            $data['must_change_password'] =
                true;
        }

        $kabag->update($data);

        return redirect()
            ->route('master.kabag.index')
            ->with(
                'success',
                'Data Kabag berhasil diperbarui.'
            );
    }

    public function destroy(User $kabag)
    {
        abort_unless(
            $kabag->role === 'kabag',
            404
        );

        $kabag
            ->managedEmployees()
            ->detach();

        $kabag->delete();

        return redirect()
            ->route('master.kabag.index')
            ->with(
                'success',
                'Akun Kabag berhasil dihapus.'
            );
    }
}
