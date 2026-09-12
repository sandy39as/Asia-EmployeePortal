<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KabagMappingController extends Controller
{
    public function index(Request $request)
    {
        $kabagId = $request->integer('kabag_id');

        $search = trim(
            (string) $request->get('search', '')
        );

        $kabags = User::query()
            ->where('role', 'kabag')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedKabag = null;

        if ($kabagId) {
            $selectedKabag = User::query()
                ->where('role', 'kabag')
                ->findOrFail($kabagId);
        }

        $employees = collect();

        $mappedEmployeeIds = [];

        if ($selectedKabag) {
            $mappedEmployeeIds = DB::table('kabag_employee')
                ->where(
                    'kabag_user_id',
                    $selectedKabag->id
                )
                ->pluck('employee_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $employees = Employee::query()
                ->where('is_active', true)

                ->when(
                    $search !== '',
                    function ($query) use ($search) {
                        $query->where(
                            function ($q) use ($search) {
                                $q->where(
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
                                );
                            }
                        );
                    }
                )

                ->orderBy('nama')
                ->paginate(30)
                ->withQueryString();
        }

        return view(
            'master.kabag-mapping.index',
            compact(
                'kabags',
                'selectedKabag',
                'employees',
                'mappedEmployeeIds',
                'search'
            )
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
            'employee_ids' => [
                'nullable',
                'array',
            ],

            'employee_ids.*' => [
                'integer',
                'exists:employees,id',
            ],
        ]);

        $employeeIds = collect(
            $validated['employee_ids'] ?? []
        )
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        DB::transaction(function () use (
            $kabag,
            $employeeIds
        ) {

            /*
            |--------------------------------------------------------------------------
            | Lepaskan mapping lama Kabag ini
            |--------------------------------------------------------------------------
            */

            DB::table('kabag_employee')
                ->where(
                    'kabag_user_id',
                    $kabag->id
                )
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | Pastikan employee tidak dimiliki Kabag lain
            |--------------------------------------------------------------------------
            */

            if (! empty($employeeIds)) {
                DB::table('kabag_employee')
                    ->whereIn(
                        'employee_id',
                        $employeeIds
                    )
                    ->delete();

                $now = now();

                $rows = collect($employeeIds)
                    ->map(function ($employeeId) use (
                        $kabag,
                        $now
                    ) {
                        return [
                            'kabag_user_id' =>
                                $kabag->id,

                            'employee_id' =>
                                $employeeId,

                            'created_at' =>
                                $now,

                            'updated_at' =>
                                $now,
                        ];
                    })
                    ->all();

                DB::table(
                    'kabag_employee'
                )->insert($rows);
            }
        });

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
                'Mapping Kabag berhasil diperbarui.'
            );
    }
}
