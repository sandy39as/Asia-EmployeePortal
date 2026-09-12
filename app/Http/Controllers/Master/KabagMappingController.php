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
        $search = trim(
            (string) $request->get('search', '')
        );

        $kabags = User::query()
            ->where('role', 'kabag')
            ->withCount('managedEmployees')
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    });
                }
            )
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view(
            'master.kabag-mapping.index',
            compact(
                'kabags',
                'search'
            )
        );
    }

    public function show(
        Request $request,
        User $kabag
    ) {
        abort_unless(
            $kabag->role === 'kabag',
            404
        );

        $search = trim(
            (string) $request->get('search', '')
        );

        $assignedEmployees = $kabag
            ->managedEmployees()
            ->orderBy('nama')
            ->paginate(
                20,
                ['*'],
                'assigned_page'
            );

        $assignedEmployeeIds = $kabag
            ->managedEmployees()
            ->pluck('employees.id');

        $availableEmployees = Employee::query()
            ->where('is_active', true)
            ->whereNotIn(
                'id',
                $assignedEmployeeIds
            )
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
            ->limit(50)
            ->get();

        return view(
            'master.kabag-mapping.show',
            compact(
                'kabag',
                'assignedEmployees',
                'availableEmployees',
                'search'
            )
        );
    }

    public function assign(
        Request $request,
        User $kabag
    ) {
        abort_unless(
            $kabag->role === 'kabag',
            404
        );

        $validated = $request->validate([
            'employee_id' => [
                'required',
                'integer',
                'exists:employees,id',
            ],
        ]);

        $employeeId = (int) $validated['employee_id'];

        DB::transaction(function () use (
            $kabag,
            $employeeId
        ) {
            /*
             * Satu karyawan hanya boleh punya satu Kabag.
             * Kalau sebelumnya sudah ada di Kabag lain,
             * pindahkan ke Kabag yang dipilih sekarang.
             */
            DB::table('kabag_employee')
                ->where(
                    'employee_id',
                    $employeeId
                )
                ->delete();

            DB::table('kabag_employee')
                ->insert([
                    'kabag_user_id' =>
                        $kabag->id,

                    'employee_id' =>
                        $employeeId,

                    'created_at' =>
                        now(),

                    'updated_at' =>
                        now(),
                ]);
        });

        return redirect()
            ->route(
                'master.kabag-mapping.show',
                $kabag
            )
            ->with(
                'success',
                'Karyawan berhasil dimasukkan ke Kabag.'
            );
    }

    public function remove(
        User $kabag,
        Employee $employee
    ) {
        abort_unless(
            $kabag->role === 'kabag',
            404
        );

        DB::table('kabag_employee')
            ->where(
                'kabag_user_id',
                $kabag->id
            )
            ->where(
                'employee_id',
                $employee->id
            )
            ->delete();

        return redirect()
            ->route(
                'master.kabag-mapping.show',
                $kabag
            )
            ->with(
                'success',
                'Karyawan berhasil dikeluarkan dari Kabag.'
            );
    }
}
