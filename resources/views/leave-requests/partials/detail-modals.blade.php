@foreach ($requests as $item)

    @php

        $modalId =
            'employeeLeaveModal-' .
            $item->id;

        $modalBoxId =
            'employeeLeaveModalBox-' .
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
         class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-950/65 px-3 py-4 backdrop-blur-sm sm:px-4 sm:py-8">


        <div id="{{ $modalBoxId }}"
             class="max-h-[94vh] w-full max-w-3xl scale-95 overflow-y-auto rounded-[30px] border border-slate-200 bg-white opacity-0 shadow-2xl transition-all duration-200 dark:border-slate-800 dark:bg-slate-900">


            {{-- HEADER --}}
            <div class="sticky top-0 z-20 flex items-start justify-between gap-4 border-b border-slate-100 bg-white/95 p-5 backdrop-blur dark:border-slate-800 dark:bg-slate-900/95">


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
                        Informasi lengkap pengajuan Anda.
                    </p>

                </div>


                <button type="button"
                        data-close-employee-leave
                        data-modal="{{ $modalId }}"
                        data-box="{{ $modalBoxId }}"
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-slate-200 text-slate-500 dark:border-slate-700 dark:text-slate-300">

                    ✕

                </button>

            </div>


            <div class="p-4 sm:p-6">


                {{-- HERO --}}
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#1D4ED8] via-[#1E40AF] to-[#172554] p-5 text-white">


                    <div class="absolute -right-16 -top-20 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>


                    <div class="relative">


                        <div class="flex flex-wrap items-center gap-2">

                            <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-black ring-1 ring-white/15">

                                {{ strtoupper(
                                    $item->jenis_label
                                ) }}

                            </span>


                            <span class="text-xs text-blue-100/70">
                                {{ $item->uuid }}
                            </span>

                        </div>


                        <div class="mt-4 text-xl font-black">
                            {{ $employee->nama }}
                        </div>


                        <div class="mt-1 text-sm text-blue-100">
                            {{ $employee->employee_code }}

                            @if ($employee->jabatan)
                                • {{ $employee->jabatan }}
                            @endif
                        </div>

                    </div>

                </div>


                {{-- INFO --}}
                <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">


                    <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                        <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                            Jenis
                        </div>

                        <div class="mt-2">

                            <span class="rounded-full px-3 py-1 text-xs font-black {{ $jenisClass }}">
                                {{ strtoupper($item->jenis_label) }}
                            </span>

                        </div>

                    </div>


                    <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                        <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                            Durasi
                        </div>

                        <div class="mt-2 text-sm font-black text-slate-800 dark:text-slate-100">

                            {{ $item->durasi_type === 'hourly'
                                ? 'Beberapa Jam'
                                : 'Sehari Penuh' }}

                        </div>

                    </div>


                    <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                        <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                            Status
                        </div>

                        <div class="mt-2">

                            <span class="rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">
                                {{ $item->status_label }}
                            </span>

                        </div>

                    </div>


                    <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                        <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                            Mulai
                        </div>

                        <div class="mt-2 text-sm font-black text-slate-800 dark:text-slate-100">
                            {{ $item->tanggal_mulai->format('d-m-Y') }}
                        </div>

                    </div>


                    <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                        <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                            Selesai
                        </div>

                        <div class="mt-2 text-sm font-black text-slate-800 dark:text-slate-100">
                            {{ $item->tanggal_selesai->format('d-m-Y') }}
                        </div>

                    </div>


                    <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                        <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">
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


                    @if ($item->durasi_type === 'hourly')


                        <div class="rounded-2xl bg-blue-50 p-4 dark:bg-blue-950/30">

                            <div class="text-[10px] font-black uppercase tracking-wider text-blue-500">
                                Jam Mulai
                            </div>

                            <div class="mt-2 font-black text-blue-700 dark:text-blue-300">

                                {{ $item->jam_mulai
                                    ? substr(
                                        $item->jam_mulai,
                                        0,
                                        5
                                    )
                                    : '-' }}

                            </div>

                        </div>


                        <div class="rounded-2xl bg-blue-50 p-4 dark:bg-blue-950/30">

                            <div class="text-[10px] font-black uppercase tracking-wider text-blue-500">
                                Jam Selesai
                            </div>

                            <div class="mt-2 font-black text-blue-700 dark:text-blue-300">

                                {{ $item->jam_selesai
                                    ? substr(
                                        $item->jam_selesai,
                                        0,
                                        5
                                    )
                                    : '-' }}

                            </div>

                        </div>


                    @endif

                </div>


                {{-- ALASAN --}}
                <div class="mt-4 rounded-2xl border border-slate-200 p-4 dark:border-slate-800">

                    <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                        Alasan
                    </div>

                    <div class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-slate-200">
                        {{ $item->alasan ?: '-' }}
                    </div>

                </div>


                {{-- LAMPIRAN --}}
                @if ($item->lampiran_path)

                    <div class="mt-4 rounded-2xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/50 dark:bg-blue-950/20">


                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">


                            <div>

                                <div class="text-xs font-black text-blue-700 dark:text-blue-300">
                                    Lampiran
                                </div>

                                <div class="mt-1 max-w-sm truncate text-xs text-blue-600/70 dark:text-blue-400">

                                    {{ $item->lampiran_original_name
                                        ?: 'Lampiran Pengajuan' }}

                                </div>

                            </div>


                            <a href="{{ \Illuminate\Support\Facades\Storage::url($item->lampiran_path) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-2.5 text-xs font-black text-white hover:bg-blue-700">

                                Lihat Lampiran

                            </a>

                        </div>

                    </div>

                @endif


                {{-- APPROVED --}}
                @if ($item->status === 'approved')

                    <div class="mt-4 rounded-3xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-900/50 dark:bg-emerald-950/20">

                        <div class="font-black text-emerald-700 dark:text-emerald-300">
                            Pengajuan Disetujui
                        </div>


                        <div class="mt-2 text-sm leading-6 text-emerald-700/80 dark:text-emerald-300/80">

                            Oleh:

                            <span class="font-bold">

                                {{ $item->approvedBy?->name
                                    ?? $item->external_approved_by_name
                                    ?? 'HRD' }}

                            </span>


                            @if ($item->approved_at)

                                <br>

                                Waktu:

                                <span class="font-bold">

                                    {{ $item->approved_at
                                        ->timezone('Asia/Jakarta')
                                        ->format('d-m-Y H:i') }}

                                </span>

                            @endif

                        </div>

                    </div>

                @endif


                {{-- REJECTED --}}
                @if ($item->status === 'rejected')

                    <div class="mt-4 rounded-3xl border border-red-200 bg-red-50 p-5 dark:border-red-900/50 dark:bg-red-950/20">


                        <div class="font-black text-red-700 dark:text-red-300">
                            Pengajuan Ditolak
                        </div>


                        <div class="mt-3 rounded-2xl bg-white/70 p-4 text-sm leading-6 text-red-700 dark:bg-black/10 dark:text-red-300">
                            {{ $item->rejection_reason ?: '-' }}
                        </div>

                    </div>

                @endif


                {{-- CANCELLED --}}
                @if ($item->status === 'cancelled')

                    <div class="mt-4 rounded-3xl border border-slate-200 bg-slate-100 p-5 dark:border-slate-700 dark:bg-slate-800">

                        <div class="font-black text-slate-700 dark:text-slate-200">
                            Pengajuan Dibatalkan
                        </div>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Pengajuan ini telah Anda batalkan.
                        </p>

                    </div>

                @endif


                {{-- FOOTER --}}
                <div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-between dark:border-slate-800">


                    <button type="button"
                            data-close-employee-leave
                            data-modal="{{ $modalId }}"
                            data-box="{{ $modalBoxId }}"
                            class="rounded-2xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-600 dark:border-slate-700 dark:text-slate-300">

                        Tutup

                    </button>


                    @if ($item->status === 'pending')

                        <button type="button"
                                data-open-cancel-leave
                                data-form="cancelLeaveForm-{{ $item->id }}"
                                data-name="{{ $item->jenis_label }}"
                                class="rounded-2xl bg-red-50 px-5 py-2.5 text-sm font-black text-red-700 ring-1 ring-red-200 transition hover:bg-red-100 dark:bg-red-950/30 dark:text-red-300 dark:ring-red-900/50">

                            Batalkan Pengajuan

                        </button>


                        <form id="cancelLeaveForm-{{ $item->id }}"
                              action="{{ route('leave-requests.cancel', $item) }}"
                              method="POST"
                              class="hidden">

                            @csrf

                        </form>

                    @endif

                </div>

            </div>

        </div>

    </div>

@endforeach


{{-- ========================================================= --}}
{{-- CANCEL CONFIRM MODAL --}}
{{-- ========================================================= --}}

<div id="cancelLeaveConfirmModal"
     class="fixed inset-0 z-[140] hidden items-center justify-center bg-slate-950/70 px-4 backdrop-blur-sm">


    <div id="cancelLeaveConfirmBox"
         class="w-full max-w-md scale-95 rounded-3xl border border-slate-200 bg-white p-6 opacity-0 shadow-2xl transition-all duration-200 dark:border-slate-800 dark:bg-slate-900">


        <div class="flex items-start gap-4">


            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-300">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-6 w-6"
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

                <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">
                    Batalkan Pengajuan?
                </h3>


                <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">

                    Pengajuan

                    <span id="cancelLeaveName"
                          class="font-black text-slate-800 dark:text-slate-200">
                        -
                    </span>

                    akan dibatalkan dan tidak dapat diproses HRD.

                </p>

            </div>

        </div>


        <div id="cancelLeaveError"
             class="mt-4 hidden rounded-2xl border border-red-200 bg-red-50 p-3 text-sm font-semibold text-red-700 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300">
        </div>


        <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">


            <button type="button"
                    id="cancelLeaveClose"
                    class="rounded-2xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-600 dark:border-slate-700 dark:text-slate-300">

                Tidak

            </button>


            <button type="button"
                    id="cancelLeaveSubmit"
                    class="rounded-2xl bg-red-600 px-5 py-2.5 text-sm font-black text-white hover:bg-red-700">

                Ya, Batalkan

            </button>

        </div>

    </div>

</div>


<script>

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            let cancelForm =
                null;


            const cancelModal =
                document.getElementById(
                    'cancelLeaveConfirmModal'
                );

            const cancelBox =
                document.getElementById(
                    'cancelLeaveConfirmBox'
                );

            const cancelName =
                document.getElementById(
                    'cancelLeaveName'
                );

            const cancelError =
                document.getElementById(
                    'cancelLeaveError'
                );

            const cancelSubmit =
                document.getElementById(
                    'cancelLeaveSubmit'
                );


            function syncBodyScroll() {

                const anyOpen =
                    document.querySelector(
                        '.fixed.inset-0.flex'
                    );


                document.body.classList.toggle(
                    'overflow-hidden',
                    !!anyOpen
                );

            }


            function openModal(
                modal,
                box
            ) {

                if (!modal || !box) {
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


            function closeModal(
                modal,
                box
            ) {

                if (!modal || !box) {
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

                        syncBodyScroll();

                    },
                    200
                );

            }


            /*
            |--------------------------------------------------------------------------
            | DETAIL OPEN / CLOSE
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function (event) {

                    const open =
                        event.target.closest(
                            '[data-open-employee-leave]'
                        );


                    if (open) {

                        const modal =
                            document.getElementById(
                                open.dataset.modal
                            );

                        const box =
                            document.getElementById(
                                open.dataset.box
                            );


                        openModal(
                            modal,
                            box
                        );

                        return;
                    }


                    const close =
                        event.target.closest(
                            '[data-close-employee-leave]'
                        );


                    if (close) {

                        closeModal(
                            document.getElementById(
                                close.dataset.modal
                            ),

                            document.getElementById(
                                close.dataset.box
                            )
                        );

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | CANCEL OPEN
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function (event) {

                    const button =
                        event.target.closest(
                            '[data-open-cancel-leave]'
                        );


                    if (!button) {
                        return;
                    }


                    cancelForm =
                        document.getElementById(
                            button.dataset.form
                        );


                    cancelName.textContent =
                        button.dataset.name
                        ?? 'pengajuan';


                    cancelError.classList.add(
                        'hidden'
                    );


                    openModal(
                        cancelModal,
                        cancelBox
                    );

                }
            );


            document.getElementById(
                'cancelLeaveClose'
            )?.addEventListener(
                'click',
                function () {

                    closeModal(
                        cancelModal,
                        cancelBox
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | CANCEL AJAX
            |--------------------------------------------------------------------------
            */

            cancelSubmit?.addEventListener(
                'click',
                async function () {

                    if (!cancelForm) {
                        return;
                    }


                    const originalText =
                        cancelSubmit.innerHTML;


                    cancelSubmit.disabled =
                        true;

                    cancelSubmit.innerHTML =
                        'Membatalkan...';


                    try {

                        const response =
                            await fetch(
                                cancelForm.action,
                                {
                                    method:
                                        'POST',

                                    body:
                                        new FormData(
                                            cancelForm
                                        ),

                                    headers: {
                                        'Accept':
                                            'application/json',

                                        'X-Requested-With':
                                            'XMLHttpRequest',
                                    },
                                }
                            );


                        const data =
                            await response.json();


                        if (!response.ok) {

                            throw new Error(
                                data.message
                                ??
                                'Pengajuan gagal dibatalkan.'
                            );

                        }


                        sessionStorage.setItem(
                            'employeePortalToast',
                            data.message
                            ??
                            'Pengajuan berhasil dibatalkan.'
                        );


                        window.location.reload();


                    } catch (error) {

                        cancelError.textContent =
                            error.message;

                        cancelError.classList.remove(
                            'hidden'
                        );


                    } finally {

                        cancelSubmit.disabled =
                            false;

                        cancelSubmit.innerHTML =
                            originalText;

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | BACKDROP
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function (event) {

                    const detailBackdrop =
                        event.target.closest(
                            '[id^="employeeLeaveModal-"]'
                        );


                    if (
                        detailBackdrop
                        &&
                        event.target ===
                            detailBackdrop
                    ) {

                        closeModal(
                            detailBackdrop,

                            document.getElementById(
                                detailBackdrop.id.replace(
                                    'employeeLeaveModal-',
                                    'employeeLeaveModalBox-'
                                )
                            )
                        );

                        return;
                    }


                    if (
                        event.target ===
                        cancelModal
                    ) {

                        closeModal(
                            cancelModal,
                            cancelBox
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


                    if (
                        cancelModal?.classList
                            .contains('flex')
                    ) {

                        closeModal(
                            cancelModal,
                            cancelBox
                        );

                        return;
                    }


                    const detail =
                        document.querySelector(
                            '[id^="employeeLeaveModal-"].flex'
                        );


                    if (detail) {

                        closeModal(
                            detail,

                            document.getElementById(
                                detail.id.replace(
                                    'employeeLeaveModal-',
                                    'employeeLeaveModalBox-'
                                )
                            )
                        );

                    }

                }
            );

        }
    );

</script>
