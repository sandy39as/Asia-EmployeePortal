<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">
            Detail Pengajuan
        </h2>
    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">


            @php
                $statusClass = match($leaveRequest->status) {
                    'approved' => 'bg-emerald-100 text-emerald-700',
                    'rejected' => 'bg-red-100 text-red-700',
                    'cancelled' => 'bg-gray-100 text-gray-600',
                    default => 'bg-amber-100 text-amber-700',
                };
            @endphp


            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-200">


                <div class="flex items-start justify-between gap-4">

                    <div>

                        <div class="text-2xl font-bold text-gray-900">
                            {{ $leaveRequest->jenis_label }}
                        </div>

                        <div class="mt-1 text-xs text-gray-400">
                            {{ $leaveRequest->uuid }}
                        </div>

                    </div>


                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">

                        {{ $leaveRequest->status_label }}

                    </span>

                </div>


                <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">

                    <div>

                        <div class="text-xs font-semibold uppercase text-gray-400">
                            Tanggal Mulai
                        </div>

                        <div class="mt-1 font-semibold text-gray-900">
                            {{ $leaveRequest->tanggal_mulai->format('d-m-Y') }}
                        </div>

                    </div>


                    <div>

                        <div class="text-xs font-semibold uppercase text-gray-400">
                            Tanggal Selesai
                        </div>

                        <div class="mt-1 font-semibold text-gray-900">
                            {{ $leaveRequest->tanggal_selesai->format('d-m-Y') }}
                        </div>

                    </div>


                    @if ($leaveRequest->durasi_type === 'hourly')

                        <div>

                            <div class="text-xs font-semibold uppercase text-gray-400">
                                Jam Mulai
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ substr($leaveRequest->jam_mulai, 0, 5) }}
                            </div>

                        </div>


                        <div>

                            <div class="text-xs font-semibold uppercase text-gray-400">
                                Jam Selesai
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ substr($leaveRequest->jam_selesai, 0, 5) }}
                            </div>

                        </div>

                    @endif

                </div>


                <div class="mt-6">

                    <div class="text-xs font-semibold uppercase text-gray-400">
                        Alasan
                    </div>

                    <div class="mt-2 whitespace-pre-line rounded-2xl bg-gray-50 p-4 text-sm leading-6 text-gray-700">
                        {{ $leaveRequest->alasan }}
                    </div>

                </div>


                @if ($leaveRequest->lampiran_path)

                    <div class="mt-6">

                        <div class="text-xs font-semibold uppercase text-gray-400">
                            Lampiran
                        </div>

                        <a href="{{ Storage::url($leaveRequest->lampiran_path) }}"
                           target="_blank"
                           class="mt-2 inline-flex rounded-xl bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700">

                            {{ $leaveRequest->lampiran_original_name ?: 'Lihat Lampiran' }}

                        </a>

                    </div>

                @endif


                @if ($leaveRequest->status === 'rejected')

                    <div class="mt-6 rounded-2xl bg-red-50 p-4">

                        <div class="text-sm font-bold text-red-700">
                            Alasan Penolakan
                        </div>

                        <div class="mt-2 text-sm text-red-600">
                            {{ $leaveRequest->rejection_reason ?: '-' }}
                        </div>

                    </div>

                @endif


                <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">

                    <a href="{{ route('leave-requests.index') }}"
                       class="inline-flex justify-center rounded-2xl border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700">

                        Kembali

                    </a>


                    @if ($leaveRequest->status === 'pending')

                        <form method="POST"
                              action="{{ route('leave-requests.cancel', $leaveRequest) }}"
                              onsubmit="return confirm('Batalkan pengajuan ini?')">

                            @csrf

                            <button type="submit"
                                    class="w-full rounded-2xl bg-red-50 px-5 py-3 text-sm font-semibold text-red-700 hover:bg-red-100">

                                Batalkan Pengajuan

                            </button>

                        </form>

                    @endif

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
