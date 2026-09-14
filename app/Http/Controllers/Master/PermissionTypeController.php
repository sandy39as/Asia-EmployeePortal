<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\PermissionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class PermissionTypeController extends Controller
{
    public function index(
        Request $request
    ): View {

        $search = trim(
            (string) $request->get(
                'search',
                ''
            )
        );

        $status = trim(
            (string) $request->get(
                'status',
                ''
            )
        );

        $items =
            PermissionType::query()
                ->when(
                    $search !== '',
                    function ($query) use ($search) {
                        $query->where(
                            function ($q) use ($search) {
                                $q
                                    ->where(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'code',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'description',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                    }
                )
                ->when(
                    $status === 'active',
                    fn ($query) =>
                        $query->where(
                            'is_active',
                            true
                        )
                )
                ->when(
                    $status === 'inactive',
                    fn ($query) =>
                        $query->where(
                            'is_active',
                            false
                        )
                )
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString();


        $summary = [
            'total' =>
                PermissionType::count(),

            'active' =>
                PermissionType::where(
                    'is_active',
                    true
                )->count(),

            'inactive' =>
                PermissionType::where(
                    'is_active',
                    false
                )->count(),
        ];


        return view(
            'master.permission-types.index',
            compact(
                'items',
                'summary',
                'search',
                'status'
            )
        );
    }


    public function store(
        Request $request
    ): RedirectResponse {

        $validated =
            $request->validate(
                [
                    'name' => [
                        'required',
                        'string',
                        'max:150',
                    ],

                    'code' => [
                        'required',
                        'string',
                        'max:50',
                        'unique:permission_types,code',
                    ],

                    'description' => [
                        'nullable',
                        'string',
                        'max:2000',
                    ],

                    'is_active' => [
                        'nullable',
                        'boolean',
                    ],
                ]
            );


        PermissionType::create([
            'name' =>
                trim(
                    $validated['name']
                ),

            'code' =>
                strtoupper(
                    trim(
                        $validated['code']
                    )
                ),

            'description' =>
                $validated['description']
                ?? null,

            'is_active' =>
                (bool) (
                    $validated['is_active']
                    ?? false
                ),
        ]);


        return redirect()
            ->route(
                'master.permission-types.index'
            )
            ->with(
                'success',
                'Jenis izin berhasil ditambahkan.'
            );
    }


    public function update(
        Request $request,
        PermissionType $permissionType
    ): RedirectResponse {

        $validated =
            $request->validate(
                [
                    'name' => [
                        'required',
                        'string',
                        'max:150',
                    ],

                    'code' => [
                        'required',
                        'string',
                        'max:50',

                        Rule::unique(
                            'permission_types',
                            'code'
                        )
                            ->ignore(
                                $permissionType->id
                            ),
                    ],

                    'description' => [
                        'nullable',
                        'string',
                        'max:2000',
                    ],

                    'is_active' => [
                        'nullable',
                        'boolean',
                    ],
                ]
            );


        $permissionType->update([
            'name' =>
                trim(
                    $validated['name']
                ),

            'code' =>
                strtoupper(
                    trim(
                        $validated['code']
                    )
                ),

            'description' =>
                $validated['description']
                ?? null,

            'is_active' =>
                (bool) (
                    $validated['is_active']
                    ?? false
                ),
        ]);


        return redirect()
            ->route(
                'master.permission-types.index'
            )
            ->with(
                'success',
                'Jenis izin berhasil diperbarui.'
            );
    }


    public function destroy(
        PermissionType $permissionType
    ): RedirectResponse {

        $permissionType->delete();


        return redirect()
            ->route(
                'master.permission-types.index'
            )
            ->with(
                'success',
                'Jenis izin berhasil dihapus.'
            );
    }
}
