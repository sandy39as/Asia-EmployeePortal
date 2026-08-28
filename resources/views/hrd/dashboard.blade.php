<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    Dashboard HRD
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Kelola pengajuan izin, cuti, dan sakit karyawan.
                </p>
            </div>

            <a href="{{ route('hrd.leave-requests.index') }}"
               class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-700">

                Kelola Pengajuan

            </a>

        </div>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">


            @if (session('success'))

                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>

            @endif


            {{-- SUMMARY --}}
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">


                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-200">

                    <div class="text-sm text-gray-500">
                        Total Pengajuan
                    </div>

                    <div class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $totalPengajuan }}
                    </div>

                </div>


                <div class="rounded-3xl bg-amber-50 p-5 ring-1 ring-amber-100">

                    <div class="text-sm font-medium text-amber-700">
                        Menunggu Approval
                    </div>

                    <div class="mt-2 text-3xl font-bold text-amber-700">
                        {{ $totalPending }}
                    </div>

                </div>


                <div class="rounded-3xl bg-emerald-50 p-5 ring-1 ring-emerald-100">

                    <div class="text-sm font-medium text-emerald-700">
                        Disetujui
                    </div>

                    <div class="mt-2 text-3xl font-bold text-emerald-700">
                        {{ $totalApproved }}
                    </div>

                </div>


                <div class="rounded-3xl bg-red-50 p-5 ring-1 ring-red-100">

                    <div class="text-sm font-medium text-red-700">
                        Ditolak
                    </div>

                    <div class="mt-2 text-3xl font-bold text-red-700">
                        {{ $totalRejected }}
                    </div>

                </div>


            </div>


            {{-- RECENT --}}
            <div class="mt-6 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-200">

                <div class="flex items-center justify-between">

                    <div>

                        <h3 class="font-bold text-gray-900">
                            Pengajuan Terbaru
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Pengajuan terbaru dari karyawan.
                        </p>

                    </div>

                    <a href="{{ route('hrd.leave-requests.index') }}"
                       class="text-sm font-bold text-blue-600">

                        Lihat Semua

                    </a>

                </div>


                <div class="mt-5 space-y-3">

                    @forelse ($recentRequests as $item)

                        @php
                            $statusClass = match($item->status) {
                                'approved' => 'bg-emerald-100 text-emerald-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                'cancelled' => 'bg-gray-100 text-gray-600',
                                default => 'bg-amber-100 text-amber-700',
                            };
                        @endphp


                        <a href="{{ route('hrd.leave-requests.show', $item) }}"
                           class="flex flex-col gap-4 rounded-2xl border border-gray-100 p-4 transition hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between">

                            <div>

                                <div class="font-bold text-gray-900">
                                    {{ $item->employee?->nama ?? '-' }}
                                </div>

                                <div class="mt-1 text-sm text-gray-500">

                                    {{ $item->jenis_label }}

                                    •

                                    {{ $item->tanggal_mulai->format('d-m-Y') }}

                                    @if (
                                        $item->tanggal_mulai->toDateString()
                                        !==
                                        $item->tanggal_selesai->toDateString()
                                    )
                                        -
                                        {{ $item->tanggal_selesai->format('d-m-Y') }}
                                    @endif

                                </div>

                            </div>


                            <span class="w-fit rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">

                                {{ $item->status_label }}

                            </span>

                        </a>

                    @empty

                        <div class="rounded-2xl bg-gray-50 py-10 text-center text-sm text-gray-500">
                            Belum ada pengajuan.
                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
