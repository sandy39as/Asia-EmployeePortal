<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HrdLeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $search = trim(
            (string) $request->get(
                'search',
                ''
            )
        );

        $jenis = trim(
            (string) $request->get(
                'jenis',
                ''
            )
        );

        $status = trim(
            (string) $request->get(
                'status',
                ''
            )
        );

        $startDate =
            $request->get(
                'start_date'
            );

        $endDate =
            $request->get(
                'end_date'
            );


        $items = LeaveRequest::query()
            ->with([
                'employee',

                'kabag',
                'kabagApprovedBy',
                'kabagRejectedBy',

                'hrdApprovedBy',
                'hrdRejectedBy',

                'approvedBy',
                'rejectedBy',

                'specialLeaveType',
                'permissionType',
            ])
            ->where(
                'kabag_status',
                'approved'
            )
            ->whereIn(
                'hrd_status',
                [
                    'pending',
                    'approved',
                    'rejected',
                ]
            )
            ->when(
                $search !== '',
                function ($q) use ($search) {

                    $q->whereHas(
                        'employee',
                        function ($employeeQuery) use ($search) {

                            $employeeQuery
                                ->where(
                                    'nama',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'employee_code',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                in_array(
                    $jenis,
                    [
                        'izin',
                        'cuti',
                        'sakit',
                    ],
                    true
                ),
                fn ($q) =>
                    $q->where(
                        'jenis',
                        $jenis
                    )
            )
            ->when(
                $status === 'pending',
                fn ($q) =>
                    $q->where(
                        'hrd_status',
                        'pending'
                    )
            )
            ->when(
                $status === 'approved',
                fn ($q) =>
                    $q->where(
                        'hrd_status',
                        'approved'
                    )
            )
            ->when(
                $status === 'rejected',
                fn ($q) =>
                    $q->where(
                        'hrd_status',
                        'rejected'
                    )
            )
            ->when(
                $status === 'cancelled',
                fn ($q) =>
                    $q->where(
                        'status',
                        'cancelled'
                    )
            )
            ->when(
                $startDate,
                fn ($q) =>
                    $q->whereDate(
                        'tanggal_selesai',
                        '>=',
                        $startDate
                    )
            )
            ->when(
                $endDate,
                fn ($q) =>
                    $q->whereDate(
                        'tanggal_mulai',
                        '<=',
                        $endDate
                    )
            )
            ->orderByRaw("
                            CASE
                                -- 1. Menunggu HRD (Wajib dieksekusi HRD)
                                WHEN status != 'cancelled' AND hrd_status = 'pending' THEN 0

                                -- 2. Sudah selesai diproses HRD (Approved / Rejected)
                                WHEN status != 'cancelled' AND hrd_status IN ('approved', 'rejected') THEN 1

                                -- 3. Dibatalkan oleh karyawan / status lainnya
                                ELSE 2
                            END ASC
                        ")
                        ->latest('created_at')
                        ->paginate(20)
                        ->withQueryString();


        return view(
            'hrd.leave-requests.index',
            compact(
                'items',
                'search',
                'jenis',
                'status',
                'startDate',
                'endDate'
            )
        );
    }


    public function show(
        LeaveRequest $leaveRequest
    ) {

        abort_unless(
            $leaveRequest->kabag_status
                === 'approved',
            404
        );


        $leaveRequest->load([
            'employee',

            'kabag',
            'kabagApprovedBy',
            'kabagRejectedBy',

            'hrdApprovedBy',
            'hrdRejectedBy',

            'approvedBy',
            'rejectedBy',

            'specialLeaveType',
            'permissionType',
        ]);


        return view(
            'hrd.leave-requests.show',
            compact(
                'leaveRequest'
            )
        );
    }


    public function approve(
        Request $request,
        LeaveRequest $leaveRequest
    ) {

        DB::transaction(
            function () use (
                $request,
                $leaveRequest
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK PENGAJUAN
                |--------------------------------------------------------------------------
                */

                $item = LeaveRequest::query()
                    ->with([
                        'employee',
                        'specialLeaveType',
                        'permissionType',
                    ])
                    ->whereKey(
                        $leaveRequest->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | IDEMPOTENT
                |--------------------------------------------------------------------------
                |
                | Jika tombol approve terpanggil ulang setelah berhasil,
                | jangan sampai saldo cuti dipotong dua kali.
                |
                */

                if (
                    $item->hrd_status
                    === 'approved'
                ) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | VALIDASI WORKFLOW
                |--------------------------------------------------------------------------
                */

                if (
                    $item->kabag_status
                    !== 'approved'
                ) {
                    throw ValidationException::withMessages([
                        'kabag_status' =>
                            'Pengajuan belum disetujui oleh Kabag.',
                    ]);
                }


                if (
                    $item->hrd_status
                    !== 'pending'
                ) {
                    throw ValidationException::withMessages([
                        'hrd_status' =>
                            'Pengajuan ini sudah diproses oleh HRD sebelumnya.',
                    ]);
                }


                if (
                    $item->status
                    !== 'pending'
                ) {
                    throw ValidationException::withMessages([
                        'status' =>
                            'Pengajuan sudah memiliki status final '
                            . $item->status
                            . '.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | POTONG SALDO CUTI TAHUNAN
                |--------------------------------------------------------------------------
                |
                | Hanya dijalankan jika:
                |
                | jenis          = cuti
                | leave_category = annual
                |
                | Cuti khusus tidak masuk ke sini.
                |
                */

                $this->consumeAnnualLeaveBalance(
                    $item
                );


                /*
                |--------------------------------------------------------------------------
                | FINAL APPROVAL HRD
                |--------------------------------------------------------------------------
                */

                $userId =
                    $request
                        ->user()
                        ->id;

                $now =
                    now();


                $item->update([

                    'status' =>
                        'approved',

                    'hrd_status' =>
                        'approved',

                    'hrd_approved_by' =>
                        $userId,

                    'hrd_approved_at' =>
                        $now,

                    'hrd_rejected_by' =>
                        null,

                    'hrd_rejected_at' =>
                        null,

                    'hrd_rejection_reason' =>
                        null,

                    'hrd_action_source' =>
                        'portal',

                    /*
                    |--------------------------------------------------------------------------
                    | LEGACY
                    |--------------------------------------------------------------------------
                    */

                    'approved_by' =>
                        $userId,

                    'approved_at' =>
                        $now,

                    'rejected_by' =>
                        null,

                    'rejected_at' =>
                        null,

                    'rejection_reason' =>
                        null,

                    /*
                    |--------------------------------------------------------------------------
                    | SYNC KE FACELOG
                    |--------------------------------------------------------------------------
                    */

                    'local_sync_status' =>
                        'pending',
                ]);
            }
        );


        return redirect()
            ->route(
                'hrd.leave-requests.index'
            )
            ->with(
                'success',
                'Pengajuan berhasil disetujui HRD dan menjadi persetujuan final.'
            );
    }


    public function reject(
        Request $request,
        LeaveRequest $leaveRequest
    ) {

        $validated =
            $request->validate(
                [
                    'rejection_reason' => [
                        'required',
                        'string',
                        'max:1000',
                    ],
                ],
                [
                    'rejection_reason.required' =>
                        'Alasan penolakan wajib diisi.',
                ]
            );


        DB::transaction(
            function () use (
                $request,
                $leaveRequest,
                $validated
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK PENGAJUAN
                |--------------------------------------------------------------------------
                */

                $item = LeaveRequest::query()
                    ->whereKey(
                        $leaveRequest->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | IDEMPOTENT
                |--------------------------------------------------------------------------
                */

                if (
                    $item->hrd_status
                    === 'rejected'
                ) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | VALIDASI WORKFLOW
                |--------------------------------------------------------------------------
                */

                if (
                    $item->kabag_status
                    !== 'approved'
                ) {
                    throw ValidationException::withMessages([
                        'kabag_status' =>
                            'Pengajuan belum disetujui oleh Kabag.',
                    ]);
                }


                if (
                    $item->hrd_status
                    !== 'pending'
                ) {
                    throw ValidationException::withMessages([
                        'hrd_status' =>
                            'Pengajuan ini sudah diproses oleh HRD sebelumnya.',
                    ]);
                }


                if (
                    $item->status
                    !== 'pending'
                ) {
                    throw ValidationException::withMessages([
                        'status' =>
                            'Pengajuan sudah memiliki status final '
                            . $item->status
                            . '.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | REJECT TIDAK MEMOTONG SALDO
                |--------------------------------------------------------------------------
                */

                $userId =
                    $request
                        ->user()
                        ->id;

                $now =
                    now();


                $item->update([

                    'status' =>
                        'rejected',

                    'hrd_status' =>
                        'rejected',

                    'hrd_approved_by' =>
                        null,

                    'hrd_approved_at' =>
                        null,

                    'hrd_rejected_by' =>
                        $userId,

                    'hrd_rejected_at' =>
                        $now,

                    'hrd_rejection_reason' =>
                        $validated[
                            'rejection_reason'
                        ],

                    'hrd_action_source' =>
                        'portal',

                    /*
                    |--------------------------------------------------------------------------
                    | LEGACY
                    |--------------------------------------------------------------------------
                    */

                    'approved_by' =>
                        null,

                    'approved_at' =>
                        null,

                    'rejected_by' =>
                        $userId,

                    'rejected_at' =>
                        $now,

                    'rejection_reason' =>
                        $validated[
                            'rejection_reason'
                        ],

                    /*
                    |--------------------------------------------------------------------------
                    | SYNC KE FACELOG
                    |--------------------------------------------------------------------------
                    */

                    'local_sync_status' =>
                        'pending',
                ]);
            }
        );


        return redirect()
            ->route(
                'hrd.leave-requests.index'
            )
            ->with(
                'success',
                'Pengajuan berhasil ditolak oleh HRD.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CONSUME ANNUAL LEAVE BALANCE
    |--------------------------------------------------------------------------
    |
    | Dipanggil HANYA pada final HRD approval.
    |
    */

    protected function consumeAnnualLeaveBalance(
        LeaveRequest $item
    ): void {

        /*
        |--------------------------------------------------------------------------
        | BUKAN CUTI TAHUNAN
        |--------------------------------------------------------------------------
        */

        if (
            $item->jenis
                !== 'cuti'
            ||
            $item->leave_category
                !== 'annual'
        ) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI EMPLOYEE
        |--------------------------------------------------------------------------
        */

        if (! $item->employee_id) {
            throw ValidationException::withMessages([
                'employee' =>
                    'Data karyawan pada pengajuan tidak ditemukan.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | LOCK EMPLOYEE
        |--------------------------------------------------------------------------
        |
        | Ini membuat approval cuti tahunan milik employee yang sama
        | berjalan berurutan jika ada dua approval secara bersamaan.
        |
        */

        $employee =
            Employee::query()
                ->whereKey(
                    $item->employee_id
                )
                ->lockForUpdate()
                ->first();


        if (! $employee) {
            throw ValidationException::withMessages([
                'employee' =>
                    'Data karyawan pengajuan tidak ditemukan.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | CUTI TAHUNAN HANYA UNTUK ASIA
        |--------------------------------------------------------------------------
        */

        if (! $employee->isAsiaEmployee()) {
            throw ValidationException::withMessages([
                'leave_category' =>
                    'Cuti tahunan hanya tersedia untuk karyawan ASIA.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | JUMLAH HARI
        |--------------------------------------------------------------------------
        */

        $leaveDays =
            (int) (
                $item->leave_days
                ?? 0
            );


        if ($leaveDays <= 0) {
            throw ValidationException::withMessages([
                'leave_days' =>
                    'Jumlah hari cuti tahunan tidak valid.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | TAHUN SALDO
        |--------------------------------------------------------------------------
        */

        $year =
            $item->tanggal_mulai
                ?->year
            ?? now()->year;


        /*
        |--------------------------------------------------------------------------
        | AMBIL SALDO DAN LOCK
        |--------------------------------------------------------------------------
        */

        $balance =
            EmployeeLeaveBalance::query()
                ->where(
                    'employee_id',
                    $employee->id
                )
                ->where(
                    'year',
                    $year
                )
                ->lockForUpdate()
                ->first();


        /*
        |--------------------------------------------------------------------------
        | JIKA SALDO BELUM ADA
        |--------------------------------------------------------------------------
        |
        | Employee ASIA normalnya sudah punya saldo dari sync.
        | Ini hanya fallback.
        |
        */

        if (! $balance) {

            $balance =
                EmployeeLeaveBalance::create([
                    'employee_id' =>
                        $employee->id,

                    'year' =>
                        $year,

                    'entitlement' =>
                        12,

                    'used' =>
                        0,

                    'remaining' =>
                        12,
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI SALDO TERBARU
        |--------------------------------------------------------------------------
        |
        | Walaupun saat submit saldo cukup, saldo bisa berubah karena
        | pengajuan lain sudah lebih dahulu disetujui HRD.
        |
        */

        if (
            $leaveDays
            >
            (int) $balance->remaining
        ) {
            throw ValidationException::withMessages([
                'leave_days' =>
                    'Saldo cuti tahunan tidak mencukupi. '
                    . 'Pengajuan membutuhkan '
                    . $leaveDays
                    . ' hari, sedangkan sisa saldo hanya '
                    . $balance->remaining
                    . ' hari.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | POTONG SALDO
        |--------------------------------------------------------------------------
        */

        $balance->update([

            'used' =>
                (int) $balance->used
                + $leaveDays,

            'remaining' =>
                (int) $balance->remaining
                - $leaveDays,
        ]);
    }
}
