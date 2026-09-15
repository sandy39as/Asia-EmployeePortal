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

        $area =
            trim(
                (string) $request->get(
                    'area',
                    ''
                )
            );

        if (
            ! in_array(
                $area,
                [
                    '',
                    '52',
                    '27',
                    'other',
                ],
                true
            )
        ) {
            $area = '';
        }


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
                ->withCount(
                    'managedEmployees'
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
        | KATEGORI / BAGIAN
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
        | SUDAH MASUK KE KABAG TERPILIH
        |--------------------------------------------------------------------------
        |
        | PENTING:
        | List ini TIDAK terkena search/filter.
        | Jadi melakukan pencarian tidak akan membuat mapping lama "hilang"
        | dari form ataupun terhapus saat menyimpan.
        |
        */

        $mappedEmployees =
            collect();

        $mappedEmployeeIds =
            collect();

        if ($selectedKabag) {

            $mappedEmployees =
                $selectedKabag
                    ->managedEmployees()
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
                    ->orderBy(
                        'source_kategori_karyawan_name'
                    )
                    ->orderBy(
                        'nama'
                    )
                    ->get();

            $mappedEmployeeIds =
                $mappedEmployees
                    ->pluck(
                        'id'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | BELUM MASUK KE KABAG TERPILIH
        |--------------------------------------------------------------------------
        |
        | Search dan filter HANYA bekerja di daftar ini.
        |
        | "Belum masuk" berarti belum terhubung ke KABAG YANG SEDANG DIPILIH.
        | Employee tetap boleh sudah punya Kabag lain karena sistem multi-Kabag.
        |
        */

        $availableEmployees =
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
                    $mappedEmployeeIds->isNotEmpty(),
                    fn ($query) =>
                        $query->whereNotIn(
                            'id',
                            $mappedEmployeeIds
                        )
                )
                ->when(
                    $search !== '',
                    function ($query) use ($search) {

                        $query->where(
                            function ($subQuery) use ($search) {

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
                ->when(
                    $area === '52',
                    fn ($query) =>
                        $query->whereIn(
                            'source_device_id',
                            [
                                1,
                                2,
                            ]
                        )
                )
                ->when(
                    $area === '27',
                    fn ($query) =>
                        $query->where(
                            'source_device_id',
                            3
                        )
                )
                ->when(
                    $area === 'other',
                    fn ($query) =>
                        $query->where(
                            function ($subQuery) {
                                $subQuery
                                    ->whereNull(
                                        'source_device_id'
                                    )
                                    ->orWhereNotIn(
                                        'source_device_id',
                                        [
                                            1,
                                            2,
                                            3,
                                        ]
                                    );
                            }
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
                $mappedEmployeeIds
                    ->count(),

            'unmapped_all' =>
                Employee::query()
                    ->where(
                        'is_active',
                        true
                    )
                    ->whereDoesntHave(
                        'kabags'
                    )
                    ->count(),

            'area_52' =>
                Employee::query()
                    ->where(
                        'is_active',
                        true
                    )
                    ->whereIn(
                        'source_device_id',
                        [
                            1,
                            2,
                        ]
                    )
                    ->count(),

            'area_27' =>
                Employee::query()
                    ->where(
                        'is_active',
                        true
                    )
                    ->where(
                        'source_device_id',
                        3
                    )
                    ->count(),
        ];


        return view(
            'master.kabag-mapping.index',
            compact(
                'kabags',
                'selectedKabag',
                'mappedEmployees',
                'availableEmployees',
                'categories',
                'search',
                'category',
                'area',
                'summary'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TAMBAHKAN EMPLOYEE KE KABAG
    |--------------------------------------------------------------------------
    |
    | syncWithoutDetaching() adalah kunci untuk multi-Kabag:
    | - mapping Kabag ini bertambah
    | - mapping lama Kabag ini tetap ada
    | - mapping employee ke Kabag lain juga tetap aman
    |
    */

    public function assign(
        Request $request,
        User $kabag
    ) {
        abort_unless(
            $kabag->role === 'kabag',
            404
        );


        $validated =
            $request->validate(
                [
                    'employee_ids' => [
                        'required',
                        'array',
                        'min:1',
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
                    'employee_ids.required' =>
                        'Pilih minimal satu karyawan.',

                    'employee_ids.min' =>
                        'Pilih minimal satu karyawan.',

                    'employee_ids.*.exists' =>
                        'Salah satu karyawan tidak ditemukan atau sudah tidak aktif.',
                ]
            );


        $employeeIds =
            collect(
                $validated[
                    'employee_ids'
                ]
            )
                ->map(
                    fn ($id) =>
                        (int) $id
                )
                ->unique()
                ->values()
                ->all();


        $kabag
            ->managedEmployees()
            ->syncWithoutDetaching(
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
                count($employeeIds)
                . ' karyawan berhasil ditambahkan ke '
                . $kabag->name
                . '.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS SATU MAPPING
    |--------------------------------------------------------------------------
    |
    | detach hanya menghapus hubungan employee dengan Kabag yang sedang dipilih.
    | Hubungan employee dengan Kabag lain tidak ikut terhapus.
    |
    */

    public function remove(
        User $kabag,
        Employee $employee
    ) {
        abort_unless(
            $kabag->role === 'kabag',
            404
        );


        $kabag
            ->managedEmployees()
            ->detach(
                $employee->id
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
                $employee->nama
                . ' berhasil dilepas dari '
                . $kabag->name
                . '.'
            );
    }
}
