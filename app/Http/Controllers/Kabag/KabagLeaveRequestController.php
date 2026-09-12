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

        $employeeIds = $kabag
            ->managedEmployees()
            ->pluck('employees.id');

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
            ])

            ->whereIn(
                'employee_id',
                $employeeIds
            )

            ->where(function ($query) use ($kabag) {
                $query
                    ->whereNull('kabag_user_id')
                    ->orWhere(
                        'kabag_user_id',
                        $kabag->id
                    );
            })

            ->latest('created_at')
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
        $kabag = $request->user();

        $this->ensureEmployeeBelongsToKabag(
            $kabag,
            $leaveRequest
        );

        if (
            $leaveRequest->kabag_status !== 'pending'
        ) {
            return back()->with(
                'error',
                'Pengajuan ini sudah diproses oleh Kabag.'
            );
        }

        DB::transaction(function () use (
            $kabag,
            $leaveRequest
        ) {
            $now = now();

            $leaveRequest->update([
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

                'hrd_status' =>
                    'pending',

                'status' =>
                    'pending',

                'local_sync_status' =>
                    'pending',
            ]);
        });

        return back()->with(
            'success',
            'Pengajuan berhasil disetujui Kabag dan diteruskan ke HRD.'
        );
    }


    public function reject(
        Request $request,
        LeaveRequest $leaveRequest
    ) {
        $kabag = $request->user();

        $this->ensureEmployeeBelongsToKabag(
            $kabag,
            $leaveRequest
        );

        $validated = $request->validate(
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

        if (
            $leaveRequest->kabag_status !== 'pending'
        ) {
            return back()->with(
                'error',
                'Pengajuan ini sudah diproses oleh Kabag.'
            );
        }

        DB::transaction(function () use (
            $kabag,
            $leaveRequest,
            $validated
        ) {
            $now = now();

            $leaveRequest->update([
                'kabag_user_id' =>
                    $kabag->id,

                'kabag_status' =>
                    'rejected',

                'kabag_rejected_by' =>
                    $kabag->id,

                'kabag_rejected_at' =>
                    $now,

                'kabag_rejection_reason' =>
                    $validated['rejection_reason'],

                'kabag_approved_by' =>
                    null,

                'kabag_approved_at' =>
                    null,

                'hrd_status' =>
                    'waiting',

                'status' =>
                    'rejected',

                'local_sync_status' =>
                    'pending',
            ]);
        });

        return back()->with(
            'success',
            'Pengajuan berhasil ditolak oleh Kabag.'
        );
    }


    protected function ensureEmployeeBelongsToKabag(
        $kabag,
        LeaveRequest $leaveRequest
    ): void {
        $allowed = $kabag
            ->managedEmployees()
            ->where(
                'employees.id',
                $leaveRequest->employee_id
            )
            ->exists();

        abort_unless(
            $allowed,
            403
        );
    }
}
