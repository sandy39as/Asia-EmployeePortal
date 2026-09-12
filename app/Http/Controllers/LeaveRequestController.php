<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $employee = $request->user()->employee;

        abort_unless(
            $employee,
            403
        );

        $status = trim(
            (string) $request->get(
                'status',
                ''
            )
        );

        $jenis = trim(
            (string) $request->get(
                'jenis',
                ''
            )
        );

        $items = LeaveRequest::query()

            ->with([
                'kabag',
                'kabagApprovedBy',
                'kabagRejectedBy',

                'hrdApprovedBy',
                'hrdRejectedBy',

                'approvedBy',
                'rejectedBy',
            ])

            ->where(
                'employee_id',
                $employee->id
            )

            ->when(
                $status !== '',
                fn ($q) =>
                    $q->where(
                        'status',
                        $status
                    )
            )

            ->when(
                $jenis !== '',
                fn ($q) =>
                    $q->where(
                        'jenis',
                        $jenis
                    )
            )

            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'leave-requests.index',
            compact(
                'employee',
                'items',
                'status',
                'jenis'
            )
        );
    }

    public function create(
        Request $request
    ): View {
        $employee = $request->user()->employee;

        abort_unless(
            $employee,
            403
        );

        return view(
            'leave-requests.create',
            compact('employee')
        );
    }

    public function store(
        Request $request
    ): RedirectResponse|JsonResponse {

        $employee = $request->user()->employee;

        abort_unless(
            $employee,
            403
        );

        if (! $employee->is_active) {

            if ($request->expectsJson()) {

                return response()->json(
                    [
                        'success' => false,

                        'message' =>
                            'Akun karyawan sedang tidak aktif.',
                    ],
                    422
                );
            }

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Akun karyawan sedang tidak aktif.'
                );
        }

        $kabag = $employee
            ->kabag()
            ->where(
                'users.is_active',
                true
            )
            ->first();

        if (! $kabag) {

            $message =
                'Pengajuan belum dapat dikirim karena karyawan belum memiliki Kabag. '
                . 'Silakan hubungi administrator/HRD.';


            if ($request->expectsJson()) {

                return response()->json(
                    [
                        'success' => false,
                        'message' => $message,
                    ],
                    422
                );
            }


            return back()
                ->withInput()
                ->with(
                    'error',
                    $message
                );
        }

        $validated =
            $request->validate(
                [
                    'jenis' => [
                        'required',
                        'in:izin,cuti,sakit',
                    ],

                    'durasi_type' => [
                        'required',
                        'in:full_day,hourly',
                    ],

                    'tanggal_mulai' => [
                        'required',
                        'date',
                    ],

                    'tanggal_selesai' => [
                        'required',
                        'date',
                        'after_or_equal:tanggal_mulai',
                    ],

                    'jam_mulai' => [
                        'nullable',
                        'date_format:H:i',
                    ],

                    'jam_selesai' => [
                        'nullable',
                        'date_format:H:i',
                    ],

                    'alasan' => [
                        'required',
                        'string',
                        'max:2000',
                    ],

                    'lampiran' => [
                        'nullable',
                        'file',
                        'mimes:pdf,jpg,jpeg,png',
                        'max:5120',
                    ],
                ],
                [
                    'jenis.required' =>
                        'Jenis pengajuan wajib dipilih.',

                    'jenis.in' =>
                        'Jenis pengajuan tidak valid.',

                    'durasi_type.required' =>
                        'Durasi pengajuan wajib dipilih.',

                    'durasi_type.in' =>
                        'Durasi pengajuan tidak valid.',

                    'tanggal_mulai.required' =>
                        'Tanggal mulai wajib diisi.',

                    'tanggal_selesai.required' =>
                        'Tanggal selesai wajib diisi.',

                    'tanggal_selesai.after_or_equal' =>
                        'Tanggal selesai tidak boleh sebelum tanggal mulai.',

                    'alasan.required' =>
                        'Alasan wajib diisi.',

                    'alasan.max' =>
                        'Alasan maksimal 2000 karakter.',

                    'lampiran.mimes' =>
                        'Lampiran harus berupa PDF, JPG, JPEG, atau PNG.',

                    'lampiran.max' =>
                        'Ukuran lampiran maksimal 5 MB.',
                ]
            );

        if (
            $validated['durasi_type']
            === 'hourly'
        ) {

            if (
                blank(
                    $validated['jam_mulai']
                    ?? null
                )
                ||
                blank(
                    $validated['jam_selesai']
                    ?? null
                )
            ) {
                throw ValidationException::withMessages([
                    'jam_mulai' =>
                        'Jam mulai dan jam selesai wajib diisi untuk izin beberapa jam.',
                ]);
            }


            if (
                $validated['tanggal_mulai']
                !==
                $validated['tanggal_selesai']
            ) {
                throw ValidationException::withMessages([
                    'tanggal_selesai' =>
                        'Pengajuan beberapa jam hanya boleh dalam tanggal yang sama.',
                ]);
            }


            $jamMulai =
                Carbon::createFromFormat(
                    'H:i',
                    $validated['jam_mulai']
                );


            $jamSelesai =
                Carbon::createFromFormat(
                    'H:i',
                    $validated['jam_selesai']
                );


            if (
                $jamSelesai
                    ->lessThanOrEqualTo(
                        $jamMulai
                    )
            ) {
                throw ValidationException::withMessages([
                    'jam_selesai' =>
                        'Jam selesai harus lebih besar dari jam mulai.',
                ]);
            }

        } else {

            $validated['jam_mulai'] = null;
            $validated['jam_selesai'] = null;
        }

        $lampiranPath = null;
        $lampiranOriginalName = null;


        if ($request->hasFile('lampiran')) {

            $file = $request->file(
                'lampiran'
            );


            $lampiranOriginalName =
                $file->getClientOriginalName();


            $lampiranPath =
                $file->store(
                    'leave-attachments',
                    'public'
                );
        }

        try {

            $leaveRequest =
                LeaveRequest::create([

                    'uuid' =>
                        (string) Str::uuid(),

                    'employee_id' =>
                        $employee->id,

                    'kabag_user_id' =>
                        $kabag->id,

                    'jenis' =>
                        $validated['jenis'],

                    'durasi_type' =>
                        $validated['durasi_type'],

                    'tanggal_mulai' =>
                        $validated['tanggal_mulai'],

                    'tanggal_selesai' =>
                        $validated['tanggal_selesai'],

                    'jam_mulai' =>
                        $validated['jam_mulai']
                        ?? null,

                    'jam_selesai' =>
                        $validated['jam_selesai']
                        ?? null,

                    'alasan' =>
                        $validated['alasan'],

                    'lampiran_path' =>
                        $lampiranPath,

                    'lampiran_original_name' =>
                        $lampiranOriginalName,

                    'kabag_status' =>
                        'pending',

                    'hrd_status' =>
                        'waiting',

                    'status' =>
                        'pending',

                    'local_sync_status' =>
                        'pending',
                ]);

        } catch (\Throwable $e) {

            if ($lampiranPath) {

                Storage::disk('public')
                    ->delete(
                        $lampiranPath
                    );
            }

            throw $e;
        }

        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,

                'message' =>
                    'Pengajuan berhasil dikirim dan menunggu persetujuan '
                    . $kabag->name
                    . '.',

                'data' => [
                    'id' =>
                        $leaveRequest->id,

                    'uuid' =>
                        $leaveRequest->uuid,

                    'status' =>
                        $leaveRequest->status,

                    'kabag_status' =>
                        $leaveRequest->kabag_status,

                    'hrd_status' =>
                        $leaveRequest->hrd_status,

                    'kabag' => [
                        'id' =>
                            $kabag->id,

                        'name' =>
                            $kabag->name,
                    ],
                ],
            ]);
        }

        return redirect()
            ->route(
                'leave-requests.index'
            )
            ->with(
                'success',
                'Pengajuan berhasil dikirim dan menunggu persetujuan '
                . $kabag->name
                . '.'
            );
    }

    public function show(
        Request $request,
        LeaveRequest $leaveRequest
    ): View {

        $employee =
            $request->user()->employee;


        abort_unless(
            $employee
            &&
            $leaveRequest->employee_id
                === $employee->id,
            403
        );

        $leaveRequest->load([
            'kabag',
            'kabagApprovedBy',
            'kabagRejectedBy',

            'hrdApprovedBy',
            'hrdRejectedBy',

            'approvedBy',
            'rejectedBy',
        ]);


        return view(
            'leave-requests.show',
            compact(
                'employee',
                'leaveRequest'
            )
        );
    }

    public function cancel(
        Request $request,
        LeaveRequest $leaveRequest
    ): RedirectResponse|JsonResponse {

        $employee =
            $request->user()->employee;


        abort_unless(
            $employee
            &&
            $leaveRequest->employee_id
                === $employee->id,
            403
        );

        if (
            $leaveRequest->status
                !== 'pending'
            ||
            $leaveRequest->kabag_status
                !== 'pending'
        ) {

            $message =
                'Pengajuan hanya dapat dibatalkan sebelum diproses oleh Kabag.';


            if ($request->expectsJson()) {

                return response()->json(
                    [
                        'success' =>
                            false,

                        'message' =>
                            $message,
                    ],
                    422
                );
            }


            return back()
                ->with(
                    'error',
                    $message
                );
        }


        $leaveRequest->update([

            'status' =>
                'cancelled',

            'hrd_status' =>
                'waiting',

            'local_sync_status' =>
                'pending',
        ]);


        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,

                'message' =>
                    'Pengajuan berhasil dibatalkan.',
            ]);
        }


        return back()->with(
            'success',
            'Pengajuan berhasil dibatalkan.'
        );
    }
}
