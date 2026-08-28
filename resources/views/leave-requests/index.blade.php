<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">


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
                              d="M9 12h6m-6 4h6m2.25 4.5H6.75A2.25 2.25 0 014.5 18.25V5.75A2.25 2.25 0 016.75 3.5h7.5L19.5 8.75v9.5a2.25 2.25 0 01-2.25 2.25z" />

                    </svg>

                </div>


                <div>

                    <h2 class="text-xl font-black text-slate-900 dark:text-slate-100">
                        Pengajuan Saya
                    </h2>

                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                        Riwayat izin, cuti, dan sakit.
                    </p>

                </div>

            </div>


            <button type="button"
                    data-open-create-leave
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">

                + Pengajuan Baru

            </button>

        </div>

    </x-slot>


    <div class="min-h-screen bg-slate-50 py-5 sm:py-6 dark:bg-slate-950">

        <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">


            {{-- FILTER --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">


                <div class="border-b border-slate-100 p-5 dark:border-slate-800">

                    <div class="flex items-center justify-between gap-3">

                        <div>

                            <div class="flex items-center gap-2">

                                <div class="h-5 w-1 rounded-full bg-blue-600"></div>

                                <h3 class="font-black text-slate-900 dark:text-slate-100">
                                    Riwayat Pengajuan
                                </h3>

                            </div>


                            <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
                                Filter pengajuan berdasarkan jenis dan status.
                            </p>

                        </div>


                        <div class="text-xs font-bold text-slate-400">
                            {{ number_format($items->total()) }} data
                        </div>

                    </div>

                </div>


                <form method="GET"
                      action="{{ route('leave-requests.index') }}"
                      class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto]">


                    <select name="jenis"
                            class="rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">

                        <option value="">
                            Semua Jenis
                        </option>

                        <option value="izin"
                                @selected($jenis === 'izin')>
                            Izin
                        </option>

                        <option value="cuti"
                                @selected($jenis === 'cuti')>
                            Cuti
                        </option>

                        <option value="sakit"
                                @selected($jenis === 'sakit')>
                            Sakit
                        </option>

                    </select>


                    <select name="status"
                            class="rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">

                        <option value="">
                            Semua Status
                        </option>

                        <option value="pending"
                                @selected($status === 'pending')>
                            Menunggu
                        </option>

                        <option value="approved"
                                @selected($status === 'approved')>
                            Disetujui
                        </option>

                        <option value="rejected"
                                @selected($status === 'rejected')>
                            Ditolak
                        </option>

                        <option value="cancelled"
                                @selected($status === 'cancelled')>
                            Dibatalkan
                        </option>

                    </select>


                    <div class="flex gap-2">


                        <a href="{{ route('leave-requests.index') }}"
                           class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-600 dark:border-slate-700 dark:text-slate-300">

                            Reset

                        </a>


                        <button type="submit"
                                class="flex-1 rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-black text-white hover:bg-blue-700 lg:flex-none">

                            Filter

                        </button>

                    </div>

                </form>

            </div>


            {{-- LIST --}}
            <div class="mt-5 space-y-3">


                @forelse ($items as $item)


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
                            class="block w-full rounded-3xl border border-slate-200 bg-white p-5 text-left shadow-sm transition hover:border-blue-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-900">


                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">


                            <div class="flex items-center gap-3">


                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl
                                    {{ $item->jenis === 'cuti'
                                        ? 'bg-violet-100 text-violet-600 dark:bg-violet-950/40 dark:text-violet-300'
                                        : (
                                            $item->jenis === 'sakit'
                                                ? 'bg-orange-100 text-orange-600 dark:bg-orange-950/40 dark:text-orange-300'
                                                : 'bg-blue-100 text-blue-600 dark:bg-blue-950/40 dark:text-blue-300'
                                        )
                                    }}">

                                    @if ($item->jenis === 'sakit')
                                        +
                                    @elseif ($item->jenis === 'cuti')
                                        ◫
                                    @else
                                        ✓
                                    @endif

                                </div>


                                <div>

                                    <div class="flex flex-wrap items-center gap-2">

                                        <span class="rounded-full px-3 py-1 text-[11px] font-black {{ $jenisClass }}">
                                            {{ strtoupper($item->jenis_label) }}
                                        </span>


                                        @if ($item->durasi_type === 'hourly')

                                            <span class="rounded-full bg-sky-100 px-3 py-1 text-[11px] font-bold text-sky-700 dark:bg-sky-950/50 dark:text-sky-300">

                                                {{ substr($item->jam_mulai, 0, 5) }}
                                                -
                                                {{ substr($item->jam_selesai, 0, 5) }}

                                            </span>

                                        @endif

                                    </div>


                                    <div class="mt-2 text-sm font-bold text-slate-700 dark:text-slate-200">

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


                                    <div class="mt-1 line-clamp-1 max-w-2xl text-sm text-slate-500 dark:text-slate-400">
                                        {{ $item->alasan }}
                                    </div>

                                </div>

                            </div>


                            <div class="flex items-center justify-between gap-4">

                                <span class="rounded-full px-3 py-1.5 text-xs font-black {{ $statusClass }}">
                                    {{ $item->status_label }}
                                </span>


                                <div class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-300 transition group-hover:text-blue-600">
                                    ›
                                </div>

                            </div>

                        </div>

                    </button>


                @empty


                    <div class="rounded-3xl border border-slate-200 bg-white px-5 py-14 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">

                        <div class="font-black text-slate-700 dark:text-slate-200">
                            Belum ada pengajuan
                        </div>


                        <p class="mt-1 text-sm text-slate-400">
                            Buat pengajuan izin, cuti, atau sakit melalui tombol Pengajuan Baru.
                        </p>


                        <button type="button"
                                data-open-create-leave
                                class="mt-5 rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-black text-white">

                            + Buat Pengajuan

                        </button>

                    </div>


                @endforelse

            </div>


            @if ($items->hasPages())

                <div class="mt-5 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">

                    {{ $items->withQueryString()->links() }}

                </div>

            @endif

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
            'requests' => $items,
        ]
    )

</x-app-layout>
