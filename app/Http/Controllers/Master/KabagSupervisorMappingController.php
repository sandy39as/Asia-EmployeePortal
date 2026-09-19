<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KabagSupervisorMappingController extends Controller
{
    public function index(): View
    {
        $kabags =
            User::query()
                ->where(
                    'role',
                    'kabag'
                )
                ->where(
                    'is_active',
                    true
                )
                ->with([
                    'selfEmployee',
                    'supervisors' => fn ($query) =>
                        $query
                            ->where(
                                'users.is_active',
                                true
                            )
                            ->orderBy(
                                'users.name'
                            ),
                ])
                ->orderBy(
                    'name'
                )
                ->get();


        return view(
            'master.kabag-supervisor-mapping.index',
            compact(
                'kabags'
            )
        );
    }


    public function update(
        Request $request,
        User $kabag
    ): RedirectResponse {

        abort_unless(
            $kabag->role === 'kabag',
            404
        );


        $validated =
            $request->validate(
                [
                    'supervisor_ids' => [
                        'nullable',
                        'array',
                    ],

                    'supervisor_ids.*' => [
                        'integer',
                        'distinct',
                        'exists:users,id',
                    ],
                ]
            );


        $supervisorIds =
            collect(
                $validated[
                    'supervisor_ids'
                ]
                ?? []
            )
                ->map(
                    fn ($id) =>
                        (int) $id
                )
                ->filter(
                    fn ($id) =>
                        $id !== (int) $kabag->id
                )
                ->unique()
                ->values();


        /*
        |--------------------------------------------------------------------------
        | SUPERVISOR SAAT INI = USER KABAG AKTIF
        |--------------------------------------------------------------------------
        |
        | Atasan boleh Kabag lain. Jika nanti ada role Manager tersendiri,
        | tinggal perluas daftar role pada query ini.
        |
        */

        $validSupervisorIds =
            User::query()
                ->whereIn(
                    'id',
                    $supervisorIds
                )
                ->where(
                    'role',
                    'kabag'
                )
                ->where(
                    'is_active',
                    true
                )
                ->pluck(
                    'id'
                );


        $kabag
            ->supervisors()
            ->sync(
                $validSupervisorIds
            );


        return back()
            ->with(
                'success',
                'Mapping atasan untuk '
                . $kabag->name
                . ' berhasil disimpan.'
            );
    }
}
