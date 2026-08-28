{{-- ========================================================= --}}
{{-- CREATE LEAVE MODAL --}}
{{-- ========================================================= --}}

<div id="createLeaveModal"
     class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-950/65 px-3 py-4 backdrop-blur-sm sm:px-4 sm:py-8">


    <div id="createLeaveModalBox"
         class="max-h-[94vh] w-full max-w-3xl scale-95 overflow-y-auto rounded-[30px] border border-slate-200 bg-white opacity-0 shadow-2xl transition-all duration-200 dark:border-slate-800 dark:bg-slate-900">


        {{-- HEADER --}}
        <div class="sticky top-0 z-20 flex items-start justify-between gap-4 border-b border-slate-100 bg-white/95 p-5 backdrop-blur sm:p-6 dark:border-slate-800 dark:bg-slate-900/95">


            <div>

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-white">

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

                    </div>


                    <div>

                        <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">
                            Buat Pengajuan
                        </h3>

                        <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                            Izin, cuti, atau sakit.
                        </p>

                    </div>

                </div>

            </div>


            <button type="button"
                    data-close-create-leave
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300">

                ✕

            </button>

        </div>


        <form id="createLeaveForm"
              method="POST"
              action="{{ route('leave-requests.store') }}"
              enctype="multipart/form-data">

            @csrf


            <div class="p-4 sm:p-6">


                {{-- EMPLOYEE --}}
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#1D4ED8] via-[#1E40AF] to-[#172554] p-5 text-white">


                    <div class="pointer-events-none absolute -right-16 -top-20 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>


                    <div class="relative flex items-center gap-4">


                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-lg font-black ring-1 ring-white/20">

                            {{ strtoupper(
                                substr(
                                    (string) $employee->nama,
                                    0,
                                    1
                                )
                            ) }}

                        </div>


                        <div class="min-w-0">

                            <div class="truncate font-black">
                                {{ $employee->nama }}
                            </div>


                            <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-blue-100">

                                <span class="font-bold">
                                    {{ $employee->employee_code }}
                                </span>


                                @if ($employee->jabatan)

                                    <span>•</span>

                                    <span>
                                        {{ $employee->jabatan }}
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ERROR --}}
                <div id="createLeaveError"
                     class="mt-4 hidden rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300">
                </div>


                {{-- JENIS --}}
                <div class="mt-5">

                    <label class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        Jenis Pengajuan
                    </label>


                    <div class="grid grid-cols-3 gap-2">


                        <label class="cursor-pointer">

                            <input type="radio"
                                   name="jenis"
                                   value="izin"
                                   class="peer sr-only"
                                   required>

                            <div class="rounded-2xl border border-slate-200 bg-white px-3 py-3 text-center text-sm font-black text-slate-600 transition peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 peer-checked:ring-2 peer-checked:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300 dark:peer-checked:bg-blue-950/30 dark:peer-checked:text-blue-300">

                                Izin

                            </div>

                        </label>


                        <label class="cursor-pointer">

                            <input type="radio"
                                   name="jenis"
                                   value="cuti"
                                   class="peer sr-only">

                            <div class="rounded-2xl border border-slate-200 bg-white px-3 py-3 text-center text-sm font-black text-slate-600 transition peer-checked:border-violet-500 peer-checked:bg-violet-50 peer-checked:text-violet-700 peer-checked:ring-2 peer-checked:ring-violet-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300 dark:peer-checked:bg-violet-950/30 dark:peer-checked:text-violet-300">

                                Cuti

                            </div>

                        </label>


                        <label class="cursor-pointer">

                            <input type="radio"
                                   name="jenis"
                                   value="sakit"
                                   class="peer sr-only">

                            <div class="rounded-2xl border border-slate-200 bg-white px-3 py-3 text-center text-sm font-black text-slate-600 transition peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 peer-checked:ring-2 peer-checked:ring-orange-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300 dark:peer-checked:bg-orange-950/30 dark:peer-checked:text-orange-300">

                                Sakit

                            </div>

                        </label>

                    </div>

                </div>


                {{-- DURASI --}}
                <div class="mt-5">

                    <label class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        Durasi
                    </label>


                    <select name="durasi_type"
                            id="modalDurasiType"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-blue-900/30"
                            required>

                        <option value="full_day">
                            Sehari Penuh / Beberapa Hari
                        </option>

                        <option value="hourly">
                            Beberapa Jam
                        </option>

                    </select>

                </div>


                {{-- DATE --}}
                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">


                    <div>

                        <label class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Tanggal Mulai
                        </label>

                        <input type="date"
                               name="tanggal_mulai"
                               id="modalTanggalMulai"
                               class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-blue-900/30"
                               required>

                    </div>


                    <div>

                        <label class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Tanggal Selesai
                        </label>

                        <input type="date"
                               name="tanggal_selesai"
                               id="modalTanggalSelesai"
                               class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-blue-900/30"
                               required>

                    </div>

                </div>


                {{-- HOURLY --}}
                <div id="modalHourlyFields"
                     class="mt-4 hidden grid-cols-1 gap-4 sm:grid-cols-2">


                    <div>

                        <label class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Jam Mulai
                        </label>

                        <input type="time"
                               name="jam_mulai"
                               class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-blue-900/30">

                    </div>


                    <div>

                        <label class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Jam Selesai
                        </label>

                        <input type="time"
                               name="jam_selesai"
                               class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-blue-900/30">

                    </div>

                </div>


                {{-- ALASAN --}}
                <div class="mt-5">

                    <div class="flex items-center justify-between gap-3">

                        <label class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Alasan
                        </label>

                        <span id="leaveReasonCounter"
                              class="text-[10px] font-bold text-slate-400">
                            0 / 2000
                        </span>

                    </div>


                    <textarea name="alasan"
                              id="leaveReason"
                              rows="5"
                              maxlength="2000"
                              placeholder="Tuliskan alasan pengajuan..."
                              class="w-full resize-none rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-blue-900/30"
                              required></textarea>

                </div>


                {{-- ATTACHMENT --}}
                <div class="mt-5">

                    <label class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        Lampiran
                    </label>


                    <label class="block cursor-pointer rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 transition hover:border-blue-400 hover:bg-blue-50/50 dark:border-slate-700 dark:bg-slate-800/50 dark:hover:border-blue-700">


                        <input type="file"
                               name="lampiran"
                               id="leaveAttachment"
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="hidden">


                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-blue-600 shadow-sm dark:bg-slate-900 dark:text-blue-300">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-5 w-5"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor"
                                     stroke-width="1.8">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M18.375 12.739l-7.693 7.693a4.125 4.125 0 01-5.833-5.833l9.193-9.193a2.625 2.625 0 113.712 3.712l-9.194 9.193a1.125 1.125 0 01-1.59-1.591l8.663-8.662" />

                                </svg>

                            </div>


                            <div class="min-w-0">

                                <div id="leaveAttachmentName"
                                     class="truncate text-sm font-bold text-slate-700 dark:text-slate-200">

                                    Pilih lampiran

                                </div>

                                <div class="mt-0.5 text-xs text-slate-400">
                                    PDF/JPG/PNG • Maksimal 5 MB
                                </div>

                            </div>

                        </div>

                    </label>

                </div>


                {{-- INFO --}}
                <div class="mt-5 rounded-2xl bg-blue-50 p-4 text-xs leading-5 text-blue-700 dark:bg-blue-950/30 dark:text-blue-300">

                    Setelah dikirim, pengajuan akan berstatus
                    <strong>Menunggu</strong>
                    sampai diproses oleh HRD.

                </div>

            </div>


            {{-- FOOTER --}}
            <div class="sticky bottom-0 z-20 flex flex-col-reverse gap-2 border-t border-slate-100 bg-white/95 p-4 backdrop-blur sm:flex-row sm:justify-end sm:p-5 dark:border-slate-800 dark:bg-slate-900/95">


                <button type="button"
                        data-close-create-leave
                        class="rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300">

                    Batal

                </button>


                <button type="submit"
                        id="submitLeaveRequest"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">

                    Kirim Pengajuan

                </button>

            </div>

        </form>

    </div>

</div>


{{-- ========================================================= --}}
{{-- CREATE SUCCESS TOAST --}}
{{-- ========================================================= --}}

<div id="employeePortalToast"
     class="pointer-events-none fixed right-4 top-4 z-[200] hidden max-w-sm translate-y-[-10px] rounded-2xl bg-slate-900 px-4 py-3 text-sm font-bold text-white opacity-0 shadow-2xl transition-all duration-300 dark:bg-white dark:text-slate-900">
</div>


<script>

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            const modal =
                document.getElementById(
                    'createLeaveModal'
                );

            const box =
                document.getElementById(
                    'createLeaveModalBox'
                );

            const form =
                document.getElementById(
                    'createLeaveForm'
                );

            const errorBox =
                document.getElementById(
                    'createLeaveError'
                );

            const durasi =
                document.getElementById(
                    'modalDurasiType'
                );

            const mulai =
                document.getElementById(
                    'modalTanggalMulai'
                );

            const selesai =
                document.getElementById(
                    'modalTanggalSelesai'
                );

            const hourly =
                document.getElementById(
                    'modalHourlyFields'
                );

            const submitButton =
                document.getElementById(
                    'submitLeaveRequest'
                );

            const reason =
                document.getElementById(
                    'leaveReason'
                );

            const reasonCounter =
                document.getElementById(
                    'leaveReasonCounter'
                );

            const attachment =
                document.getElementById(
                    'leaveAttachment'
                );

            const attachmentName =
                document.getElementById(
                    'leaveAttachmentName'
                );


            function syncBodyScroll() {

                const anyOpen =
                    document.querySelector(
                        '.fixed.inset-0.flex'
                    );

                document.body.classList.toggle(
                    'overflow-hidden',
                    !! anyOpen
                );
            }


            function openCreateLeave(
                jenis = ''
            ) {

                if (!modal || !box) {
                    return;
                }


                if (jenis) {

                    const radio =
                        form.querySelector(
                            `[name="jenis"][value="${jenis}"]`
                        );

                    if (radio) {
                        radio.checked = true;
                    }

                }


                errorBox?.classList.add(
                    'hidden'
                );


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


            function closeCreateLeave() {

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


            function syncDuration() {

                if (!durasi) {
                    return;
                }


                const isHourly =
                    durasi.value ===
                    'hourly';


                if (isHourly) {

                    hourly.classList.remove(
                        'hidden'
                    );

                    hourly.classList.add(
                        'grid'
                    );


                    if (mulai.value) {

                        selesai.value =
                            mulai.value;

                    }


                    selesai.readOnly =
                        true;

                } else {

                    hourly.classList.add(
                        'hidden'
                    );

                    hourly.classList.remove(
                        'grid'
                    );


                    selesai.readOnly =
                        false;

                }

            }


            document.addEventListener(
                'click',
                function (event) {

                    const open =
                        event.target.closest(
                            '[data-open-create-leave]'
                        );


                    if (open) {

                        openCreateLeave(
                            open.dataset.jenis
                            ?? ''
                        );

                        return;
                    }


                    const close =
                        event.target.closest(
                            '[data-close-create-leave]'
                        );


                    if (close) {

                        closeCreateLeave();

                    }

                }
            );


            modal?.addEventListener(
                'click',
                function (event) {

                    if (
                        event.target ===
                        modal
                    ) {
                        closeCreateLeave();
                    }

                }
            );


            document.addEventListener(
                'keydown',
                function (event) {

                    if (
                        event.key ===
                        'Escape'
                        &&
                        modal?.classList
                            .contains('flex')
                    ) {

                        closeCreateLeave();

                    }

                }
            );


            durasi?.addEventListener(
                'change',
                syncDuration
            );


            mulai?.addEventListener(
                'change',
                function () {

                    selesai.min =
                        mulai.value;


                    if (
                        durasi.value ===
                        'hourly'
                    ) {

                        selesai.value =
                            mulai.value;

                    }

                }
            );


            reason?.addEventListener(
                'input',
                function () {

                    reasonCounter.textContent =
                        `${reason.value.length} / 2000`;

                }
            );


            attachment?.addEventListener(
                'change',
                function () {

                    const file =
                        attachment.files?.[0];


                    attachmentName.textContent =
                        file
                            ? file.name
                            : 'Pilih lampiran';

                }
            );


            form?.addEventListener(
                'submit',
                async function (event) {

                    event.preventDefault();


                    errorBox.classList.add(
                        'hidden'
                    );

                    errorBox.innerHTML =
                        '';


                    const originalText =
                        submitButton.innerHTML;


                    submitButton.disabled =
                        true;

                    submitButton.innerHTML =
                        'Mengirim...';

                    submitButton.classList.add(
                        'opacity-70',
                        'cursor-not-allowed'
                    );


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

                            let messages =
                                [];


                            if (data.errors) {

                                messages =
                                    Object.values(
                                        data.errors
                                    ).flat();

                            }


                            if (
                                messages.length ===
                                0
                            ) {

                                messages.push(
                                    data.message
                                    ??
                                    'Pengajuan gagal dikirim.'
                                );

                            }


                            errorBox.innerHTML =
                                messages
                                    .map(
                                        message =>
                                            `<div>• ${escapeHtml(message)}</div>`
                                    )
                                    .join('');


                            errorBox.classList.remove(
                                'hidden'
                            );


                            errorBox.scrollIntoView({
                                behavior:
                                    'smooth',

                                block:
                                    'nearest',
                            });


                            return;
                        }


                        sessionStorage.setItem(
                            'employeePortalToast',
                            data.message
                            ??
                            'Pengajuan berhasil dikirim.'
                        );


                        window.location.reload();


                    } catch (error) {

                        errorBox.textContent =
                            'Tidak dapat mengirim pengajuan. Periksa koneksi dan coba lagi.';

                        errorBox.classList.remove(
                            'hidden'
                        );


                    } finally {

                        submitButton.disabled =
                            false;

                        submitButton.innerHTML =
                            originalText;

                        submitButton.classList.remove(
                            'opacity-70',
                            'cursor-not-allowed'
                        );

                    }

                }
            );


            function escapeHtml(value) {

                const div =
                    document.createElement(
                        'div'
                    );

                div.textContent =
                    String(value);

                return div.innerHTML;
            }


            /*
            |--------------------------------------------------------------------------
            | SESSION TOAST
            |--------------------------------------------------------------------------
            */

            const storedToast =
                sessionStorage.getItem(
                    'employeePortalToast'
                );


            if (storedToast) {

                sessionStorage.removeItem(
                    'employeePortalToast'
                );


                const toast =
                    document.getElementById(
                        'employeePortalToast'
                    );


                if (toast) {

                    toast.textContent =
                        storedToast;

                    toast.classList.remove(
                        'hidden'
                    );


                    setTimeout(
                        function () {

                            toast.classList.remove(
                                'opacity-0',
                                'translate-y-[-10px]'
                            );

                        },
                        20
                    );


                    setTimeout(
                        function () {

                            toast.classList.add(
                                'opacity-0',
                                'translate-y-[-10px]'
                            );


                            setTimeout(
                                () =>
                                    toast.classList.add(
                                        'hidden'
                                    ),
                                300
                            );

                        },
                        3500
                    );

                }

            }


            syncDuration();

        }
    );

</script>
