<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class HrdEmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim(
            (string) $request->get('search', '')
        );

        $status = trim(
            (string) $request->get('status', '')
        );

        $leaveYear = (int) $request->get(
            'leave_year',
            now()->year
        );

        if (
            $leaveYear < 2000
            ||
            $leaveYear > ((int) now()->year + 1)
        ) {
            $leaveYear = (int) now()->year;
        }

        $employees = Employee::query()
            ->with([
                'user',

                'leaveBalances' =>
                    fn ($query) =>
                        $query->where(
                            'year',
                            $leaveYear
                        ),
            ])

            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        function ($q) use ($search) {
                            $q
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
                                ->orWhereHas(
                                    'user',
                                    function ($userQuery) use ($search) {
                                        $userQuery->where(
                                            'username',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
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

            ->orderBy('nama')
            ->paginate(25)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | PASTIKAN KARYAWAN ASIA PUNYA SALDO TAHUN TERPILIH
        |--------------------------------------------------------------------------
        |
        | Jika row saldo belum ada, helper leaveBalanceForYear() akan membuat:
        | entitlement = 12
        | used        = 0
        | remaining   = 12
        |
        | Outsourcing tidak dibuatkan saldo annual.
        |
        */

        foreach ($employees->getCollection() as $employee) {

            if (! $employee->isAsiaEmployee()) {
                continue;
            }

            $balance =
                $employee
                    ->leaveBalances
                    ->first();

            if (! $balance) {

                $balance =
                    $employee
                        ->leaveBalanceForYear(
                            $leaveYear
                        );

                $employee->setRelation(
                    'leaveBalances',
                    collect([$balance])
                );
            }
        }


        $summary = [
            'total' =>
                Employee::count(),

            'active' =>
                Employee::where(
                    'is_active',
                    true
                )->count(),

            'inactive' =>
                Employee::where(
                    'is_active',
                    false
                )->count(),

            'must_change_password' =>
                Employee::query()
                    ->whereHas(
                        'user',
                        fn ($q) =>
                            $q->where(
                                'must_change_password',
                                true
                            )
                    )
                    ->count(),

            'asia' =>
                Employee::where(
                    'employment_group',
                    'asia'
                )->count(),

            'outsourcing' =>
                Employee::where(
                    'employment_group',
                    'outsourcing'
                )->count(),
        ];


        return view(
            'hrd.employees.index',
            compact(
                'employees',
                'summary',
                'search',
                'status',
                'leaveYear'
            )
        );
    }


    public function resetPassword(
        Request $request,
        Employee $employee
    ): RedirectResponse|JsonResponse {

        $employee->load('user');

        $user =
            $employee->user;


        if (! $user) {

            if ($request->expectsJson()) {

                return response()->json(
                    [
                        'message' =>
                            'Akun login karyawan belum tersedia.',
                    ],
                    422
                );
            }


            return back()->with(
                'error',
                'Akun login karyawan belum tersedia.'
            );
        }


        if ($user->role !== 'karyawan') {

            if ($request->expectsJson()) {

                return response()->json(
                    [
                        'message' =>
                            'Password akun HRD/Admin tidak dapat direset dari halaman ini.',
                    ],
                    422
                );
            }


            return back()->with(
                'error',
                'Password akun HRD/Admin tidak dapat direset dari halaman ini.'
            );
        }


        $temporaryPassword =
            (string) random_int(
                100000,
                999999
            );


        DB::transaction(
            function () use (
                $user,
                $temporaryPassword
            ) {

                $user->update([
                    'password' =>
                        Hash::make(
                            $temporaryPassword
                        ),

                    'must_change_password' =>
                        true,
                ]);
            }
        );


        $result = [
            'nama' =>
                $employee->nama,

            'username' =>
                $user->username,

            'password' =>
                $temporaryPassword,
        ];


        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,

                'message' =>
                    'Password berhasil direset.',

                'data' =>
                    $result,
            ]);
        }


        return redirect()
            ->route(
                'hrd.employees.index',
                [
                    'search' =>
                        $employee->employee_code,

                    'leave_year' =>
                        $request->get(
                            'leave_year',
                            now()->year
                        ),
                ]
            )
            ->with(
                'reset_password_result',
                $result
            )
            ->with(
                'success',
                'Password berhasil direset.'
            );
    }
}
