<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">
                Employee Portal
            </h2>

            @if ($employee)
                <p class="mt-1 text-sm text-gray-500">
                    Selamat datang, {{ $employee->nama }}
                </p>
            @endif
        </div>
    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">


            @if (session('success'))

                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">

                    {{ session('success') }}

                </div>

            @endif


            @if ($employee)

                {{-- PROFILE --}}
                <div class="mb-6 overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 to-cyan-500 p-6 text-white shadow-lg">

                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <div class="text-sm text-blue-100">
                                ID Karyawan
                            </div>

                            <div class="mt-1 text-xl font-bold">
                                {{ $employee->employee_code }}
                            </div>

                            <div class="mt-4 text-2xl font-bold">
                                {{ $employee->nama }}
                            </div>

                            <div class="mt-1 text-sm text-blue-100">
                                {{ $employee->jabatan ?: 'Jabatan belum tersedia' }}
                            </div>

                        </div>


                        <a href="{{ route('leave-requests.create') }}"
                           class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold text-blue-700 shadow transition hover:bg-blue-50">

                            + Buat Pengajuan

                        </a>

                    </div>

                </div>


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

                        <div class="text-sm text-amber-700">
                            Menunggu
                        </div>

                        <div class="mt-2 text-3xl font-bold text-amber-700">
                            {{ $totalPending }}
                        </div>

                    </div>


                    <div class="rounded-3xl bg-emerald-50 p-5 ring-1 ring-emerald-100">

                        <div class="text-sm text-emerald-700">
                            Disetujui
                        </div>

                        <div class="mt-2 text-3xl font-bold text-emerald-700">
                            {{ $totalApproved }}
                        </div>

                    </div>


                    <div class="rounded-3xl bg-red-50 p-5 ring-1 ring-red-100">

                        <div class="text-sm text-red-700">
                            Ditolak
                        </div>

                        <div class="mt-2 text-3xl font-bold text-red-700">
                            {{ $totalRejected }}
                        </div>

                    </div>

                </div>


                {{-- QUICK ACTION --}}
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">

                    <a href="{{ route('leave-requests.create', ['jenis' => 'izin']) }}"
                       class="rounded-3xl bg-blue-50 p-5 ring-1 ring-blue-100 transition hover:bg-blue-100">

                        <div class="text-lg font-bold text-blue-700">
                            Izin
                        </div>

                        <p class="mt-1 text-sm text-blue-600">
                            Pengajuan izin kerja
                        </p>

                    </a>


                    <a href="{{ route('leave-requests.create', ['jenis' => 'cuti']) }}"
                       class="rounded-3xl bg-violet-50 p-5 ring-1 ring-violet-100 transition hover:bg-violet-100">

                        <div class="text-lg font-bold text-violet-700">
                            Cuti
                        </div>

                        <p class="mt-1 text-sm text-violet-600">
                            Pengajuan cuti karyawan
                        </p>

                    </a>


                    <a href="{{ route('leave-requests.create', ['jenis' => 'sakit']) }}"
                       class="rounded-3xl bg-orange-50 p-5 ring-1 ring-orange-100 transition hover:bg-orange-100">

                        <div class="text-lg font-bold text-orange-700">
                            Sakit
                        </div>

                        <p class="mt-1 text-sm text-orange-600">
                            Izin sakit / surat dokter
                        </p>

                    </a>

                </div>


                {{-- RECENT --}}
                <div class="mt-6 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-200">

                    <div class="flex items-center justify-between">

                        <div>

                            <h3 class="font-bold text-gray-900">
                                Pengajuan Terbaru
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Status pengajuan terakhir Anda
                            </p>

                        </div>


                        <a href="{{ route('leave-requests.index') }}"
                           class="text-sm font-semibold text-blue-600 hover:text-blue-700">

                            Lihat Semua

                        </a>

                    </div>


                    <div class="mt-5 space-y-3">

                        @forelse ($recentRequests as $item)

                            <a href="{{ route('leave-requests.show', $item) }}"
                               class="flex items-center justify-between rounded-2xl border border-gray-100 p-4 transition hover:bg-gray-50">

                                <div>

                                    <div class="font-semibold text-gray-900">
                                        {{ $item->jenis_label }}
                                    </div>

                                    <div class="mt-1 text-sm text-gray-500">

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


                                @php
                                    $statusClass = match($item->status) {
                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        'cancelled' => 'bg-gray-100 text-gray-600',
                                        default => 'bg-amber-100 text-amber-700',
                                    };
                                @endphp


                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">

                                    {{ $item->status_label }}

                                </span>

                            </a>

                        @empty

                            <div class="rounded-2xl bg-gray-50 px-4 py-8 text-center text-sm text-gray-500">

                                Belum ada pengajuan.

                            </div>

                        @endforelse

                    </div>

                </div>

            @else

                <div class="rounded-3xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-200">

                    Akun ini belum terhubung ke data karyawan.

                </div>

            @endif

        </div>

    </div>

</x-app-layout>
