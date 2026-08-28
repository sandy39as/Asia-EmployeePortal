<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FaceLogLeaveRequestController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST / SYNC
    |--------------------------------------------------------------------------
    |
    | FaceLog lokal akan memanggil endpoint ini secara berkala.
    |
    | GET:
    | /api/facelog/leave-requests
    |
    | Optional:
    | ?updated_after=2026-08-28T10:00:00
    |
    */
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
                'approvedBy',
                'rejectedBy',
            ]);

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
                )->map(
                    fn (LeaveRequest $item) =>
                        $this->transform($item)
                )->values(),

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

    /*
    |--------------------------------------------------------------------------
    | DETAIL
    |--------------------------------------------------------------------------
    */
    public function show(
        string $uuid
    ): JsonResponse {
        $item = LeaveRequest::query()
            ->with([
                'employee',
                'approvedBy',
                'rejectedBy',
            ])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->transform($item),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE DARI FACELOG LOKAL
    |--------------------------------------------------------------------------
    */
    public function approve(
        Request $request,
        string $uuid
    ): JsonResponse {
        $validated = $request->validate([
            /*
             * Identitas approver dari sistem FaceLog.
             * Tidak kita FK-kan ke users Portal.
             */
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
                $item = LeaveRequest::query()
                    ->where(
                        'uuid',
                        $uuid
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                 * Idempotent:
                 * Kalau sudah approved,
                 * request ulang tetap aman.
                 */
                if (
                    $item->status
                    === 'approved'
                ) {
                    return $item;
                }

                if (
                    $item->status
                    !== 'pending'
                ) {
                    throw ValidationException::withMessages([
                        'status' =>
                            'Pengajuan sudah diproses dengan status '
                            . $item->status
                            . '.',
                    ]);
                }

                $item->update([
                    'status' =>
                        'approved',

                    /*
                     * approved_by Portal tidak kita isi
                     * karena approver berasal dari FaceLog.
                     */
                    'approved_by' =>
                        null,

                    'approved_at' =>
                        now(),

                    'rejected_by' =>
                        null,

                    'rejected_at' =>
                        null,

                    'rejection_reason' =>
                        null,

                    'local_sync_status' =>
                        'synced',

                    'local_synced_at' =>
                        now(),
                ]);

                /*
                 * Nama approver FaceLog nanti kita simpan
                 * dalam kolom khusus. Kita tambahkan
                 * migration setelah ini.
                 */
                $item->update([
                    'external_approved_by_name' =>
                        $validated['approved_by_name'],

                    'external_rejected_by_name' =>
                        null,
                ]);

                return $item->fresh([
                    'employee',
                    'approvedBy',
                    'rejectedBy',
                ]);
            }
        );

        return response()->json([
            'success' => true,
            'message' =>
                'Pengajuan berhasil disetujui.',

            'data' =>
                $this->transform($item),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT DARI FACELOG LOKAL
    |--------------------------------------------------------------------------
    */
    public function reject(
        Request $request,
        string $uuid
    ): JsonResponse {
        $validated = $request->validate([
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
                    ->where(
                        'uuid',
                        $uuid
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $item->status
                    === 'rejected'
                ) {
                    return $item;
                }

                if (
                    $item->status
                    !== 'pending'
                ) {
                    throw ValidationException::withMessages([
                        'status' =>
                            'Pengajuan sudah diproses dengan status '
                            . $item->status
                            . '.',
                    ]);
                }

                $item->update([
                    'status' =>
                        'rejected',

                    'approved_by' =>
                        null,

                    'approved_at' =>
                        null,

                    'rejected_by' =>
                        null,

                    'rejected_at' =>
                        now(),

                    'rejection_reason' =>
                        $validated[
                            'rejection_reason'
                        ],

                    'local_sync_status' =>
                        'synced',

                    'local_synced_at' =>
                        now(),

                    'external_approved_by_name' =>
                        null,

                    'external_rejected_by_name' =>
                        $validated[
                            'rejected_by_name'
                        ],
                ]);

                return $item->fresh([
                    'employee',
                    'approvedBy',
                    'rejectedBy',
                ]);
            }
        );

        return response()->json([
            'success' => true,
            'message' =>
                'Pengajuan berhasil ditolak.',

            'data' =>
                $this->transform($item),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TRANSFORM
    |--------------------------------------------------------------------------
    |
    | Format yang diterima FaceLog dibuat konsisten.
    |
    */
    protected function transform(
        LeaveRequest $item
    ): array {
        $employee = $item->employee;

        return [
            'uuid' =>
                $item->uuid,

            /*
             * ID utama penghubung ke FaceLog.
             */
            'source_karyawan_id' =>
                $employee?->source_karyawan_id,

            'employee_code' =>
                $employee?->employee_code,

            'nama' =>
                $employee?->nama,

            /*
             * Hanya reference.
             * BUKAN identity utama.
             */
            'pin_fingerspot' =>
                $employee?->pin_fingerspot,

            'source_device_id' =>
                $employee?->source_device_id,

            'jabatan' =>
                $employee?->jabatan,

            'jenis' =>
                $item->jenis,

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
                $item->lampiran_path
                    ? url(
                        '/storage/'
                        . $item->lampiran_path
                    )
                    : null,

            'status' =>
                $item->status,

            'approved_by' =>
                $item->approvedBy?->name
                ?? $item->external_approved_by_name,

            'approved_at' =>
                $item->approved_at
                    ?->toIso8601String(),

            'rejected_by' =>
                $item->rejectedBy?->name
                ?? $item->external_rejected_by_name,

            'rejected_at' =>
                $item->rejected_at
                    ?->toIso8601String(),

            'rejection_reason' =>
                $item->rejection_reason,

            'created_at' =>
                $item->created_at
                    ?->toIso8601String(),

            'updated_at' =>
                $item->updated_at
                    ?->toIso8601String(),
        ];
    }
}
