<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FaceLogLeaveRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'updated_after' => [
                'nullable',
                'date',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:200',
            ],
        ]);

        $perPage = (int) (
            $validated['per_page']
            ?? 100
        );

        $query = LeaveRequest::query()
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
            );

        if (
            ! empty(
                $validated['updated_after']
            )
        ) {
            $query->where(
                'updated_at',
                '>',
                $validated['updated_after']
            );
        }

        $items = $query
            ->orderBy('updated_at')
            ->orderBy('id')
            ->paginate($perPage);

        return response()->json([
            'success' => true,

            'server_time' =>
                now()->toIso8601String(),

            'data' =>
                collect(
                    $items->items()
                )
                    ->map(
                        fn (LeaveRequest $item) =>
                            $this->transform($item)
                    )
                    ->values(),

            'pagination' => [
                'current_page' =>
                    $items->currentPage(),

                'last_page' =>
                    $items->lastPage(),

                'per_page' =>
                    $items->perPage(),

                'total' =>
                    $items->total(),

                'has_more' =>
                    $items->hasMorePages(),
            ],
        ]);
    }


    public function show(
        string $uuid
    ): JsonResponse {

        $item = LeaveRequest::query()
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
            ])
            ->where(
                'uuid',
                $uuid
            )
            ->firstOrFail();

        return response()->json([
            'success' => true,

            'data' =>
                $this->transform($item),
        ]);
    }


    public function approve(
        Request $request,
        string $uuid
    ): JsonResponse {

        $validated =
            $request->validate([
                'approved_by_name' => [
                    'required',
                    'string',
                    'max:255',
                ],
            ]);

        $item = DB::transaction(
            function () use (
                $uuid,
                $validated
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
                    ])
                    ->where(
                        'uuid',
                        $uuid
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | IDEMPOTENT
                |--------------------------------------------------------------------------
                |
                | Kalau request approval yang sama terkirim lagi,
                | jangan potong saldo dua kali.
                |
                */

                if (
                    $item->hrd_status
                    === 'approved'
                ) {
                    return $item->fresh([
                        'employee',

                        'kabag',
                        'kabagApprovedBy',
                        'kabagRejectedBy',

                        'hrdApprovedBy',
                        'hrdRejectedBy',

                        'approvedBy',
                        'rejectedBy',

                        'specialLeaveType',
                    ]);
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
                            'Pengajuan sudah diproses HRD dengan status '
                            . (
                                $item->hrd_status
                                ?? '-'
                            )
                            . '.',
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
                | Hanya:
                | jenis          = cuti
                | leave_category = annual
                |
                | Cuti khusus tidak menyentuh saldo tahunan.
                |
                */

                $this->consumeAnnualLeaveBalance(
                    $item
                );


                /*
                |--------------------------------------------------------------------------
                | FINAL APPROVE
                |--------------------------------------------------------------------------
                */

                $now = now();

                $item->update([

                    'status' =>
                        'approved',

                    'hrd_status' =>
                        'approved',

                    'hrd_approved_by' =>
                        null,

                    'hrd_approved_at' =>
                        $now,

                    'hrd_rejected_by' =>
                        null,

                    'hrd_rejected_at' =>
                        null,

                    'hrd_rejection_reason' =>
                        null,

                    'hrd_action_source' =>
                        'facelog',

                    /*
                    |--------------------------------------------------------------------------
                    | LEGACY
                    |--------------------------------------------------------------------------
                    */

                    'approved_by' =>
                        null,

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
                    | FACELOG USER NAME
                    |--------------------------------------------------------------------------
                    */

                    'external_approved_by_name' =>
                        $validated[
                            'approved_by_name'
                        ],

                    'external_rejected_by_name' =>
                        null,

                    /*
                    |--------------------------------------------------------------------------
                    | SYNC
                    |--------------------------------------------------------------------------
                    */

                    'local_sync_status' =>
                        'synced',

                    'local_synced_at' =>
                        $now,
                ]);


                return $item->fresh([
                    'employee',

                    'kabag',
                    'kabagApprovedBy',
                    'kabagRejectedBy',

                    'hrdApprovedBy',
                    'hrdRejectedBy',

                    'approvedBy',
                    'rejectedBy',

                    'specialLeaveType',
                ]);
            }
        );


        return response()->json([
            'success' => true,

            'message' =>
                'Pengajuan berhasil disetujui HRD melalui FaceLog.',

            'data' =>
                $this->transform($item),
        ]);
    }


    public function reject(
        Request $request,
        string $uuid
    ): JsonResponse {

        $validated =
            $request->validate([
                'rejected_by_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'rejection_reason' => [
                    'required',
                    'string',
                    'max:1000',
                ],
            ]);


        $item = DB::transaction(
            function () use (
                $uuid,
                $validated
            ) {

                $item = LeaveRequest::query()
                    ->with([
                        'employee',
                        'specialLeaveType',
                    ])
                    ->where(
                        'uuid',
                        $uuid
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
                    return $item->fresh([
                        'employee',

                        'kabag',
                        'kabagApprovedBy',
                        'kabagRejectedBy',

                        'hrdApprovedBy',
                        'hrdRejectedBy',

                        'approvedBy',
                        'rejectedBy',

                        'specialLeaveType',
                    ]);
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
                            'Pengajuan sudah diproses HRD dengan status '
                            . (
                                $item->hrd_status
                                ?? '-'
                            )
                            . '.',
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
                | REJECT TIDAK MENGUBAH SALDO
                |--------------------------------------------------------------------------
                */

                $now = now();


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
                        null,

                    'hrd_rejected_at' =>
                        $now,

                    'hrd_rejection_reason' =>
                        $validated[
                            'rejection_reason'
                        ],

                    'hrd_action_source' =>
                        'facelog',

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
                        null,

                    'rejected_at' =>
                        $now,

                    'rejection_reason' =>
                        $validated[
                            'rejection_reason'
                        ],

                    /*
                    |--------------------------------------------------------------------------
                    | FACELOG USER NAME
                    |--------------------------------------------------------------------------
                    */

                    'external_approved_by_name' =>
                        null,

                    'external_rejected_by_name' =>
                        $validated[
                            'rejected_by_name'
                        ],

                    /*
                    |--------------------------------------------------------------------------
                    | SYNC
                    |--------------------------------------------------------------------------
                    */

                    'local_sync_status' =>
                        'synced',

                    'local_synced_at' =>
                        $now,
                ]);


                return $item->fresh([
                    'employee',

                    'kabag',
                    'kabagApprovedBy',
                    'kabagRejectedBy',

                    'hrdApprovedBy',
                    'hrdRejectedBy',

                    'approvedBy',
                    'rejectedBy',

                    'specialLeaveType',
                ]);
            }
        );


        return response()->json([
            'success' => true,

            'message' =>
                'Pengajuan berhasil ditolak HRD melalui FaceLog.',

            'data' =>
                $this->transform($item),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | POTONG SALDO CUTI TAHUNAN
    |--------------------------------------------------------------------------
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
            $item->jenis !== 'cuti'
            ||
            $item->leave_category !== 'annual'
        ) {
            return;
        }


        $employee =
            $item->employee;


        if (! $employee) {
            throw ValidationException::withMessages([
                'employee' =>
                    'Data karyawan pengajuan tidak ditemukan.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | ANNUAL HANYA UNTUK ASIA
        |--------------------------------------------------------------------------
        */

        if (! $employee->isAsiaEmployee()) {
            throw ValidationException::withMessages([
                'leave_category' =>
                    'Cuti tahunan hanya tersedia untuk karyawan ASIA.',
            ]);
        }


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
        | LOCK SALDO
        |--------------------------------------------------------------------------
        |
        | Penting kalau ada dua pengajuan yang diapprove hampir bersamaan.
        |
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
        | FALLBACK JIKA BELUM ADA SALDO
        |--------------------------------------------------------------------------
        */

        if (! $balance) {

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
                    ->firstOrFail();
        }


        /*
        |--------------------------------------------------------------------------
        | CEK SALDO TERBARU
        |--------------------------------------------------------------------------
        |
        | Walaupun saat submit saldonya cukup, bisa saja ada pengajuan lain
        | yang lebih dulu disetujui HRD.
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
                    . ' hari, sedangkan sisa saldo '
                    . $balance->remaining
                    . ' hari.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | POTONG
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


    /*
    |--------------------------------------------------------------------------
    | TRANSFORM UNTUK FACELOG
    |--------------------------------------------------------------------------
    */

    protected function transform(
        LeaveRequest $item
    ): array {

        $employee =
            $item->employee;


        $hrdApprovedName =
            $item->hrdApprovedBy?->name
            ?? $item->approvedBy?->name
            ?? $item->external_approved_by_name;


        $hrdRejectedName =
            $item->hrdRejectedBy?->name
            ?? $item->rejectedBy?->name
            ?? $item->external_rejected_by_name;


        /*
        |--------------------------------------------------------------------------
        | LAMPIRAN
        |--------------------------------------------------------------------------
        */

        $lampiranUrl = null;


        if ($item->lampiran_path) {

            $lampiranPath =
                ltrim(
                    (string) $item->lampiran_path,
                    '/'
                );


            if (
                str_starts_with(
                    $lampiranPath,
                    'storage/'
                )
            ) {
                $lampiranPath =
                    substr(
                        $lampiranPath,
                        strlen('storage/')
                    );
            }


            if (
                str_starts_with(
                    $lampiranPath,
                    'uploads/'
                )
            ) {
                $lampiranPath =
                    substr(
                        $lampiranPath,
                        strlen('uploads/')
                    );
            }


            $lampiranUrl =
                url(
                    '/uploads/'
                    . $lampiranPath
                );
        }


        return [

            /*
            |--------------------------------------------------------------------------
            | EMPLOYEE
            |--------------------------------------------------------------------------
            */

            'uuid' =>
                $item->uuid,

            'source_karyawan_id' =>
                $employee?->source_karyawan_id,

            'employee_code' =>
                $employee?->employee_code,

            'nama' =>
                $employee?->nama,

            'pin_fingerspot' =>
                $employee?->pin_fingerspot,

            'source_device_id' =>
                $employee?->source_device_id,

            'jabatan' =>
                $employee?->jabatan,


            /*
            |--------------------------------------------------------------------------
            | PENGAJUAN
            |--------------------------------------------------------------------------
            */

            'jenis' =>
                $item->jenis,

            'leave_category' =>
                $item->leave_category,

            'leave_days' =>
                $item->leave_days,

            'special_leave_type_id' =>
                $item->special_leave_type_id,

            'special_leave_type_name' =>
                $item->specialLeaveType?->name,

            'special_leave_type_code' =>
                $item->specialLeaveType?->code,

            'durasi_type' =>
                $item->durasi_type,

            'tanggal_mulai' =>
                $item->tanggal_mulai
                    ?->toDateString(),

            'tanggal_selesai' =>
                $item->tanggal_selesai
                    ?->toDateString(),

            'jam_mulai' =>
                $item->jam_mulai
                    ? substr(
                        $item->jam_mulai,
                        0,
                        5
                    )
                    : null,

            'jam_selesai' =>
                $item->jam_selesai
                    ? substr(
                        $item->jam_selesai,
                        0,
                        5
                    )
                    : null,

            'alasan' =>
                $item->alasan,

            'lampiran_url' =>
                $lampiranUrl,


            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            'status' =>
                $item->status,

            'kabag_status' =>
                $item->kabag_status,

            'kabag_name' =>
                $item->kabagApprovedBy?->name
                ?? $item->kabag?->name,

            'kabag_approved_at' =>
                $item->kabag_approved_at
                    ?->toIso8601String(),

            'kabag_rejection_reason' =>
                $item->kabag_rejection_reason,

            'hrd_status' =>
                $item->hrd_status,

            'hrd_action_source' =>
                $item->hrd_action_source,

            'hrd_approved_by' =>
                $hrdApprovedName,

            'hrd_approved_at' =>
                $item->hrd_approved_at
                    ?->toIso8601String(),

            'hrd_rejected_by' =>
                $hrdRejectedName,

            'hrd_rejected_at' =>
                $item->hrd_rejected_at
                    ?->toIso8601String(),

            'hrd_rejection_reason' =>
                $item->hrd_rejection_reason,


            /*
            |--------------------------------------------------------------------------
            | LEGACY
            |--------------------------------------------------------------------------
            */

            'approved_by' =>
                $hrdApprovedName,

            'approved_at' =>
                (
                    $item->hrd_approved_at
                    ?? $item->approved_at
                )
                    ?->toIso8601String(),

            'rejected_by' =>
                $hrdRejectedName,

            'rejected_at' =>
                (
                    $item->hrd_rejected_at
                    ?? $item->rejected_at
                )
                    ?->toIso8601String(),

            'rejection_reason' =>
                $item->hrd_rejection_reason
                ?? $item->rejection_reason,


            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */

            'created_at' =>
                $item->created_at
                    ?->toIso8601String(),

            'updated_at' =>
                $item->updated_at
                    ?->toIso8601String(),
        ];
    }
}
