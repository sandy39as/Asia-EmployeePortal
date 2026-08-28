<x-app-layout>

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <x-slot name="header">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div class="min-w-0">

                <div class="flex items-center gap-3">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="1.8">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M8.25 6.75V4.5m7.5 2.25V4.5M4.5 9.75h15m-13.5-3h12A1.5 1.5 0 0119.5 8.25v10.5a1.5 1.5 0 01-1.5 1.5H6a1.5 1.5 0 01-1.5-1.5V8.25A1.5 1.5 0 016 6.75z" />

                        </svg>

                    </div>


                    <div>

                        <h2 class="text-xl font-black tracking-tight text-slate-900 dark:text-slate-100">
                            Pengajuan Karyawan
                        </h2>

                        <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                            Kelola izin, cuti, dan sakit seluruh karyawan.
                        </p>

                    </div>

                </div>

            </div>


            <a href="{{ route('hrd.dashboard') }}"
               class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M15 19l-7-7 7-7" />

                </svg>

                Dashboard

            </a>

        </div>

    </x-slot>


    <div class="min-h-screen bg-slate-50 py-5 sm:py-6 dark:bg-slate-950">

        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-8">


            {{-- ================================================= --}}
            {{-- FLASH --}}
            {{-- ================================================= --}}

            @if (session('success'))

                <div id="successFlash"
                     class="mb-5 rounded-3xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-700 shadow-sm dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300">

                    <div class="flex items-center gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-500/10">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M5 13l4 4L19 7" />

                            </svg>

                        </div>

                        {{ session('success') }}

                    </div>

                </div>

            @endif


            @if ($errors->any())

                <div class="mb-5 rounded-3xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700 shadow-sm dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300">

                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- FILTER CARD --}}
            {{-- ================================================= --}}

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">


                <div class="border-b border-slate-100 p-5 sm:p-6 dark:border-slate-800">

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <div class="flex items-center gap-2">

                                <div class="h-5 w-1 rounded-full bg-blue-600"></div>

                                <h3 class="font-black text-slate-900 dark:text-slate-100">
                                    Filter Pengajuan
                                </h3>

                            </div>

                            <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
                                Cari berdasarkan karyawan, jenis, status, atau periode tanggal.
                            </p>

                        </div>


                        <div class="text-xs font-semibold text-slate-400 dark:text-slate-500">

                            Total hasil:

                            <span class="font-black text-slate-700 dark:text-slate-200">
                                {{ number_format($items->total()) }}
                            </span>

                        </div>

                    </div>

                </div>


                <form method="GET"
                      action="{{ route('hrd.leave-requests.index') }}"
                      class="p-5 sm:p-6">


                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12">


                        {{-- SEARCH --}}
                        <div class="xl:col-span-3">

                            <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Cari
                            </label>

                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Nama / ID Karyawan"
                                   autocomplete="off"
                                   class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-blue-900/30">

                        </div>


                        {{-- JENIS --}}
                        <div class="xl:col-span-2">

                            <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Jenis
                            </label>

                            <select name="jenis"
                                    class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-blue-900/30">

                                <option value="">
                                    Semua Jenis
                                </option>

                                <option value="izin" @selected($jenis === 'izin')>
                                    Izin
                                </option>

                                <option value="cuti" @selected($jenis === 'cuti')>
                                    Cuti
                                </option>

                                <option value="sakit" @selected($jenis === 'sakit')>
                                    Sakit
                                </option>

                            </select>

                        </div>


                        {{-- STATUS --}}
                        <div class="xl:col-span-2">

                            <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Status
                            </label>

                            <select name="status"
                                    class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-blue-900/30">

                                <option value="">
                                    Semua Status
                                </option>

                                <option value="pending" @selected($status === 'pending')>
                                    Menunggu
                                </option>

                                <option value="approved" @selected($status === 'approved')>
                                    Disetujui
                                </option>

                                <option value="rejected" @selected($status === 'rejected')>
                                    Ditolak
                                </option>

                                <option value="cancelled" @selected($status === 'cancelled')>
                                    Dibatalkan
                                </option>

                            </select>

                        </div>


                        {{-- START DATE --}}
                        <div class="xl:col-span-2">

                            <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Mulai
                            </label>

                            <input type="date"
                                   name="start_date"
                                   value="{{ $startDate }}"
                                   class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-blue-900/30">

                        </div>


                        {{-- END DATE --}}
                        <div class="xl:col-span-3">

                            <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Selesai
                            </label>

                            <input type="date"
                                   name="end_date"
                                   value="{{ $endDate }}"
                                   class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-blue-900/30">

                        </div>

                    </div>


                    <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">

                        <a href="{{ route('hrd.leave-requests.index') }}"
                           class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300 dark:hover:bg-slate-800">

                            Reset

                        </a>


                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-4 w-4"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M3 4.5h18M6 9h12M10 13.5h4M11 18h2" />

                            </svg>

                            Terapkan Filter

                        </button>

                    </div>

                </form>

            </div>


            {{-- ================================================= --}}
            {{-- LIST --}}
            {{-- ================================================= --}}

            <div class="mt-5 space-y-3">


                @forelse ($items as $index => $item)


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
                            'leaveModal-' .
                            $item->id;

                        $modalBoxId =
                            'leaveModalBox-' .
                            $item->id;

                    @endphp


                    <div class="group rounded-3xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:shadow-md sm:p-5 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-900">


                        <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">


                            {{-- EMPLOYEE --}}
                            <div class="flex min-w-0 items-start gap-3 xl:w-[30%]">


                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 text-base font-black text-white shadow-sm">

                                    {{ strtoupper(
                                        substr(
                                            (string) (
                                                $item->employee?->nama
                                                ?? '?'
                                            ),
                                            0,
                                            1
                                        )
                                    ) }}

                                </div>


                                <div class="min-w-0">

                                    <div class="truncate font-black text-slate-900 dark:text-slate-100">
                                        {{ $item->employee?->nama ?? '-' }}
                                    </div>


                                    <div class="mt-1 flex flex-wrap items-center gap-2">

                                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-black text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">

                                            {{ $item->employee?->employee_code ?? '-' }}

                                        </span>


                                        @if ($item->employee?->jabatan)

                                            <span class="truncate text-xs text-slate-500 dark:text-slate-400">
                                                {{ $item->employee->jabatan }}
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </div>


                            {{-- INFORMATION --}}
                            <div class="flex flex-1 flex-wrap items-center gap-2">


                                <span class="rounded-full px-3 py-1.5 text-[11px] font-black {{ $jenisClass }}">
                                    {{ strtoupper($item->jenis_label) }}
                                </span>


                                <span class="rounded-full bg-slate-100 px-3 py-1.5 text-[11px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">

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


                                @if ($item->durasi_type === 'hourly')

                                    <span class="rounded-full bg-sky-100 px-3 py-1.5 text-[11px] font-bold text-sky-700 dark:bg-sky-950/50 dark:text-sky-300">

                                        {{ $item->jam_mulai
                                            ? substr($item->jam_mulai, 0, 5)
                                            : '-' }}

                                        -

                                        {{ $item->jam_selesai
                                            ? substr($item->jam_selesai, 0, 5)
                                            : '-' }}

                                    </span>

                                @else

                                    <span class="rounded-full bg-slate-100 px-3 py-1.5 text-[11px] font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                        Sehari Penuh
                                    </span>

                                @endif

                            </div>


                            {{-- REASON --}}
                            <div class="min-w-0 xl:w-[22%]">

                                <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400">
                                    Alasan
                                </div>

                                <div class="mt-1 line-clamp-2 text-sm leading-5 text-slate-600 dark:text-slate-300">
                                    {{ $item->alasan ?: '-' }}
                                </div>

                            </div>


                            {{-- STATUS + ACTION --}}
                            <div class="flex shrink-0 items-center justify-between gap-3 xl:justify-end">


                                <span class="rounded-full px-3 py-1.5 text-xs font-black {{ $statusClass }}">
                                    {{ $item->status_label }}
                                </span>


                                <button type="button"
                                        data-modal="{{ $modalId }}"
                                        data-box="{{ $modalBoxId }}"
                                        class="openLeaveModal inline-flex items-center justify-center gap-2 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-xs font-black text-blue-700 transition hover:bg-blue-100 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-300 dark:hover:bg-blue-950/50">

                                    Detail

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-4 w-4"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor"
                                         stroke-width="2">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M9 5l7 7-7 7" />

                                    </svg>

                                </button>

                            </div>

                        </div>

                    </div>


                @empty


                    <div class="rounded-3xl border border-slate-200 bg-white px-5 py-16 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">


                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-7 w-7"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="1.6">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M9 12h6m-6 4h6m2.25 4.5H6.75A2.25 2.25 0 014.5 18.25V5.75A2.25 2.25 0 016.75 3.5h7.5L19.5 8.75v9.5a2.25 2.25 0 01-2.25 2.25z" />

                            </svg>

                        </div>


                        <div class="mt-4 font-black text-slate-700 dark:text-slate-200">
                            Tidak ada pengajuan
                        </div>

                        <div class="mt-1 text-sm text-slate-400 dark:text-slate-500">
                            Tidak ditemukan data sesuai filter yang dipilih.
                        </div>

                    </div>


                @endforelse

            </div>


            {{-- ================================================= --}}
            {{-- PAGINATION --}}
            {{-- ================================================= --}}

            @if ($items->hasPages())

                <div class="mt-5 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">

                    {{ $items->withQueryString()->links() }}

                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DETAIL MODALS --}}
    {{-- ========================================================= --}}

    @foreach ($items as $item)


        @php

            $modalId =
                'leaveModal-' .
                $item->id;

            $modalBoxId =
                'leaveModalBox-' .
                $item->id;


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

        @endphp


        <div id="{{ $modalId }}"
             class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/65 px-3 py-4 backdrop-blur-sm sm:px-4 sm:py-8">


            <div id="{{ $modalBoxId }}"
                 class="max-h-[94vh] w-full max-w-4xl scale-95 overflow-y-auto rounded-[28px] border border-slate-200 bg-white opacity-0 shadow-2xl transition-all duration-200 dark:border-slate-800 dark:bg-slate-900">


                {{-- ================================================= --}}
                {{-- MODAL HEADER --}}
                {{-- ================================================= --}}

                <div class="sticky top-0 z-20 flex items-start justify-between gap-4 border-b border-slate-100 bg-white/95 p-5 backdrop-blur sm:p-6 dark:border-slate-800 dark:bg-slate-900/95">


                    <div>

                        <div class="flex flex-wrap items-center gap-2">

                            <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">
                                Detail Pengajuan
                            </h3>

                            <span class="rounded-full px-3 py-1 text-[11px] font-black {{ $statusClass }}">
                                {{ $item->status_label }}
                            </span>

                        </div>


                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Informasi lengkap pengajuan karyawan.
                        </p>

                    </div>


                    <button type="button"
                            data-modal="{{ $modalId }}"
                            data-box="{{ $modalBoxId }}"
                            class="closeLeaveModal inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300 dark:hover:bg-slate-800">

                        ✕

                    </button>

                </div>


                <div class="p-4 sm:p-6">


                    {{-- ================================================= --}}
                    {{-- EMPLOYEE HERO --}}
                    {{-- ================================================= --}}

                    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#1D4ED8] via-[#1E40AF] to-[#172554] p-5 text-white sm:p-6">


                        <div class="pointer-events-none absolute -right-16 -top-20 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>


                        <div class="relative flex items-center gap-4">


                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-xl font-black ring-1 ring-white/20">

                                {{ strtoupper(
                                    substr(
                                        (string) (
                                            $item->employee?->nama
                                            ?? '?'
                                        ),
                                        0,
                                        1
                                    )
                                ) }}

                            </div>


                            <div class="min-w-0">

                                <div class="truncate text-lg font-black">
                                    {{ $item->employee?->nama ?? '-' }}
                                </div>


                                <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-blue-100">

                                    <span class="font-bold">
                                        {{ $item->employee?->employee_code ?? '-' }}
                                    </span>


                                    @if ($item->employee?->jabatan)

                                        <span>•</span>

                                        <span>
                                            {{ $item->employee->jabatan }}
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- INFORMATION --}}
                    {{-- ================================================= --}}

                    <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4">


                        {{-- JENIS --}}
                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                Jenis
                            </div>

                            <div class="mt-2">

                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-black {{ $jenisClass }}">
                                    {{ strtoupper($item->jenis_label) }}
                                </span>

                            </div>

                        </div>


                        {{-- DURASI --}}
                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                Durasi
                            </div>

                            <div class="mt-2 text-sm font-black text-slate-800 dark:text-slate-100">

                                {{ $item->durasi_type === 'hourly'
                                    ? 'Per Jam'
                                    : 'Sehari Penuh' }}

                            </div>

                        </div>


                        {{-- MULAI --}}
                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                Tanggal Mulai
                            </div>

                            <div class="mt-2 text-sm font-black text-slate-800 dark:text-slate-100">
                                {{ $item->tanggal_mulai->format('d-m-Y') }}
                            </div>

                        </div>


                        {{-- SELESAI --}}
                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                Tanggal Selesai
                            </div>

                            <div class="mt-2 text-sm font-black text-slate-800 dark:text-slate-100">
                                {{ $item->tanggal_selesai->format('d-m-Y') }}
                            </div>

                        </div>


                        @if ($item->durasi_type === 'hourly')


                            <div class="rounded-2xl bg-blue-50 p-4 ring-1 ring-blue-100 dark:bg-blue-950/30 dark:ring-blue-900/50">

                                <div class="text-[10px] font-bold uppercase tracking-wider text-blue-500">
                                    Jam Mulai
                                </div>

                                <div class="mt-2 text-base font-black text-blue-700 dark:text-blue-300">

                                    {{ $item->jam_mulai
                                        ? substr($item->jam_mulai, 0, 5)
                                        : '-' }}

                                </div>

                            </div>


                            <div class="rounded-2xl bg-blue-50 p-4 ring-1 ring-blue-100 dark:bg-blue-950/30 dark:ring-blue-900/50">

                                <div class="text-[10px] font-bold uppercase tracking-wider text-blue-500">
                                    Jam Selesai
                                </div>

                                <div class="mt-2 text-base font-black text-blue-700 dark:text-blue-300">

                                    {{ $item->jam_selesai
                                        ? substr($item->jam_selesai, 0, 5)
                                        : '-' }}

                                </div>

                            </div>


                        @endif


                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                Diajukan
                            </div>

                            <div class="mt-2 text-sm font-black text-slate-800 dark:text-slate-100">

                                {{ $item->created_at
                                    ? $item->created_at
                                        ->timezone('Asia/Jakarta')
                                        ->format('d-m-Y H:i')
                                    : '-' }}

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- ALASAN --}}
                    {{-- ================================================= --}}

                    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">

                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Alasan Pengajuan
                        </div>

                        <div class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-slate-200">
                            {{ $item->alasan ?: '-' }}
                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- LAMPIRAN --}}
                    {{-- ================================================= --}}

                    @if ($item->lampiran_path)

                        <div class="mt-4 rounded-2xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/50 dark:bg-blue-950/20">

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                <div>

                                    <div class="text-xs font-black text-blue-700 dark:text-blue-300">
                                        Lampiran Pengajuan
                                    </div>

                                    <div class="mt-1 text-xs text-blue-600/80 dark:text-blue-400">
                                        Dokumen pendukung yang dikirim oleh karyawan.
                                    </div>

                                </div>


                                <a href="{{ \Illuminate\Support\Facades\Storage::url($item->lampiran_path) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex shrink-0 items-center justify-center gap-2 rounded-2xl bg-blue-600 px-4 py-2.5 text-xs font-black text-white shadow-sm transition hover:bg-blue-700">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-4 w-4"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor"
                                         stroke-width="2">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M15 10l4.553-4.553a2.121 2.121 0 013 3L18 13m-6 1l-4.553 4.553a2.121 2.121 0 01-3-3L9 11m6-2l-6 6" />

                                    </svg>

                                    Lihat Lampiran

                                </a>

                            </div>

                        </div>

                    @endif


                    {{-- ================================================= --}}
                    {{-- APPROVED --}}
                    {{-- ================================================= --}}

                    @if ($item->status === 'approved')

                        <div class="mt-5 rounded-3xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-900/50 dark:bg-emerald-950/20">


                            <div class="flex items-start gap-3">


                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-5 w-5"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor"
                                         stroke-width="2">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M5 13l4 4L19 7" />

                                    </svg>

                                </div>


                                <div>

                                    <div class="font-black text-emerald-700 dark:text-emerald-300">
                                        Pengajuan Disetujui
                                    </div>


                                    <div class="mt-2 text-sm leading-6 text-emerald-700/80 dark:text-emerald-300/80">

                                        Oleh:

                                        <span class="font-bold">
                                            {{ $item->approvedBy?->name
                                                ?? $item->external_approved_by_name
                                                ?? '-' }}
                                        </span>

                                        <br>

                                        Waktu:

                                        <span class="font-bold">

                                            {{ $item->approved_at
                                                ? $item->approved_at
                                                    ->timezone('Asia/Jakarta')
                                                    ->format('d-m-Y H:i')
                                                : '-' }}

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endif


                    {{-- ================================================= --}}
                    {{-- REJECTED --}}
                    {{-- ================================================= --}}

                    @if ($item->status === 'rejected')

                        <div class="mt-5 rounded-3xl border border-red-200 bg-red-50 p-5 dark:border-red-900/50 dark:bg-red-950/20">


                            <div class="flex items-start gap-3">


                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-300">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-5 w-5"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor"
                                         stroke-width="2">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M6 18L18 6M6 6l12 12" />

                                    </svg>

                                </div>


                                <div class="min-w-0 flex-1">

                                    <div class="font-black text-red-700 dark:text-red-300">
                                        Pengajuan Ditolak
                                    </div>


                                    <div class="mt-2 text-sm leading-6 text-red-700/80 dark:text-red-300/80">

                                        Oleh:

                                        <span class="font-bold">

                                            {{ $item->rejectedBy?->name
                                                ?? $item->external_rejected_by_name
                                                ?? '-' }}

                                        </span>

                                        <br>

                                        Waktu:

                                        <span class="font-bold">

                                            {{ $item->rejected_at
                                                ? $item->rejected_at
                                                    ->timezone('Asia/Jakarta')
                                                    ->format('d-m-Y H:i')
                                                : '-' }}

                                        </span>

                                    </div>


                                    <div class="mt-4 rounded-2xl bg-white/70 p-4 text-sm leading-6 text-red-700 dark:bg-black/10 dark:text-red-300">
                                        {{ $item->rejection_reason ?: '-' }}
                                    </div>

                                </div>

                            </div>

                        </div>

                    @endif


                    {{-- ================================================= --}}
                    {{-- CANCELLED --}}
                    {{-- ================================================= --}}

                    @if ($item->status === 'cancelled')

                        <div class="mt-5 rounded-3xl border border-slate-200 bg-slate-100 p-5 dark:border-slate-700 dark:bg-slate-800/70">

                            <div class="font-black text-slate-700 dark:text-slate-200">
                                Pengajuan Dibatalkan
                            </div>

                            <div class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                Pengajuan ini sudah dibatalkan oleh karyawan.
                            </div>

                        </div>

                    @endif


                    {{-- ================================================= --}}
                    {{-- ACTION PENDING --}}
                    {{-- ================================================= --}}

                    @if ($item->status === 'pending')


                        <div class="mt-6 border-t border-slate-100 pt-6 dark:border-slate-800">


                            <div class="mb-4">

                                <h4 class="font-black text-slate-900 dark:text-slate-100">
                                    Keputusan HRD
                                </h4>

                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                    Periksa data pengajuan sebelum memberikan keputusan.
                                </p>

                            </div>


                            {{-- AJAX ERROR --}}
                            <div id="actionError-{{ $item->id }}"
                                 class="mb-4 hidden rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700 dark:border-red-900/50 dark:bg-red-950/20 dark:text-red-300">
                            </div>


                            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">


                                {{-- ===================================== --}}
                                {{-- REJECT --}}
                                {{-- ===================================== --}}

                                <div class="rounded-3xl border border-red-200 bg-red-50/60 p-4 sm:p-5 dark:border-red-900/40 dark:bg-red-950/10">


                                    <div class="flex items-center gap-3">

                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-300">

                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="h-5 w-5"
                                                 fill="none"
                                                 viewBox="0 0 24 24"
                                                 stroke="currentColor"
                                                 stroke-width="2">

                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      d="M6 18L18 6M6 6l12 12" />

                                            </svg>

                                        </div>


                                        <div>

                                            <div class="font-black text-red-700 dark:text-red-300">
                                                Tolak Pengajuan
                                            </div>

                                            <div class="mt-0.5 text-xs text-red-600/70 dark:text-red-400">
                                                Alasan penolakan wajib diisi.
                                            </div>

                                        </div>

                                    </div>


                                    <form method="POST"
                                          action="{{ route('hrd.leave-requests.reject', $item) }}"
                                          data-leave-action-form
                                          data-type="reject"
                                          data-item="{{ $item->id }}"
                                          class="mt-4">

                                        @csrf


                                        <textarea name="rejection_reason"
                                                  rows="4"
                                                  placeholder="Contoh: Jadwal cuti bertabrakan dengan kebutuhan operasional..."
                                                  class="w-full resize-none rounded-2xl border border-red-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100 dark:border-red-900/50 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-red-900/30"></textarea>


                                        <button type="submit"
                                                data-submit-button
                                                class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:bg-red-700">

                                            Tolak Pengajuan

                                        </button>

                                    </form>

                                </div>


                                {{-- ===================================== --}}
                                {{-- APPROVE --}}
                                {{-- ===================================== --}}

                                <div class="flex flex-col rounded-3xl border border-emerald-200 bg-emerald-50/60 p-4 sm:p-5 dark:border-emerald-900/40 dark:bg-emerald-950/10">


                                    <div class="flex items-center gap-3">

                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300">

                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="h-5 w-5"
                                                 fill="none"
                                                 viewBox="0 0 24 24"
                                                 stroke="currentColor"
                                                 stroke-width="2">

                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      d="M5 13l4 4L19 7" />

                                            </svg>

                                        </div>


                                        <div>

                                            <div class="font-black text-emerald-700 dark:text-emerald-300">
                                                Setujui Pengajuan
                                            </div>

                                            <div class="mt-0.5 text-xs text-emerald-600/70 dark:text-emerald-400">
                                                Approval akan langsung disimpan.
                                            </div>

                                        </div>

                                    </div>


                                    <div class="mt-4 flex-1 rounded-2xl bg-white/70 p-4 text-sm leading-6 text-emerald-700 dark:bg-black/10 dark:text-emerald-300">

                                        Pastikan jenis pengajuan, tanggal, durasi, alasan, dan lampiran sudah sesuai sebelum menyetujui.

                                    </div>


                                    <form method="POST"
                                          action="{{ route('hrd.leave-requests.approve', $item) }}"
                                          data-leave-action-form
                                          data-type="approve"
                                          data-item="{{ $item->id }}"
                                          class="mt-3">

                                        @csrf


                                        <button type="submit"
                                                data-submit-button
                                                class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:bg-emerald-700">

                                            Setujui Pengajuan

                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    @endif


                    {{-- ================================================= --}}
                    {{-- MODAL FOOTER --}}
                    {{-- ================================================= --}}

                    <div class="mt-6 flex justify-end border-t border-slate-100 pt-5 dark:border-slate-800">

                        <button type="button"
                                data-modal="{{ $modalId }}"
                                data-box="{{ $modalBoxId }}"
                                class="closeLeaveModal inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-black text-white transition hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600">

                            Tutup

                        </button>

                    </div>

                </div>

            </div>

        </div>

    @endforeach


    {{-- ========================================================= --}}
    {{-- JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {


                /*
                |--------------------------------------------------------------------------
                | FLASH AUTO HIDE
                |--------------------------------------------------------------------------
                */

                const successFlash =
                    document.getElementById(
                        'successFlash'
                    );


                if (successFlash) {

                    setTimeout(
                        function () {

                            successFlash.style.transition =
                                'opacity .4s ease, transform .4s ease';

                            successFlash.style.opacity =
                                '0';

                            successFlash.style.transform =
                                'translateY(-8px)';


                            setTimeout(
                                function () {
                                    successFlash.remove();
                                },
                                400
                            );

                        },
                        3500
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | OPEN MODAL
                |--------------------------------------------------------------------------
                */

                function openModal(
                    modalId,
                    boxId
                ) {

                    const modal =
                        document.getElementById(
                            modalId
                        );

                    const box =
                        document.getElementById(
                            boxId
                        );


                    if (
                        ! modal ||
                        ! box
                    ) {
                        return;
                    }


                    modal.classList.remove(
                        'hidden'
                    );

                    modal.classList.add(
                        'flex'
                    );


                    document.body.classList.add(
                        'overflow-hidden'
                    );


                    setTimeout(
                        function () {

                            box.classList.remove(
                                'scale-95',
                                'opacity-0'
                            );

                            box.classList.add(
                                'scale-100',
                                'opacity-100'
                            );

                        },
                        10
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | CLOSE MODAL
                |--------------------------------------------------------------------------
                */

                function closeModal(
                    modalId,
                    boxId
                ) {

                    const modal =
                        document.getElementById(
                            modalId
                        );

                    const box =
                        document.getElementById(
                            boxId
                        );


                    if (
                        ! modal ||
                        ! box
                    ) {
                        return;
                    }


                    box.classList.remove(
                        'scale-100',
                        'opacity-100'
                    );

                    box.classList.add(
                        'scale-95',
                        'opacity-0'
                    );


                    setTimeout(
                        function () {

                            modal.classList.remove(
                                'flex'
                            );

                            modal.classList.add(
                                'hidden'
                            );


                            document.body.classList.remove(
                                'overflow-hidden'
                            );

                        },
                        200
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | OPEN / CLOSE BUTTON
                |--------------------------------------------------------------------------
                */

                document.addEventListener(
                    'click',
                    function (event) {


                        const openButton =
                            event.target.closest(
                                '.openLeaveModal'
                            );


                        if (openButton) {

                            openModal(
                                openButton.dataset.modal,
                                openButton.dataset.box
                            );

                            return;

                        }


                        const closeButton =
                            event.target.closest(
                                '.closeLeaveModal'
                            );


                        if (closeButton) {

                            closeModal(
                                closeButton.dataset.modal,
                                closeButton.dataset.box
                            );

                            return;

                        }


                        /*
                         * Backdrop.
                         */
                        const modalBackdrop =
                            event.target.closest(
                                '[id^="leaveModal-"]'
                            );


                        if (
                            modalBackdrop &&
                            event.target === modalBackdrop
                        ) {

                            const modalId =
                                modalBackdrop.id;

                            const boxId =
                                modalId.replace(
                                    'leaveModal-',
                                    'leaveModalBox-'
                                );


                            closeModal(
                                modalId,
                                boxId
                            );

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | ESC
                |--------------------------------------------------------------------------
                */

                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key !==
                            'Escape'
                        ) {
                            return;
                        }


                        const openedModal =
                            document.querySelector(
                                '[id^="leaveModal-"].flex'
                            );


                        if (! openedModal) {
                            return;
                        }


                        const modalId =
                            openedModal.id;

                        const boxId =
                            modalId.replace(
                                'leaveModal-',
                                'leaveModalBox-'
                            );


                        closeModal(
                            modalId,
                            boxId
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | APPROVE / REJECT AJAX
                |--------------------------------------------------------------------------
                |
                | POST tetap menggunakan route Laravel yang sekarang.
                |
                | Bahkan kalau controller redirect ke halaman show(),
                | browser TIDAK pindah karena request dilakukan via fetch.
                |
                | Setelah sukses, halaman index yang sedang dibuka
                | direfresh sehingga status terbaru muncul.
                |
                */

                document.addEventListener(
                    'submit',
                    async function (event) {


                        const form =
                            event.target.closest(
                                '[data-leave-action-form]'
                            );


                        if (! form) {
                            return;
                        }


                        event.preventDefault();


                        const type =
                            form.dataset.type;


                        const itemId =
                            form.dataset.item;


                        const submitButton =
                            form.querySelector(
                                '[data-submit-button]'
                            );


                        const errorBox =
                            document.getElementById(
                                `actionError-${itemId}`
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | REJECT VALIDATION
                        |--------------------------------------------------------------------------
                        */

                        if (type === 'reject') {

                            const textarea =
                                form.querySelector(
                                    '[name="rejection_reason"]'
                                );


                            const reason =
                                textarea
                                    ?.value
                                    .trim()
                                ?? '';


                            if (reason === '') {

                                if (errorBox) {

                                    errorBox.textContent =
                                        'Alasan penolakan wajib diisi.';

                                    errorBox.classList.remove(
                                        'hidden'
                                    );

                                }


                                textarea?.focus();

                                return;

                            }

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | CONFIRM
                        |--------------------------------------------------------------------------
                        */

                        const confirmMessage =
                            type === 'approve'
                                ? 'Setujui pengajuan ini?'
                                : 'Tolak pengajuan ini?';


                        if (
                            ! window.confirm(
                                confirmMessage
                            )
                        ) {
                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | RESET ERROR
                        |--------------------------------------------------------------------------
                        */

                        if (errorBox) {

                            errorBox.classList.add(
                                'hidden'
                            );

                            errorBox.textContent =
                                '';

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | BUTTON LOADING
                        |--------------------------------------------------------------------------
                        */

                        const originalText =
                            submitButton
                                ?.innerHTML
                            ?? '';


                        if (submitButton) {

                            submitButton.disabled =
                                true;

                            submitButton.classList.add(
                                'opacity-70',
                                'cursor-not-allowed'
                            );


                            submitButton.innerHTML =
                                type === 'approve'
                                    ? 'Menyetujui...'
                                    : 'Menolak...';

                        }


                        try {


                            const response =
                                await fetch(
                                    form.action,
                                    {
                                        method:
                                            'POST',

                                        body:
                                            new FormData(
                                                form
                                            ),

                                        headers: {

                                            'X-Requested-With':
                                                'XMLHttpRequest',

                                            'Accept':
                                                'application/json, text/html',

                                        },
                                    }
                                );


                            /*
                            |--------------------------------------------------------------------------
                            | VALIDATION / ERROR
                            |--------------------------------------------------------------------------
                            */

                            if (! response.ok) {

                                let message =
                                    'Terjadi kesalahan saat memproses pengajuan.';


                                const contentType =
                                    response.headers.get(
                                        'content-type'
                                    )
                                    ?? '';


                                if (
                                    contentType.includes(
                                        'application/json'
                                    )
                                ) {

                                    try {

                                        const data =
                                            await response.json();


                                        if (data.message) {

                                            message =
                                                data.message;

                                        }


                                        if (
                                            data.errors
                                        ) {

                                            const firstError =
                                                Object
                                                    .values(
                                                        data.errors
                                                    )
                                                    .flat()
                                                    [0];


                                            if (firstError) {

                                                message =
                                                    firstError;

                                            }

                                        }

                                    } catch (e) {
                                        //
                                    }

                                }


                                throw new Error(
                                    message
                                );

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | SUCCESS
                            |--------------------------------------------------------------------------
                            |
                            | fetch tidak memindahkan browser walaupun
                            | controller mengembalikan redirect().
                            |
                            | Refresh URL INDEX saat ini.
                            |
                            */

                            window.location.reload();


                        } catch (error) {


                            if (errorBox) {

                                errorBox.textContent =
                                    error.message
                                    ||
                                    'Terjadi kesalahan.';

                                errorBox.classList.remove(
                                    'hidden'
                                );

                            }


                            if (submitButton) {

                                submitButton.disabled =
                                    false;

                                submitButton.classList.remove(
                                    'opacity-70',
                                    'cursor-not-allowed'
                                );

                                submitButton.innerHTML =
                                    originalText;

                            }

                        }

                    }
                );

            }
        );

    </script>

</x-app-layout>
