<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeTempCredential;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HrdEmployeeController extends Controller
{
    private const MASTER_EMAIL =
        'sandyramdani65@gmail.com';


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

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

        $leaveYear = (int) $request->get(
            'leave_year',
            now()->year
        );

        if (
            $leaveYear < 2000
            ||
            $leaveYear
            >
            ((int) now()->year + 1)
        ) {
            $leaveYear =
                (int) now()->year;
        }


        /*
        |--------------------------------------------------------------------------
        | BASE QUERY + SCOPE HRD
        |--------------------------------------------------------------------------
        */

        $baseQuery =
            Employee::query();

        $this->applyHrdScope(
            $baseQuery,
            $request
        );


        /*
        |--------------------------------------------------------------------------
        | QUERY LIST KARYAWAN
        |--------------------------------------------------------------------------
        */

        $employees =
            (clone $baseQuery)
                ->with([
                    'user',

                    'leaveBalances' =>
                        fn ($query) =>
                            $query->where(
                                'year',
                                $leaveYear
                            ),
                ])
                ->orderBy(
                    'nama'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | PASTIKAN KARYAWAN ASIA PUNYA SALDO TAHUN TERPILIH
        |--------------------------------------------------------------------------
        */

        foreach (
            $employees
            as $employee
        ) {
            if (
                ! $employee->isAsiaEmployee()
            ) {
                continue;
            }

            $balance =
                $employee
                    ->leaveBalances
                    ->first();

            if (
                ! $balance
            ) {
                $balance =
                    $employee
                        ->leaveBalanceForYear(
                            $leaveYear
                        );

                $employee->setRelation(
                    'leaveBalances',
                    collect([
                        $balance,
                    ])
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SUMMARY CARDS
        |--------------------------------------------------------------------------
        */

        $summary = [
            'total' =>
                (clone $baseQuery)
                    ->count(),

            'active' =>
                (clone $baseQuery)
                    ->where(
                        'is_active',
                        true
                    )
                    ->count(),

            'inactive' =>
                (clone $baseQuery)
                    ->where(
                        'is_active',
                        false
                    )
                    ->count(),

            'must_change_username' =>
                (clone $baseQuery)
                    ->whereHas(
                        'user',
                        fn (
                            Builder $q
                        ) =>
                            $q->where(
                                'must_change_username',
                                true
                            )
                    )
                    ->count(),

            'must_change_password' =>
                (clone $baseQuery)
                    ->whereHas(
                        'user',
                        fn (
                            Builder $q
                        ) =>
                            $q->where(
                                'must_change_password',
                                true
                            )
                    )
                    ->count(),

            'asia' =>
                (clone $baseQuery)
                    ->where(
                        'employment_group',
                        'asia'
                    )
                    ->count(),

            'outsourcing' =>
                (clone $baseQuery)
                    ->where(
                        'employment_group',
                        'outsourcing'
                    )
                    ->count(),
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


    /*
    |--------------------------------------------------------------------------
    | RESET AKUN KARYAWAN
    |--------------------------------------------------------------------------
    |
    | reset_mode:
    |
    | password_only
    | - ID Login aktif tetap
    | - password dibuat sementara
    | - wajib ganti password
    |
    | login_and_password
    | - HANYA master Sandy
    | - ID Login kembali ke employee_code
    | - password dibuat sementara
    | - wajib ganti ID Login
    | - wajib ganti password
    |
    */

    public function resetPassword(
        Request $request,
        Employee $employee
    ): RedirectResponse|JsonResponse {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI MODE RESET
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate([
                'reset_mode' => [
                    'nullable',
                    Rule::in([
                        'password_only',
                        'login_and_password',
                    ]),
                ],
            ]);

        $resetMode =
            $validated['reset_mode']
            ?? 'password_only';


        /*
        |--------------------------------------------------------------------------
        | PASTIKAN HRD HANYA BISA RESET KARYAWAN DALAM SCOPE-NYA
        |--------------------------------------------------------------------------
        */

        $allowedEmployeeQuery =
            Employee::query()
                ->whereKey(
                    $employee->id
                );

        $this->applyHrdScope(
            $allowedEmployeeQuery,
            $request
        );

        $employee =
            $allowedEmployeeQuery
                ->first();

        if (
            ! $employee
        ) {
            return $this->resetError(
                $request,
                'Karyawan tidak berada dalam area akses HRD Anda.',
                403
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RESET ID + PASSWORD HANYA MASTER ADMIN
        |--------------------------------------------------------------------------
        */

        if (
            $resetMode
            ===
            'login_and_password'
            &&
            ! $this->isMasterAdmin(
                $request
            )
        ) {
            return $this->resetError(
                $request,
                'Reset ID Login + Password hanya dapat dilakukan oleh master admin.',
                403
            );
        }


        $employee->load(
            'user'
        );

        $user =
            $employee->user;


        /*
        |--------------------------------------------------------------------------
        | VALIDASI AKUN
        |--------------------------------------------------------------------------
        */

        if (
            ! $user
        ) {
            return $this->resetError(
                $request,
                'Akun login karyawan belum tersedia.',
                422
            );
        }


        if (
            $user->role !== 'karyawan'
        ) {
            return $this->resetError(
                $request,
                'Akun HRD/Admin tidak dapat direset dari halaman ini.',
                422
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI ID AWAL SAAT RESET TOTAL
        |--------------------------------------------------------------------------
        */

        if (
            $resetMode
            ===
            'login_and_password'
            &&
            blank(
                $employee->employee_code
            )
        ) {
            return $this->resetError(
                $request,
                'ID karyawan tidak tersedia sehingga ID Login tidak dapat dikembalikan.',
                422
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PASSWORD SEMENTARA 6 DIGIT
        |--------------------------------------------------------------------------
        */

        $temporaryPassword =
            (string) random_int(
                100000,
                999999
            );


        /*
        |--------------------------------------------------------------------------
        | RESET DALAM TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $request,
                $employee,
                $user,
                $temporaryPassword,
                $resetMode
            ) {
                $userData = [
                    'password' =>
                        Hash::make(
                            $temporaryPassword
                        ),

                    'must_change_password' =>
                        true,
                ];


                if (
                    $resetMode
                    ===
                    'login_and_password'
                ) {
                    $userData[
                        'username'
                    ] =
                        $employee
                            ->employee_code;

                    $userData[
                        'must_change_username'
                    ] =
                        true;
                }


                $user
                    ->forceFill(
                        $userData
                    )
                    ->save();


                /*
                |--------------------------------------------------------------------------
                | SIMPAN PASSWORD SEMENTARA TERENKRIPSI
                |--------------------------------------------------------------------------
                |
                | Model EmployeeTempCredential memakai cast "encrypted",
                | jadi plaintext tidak disimpan langsung ke database.
                |
                */

                EmployeeTempCredential::updateOrCreate(
                    [
                        'user_id' =>
                            $user->id,
                    ],
                    [
                        'employee_id' =>
                            $employee->id,

                        'password_encrypted' =>
                            $temporaryPassword,

                        'created_by' =>
                            $request
                                ->user()
                                ->id,

                        'generated_at' =>
                            now(),

                        'exported_at' =>
                            null,

                        'expires_at' =>
                            now()
                                ->addDays(
                                    7
                                ),
                    ]
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | REFRESH USER SETELAH UPDATE
        |--------------------------------------------------------------------------
        */

        $user->refresh();


        $result = [
            'nama' =>
                $employee->nama,

            'username' =>
                $user->username,

            'password' =>
                $temporaryPassword,

            'reset_mode' =>
                $resetMode,

            'must_change_username' =>
                (bool) $user
                    ->must_change_username,

            'must_change_password' =>
                (bool) $user
                    ->must_change_password,
        ];


        /*
        |--------------------------------------------------------------------------
        | JSON AJAX
        |--------------------------------------------------------------------------
        */

        if (
            $request->expectsJson()
        ) {
            return response()->json([
                'success' =>
                    true,

                'message' =>
                    $resetMode
                    ===
                    'login_and_password'
                        ? 'ID Login dan password berhasil direset.'
                        : 'Password berhasil direset.',

                'data' =>
                    $result,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | NON AJAX
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'hrd.employees.index',
                [
                    'search' =>
                        $employee
                            ->employee_code,

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
                $resetMode
                ===
                'login_and_password'
                    ? 'ID Login dan password berhasil direset.'
                    : 'Password berhasil direset.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | APPLY HRD ACCESS SCOPE
    |--------------------------------------------------------------------------
    */

    private function applyHrdScope(
        Builder $query,
        Request $request
    ): void {
        $user =
            $request->user();

        $email =
            strtolower(
                trim(
                    (string) (
                        $user->email
                        ?? ''
                    )
                )
            );

        $name =
            strtolower(
                trim(
                    (string) (
                        $user->name
                        ?? ''
                    )
                )
            );


        /*
        |--------------------------------------------------------------------------
        | SANDY / SUPER HRD
        |--------------------------------------------------------------------------
        |
        | Dipertahankan sesuai behavior controller lama:
        | email Sandy ATAU nama mengandung "sandy".
        |
        */

        $isSuperHrd =
            in_array(
                $email,
                [
                    self::MASTER_EMAIL,
                ],
                true
            )
            ||
            str_contains(
                $name,
                'sandy'
            );


        if (
            $isSuperHrd
        ) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | ASIA 52
        |--------------------------------------------------------------------------
        */

        if (
            $email
            ===
            'admin@asia.com'
        ) {
            $query->whereIn(
                'source_device_id',
                [
                    1,
                    2,
                ]
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | HRD ASIA 52
        |--------------------------------------------------------------------------
        */

        if (
            $email
            ===
            'hrdasia52@gmail.com'
        ) {
            $query
                ->whereIn(
                    'source_device_id',
                    [
                        1,
                        2,
                    ]
                )
                ->where(
                    function (
                        Builder $q
                    ) {
                        $q
                            ->where(
                                'source_kategori_karyawan_name',
                                'like',
                                '%ASIA%'
                            )
                            ->where(
                                'source_kategori_karyawan_name',
                                'not like',
                                '%OUT%'
                            );
                    }
                );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | HRD OUTSOURCING 52
        |--------------------------------------------------------------------------
        */

        if (
            $email
            ===
            'hrdoutsourcing52@gmail.com'
        ) {
            $query
                ->whereIn(
                    'source_device_id',
                    [
                        1,
                        2,
                    ]
                )
                ->where(
                    function (
                        Builder $q
                    ) {
                        $q
                            ->where(
                                'source_kategori_karyawan_name',
                                'like',
                                '%OUT%'
                            )
                            ->orWhere(
                                'source_kategori_karyawan_name',
                                'like',
                                '%OUTSOURCING%'
                            );
                    }
                );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | ASIA 27
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $email,
                [
                    'admin27@asia.com',
                    'adminasia27@gmail.com',
                ],
                true
            )
        ) {
            $query->where(
                'source_device_id',
                3
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | FAIL CLOSED
        |--------------------------------------------------------------------------
        |
        | Kalau ada akun HRD yang belum terdaftar di mapping scope di atas,
        | jangan otomatis beri akses semua data.
        |
        */

        $query->whereRaw(
            '1 = 0'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MASTER ADMIN CHECK
    |--------------------------------------------------------------------------
    */

    private function isMasterAdmin(
        Request $request
    ): bool {
        $email =
            strtolower(
                trim(
                    (string) (
                        $request
                            ->user()
                            ?->email
                        ?? ''
                    )
                )
            );

        return $email
            ===
            self::MASTER_EMAIL;
    }


    /*
    |--------------------------------------------------------------------------
    | ERROR RESPONSE
    |--------------------------------------------------------------------------
    */

    private function resetError(
        Request $request,
        string $message,
        int $status
    ): RedirectResponse|JsonResponse {
        if (
            $request->expectsJson()
        ) {
            return response()->json(
                [
                    'message' =>
                        $message,
                ],
                $status
            );
        }

        return back()
            ->with(
                'error',
                $message
            );
    }
}
