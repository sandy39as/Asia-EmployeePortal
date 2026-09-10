<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Carbon\Carbon;
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
                'approvedBy',
                'rejectedBy',
            ])

            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('employee', function ($employeeQuery) use ($search) {
                    $employeeQuery
                        ->where('nama', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%");
                });
            })

            ->when(
                in_array($jenis, ['izin', 'cuti', 'sakit'], true),
                fn ($q) => $q->where('jenis', $jenis)
            )

            ->when(
                in_array(
                    $status,
                    ['pending', 'approved', 'rejected', 'cancelled'],
                    true
                ),
                fn ($q) => $q->where('status', $status)
            )

            ->when(
                $startDate,
                fn ($q) => $q->whereDate(
                    'tanggal_selesai',
                    '>=',
                    $startDate
                )
            )

            ->when(
                $endDate,
                fn ($q) => $q->whereDate(
                    'tanggal_mulai',
                    '<=',
                    $endDate
                )
            )

            ->orderByRaw("
                CASE
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'approved' THEN 2
                    WHEN status = 'rejected' THEN 3
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
        $leaveRequest->load([
            'employee',
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
                ->whereKey($leaveRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($item->status !== 'pending') {
                abort(
                    422,
                    'Pengajuan ini sudah diproses sebelumnya.'
                );
            }

            $item->update([
                'status' => 'approved',

                'approved_by' =>
                    $request->user()->id,

                'approved_at' =>
                    now(),

                'rejected_by' =>
                    null,

                'rejected_at' =>
                    null,

                'rejection_reason' =>
                    null,

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
                'Pengajuan berhasil disetujui.'
            );
    }

    public function reject(
        Request $request,
        LeaveRequest $leaveRequest
    ) {
        $validated = $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ], [
            'rejection_reason.required' =>
                'Alasan penolakan wajib diisi.',
        ]);

        DB::transaction(function () use (
            $request,
            $leaveRequest,
            $validated
        ) {
            $item = LeaveRequest::query()
                ->whereKey($leaveRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($item->status !== 'pending') {
                abort(
                    422,
                    'Pengajuan ini sudah diproses sebelumnya.'
                );
            }

            $item->update([
                'status' =>
                    'rejected',

                'approved_by' =>
                    null,

                'approved_at' =>
                    null,

                'rejected_by' =>
                    $request->user()->id,

                'rejected_at' =>
                    now(),

                'rejection_reason' =>
                    $validated['rejection_reason'],

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
                'Pengajuan berhasil ditolak.'
            );
    }
}
