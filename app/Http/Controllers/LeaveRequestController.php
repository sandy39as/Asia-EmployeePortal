<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\SpecialLeaveType;
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

                'specialLeaveType',
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


        /*
        |--------------------------------------------------------------------------
        | MASTER CUTI KHUSUS
        |--------------------------------------------------------------------------
        */

        $specialLeaveTypes =
            SpecialLeaveType::query()
                ->where(
                    'is_active',
                    true
                )
                ->orderBy('name')
                ->get();


        /*
        |--------------------------------------------------------------------------
        | SALDO CUTI TAHUNAN
        |--------------------------------------------------------------------------
        |
        | Hanya employee ASIA yang punya saldo cuti tahunan.
        |
        */

        $leaveBalance = null;

        if ($employee->isAsiaEmployee()) {
            $leaveBalance =
                $employee->leaveBalanceForYear(
                    now()->year
                );
        }


        return view(
            'leave-requests.index',
            compact(
                'employee',
                'items',
                'status',
                'jenis',
                'specialLeaveTypes',
                'leaveBalance'
            )
        );
    }


    public function create(
        Request $request
    ): View {
        $employee =
            $request->user()->employee;

        abort_unless(
            $employee,
            403
        );


        $specialLeaveTypes =
            SpecialLeaveType::query()
                ->where(
                    'is_active',
                    true
                )
                ->orderBy('name')
                ->get();


        $leaveBalance = null;

        if ($employee->isAsiaEmployee()) {
            $leaveBalance =
                $employee->leaveBalanceForYear(
                    now()->year
                );
        }


        return view(
            'leave-requests.create',
            compact(
                'employee',
                'specialLeaveTypes',
                'leaveBalance'
            )
        );
    }


    public function store(
        Request $request
    ): RedirectResponse|JsonResponse {

        $employee =
            $request->user()->employee;


        abort_unless(
            $employee,
            403
        );


        /*
        |--------------------------------------------------------------------------
        | CEK EMPLOYEE AKTIF
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | CEK KABAG
        |--------------------------------------------------------------------------
        */

        $kabag =
            $employee
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


        /*
        |--------------------------------------------------------------------------
        | VALIDASI DASAR
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate(
                [
                    'jenis' => [
                        'required',
                        'in:izin,cuti,sakit',
                    ],

                    'leave_category' => [
                        'nullable',
                        'in:annual,special',
                    ],

                    'special_leave_type_id' => [
                        'nullable',
                        'integer',
                        'exists:special_leave_types,id',
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

                    'leave_category.in' =>
                        'Jenis cuti tidak valid.',

                    'special_leave_type_id.exists' =>
                        'Jenis cuti khusus tidak ditemukan.',

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


        /*
        |--------------------------------------------------------------------------
        | HITUNG JUMLAH HARI
        |--------------------------------------------------------------------------
        |
        | Saat ini dihitung secara tanggal kalender:
        | tanggal mulai sampai tanggal selesai, inklusif.
        |
        */

        $tanggalMulai =
            Carbon::parse(
                $validated['tanggal_mulai']
            )->startOfDay();


        $tanggalSelesai =
            Carbon::parse(
                $validated['tanggal_selesai']
            )->startOfDay();


        $leaveDays =
            $tanggalMulai
                ->diffInDays(
                    $tanggalSelesai
                )
            + 1;


        /*
        |--------------------------------------------------------------------------
        | LOGIC JENIS CUTI
        |--------------------------------------------------------------------------
        */

        $leaveCategory = null;
        $specialLeaveTypeId = null;


        if (
            $validated['jenis']
            === 'cuti'
        ) {

            /*
            |--------------------------------------------------------------------------
            | CUTI WAJIB FULL DAY
            |--------------------------------------------------------------------------
            */

            if (
                $validated['durasi_type']
                !== 'full_day'
            ) {
                throw ValidationException::withMessages([
                    'durasi_type' =>
                        'Pengajuan cuti hanya dapat menggunakan durasi hari penuh.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | WAJIB PILIH CUTI TAHUNAN / CUTI KHUSUS
            |--------------------------------------------------------------------------
            */

            if (
                blank(
                    $validated['leave_category']
                    ?? null
                )
            ) {
                throw ValidationException::withMessages([
                    'leave_category' =>
                        'Jenis cuti wajib dipilih.',
                ]);
            }


            $leaveCategory =
                $validated['leave_category'];


            /*
            |--------------------------------------------------------------------------
            | CUTI TAHUNAN
            |--------------------------------------------------------------------------
            */

            if (
                $leaveCategory
                === 'annual'
            ) {

                /*
                | Outsourcing tidak memiliki cuti tahunan.
                */

                if (! $employee->isAsiaEmployee()) {

                    throw ValidationException::withMessages([
                        'leave_category' =>
                            'Cuti tahunan hanya tersedia untuk karyawan ASIA.',
                    ]);
                }


                $leaveBalance =
                    $employee
                        ->leaveBalanceForYear(
                            $tanggalMulai->year
                        );


                if (! $leaveBalance) {

                    throw ValidationException::withMessages([
                        'leave_category' =>
                            'Saldo cuti tahunan belum tersedia.',
                    ]);
                }


                /*
                | Cek saldo saat pengajuan.
                | Saldo BELUM dikurangi di sini.
                */

                if (
                    $leaveDays
                    >
                    $leaveBalance->remaining
                ) {

                    throw ValidationException::withMessages([
                        'tanggal_selesai' =>
                            'Jumlah cuti yang diajukan adalah '
                            . $leaveDays
                            . ' hari, sedangkan sisa cuti tahunan hanya '
                            . $leaveBalance->remaining
                            . ' hari.',
                    ]);
                }


                $specialLeaveTypeId = null;
            }


            /*
            |--------------------------------------------------------------------------
            | CUTI KHUSUS
            |--------------------------------------------------------------------------
            */

            if (
                $leaveCategory
                === 'special'
            ) {

                if (
                    blank(
                        $validated[
                            'special_leave_type_id'
                        ]
                        ?? null
                    )
                ) {
                    throw ValidationException::withMessages([
                        'special_leave_type_id' =>
                            'Jenis cuti khusus wajib dipilih.',
                    ]);
                }


                $specialLeaveType =
                    SpecialLeaveType::query()
                        ->whereKey(
                            $validated[
                                'special_leave_type_id'
                            ]
                        )
                        ->where(
                            'is_active',
                            true
                        )
                        ->first();


                if (! $specialLeaveType) {

                    throw ValidationException::withMessages([
                        'special_leave_type_id' =>
                            'Jenis cuti khusus tidak aktif atau tidak ditemukan.',
                    ]);
                }


                $specialLeaveTypeId =
                    $specialLeaveType->id;
            }

        } else {

            /*
            |--------------------------------------------------------------------------
            | BUKAN CUTI
            |--------------------------------------------------------------------------
            */

            $leaveCategory = null;
            $specialLeaveTypeId = null;
            $leaveDays = null;
        }


        /*
        |--------------------------------------------------------------------------
        | IZIN BEBERAPA JAM
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | LAMPIRAN
        |--------------------------------------------------------------------------
        */

        $lampiranPath = null;
        $lampiranOriginalName = null;


        if ($request->hasFile('lampiran')) {

            $file =
                $request->file(
                    'lampiran'
                );


            $lampiranOriginalName =
                $file
                    ->getClientOriginalName();


            $lampiranPath =
                $file->store(
                    'leave-attachments',
                    'public'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN PENGAJUAN
        |--------------------------------------------------------------------------
        */

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

                    /*
                    |--------------------------------------------------------------------------
                    | DATA CUTI
                    |--------------------------------------------------------------------------
                    */

                    'leave_category' =>
                        $leaveCategory,

                    'special_leave_type_id' =>
                        $specialLeaveTypeId,

                    'leave_days' =>
                        $leaveDays,

                    /*
                    |--------------------------------------------------------------------------
                    | DURASI
                    |--------------------------------------------------------------------------
                    */

                    'durasi_type' =>
                        $validated[
                            'durasi_type'
                        ],

                    'tanggal_mulai' =>
                        $validated[
                            'tanggal_mulai'
                        ],

                    'tanggal_selesai' =>
                        $validated[
                            'tanggal_selesai'
                        ],

                    'jam_mulai' =>
                        $validated[
                            'jam_mulai'
                        ]
                        ?? null,

                    'jam_selesai' =>
                        $validated[
                            'jam_selesai'
                        ]
                        ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | DETAIL
                    |--------------------------------------------------------------------------
                    */

                    'alasan' =>
                        $validated['alasan'],

                    'lampiran_path' =>
                        $lampiranPath,

                    'lampiran_original_name' =>
                        $lampiranOriginalName,

                    /*
                    |--------------------------------------------------------------------------
                    | APPROVAL
                    |--------------------------------------------------------------------------
                    */

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

                Storage::disk(
                    'public'
                )->delete(
                    $lampiranPath
                );
            }


            throw $e;
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSE JSON
        |--------------------------------------------------------------------------
        */

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

                    'jenis' =>
                        $leaveRequest->jenis,

                    'leave_category' =>
                        $leaveRequest->leave_category,

                    'leave_days' =>
                        $leaveRequest->leave_days,

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

            'specialLeaveType',
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
