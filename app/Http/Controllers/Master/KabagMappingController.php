<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KabagMappingController extends Controller
{
    public function index(Request $request)
    {
        $kabagId =
            $request->integer(
                'kabag_id'
            );

        $search =
            trim(
                (string) $request->get(
                    'search',
                    ''
                )
            );

        $category =
            trim(
                (string) $request->get(
                    'category',
                    ''
                )
            );


        /*
        |--------------------------------------------------------------------------
        | DAFTAR KABAG
        |--------------------------------------------------------------------------
        */

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
                ->orderBy(
                    'name'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | KABAG TERPILIH
        |--------------------------------------------------------------------------
        */

        $selectedKabag =
            $kabagId
                ? $kabags->firstWhere(
                    'id',
                    $kabagId
                )
                : $kabags->first();


        /*
        |--------------------------------------------------------------------------
        | KATEGORI EMPLOYEE
        |--------------------------------------------------------------------------
        */

        $categories =
            Employee::query()
                ->where(
                    'is_active',
                    true
                )
                ->whereNotNull(
                    'source_kategori_karyawan_name'
                )
                ->where(
                    'source_kategori_karyawan_name',
                    '<>',
                    ''
                )
                ->distinct()
                ->orderBy(
                    'source_kategori_karyawan_name'
                )
                ->pluck(
                    'source_kategori_karyawan_name'
                );


        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE
        |--------------------------------------------------------------------------
        */

        $employees =
            Employee::query()
                ->with([
                    'kabags' => function ($query) {
                        $query
                            ->where(
                                'users.is_active',
                                true
                            )
                            ->orderBy(
                                'users.name'
                            );
                    },
                ])
                ->where(
                    'is_active',
                    true
                )

                ->when(
                    $search !== '',
                    function ($query) use (
                        $search
                    ) {
                        $query->where(
                            function ($subQuery) use (
                                $search
                            ) {
                                $subQuery
                                    ->where(
                                        'nama',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'employee_code',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'jabatan',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'source_kategori_karyawan_name',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                    }
                )

                ->when(
                    $category !== '',
                    fn ($query) =>
                        $query->where(
                            'source_kategori_karyawan_name',
                            $category
                        )
                )

                ->orderBy(
                    'source_kategori_karyawan_name'
                )
                ->orderBy(
                    'nama'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE YANG SUDAH DIMAPPING KE KABAG TERPILIH
        |--------------------------------------------------------------------------
        */

        $selectedEmployeeIds =
            collect();

        if ($selectedKabag) {
            $selectedEmployeeIds =
                $selectedKabag
                    ->managedEmployees()
                    ->pluck(
                        'employees.id'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $summary = [
            'total_kabag' =>
                $kabags->count(),

            'total_employee' =>
                Employee::query()
                    ->where(
                        'is_active',
                        true
                    )
                    ->count(),

            'mapped_to_selected' =>
                $selectedEmployeeIds
                    ->count(),

            'unmapped' =>
                Employee::query()
                    ->where(
                        'is_active',
                        true
                    )
                    ->whereDoesntHave(
                        'kabags'
                    )
                    ->count(),
        ];


        return view(
            'master.kabag-mapping.index',
            compact(
                'kabags',
                'selectedKabag',
                'employees',
                'selectedEmployeeIds',
                'categories',
                'search',
                'category',
                'summary'
            )
        );
    }


    public function update(
        Request $request,
        User $kabag
    ) {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI KABAG
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $kabag->role === 'kabag',
            404
        );


        $validated =
            $request->validate(
                [
                    'employee_ids' => [
                        'nullable',
                        'array',
                    ],

                    'employee_ids.*' => [
                        'integer',

                        Rule::exists(
                            'employees',
                            'id'
                        )->where(
                            fn ($query) =>
                                $query->where(
                                    'is_active',
                                    true
                                )
                        ),
                    ],
                ],
                [
                    'employee_ids.array' =>
                        'Data karyawan tidak valid.',

                    'employee_ids.*.exists' =>
                        'Salah satu karyawan tidak ditemukan atau sudah tidak aktif.',
                ]
            );


        $employeeIds =
            collect(
                $validated[
                    'employee_ids'
                ]
                ?? []
            )
                ->map(
                    fn ($id) =>
                        (int) $id
                )
                ->unique()
                ->values()
                ->all();


        /*
        |--------------------------------------------------------------------------
        | SYNC KHUSUS UNTUK KABAG INI
        |--------------------------------------------------------------------------
        |
        | Aman untuk multi-Kabag.
        |
        | sync() hanya mengubah pasangan milik $kabag ini.
        | Mapping employee dengan Kabag lain tidak akan terhapus.
        |
        */

        $kabag
            ->managedEmployees()
            ->sync(
                $employeeIds
            );


        return redirect()
            ->route(
                'master.kabag-mapping.index',
                [
                    'kabag_id' =>
                        $kabag->id,
                ]
            )
            ->with(
                'success',
                'Mapping Kabag '
                . $kabag->name
                . ' berhasil diperbarui. Total karyawan: '
                . count($employeeIds)
                . '.'
            );
    }
}
