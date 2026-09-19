<?php

namespace App\Http\Controllers\Kabag;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class KabagLeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $kabag =
            $request->user();


        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE YANG BOLEH DIPROSES
        |--------------------------------------------------------------------------
        |
        | 1. Karyawan biasa yang ter-mapping melalui kabag_employee.
        | 2. Employee milik Kabag lain yang menjadikan user ini sebagai Atasan
        |    melalui kabag_supervisor.
        |
        */

        $employeeIds =
            $this->approvableEmployeeIds(
                $kabag
            );


        $items =
            LeaveRequest::query()
                ->with([
                    'employee',
                    'employee.user',
                    'kabag',
                    'kabagApprovedBy',
                    'kabagRejectedBy',
                    'hrdApprovedBy',
                    'hrdRejectedBy',
                    'approvedBy',
                    'rejectedBy',
                    'permissionType',
                    'specialLeaveType',
                ])
                ->whereIn(
                    'employee_id',
                    $employeeIds
                )
                ->orderByRaw("
                    CASE
                        WHEN kabag_status = 'pending' THEN 0
                        WHEN kabag_status = 'approved' AND hrd_status = 'pending' THEN 1
                        ELSE 2
                    END ASC
                ")
                ->latest(
                    'created_at'
                )
                ->paginate(20)
                ->withQueryString();


        return view(
            'kabag.leave-requests.index',
            compact(
                'kabag',
                'items'
            )
        );
    }


    public function approve(
        Request $request,
        LeaveRequest $leaveRequest
    ) {
        $kabag =
            $request->user();


        $this->ensureUserCanApprove(
            $kabag,
            $leaveRequest
        );


        $result =
            DB::transaction(
                function () use (
                    $kabag,
                    $leaveRequest
                ) {

                    $item =
                        LeaveRequest::query()
                            ->whereKey(
                                $leaveRequest->id
                            )
                            ->lockForUpdate()
                            ->firstOrFail();


                    $this->ensureUserCanApprove(
                        $kabag,
                        $item
                    );


                    if (
                        $item->kabag_status
                        !== 'pending'
                    ) {
                        return [
                            'success' =>
                                false,

                            'message' =>
                                'Pengajuan ini sudah diproses oleh '
                                . (
                                    $item->kabagApprovedBy?->name
                                    ?? $item->kabagRejectedBy?->name
                                    ?? $item->kabag?->name
                                    ?? 'approver lain'
                                )
                                . '.',
                        ];
                    }


                    $now =
                        now();


                    $item->update([

                        /*
                        |--------------------------------------------------------------------------
                        | APPROVER TAHAP PERTAMA
                        |--------------------------------------------------------------------------
                        |
                        | Field kabag_* tetap dipakai agar kompatibel dengan struktur
                        | existing. Untuk pengajuan milik Kabag, user yang tercatat
                        | di sini adalah Atasan Kabag.
                        |
                        */

                        'kabag_user_id' =>
                            $kabag->id,

                        'kabag_status' =>
                            'approved',

                        'kabag_approved_by' =>
                            $kabag->id,

                        'kabag_approved_at' =>
                            $now,

                        'kabag_rejected_by' =>
                            null,

                        'kabag_rejected_at' =>
                            null,

                        'kabag_rejection_reason' =>
                            null,

                        /*
                        |--------------------------------------------------------------------------
                        | TERUSKAN KE HRD
                        |--------------------------------------------------------------------------
                        */

                        'hrd_status' =>
                            'pending',

                        'status' =>
                            'pending',

                        'local_sync_status' =>
                            'pending',
                    ]);


                    return [
                        'success' =>
                            true,

                        'message' =>
                            'Pengajuan berhasil disetujui dan diteruskan ke HRD.',
                    ];
                }
            );


        return back()->with(
            $result['success']
                ? 'success'
                : 'error',
            $result['message']
        );
    }


    public function reject(
        Request $request,
        LeaveRequest $leaveRequest
    ) {
        $kabag =
            $request->user();


        $this->ensureUserCanApprove(
            $kabag,
            $leaveRequest
        );


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


        $result =
            DB::transaction(
                function () use (
                    $kabag,
                    $leaveRequest,
                    $validated
                ) {

                    $item =
                        LeaveRequest::query()
                            ->whereKey(
                                $leaveRequest->id
                            )
                            ->lockForUpdate()
                            ->firstOrFail();


                    $this->ensureUserCanApprove(
                        $kabag,
                        $item
                    );


                    if (
                        $item->kabag_status
                        !== 'pending'
                    ) {
                        return [
                            'success' =>
                                false,

                            'message' =>
                                'Pengajuan ini sudah diproses oleh '
                                . (
                                    $item->kabagApprovedBy?->name
                                    ?? $item->kabagRejectedBy?->name
                                    ?? $item->kabag?->name
                                    ?? 'approver lain'
                                )
                                . '.',
                        ];
                    }


                    $now =
                        now();


                    $item->update([

                        'kabag_user_id' =>
                            $kabag->id,

                        'kabag_status' =>
                            'rejected',

                        'kabag_rejected_by' =>
                            $kabag->id,

                        'kabag_rejected_at' =>
                            $now,

                        'kabag_rejection_reason' =>
                            $validated[
                                'rejection_reason'
                            ],

                        'kabag_approved_by' =>
                            null,

                        'kabag_approved_at' =>
                            null,

                        /*
                        |--------------------------------------------------------------------------
                        | FINAL DITOLAK DI APPROVAL TAHAP PERTAMA
                        |--------------------------------------------------------------------------
                        */

                        'hrd_status' =>
                            'waiting',

                        'status' =>
                            'rejected',

                        'local_sync_status' =>
                            'pending',
                    ]);


                    return [
                        'success' =>
                            true,

                        'message' =>
                            'Pengajuan berhasil ditolak.',
                    ];
                }
            );


        return back()->with(
            $result['success']
                ? 'success'
                : 'error',
            $result['message']
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DAFTAR EMPLOYEE YANG BOLEH DIPROSES USER KABAG
    |--------------------------------------------------------------------------
    */

    protected function approvableEmployeeIds(
        $kabag
    ): Collection {

        $managedEmployeeIds =
            $kabag
                ->managedEmployees()
                ->pluck(
                    'employees.id'
                );


        $supervisedKabagEmployeeIds =
            $kabag
                ->supervisedKabags()
                ->where(
                    'users.is_active',
                    true
                )
                ->whereNotNull(
                    'users.self_employee_id'
                )
                ->pluck(
                    'users.self_employee_id'
                );


        return $managedEmployeeIds
            ->merge(
                $supervisedKabagEmployeeIds
            )
            ->filter()
            ->map(
                fn ($id) =>
                    (int) $id
            )
            ->unique()
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | AUTHORIZATION APPROVAL
    |--------------------------------------------------------------------------
    */

    protected function ensureUserCanApprove(
        $kabag,
        LeaveRequest $leaveRequest
    ): void {

        /*
        | Tidak boleh approve/reject pengajuan sendiri.
        */
        abort_if(
            $kabag->self_employee_id
            &&
            (int) $kabag->self_employee_id
                ===
            (int) $leaveRequest->employee_id,
            403
        );


        $allowedAsEmployeeKabag =
            $kabag
                ->managedEmployees()
                ->where(
                    'employees.id',
                    $leaveRequest->employee_id
                )
                ->exists();


        $allowedAsSupervisor =
            $kabag
                ->supervisedKabags()
                ->where(
                    'users.is_active',
                    true
                )
                ->where(
                    'users.self_employee_id',
                    $leaveRequest->employee_id
                )
                ->exists();


        abort_unless(
            $allowedAsEmployeeKabag
            ||
            $allowedAsSupervisor,
            403
        );
    }
}
