<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HrdLeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $search = trim(
            (string) $request->get('search', '')
        );

        $jenis = trim(
            (string) $request->get('jenis', '')
        );

        $status = trim(
            (string) $request->get('status', '')
        );

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $items = LeaveRequest::query()
            ->with([
                'employee',

                // Approval Kabag
                'kabag',
                'kabagApprovedBy',
                'kabagRejectedBy',

                // Approval HRD baru
                'hrdApprovedBy',
                'hrdRejectedBy',

                // Field/relationship lama tetap dipakai
                // untuk compatibility.
                'approvedBy',
                'rejectedBy',
            ])

            /*
            |--------------------------------------------------------------------------
            | HRD hanya melihat yang sudah ACC Kabag
            |--------------------------------------------------------------------------
            |
            | Pending Kabag tidak boleh muncul untuk diproses HRD.
            |
            */
            ->where(
                'kabag_status',
                'approved'
            )

            /*
            |--------------------------------------------------------------------------
            | Status HRD
            |--------------------------------------------------------------------------
            |
            | waiting = belum dikirim ke HRD
            | pending = sudah ACC Kabag, siap diproses HRD
            | approved/rejected = selesai
            |
            */
            ->whereIn(
                'hrd_status',
                [
                    'pending',
                    'approved',
                    'rejected',
                ]
            )

            /*
            |--------------------------------------------------------------------------
            | Search Employee
            |--------------------------------------------------------------------------
            */
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

            /*
            |--------------------------------------------------------------------------
            | Jenis
            |--------------------------------------------------------------------------
            */
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

            /*
            |--------------------------------------------------------------------------
            | Filter Status
            |--------------------------------------------------------------------------
            */
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

            /*
            |--------------------------------------------------------------------------
            | Range Tanggal
            |--------------------------------------------------------------------------
            */
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

            /*
            |--------------------------------------------------------------------------
            | Pending HRD paling atas
            |--------------------------------------------------------------------------
            */
            ->orderByRaw("
                CASE
                    WHEN hrd_status = 'pending'
                        THEN 1

                    WHEN hrd_status = 'approved'
                        THEN 2

                    WHEN hrd_status = 'rejected'
                        THEN 3

                    ELSE 4
                END
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
        /*
        |--------------------------------------------------------------------------
        | HRD hanya boleh review setelah ACC Kabag
        |--------------------------------------------------------------------------
        */
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
        ]);

        return view(
            'hrd.leave-requests.show',
            compact('leaveRequest')
        );
    }


    public function approve(
        Request $request,
        LeaveRequest $leaveRequest
    ) {
        DB::transaction(function () use (
            $request,
            $leaveRequest
        ) {

            $item = LeaveRequest::query()
                ->whereKey(
                    $leaveRequest->id
                )
                ->lockForUpdate()
                ->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | Harus sudah ACC Kabag
            |--------------------------------------------------------------------------
            */
            if (
                $item->kabag_status
                !== 'approved'
            ) {
                abort(
                    422,
                    'Pengajuan belum disetujui oleh Kabag.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Harus masih pending HRD
            |--------------------------------------------------------------------------
            */
            if (
                $item->hrd_status
                !== 'pending'
            ) {
                abort(
                    422,
                    'Pengajuan ini sudah diproses oleh HRD sebelumnya.'
                );
            }

            $userId =
                $request->user()->id;

            $now =
                now();

            $item->update([

                /*
                |--------------------------------------------------------------------------
                | STATUS FINAL
                |--------------------------------------------------------------------------
                */
                'status' =>
                    'approved',

                /*
                |--------------------------------------------------------------------------
                | HRD WORKFLOW BARU
                |--------------------------------------------------------------------------
                */
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
                | FIELD LAMA
                |--------------------------------------------------------------------------
                |
                | Tetap diisi agar API/FaceLog lama tetap kompatibel.
                |
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
                | Trigger Sync FaceLog
                |--------------------------------------------------------------------------
                */
                'local_sync_status' =>
                    'pending',
            ]);
        });


        return redirect()
            ->route(
                'hrd.leave-requests.show',
                $leaveRequest
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


        DB::transaction(function () use (
            $request,
            $leaveRequest,
            $validated
        ) {

            $item = LeaveRequest::query()
                ->whereKey(
                    $leaveRequest->id
                )
                ->lockForUpdate()
                ->firstOrFail();


            /*
            |--------------------------------------------------------------------------
            | Harus sudah ACC Kabag
            |--------------------------------------------------------------------------
            */
            if (
                $item->kabag_status
                !== 'approved'
            ) {
                abort(
                    422,
                    'Pengajuan belum disetujui oleh Kabag.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Harus masih pending HRD
            |--------------------------------------------------------------------------
            */
            if (
                $item->hrd_status
                !== 'pending'
            ) {
                abort(
                    422,
                    'Pengajuan ini sudah diproses oleh HRD sebelumnya.'
                );
            }


            $userId =
                $request->user()->id;

            $now =
                now();


            $item->update([

                /*
                |--------------------------------------------------------------------------
                | STATUS FINAL
                |--------------------------------------------------------------------------
                */
                'status' =>
                    'rejected',

                /*
                |--------------------------------------------------------------------------
                | HRD WORKFLOW BARU
                |--------------------------------------------------------------------------
                */
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
                | FIELD LAMA
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
                | Trigger Sync FaceLog
                |--------------------------------------------------------------------------
                */
                'local_sync_status' =>
                    'pending',
            ]);
        });


        return redirect()
            ->route(
                'hrd.leave-requests.show',
                $leaveRequest
            )
            ->with(
                'success',
                'Pengajuan berhasil ditolak oleh HRD.'
            );
    }
}
