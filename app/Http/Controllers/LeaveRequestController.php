<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $employee = $request->user()->employee;

        abort_unless($employee, 403);

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
    ) {
        $employee =
            $request->user()->employee;

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
    ) {
        $employee =
            $request->user()->employee;

        abort_unless(
            $employee,
            403
        );

        if (! $employee->is_active) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Akun karyawan sedang tidak aktif.'
                );
        }

        $validated =
            $request->validate([
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
            ], [
                'jenis.required' =>
                    'Jenis pengajuan wajib dipilih.',

                'tanggal_mulai.required' =>
                    'Tanggal mulai wajib diisi.',

                'tanggal_selesai.required' =>
                    'Tanggal selesai wajib diisi.',

                'tanggal_selesai.after_or_equal' =>
                    'Tanggal selesai tidak boleh sebelum tanggal mulai.',

                'alasan.required' =>
                    'Alasan wajib diisi.',

                'lampiran.max' =>
                    'Ukuran lampiran maksimal 5 MB.',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Izin Beberapa Jam
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
        | Lampiran
        |--------------------------------------------------------------------------
        */
        $lampiranPath = null;
        $lampiranOriginalName = null;

        if ($request->hasFile('lampiran')) {
            $file =
                $request->file('lampiran');

            $lampiranOriginalName =
                $file->getClientOriginalName();

            $lampiranPath =
                $file->store(
                    'leave-attachments',
                    'public'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */
        $uuid =
            (string) Str::uuid();

        LeaveRequest::create([
            'uuid' =>
                $uuid,

            'employee_id' =>
                $employee->id,

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

            'status' =>
                'pending',
        ]);

        return redirect()
            ->route(
                'leave-requests.index'
            )
            ->with(
                'success',
                'Pengajuan berhasil dikirim dan menunggu persetujuan HRD.'
            );
    }

    public function show(
        Request $request,
        LeaveRequest $leaveRequest
    ) {
        $employee =
            $request->user()->employee;

        abort_unless(
            $employee
            &&
            $leaveRequest->employee_id
                === $employee->id,
            403
        );

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
    ) {
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
        ) {
            return back()
                ->with(
                    'error',
                    'Hanya pengajuan yang masih menunggu yang dapat dibatalkan.'
                );
        }

        $leaveRequest->update([
            'status' =>
                'cancelled',
        ]);

        return back()->with(
            'success',
            'Pengajuan berhasil dibatalkan.'
        );
    }
}
