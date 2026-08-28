<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <h2 class="text-xl font-bold text-gray-900">
                Detail Pengajuan
            </h2>

            <a href="{{ route('hrd.leave-requests.index') }}"
               class="text-sm font-semibold text-gray-600">
                ← Kembali
            </a>

        </div>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">


            @if (session('success'))

                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 font-medium text-emerald-700">
                    {{ session('success') }}
                </div>

            @endif


            @if ($errors->any())

                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">

                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach

                </div>

            @endif


            @php
                $statusClass = match($leaveRequest->status) {
                    'approved' => 'bg-emerald-100 text-emerald-700',
                    'rejected' => 'bg-red-100 text-red-700',
                    'cancelled' => 'bg-gray-100 text-gray-600',
                    default => 'bg-amber-100 text-amber-700',
                };
            @endphp


            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-200">


                {{-- HEADER --}}
                <div class="flex flex-col gap-4 border-b border-gray-100 pb-5 sm:flex-row sm:items-start sm:justify-between">


                    <div>

                        <div class="text-2xl font-bold text-gray-900">
                            {{ $leaveRequest->employee?->nama ?? '-' }}
                        </div>

                        <div class="mt-1 text-sm text-gray-500">

                            {{ $leaveRequest->employee?->employee_code }}

                            @if ($leaveRequest->employee?->jabatan)
                                • {{ $leaveRequest->employee->jabatan }}
                            @endif

                        </div>

                    </div>


                    <span class="w-fit rounded-full px-4 py-2 text-xs font-bold {{ $statusClass }}">
                        {{ $leaveRequest->status_label }}
                    </span>

                </div>


                {{-- INFO --}}
                <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">


                    <div>
                        <div class="text-xs font-bold uppercase text-gray-400">
                            Jenis
                        </div>

                        <div class="mt-1 font-semibold text-gray-900">
                            {{ $leaveRequest->jenis_label }}
                        </div>
                    </div>


                    <div>
                        <div class="text-xs font-bold uppercase text-gray-400">
                            Tanggal Mulai
                        </div>

                        <div class="mt-1 font-semibold text-gray-900">
                            {{ $leaveRequest->tanggal_mulai->format('d-m-Y') }}
                        </div>
                    </div>


                    <div>
                        <div class="text-xs font-bold uppercase text-gray-400">
                            Tanggal Selesai
                        </div>

                        <div class="mt-1 font-semibold text-gray-900">
                            {{ $leaveRequest->tanggal_selesai->format('d-m-Y') }}
                        </div>
                    </div>


                    @if ($leaveRequest->durasi_type === 'hourly')

                        <div>
                            <div class="text-xs font-bold uppercase text-gray-400">
                                Jam Mulai
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ substr($leaveRequest->jam_mulai, 0, 5) }}
                            </div>
                        </div>


                        <div>
                            <div class="text-xs font-bold uppercase text-gray-400">
                                Jam Selesai
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ substr($leaveRequest->jam_selesai, 0, 5) }}
                            </div>
                        </div>

                    @endif


                    <div>
                        <div class="text-xs font-bold uppercase text-gray-400">
                            Diajukan
                        </div>

                        <div class="mt-1 font-semibold text-gray-900">
                            {{ $leaveRequest->created_at->format('d-m-Y H:i') }}
                        </div>
                    </div>

                </div>


                {{-- ALASAN --}}
                <div class="mt-7">

                    <div class="text-xs font-bold uppercase text-gray-400">
                        Alasan
                    </div>

                    <div class="mt-2 whitespace-pre-line rounded-2xl bg-gray-50 p-4 text-sm leading-6 text-gray-700">
                        {{ $leaveRequest->alasan }}
                    </div>

                </div>


                {{-- LAMPIRAN --}}
                @if ($leaveRequest->lampiran_path)

                    <div class="mt-6">

                        <div class="text-xs font-bold uppercase text-gray-400">
                            Lampiran
                        </div>

                        <a href="{{ \Illuminate\Support\Facades\Storage::url($leaveRequest->lampiran_path) }}"
                           target="_blank"
                           class="mt-2 inline-flex rounded-2xl bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700">

                            Lihat Lampiran

                        </a>

                    </div>

                @endif


                {{-- APPROVED --}}
                @if ($leaveRequest->status === 'approved')

                    <div class="mt-7 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">

                        <div class="font-bold text-emerald-700">
                            Pengajuan Disetujui
                        </div>

                        <div class="mt-2 text-sm text-emerald-600">

                            Oleh:
                            {{ $leaveRequest->approvedBy?->name ?? '-' }}

                            <br>

                            Waktu:
                            {{ $leaveRequest->approved_at?->format('d-m-Y H:i') ?? '-' }}

                        </div>

                    </div>

                @endif


                {{-- REJECTED --}}
                @if ($leaveRequest->status === 'rejected')

                    <div class="mt-7 rounded-2xl border border-red-200 bg-red-50 p-4">

                        <div class="font-bold text-red-700">
                            Pengajuan Ditolak
                        </div>

                        <div class="mt-2 text-sm text-red-600">

                            Oleh:
                            {{ $leaveRequest->rejectedBy?->name ?? '-' }}

                            <br>

                            Waktu:
                            {{ $leaveRequest->rejected_at?->format('d-m-Y H:i') ?? '-' }}

                        </div>

                        <div class="mt-4 rounded-xl bg-white/60 p-3 text-sm text-red-700">
                            {{ $leaveRequest->rejection_reason }}
                        </div>

                    </div>

                @endif


                {{-- ACTION --}}
                @if ($leaveRequest->status === 'pending')

                    <div class="mt-8 border-t border-gray-100 pt-6">


                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">


                            {{-- REJECT --}}
                            <form method="POST"
                                  action="{{ route('hrd.leave-requests.reject', $leaveRequest) }}">

                                @csrf

                                <label class="mb-2 block text-sm font-semibold text-gray-700">
                                    Alasan Penolakan
                                </label>

                                <textarea name="rejection_reason"
                                          rows="3"
                                          placeholder="Isi jika pengajuan akan ditolak..."
                                          class="w-full rounded-2xl border-gray-300">{{ old('rejection_reason') }}</textarea>

                                <button type="submit"
                                        onclick="return confirm('Yakin menolak pengajuan ini?')"
                                        class="mt-3 w-full rounded-2xl bg-red-50 px-5 py-3 text-sm font-bold text-red-700 hover:bg-red-100">

                                    Tolak Pengajuan

                                </button>

                            </form>


                            {{-- APPROVE --}}
                            <div class="flex flex-col justify-end">

                                <div class="rounded-2xl bg-emerald-50 p-4 text-sm leading-6 text-emerald-700">

                                    Pastikan jenis, tanggal, jam, alasan, dan lampiran sudah sesuai sebelum menyetujui.

                                </div>


                                <form method="POST"
                                      action="{{ route('hrd.leave-requests.approve', $leaveRequest) }}"
                                      class="mt-3">

                                    @csrf

                                    <button type="submit"
                                            onclick="return confirm('Setujui pengajuan ini?')"
                                            class="w-full rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-700">

                                        Setujui Pengajuan

                                    </button>

                                </form>

                            </div>


                        </div>

                    </div>

                @endif


            </div>

        </div>

    </div>

</x-app-layout>
