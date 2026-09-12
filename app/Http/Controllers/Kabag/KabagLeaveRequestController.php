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
        | Karyawan yang berada di bawah Kabag ini
        |--------------------------------------------------------------------------
        */
        $employeeIds = $kabag
            ->managedEmployees()
            ->pluck('employees.id');

        /*
        |--------------------------------------------------------------------------
        | Pengajuan karyawan bawahan
        |--------------------------------------------------------------------------
        */
        $items = LeaveRequest::query()
            ->with([
                'employee',
                'kabagApprovedBy',
                'kabagRejectedBy',
            ])

            ->whereIn(
                'employee_id',
                $employeeIds
            )

            /*
             * Untuk request lama yang kabag_user_id masih NULL,
             * tetap bisa terlihat berdasarkan mapping employee.
             */
            ->where(function ($query) use ($kabag) {
                $query
                    ->whereNull('kabag_user_id')
                    ->orWhere(
                        'kabag_user_id',
                        $kabag->id
                    );
            })

            ->latest('created_at')
            ->paginate(20);

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

            $leaveRequest->update([
                'kabag_user_id' =>
                    $kabag->id,

                'kabag_status' =>
                    'approved',

                'kabag_approved_by' =>
                    $kabag->id,

                'kabag_approved_at' =>
                    now(),

                'kabag_rejected_by' =>
                    null,

                'kabag_rejected_at' =>
                    null,

                'kabag_rejection_reason' =>
                    null,
            ]);
        });

        return back()->with(
            'success',
            'Pengajuan berhasil disetujui dan akan diteruskan ke HRD.'
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

        $validated = $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

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

            $leaveRequest->update([
                'kabag_user_id' =>
                    $kabag->id,

                'kabag_status' =>
                    'rejected',

                'kabag_rejected_by' =>
                    $kabag->id,

                'kabag_rejected_at' =>
                    now(),

                'kabag_rejection_reason' =>
                    $validated['rejection_reason'],

                'kabag_approved_by' =>
                    null,

                'kabag_approved_at' =>
                    null,
            ]);
        });

        return back()->with(
            'success',
            'Pengajuan berhasil ditolak.'
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
