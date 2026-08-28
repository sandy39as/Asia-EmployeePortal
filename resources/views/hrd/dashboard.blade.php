<x-app-layout>

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <x-slot name="header">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="text-xl font-black tracking-tight text-slate-900 dark:text-slate-100">
                    Dashboard HRD
                </h2>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Ringkasan Employee Portal dan pengajuan karyawan.
                </p>

            </div>


            <div class="flex flex-wrap gap-2">

                <a href="{{ route('hrd.employees.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">

                    Data Karyawan

                </a>


                <a href="{{ route('hrd.leave-requests.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">

                    Kelola Pengajuan

                    @if (($totalPending ?? 0) > 0)

                        <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-black text-blue-700">
                            {{ $totalPending }}
                        </span>

                    @endif

                </a>

            </div>

        </div>

    </x-slot>


    <div class="min-h-screen bg-slate-50 py-5 sm:py-6 dark:bg-slate-950">

        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-8">


            {{-- ================================================= --}}
            {{-- FLASH --}}
            {{-- ================================================= --}}

            @if (session('success'))

                <div class="mb-5 rounded-3xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-700 shadow-sm dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300">

                    {{ session('success') }}

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- HERO --}}
            {{-- ================================================= --}}

            <div class="relative overflow-hidden rounded-[30px] bg-gradient-to-br from-[#1D4ED8] via-[#1E40AF] to-[#172554] p-6 shadow-xl shadow-blue-900/10 sm:p-8">


                <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>

                <div class="pointer-events-none absolute -bottom-32 left-1/3 h-64 w-64 rounded-full bg-sky-400/10 blur-3xl"></div>


                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">


                    <div>

                        <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold text-blue-100 ring-1 ring-white/10">

                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>

                            HRD Employee Portal

                        </div>


                        <h1 class="mt-4 text-2xl font-black text-white sm:text-3xl">
                            Employee Management Dashboard
                        </h1>


                        <p class="mt-2 max-w-xl text-sm leading-6 text-blue-100/80">
                            Pantau pengajuan izin, cuti, dan sakit serta kelola akun karyawan melalui Employee Portal.
                        </p>


                        <div class="mt-5 flex flex-wrap gap-2">

                            <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-blue-100 ring-1 ring-white/10">

                                {{ now()
                                    ->timezone('Asia/Jakarta')
                                    ->translatedFormat('d F Y') }}

                            </span>


                            <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-blue-100 ring-1 ring-white/10">

                                {{ now()
                                    ->timezone('Asia/Jakarta')
                                    ->format('H:i') }}
                                WIB

                            </span>

                        </div>

                    </div>


                    <div class="grid grid-cols-2 gap-3">


                        <a href="{{ route('hrd.leave-requests.index') }}"
                           class="rounded-2xl bg-white/10 p-4 text-white ring-1 ring-white/10 backdrop-blur transition hover:bg-white/15">

                            <div class="text-2xl font-black">
                                {{ $totalPending ?? 0 }}
                            </div>

                            <div class="mt-1 text-xs font-semibold text-blue-100/70">
                                Menunggu Approval
                            </div>

                        </a>


                        <a href="{{ route('hrd.employees.index') }}"
                           class="rounded-2xl bg-white/10 p-4 text-white ring-1 ring-white/10 backdrop-blur transition hover:bg-white/15">

                            <div class="text-sm font-black">
                                Data
                            </div>

                            <div class="mt-1 text-xs font-semibold text-blue-100/70">
                                Kelola Karyawan
                            </div>

                        </a>

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- SUMMARY --}}
            {{-- ================================================= --}}

            <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4">


                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">

                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total Pengajuan
                    </div>

                    <div class="mt-2 text-3xl font-black text-slate-900 dark:text-white">
                        {{ number_format($totalPengajuan ?? 0) }}
                    </div>

                </div>


                <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-900/40 dark:bg-amber-950/20">

                    <div class="text-xs font-bold uppercase tracking-wider text-amber-600">
                        Menunggu
                    </div>

                    <div class="mt-2 text-3xl font-black text-amber-700 dark:text-amber-300">
                        {{ number_format($totalPending ?? 0) }}
                    </div>

                </div>


                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-900/40 dark:bg-emerald-950/20">

                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-600">
                        Disetujui
                    </div>

                    <div class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300">
                        {{ number_format($totalApproved ?? 0) }}
                    </div>

                </div>


                <div class="rounded-3xl border border-red-200 bg-red-50 p-5 shadow-sm dark:border-red-900/40 dark:bg-red-950/20">

                    <div class="text-xs font-bold uppercase tracking-wider text-red-600">
                        Ditolak
                    </div>

                    <div class="mt-2 text-3xl font-black text-red-700 dark:text-red-300">
                        {{ number_format($totalRejected ?? 0) }}
                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- RECENT --}}
            {{-- ================================================= --}}

            <div class="mt-5 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">


                <div class="flex items-center justify-between border-b border-slate-100 p-5 sm:p-6 dark:border-slate-800">

                    <div>

                        <h3 class="font-black text-slate-900 dark:text-slate-100">
                            Pengajuan Terbaru
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Klik pengajuan untuk melihat detail tanpa berpindah halaman.
                        </p>

                    </div>


                    <a href="{{ route('hrd.leave-requests.index') }}"
                       class="text-sm font-bold text-blue-600 dark:text-blue-400">

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
                                'leaveDetailModal-' .
                                $item->id;

                            $modalBoxId =
                                'leaveDetailModalBox-' .
                                $item->id;

                        @endphp


                        <button type="button"
                                data-modal="{{ $modalId }}"
                                data-box="{{ $modalBoxId }}"
                                class="openLeaveDetailModal block w-full rounded-2xl border border-slate-200 bg-white p-4 text-left transition hover:border-blue-200 hover:bg-blue-50/40 hover:shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-900 dark:hover:bg-blue-950/10">


                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">


                                <div class="flex min-w-0 items-center gap-3">


                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 text-sm font-black text-white">

                                        {{ strtoupper(
                                            substr(
                                                (string) (
                                                    $item
                                                        ->employee
                                                        ?->nama
                                                    ?? '?'
                                                ),
                                                0,
                                                1
                                            )
                                        ) }}

                                    </div>


                                    <div class="min-w-0">

                                        <div class="truncate text-sm font-black text-slate-900 dark:text-slate-100">

                                            {{ $item->employee?->nama ?? '-' }}

                                        </div>


                                        <div class="mt-1 text-xs font-semibold text-blue-600 dark:text-blue-400">

                                            {{ $item->employee?->employee_code ?? '-' }}

                                        </div>

                                    </div>

                                </div>


                                <div class="flex flex-wrap items-center gap-2">


                                    <span class="rounded-full px-3 py-1 text-[11px] font-black {{ $jenisClass }}">

                                        {{ strtoupper($item->jenis_label) }}

                                    </span>


                                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-300">

                                        {{ $item
                                            ->tanggal_mulai
                                            ->format('d-m-Y') }}

                                        @if (
                                            $item
                                                ->tanggal_mulai
                                                ->toDateString()
                                            !==
                                            $item
                                                ->tanggal_selesai
                                                ->toDateString()
                                        )

                                            -

                                            {{ $item
                                                ->tanggal_selesai
                                                ->format('d-m-Y') }}

                                        @endif

                                    </span>


                                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">

                                        {{ $item->status_label }}

                                    </span>

                                </div>

                            </div>

                        </button>


                    @empty


                        <div class="rounded-3xl bg-slate-50 py-14 text-center text-sm font-semibold text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">

                            Belum ada pengajuan.

                        </div>


                    @endforelse

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DETAIL MODALS --}}
    {{-- ========================================================= --}}

    @foreach ($recentRequests as $item)


        @php

            $modalId =
                'leaveDetailModal-' .
                $item->id;

            $modalBoxId =
                'leaveDetailModalBox-' .
                $item->id;


            $statusClass = match($item->status) {

                'approved' =>
                    'bg-emerald-100 text-emerald-700',

                'rejected' =>
                    'bg-red-100 text-red-700',

                'cancelled' =>
                    'bg-slate-100 text-slate-600',

                default =>
                    'bg-amber-100 text-amber-700',

            };


            $jenisClass = match($item->jenis) {

                'cuti' =>
                    'bg-violet-100 text-violet-700',

                'sakit' =>
                    'bg-orange-100 text-orange-700',

                default =>
                    'bg-blue-100 text-blue-700',

            };

        @endphp


        <div id="{{ $modalId }}"
             class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 px-3 py-5 backdrop-blur-sm">


            <div id="{{ $modalBoxId }}"
                 class="max-h-[92vh] w-full max-w-3xl scale-95 overflow-y-auto rounded-3xl border border-slate-200 bg-white opacity-0 shadow-2xl transition-all duration-200 dark:border-slate-800 dark:bg-slate-900">


                {{-- HEADER --}}

                <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-slate-100 bg-white/95 p-5 backdrop-blur dark:border-slate-800 dark:bg-slate-900/95">


                    <div>

                        <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">
                            Detail Pengajuan
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Informasi pengajuan karyawan.
                        </p>

                    </div>


                    <button type="button"
                            data-modal="{{ $modalId }}"
                            data-box="{{ $modalBoxId }}"
                            class="closeLeaveDetailModal inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 text-slate-500 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">

                        ✕

                    </button>

                </div>


                <div class="p-5 sm:p-6">


                    {{-- EMPLOYEE --}}

                    <div class="rounded-3xl bg-gradient-to-br from-blue-600 to-blue-800 p-5 text-white">


                        <div class="flex items-center gap-4">


                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-xl font-black ring-1 ring-white/15">

                                {{ strtoupper(
                                    substr(
                                        (string) (
                                            $item
                                                ->employee
                                                ?->nama
                                            ?? '?'
                                        ),
                                        0,
                                        1
                                    )
                                ) }}

                            </div>


                            <div>

                                <div class="text-lg font-black">

                                    {{ $item->employee?->nama ?? '-' }}

                                </div>


                                <div class="mt-1 text-sm text-blue-100">

                                    {{ $item->employee?->employee_code ?? '-' }}

                                    @if ($item->employee?->jabatan)

                                        •
                                        {{ $item->employee->jabatan }}

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- INFO --}}

                    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">


                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Jenis
                            </div>

                            <div class="mt-2">

                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $jenisClass }}">

                                    {{ strtoupper($item->jenis_label) }}

                                </span>

                            </div>

                        </div>


                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Status
                            </div>

                            <div class="mt-2">

                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">

                                    {{ $item->status_label }}

                                </span>

                            </div>

                        </div>


                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Tanggal Mulai
                            </div>

                            <div class="mt-2 text-sm font-bold text-slate-900 dark:text-slate-100">

                                {{ $item
                                    ->tanggal_mulai
                                    ->format('d-m-Y') }}

                            </div>

                        </div>


                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Tanggal Selesai
                            </div>

                            <div class="mt-2 text-sm font-bold text-slate-900 dark:text-slate-100">

                                {{ $item
                                    ->tanggal_selesai
                                    ->format('d-m-Y') }}

                            </div>

                        </div>


                        @if ($item->durasi_type === 'hourly')

                            <div class="rounded-2xl bg-blue-50 p-4 dark:bg-blue-950/30">

                                <div class="text-xs font-bold uppercase tracking-wide text-blue-500">
                                    Jam Mulai
                                </div>

                                <div class="mt-2 text-sm font-black text-blue-700 dark:text-blue-300">

                                    {{ $item->jam_mulai
                                        ? substr($item->jam_mulai, 0, 5)
                                        : '-' }}

                                </div>

                            </div>


                            <div class="rounded-2xl bg-blue-50 p-4 dark:bg-blue-950/30">

                                <div class="text-xs font-bold uppercase tracking-wide text-blue-500">
                                    Jam Selesai
                                </div>

                                <div class="mt-2 text-sm font-black text-blue-700 dark:text-blue-300">

                                    {{ $item->jam_selesai
                                        ? substr($item->jam_selesai, 0, 5)
                                        : '-' }}

                                </div>

                            </div>

                        @endif

                    </div>


                    {{-- ALASAN --}}

                    <div class="mt-4 rounded-2xl border border-slate-200 p-4 dark:border-slate-800">

                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Alasan
                        </div>

                        <div class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-slate-200">

                            {{ $item->alasan ?: '-' }}

                        </div>

                    </div>


                    {{-- APPROVAL INFO --}}

                    @if ($item->status === 'approved')

                        <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900/50 dark:bg-emerald-950/20">

                            <div class="text-xs font-black uppercase tracking-wide text-emerald-600">
                                Approval
                            </div>

                            <div class="mt-2 text-sm font-semibold text-emerald-700 dark:text-emerald-300">

                                Pengajuan telah disetujui.

                                @if ($item->approved_at)

                                    <br>

                                    {{ \Carbon\Carbon::parse(
                                        $item->approved_at
                                    )
                                        ->timezone('Asia/Jakarta')
                                        ->format('d-m-Y H:i') }}

                                @endif

                            </div>

                        </div>

                    @endif


                    {{-- REJECTION --}}

                    @if ($item->status === 'rejected')

                        <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 p-4 dark:border-red-900/50 dark:bg-red-950/20">

                            <div class="text-xs font-black uppercase tracking-wide text-red-600">
                                Alasan Penolakan
                            </div>

                            <div class="mt-2 whitespace-pre-line text-sm text-red-700 dark:text-red-300">

                                {{ $item->rejection_reason ?: '-' }}

                            </div>

                        </div>

                    @endif


                    {{-- FOOTER --}}

                    <div class="mt-6 flex justify-end">

                        <button type="button"
                                data-modal="{{ $modalId }}"
                                data-box="{{ $modalBoxId }}"
                                class="closeLeaveDetailModal inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600">

                            Tutup

                        </button>

                    </div>

                </div>

            </div>

        </div>

    @endforeach


    {{-- ========================================================= --}}
    {{-- MODAL JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {


                function openLeaveModal(
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
                        !modal ||
                        !box
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


                function closeLeaveModal(
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
                        !modal ||
                        !box
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

                            document.body
                                .classList
                                .remove(
                                    'overflow-hidden'
                                );

                        },
                        200
                    );

                }


                document.addEventListener(
                    'click',
                    function (event) {


                        const openButton =
                            event.target.closest(
                                '.openLeaveDetailModal'
                            );


                        if (openButton) {

                            openLeaveModal(
                                openButton.dataset.modal,
                                openButton.dataset.box
                            );

                            return;

                        }


                        const closeButton =
                            event.target.closest(
                                '.closeLeaveDetailModal'
                            );


                        if (closeButton) {

                            closeLeaveModal(
                                closeButton.dataset.modal,
                                closeButton.dataset.box
                            );

                            return;

                        }


                        const backdrop =
                            event.target.closest(
                                '[id^="leaveDetailModal-"]'
                            );


                        if (
                            backdrop &&
                            event.target === backdrop
                        ) {

                            const modalId =
                                backdrop.id;

                            const boxId =
                                modalId.replace(
                                    'leaveDetailModal-',
                                    'leaveDetailModalBox-'
                                );


                            closeLeaveModal(
                                modalId,
                                boxId
                            );

                        }

                    }
                );


                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key !==
                            'Escape'
                        ) {
                            return;
                        }


                        const modal =
                            document.querySelector(
                                '[id^="leaveDetailModal-"].flex'
                            );


                        if (!modal) {
                            return;
                        }


                        const modalId =
                            modal.id;

                        const boxId =
                            modalId.replace(
                                'leaveDetailModal-',
                                'leaveDetailModalBox-'
                            );


                        closeLeaveModal(
                            modalId,
                            boxId
                        );

                    }
                );

            }
        );

    </script>

</x-app-layout>
