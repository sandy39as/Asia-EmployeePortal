<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center gap-3">

            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M3.75 13.5l8.25-8.25 8.25 8.25M5.25 12v7.5h4.5v-4.5h4.5v4.5h4.5V12" />

                </svg>

            </div>


            <div>

                <h2 class="text-xl font-black text-slate-900 dark:text-slate-100">
                    Dashboard
                </h2>

                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                    Employee Portal
                </p>

            </div>

        </div>

    </x-slot>


    <div class="min-h-screen bg-slate-50 py-5 sm:py-6 dark:bg-slate-950">

        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-8">


            @if ($employee)


                {{-- HERO --}}
                <div class="relative overflow-hidden rounded-[30px] bg-gradient-to-br from-[#1D4ED8] via-[#1E40AF] to-[#172554] p-6 text-white shadow-xl shadow-blue-900/10 sm:p-8">


                    <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>


                    <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">


                        <div>


                            <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold text-blue-100 ring-1 ring-white/10">

                                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>

                                Employee Portal

                            </div>


                            <p class="mt-5 text-sm text-blue-100/70">
                                Selamat datang,
                            </p>


                            <h1 class="mt-1 text-2xl font-black sm:text-3xl">
                                {{ $employee->nama }}
                            </h1>


                            <div class="mt-3 flex flex-wrap gap-2">


                                <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold text-blue-100 ring-1 ring-white/10">
                                    {{ $employee->employee_code }}
                                </span>


                                @if ($employee->jabatan)

                                    <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs text-blue-100 ring-1 ring-white/10">
                                        {{ $employee->jabatan }}
                                    </span>

                                @endif

                            </div>

                        </div>


                        <button type="button"
                                data-open-create-leave
                                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-blue-700 shadow-lg transition hover:bg-blue-50">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M12 6v12m6-6H6" />

                            </svg>

                            Buat Pengajuan

                        </button>

                    </div>

                </div>


                {{-- SUMMARY --}}
                <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4">


                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">

                        <div class="text-xs font-black uppercase tracking-wider text-slate-400">
                            Total
                        </div>

                        <div class="mt-2 text-3xl font-black text-slate-900 dark:text-white">
                            {{ number_format($totalPengajuan ?? 0) }}
                        </div>

                    </div>


                    <a href="{{ route('leave-requests.index', ['status' => 'pending']) }}"
                       class="rounded-3xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-amber-900/40 dark:bg-amber-950/20">

                        <div class="text-xs font-black uppercase tracking-wider text-amber-600">
                            Menunggu
                        </div>

                        <div class="mt-2 text-3xl font-black text-amber-700 dark:text-amber-300">
                            {{ number_format($totalPending ?? 0) }}
                        </div>

                    </a>


                    <a href="{{ route('leave-requests.index', ['status' => 'approved']) }}"
                       class="rounded-3xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-emerald-900/40 dark:bg-emerald-950/20">

                        <div class="text-xs font-black uppercase tracking-wider text-emerald-600">
                            Disetujui
                        </div>

                        <div class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300">
                            {{ number_format($totalApproved ?? 0) }}
                        </div>

                    </a>


                    <a href="{{ route('leave-requests.index', ['status' => 'rejected']) }}"
                       class="rounded-3xl border border-red-200 bg-red-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-red-900/40 dark:bg-red-950/20">

                        <div class="text-xs font-black uppercase tracking-wider text-red-600">
                            Ditolak
                        </div>

                        <div class="mt-2 text-3xl font-black text-red-700 dark:text-red-300">
                            {{ number_format($totalRejected ?? 0) }}
                        </div>

                    </a>

                </div>


                {{-- QUICK ACTION --}}
                <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">


                    <button type="button"
                            data-open-create-leave
                            data-jenis="izin"
                            class="group rounded-3xl border border-blue-200 bg-blue-50 p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-100 hover:shadow-md dark:border-blue-900/40 dark:bg-blue-950/20">


                        <div class="flex items-center justify-between">


                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-5 w-5"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor"
                                     stroke-width="1.8">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M9 12h6m-6 4h6m2.25 4.5H6.75A2.25 2.25 0 014.5 18.25V5.75A2.25 2.25 0 016.75 3.5h7.5L19.5 8.75v9.5a2.25 2.25 0 01-2.25 2.25z" />

                                </svg>

                            </div>

                            <span class="text-blue-400 transition group-hover:translate-x-1">
                                →
                            </span>

                        </div>


                        <div class="mt-4 font-black text-blue-700 dark:text-blue-300">
                            Izin
                        </div>

                        <p class="mt-1 text-sm text-blue-600/80 dark:text-blue-400">
                            Pengajuan izin kerja
                        </p>

                    </button>


                    <button type="button"
                            data-open-create-leave
                            data-jenis="cuti"
                            class="group rounded-3xl border border-violet-200 bg-violet-50 p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:bg-violet-100 hover:shadow-md dark:border-violet-900/40 dark:bg-violet-950/20">


                        <div class="flex items-center justify-between">

                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-100 text-violet-600 dark:bg-violet-500/10 dark:text-violet-300">
                                ◫
                            </div>

                            <span class="text-violet-400 transition group-hover:translate-x-1">
                                →
                            </span>

                        </div>


                        <div class="mt-4 font-black text-violet-700 dark:text-violet-300">
                            Cuti
                        </div>

                        <p class="mt-1 text-sm text-violet-600/80 dark:text-violet-400">
                            Pengajuan cuti karyawan
                        </p>

                    </button>


                    <button type="button"
                            data-open-create-leave
                            data-jenis="sakit"
                            class="group rounded-3xl border border-orange-200 bg-orange-50 p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:bg-orange-100 hover:shadow-md dark:border-orange-900/40 dark:bg-orange-950/20">


                        <div class="flex items-center justify-between">

                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-orange-100 text-orange-600 dark:bg-orange-500/10 dark:text-orange-300">
                                +
                            </div>

                            <span class="text-orange-400 transition group-hover:translate-x-1">
                                →
                            </span>

                        </div>


                        <div class="mt-4 font-black text-orange-700 dark:text-orange-300">
                            Sakit
                        </div>

                        <p class="mt-1 text-sm text-orange-600/80 dark:text-orange-400">
                            Izin sakit / surat dokter
                        </p>

                    </button>

                </div>


                {{-- RECENT --}}
                <div class="mt-5 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">


                    <div class="flex items-center justify-between border-b border-slate-100 p-5 dark:border-slate-800">


                        <div>

                            <div class="flex items-center gap-2">

                                <div class="h-5 w-1 rounded-full bg-blue-600"></div>

                                <h3 class="font-black text-slate-900 dark:text-slate-100">
                                    Pengajuan Terbaru
                                </h3>

                            </div>


                            <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
                                Status pengajuan terakhir Anda.
                            </p>

                        </div>


                        <a href="{{ route('leave-requests.index') }}"
                           class="text-sm font-black text-blue-600 dark:text-blue-400">

                            Lihat Semua

                        </a>

                    </div>


                    <div class="space-y-3 p-4 sm:p-5">


                        @forelse ($recentRequests as $item)


                            @php

                                $statusClass = match($item->status) {

                                    'approved' =>
                                        'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',

                                    'rejected' =>
                                        'bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-300',

                                    'cancelled' =>
                                        'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',

                                    default =>
                                        'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300',

                                };


                                $jenisClass = match($item->jenis) {

                                    'cuti' =>
                                        'bg-violet-100 text-violet-700 dark:bg-violet-950/50 dark:text-violet-300',

                                    'sakit' =>
                                        'bg-orange-100 text-orange-700 dark:bg-orange-950/50 dark:text-orange-300',

                                    default =>
                                        'bg-blue-100 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300',

                                };


                                $modalId =
                                    'employeeLeaveModal-' .
                                    $item->id;

                                $modalBoxId =
                                    'employeeLeaveModalBox-' .
                                    $item->id;

                            @endphp


                            <button type="button"
                                    data-open-employee-leave
                                    data-modal="{{ $modalId }}"
                                    data-box="{{ $modalBoxId }}"
                                    class="block w-full rounded-2xl border border-slate-200 p-4 text-left transition hover:border-blue-200 hover:bg-blue-50/40 dark:border-slate-800 dark:hover:border-blue-900 dark:hover:bg-blue-950/10">


                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">


                                    <div>

                                        <div class="flex flex-wrap items-center gap-2">

                                            <span class="rounded-full px-3 py-1 text-[11px] font-black {{ $jenisClass }}">
                                                {{ strtoupper($item->jenis_label) }}
                                            </span>


                                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-300">

                                                {{ $item->tanggal_mulai->format('d-m-Y') }}

                                                @if (
                                                    $item->tanggal_mulai->toDateString()
                                                    !==
                                                    $item->tanggal_selesai->toDateString()
                                                )

                                                    -
                                                    {{ $item->tanggal_selesai->format('d-m-Y') }}

                                                @endif

                                            </span>

                                        </div>


                                        <div class="mt-2 line-clamp-1 text-sm text-slate-600 dark:text-slate-300">
                                            {{ $item->alasan }}
                                        </div>

                                    </div>


                                    <div class="flex items-center justify-between gap-3">

                                        <span class="rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">
                                            {{ $item->status_label }}
                                        </span>

                                        <span class="text-slate-300">
                                            ›
                                        </span>

                                    </div>

                                </div>

                            </button>


                        @empty


                            <div class="rounded-3xl bg-slate-50 py-12 text-center dark:bg-slate-800/60">

                                <div class="font-black text-slate-600 dark:text-slate-300">
                                    Belum ada pengajuan
                                </div>

                                <p class="mt-1 text-sm text-slate-400">
                                    Pengajuan yang Anda buat akan tampil di sini.
                                </p>

                            </div>


                        @endforelse

                    </div>

                </div>


                @include(
                    'leave-requests.partials.create-modal',
                    [
                        'employee' => $employee
                    ]
                )


                @include(
                    'leave-requests.partials.detail-modals',
                    [
                        'employee' => $employee,
                        'requests' => $recentRequests,
                    ]
                )


            @else


                <div class="rounded-3xl border border-red-200 bg-white p-8 text-center shadow-sm dark:border-red-900/40 dark:bg-slate-900">

                    <div class="text-lg font-black text-slate-800 dark:text-slate-100">
                        Data Karyawan Tidak Ditemukan
                    </div>

                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        Akun ini belum terhubung dengan data Employee Portal.
                    </p>

                </div>


            @endif

        </div>

    </div>

</x-app-layout>
