<?php

namespace App\Http\Controllers\Kabag;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KabagLeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $kabag = $request->user();

        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE YANG DITANGANI KABAG
        |--------------------------------------------------------------------------
        |
        | Pivot kabag_employee sekarang many-to-many.
        | Satu employee dapat muncul pada lebih dari satu Kabag.
        |
        */

        $employeeIds =
            $kabag
                ->managedEmployees()
                ->pluck(
                    'employees.id'
                );


            $items =
                        LeaveRequest::query()
                            ->with([
                                'employee',
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


        $this->ensureEmployeeBelongsToKabag(
            $kabag,
            $leaveRequest
        );


        $result =
            DB::transaction(
                function () use (
                    $kabag,
                    $leaveRequest
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | LOCK PENGAJUAN
                    |--------------------------------------------------------------------------
                    |
                    | Jika dua Kabag menekan approve hampir bersamaan, hanya request
                    | pertama yang berhasil. Request kedua menunggu lock lalu melihat
                    | status sudah bukan pending.
                    |
                    */

                    $item =
                        LeaveRequest::query()
                            ->whereKey(
                                $leaveRequest->id
                            )
                            ->lockForUpdate()
                            ->firstOrFail();


                    $this->ensureEmployeeBelongsToKabag(
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
                                'Pengajuan ini sudah diproses oleh Kabag '
                                . (
                                    $item->kabagApprovedBy?->name
                                    ?? $item->kabagRejectedBy?->name
                                    ?? $item->kabag?->name
                                    ?? 'lain'
                                )
                                . '.',
                        ];
                    }


                    $now =
                        now();


                    $item->update([

                        /*
                        |--------------------------------------------------------------------------
                        | KABAG YANG MEMPROSES
                        |--------------------------------------------------------------------------
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
                            'Pengajuan berhasil disetujui Kabag dan diteruskan ke HRD.',
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


        $this->ensureEmployeeBelongsToKabag(
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

                    /*
                    |--------------------------------------------------------------------------
                    | LOCK PENGAJUAN
                    |--------------------------------------------------------------------------
                    */

                    $item =
                        LeaveRequest::query()
                            ->whereKey(
                                $leaveRequest->id
                            )
                            ->lockForUpdate()
                            ->firstOrFail();


                    $this->ensureEmployeeBelongsToKabag(
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
                                'Pengajuan ini sudah diproses oleh Kabag '
                                . (
                                    $item->kabagApprovedBy?->name
                                    ?? $item->kabagRejectedBy?->name
                                    ?? $item->kabag?->name
                                    ?? 'lain'
                                )
                                . '.',
                        ];
                    }


                    $now =
                        now();


                    $item->update([

                        /*
                        |--------------------------------------------------------------------------
                        | KABAG YANG MEMPROSES
                        |--------------------------------------------------------------------------
                        */

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
                        | FINAL DITOLAK DI KABAG
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
                            'Pengajuan berhasil ditolak oleh Kabag.',
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


    protected function ensureEmployeeBelongsToKabag(
        $kabag,
        LeaveRequest $leaveRequest
    ): void {

        $allowed =
            $kabag
                ->managedEmployees()
                ->where(
                    'employees.id',
                    $leaveRequest
                        ->employee_id
                )
                ->exists();


        abort_unless(
            $allowed,
            403
        );
    }
}
