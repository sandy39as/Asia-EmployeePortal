<x-app-layout>

    @php
        /*
        |--------------------------------------------------------------------------
        | LABEL APPROVAL TAHAP PERTAMA
        |--------------------------------------------------------------------------
        |
        | Karyawan biasa: Kabag
        | Kabag: Atasan
        |
        */
        $firstApprovalLabel =
            auth()->user()?->isKabag()
                ? 'Atasan'
                : 'Kabag';
    @endphp

    {{-- ============================================================= --}}
    {{-- MOBILE MODAL FIX
         - 100dvh mengikuti viewport browser iPhone/Android
         - header & footer modal tetap terlihat
         - hanya isi modal yang scroll
         - safe-area notch/home indicator ikut dihitung
    {{-- ============================================================= --}}
    <style>
        #createLeaveModal,
        [id^="employeeLeaveModal-"],
        [id^="employeeDocModal-"] {
            height: 100vh;
            height: 100dvh;
        }

        #createLeaveModalScroll {
            scrollbar-gutter: stable;
        }

        @media (max-width: 639px) {
            #createLeaveModalBox {
                width: 100%;
            }

            /* Ketika keyboard iPhone/Android terbuka, scroll area tetap nyaman. */
            #createLeaveModalScroll {
                scroll-padding-bottom: 24px;
            }
        }
    </style>

    <div class="max-w-5xl mx-auto space-y-3 sm:space-y-4">

        {{-- ========================================================= --}}
        {{-- TOMBOL BUAT PENGAJUAN --}}
        {{-- ========================================================= --}}
        <div>
            <button
                type="button"
                data-open-create-leave
                class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-950 hover:bg-black py-3.5 px-4 text-sm sm:text-base font-extrabold text-white shadow-xs transition active:scale-[0.99]"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 shrink-0 text-white"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4v16m8-8H4"
                    />
                </svg>

                <span>Buat Pengajuan</span>
            </button>
        </div>


        {{-- ========================================================= --}}
        {{-- DAFTAR PENGAJUAN --}}
        {{-- ========================================================= --}}
        <div class="space-y-3">

            @forelse ($items as $item)

                @php
                    $kabagStatus = $item->kabag_status ?? 'pending';
                    $hrdStatus = $item->hrd_status ?? 'waiting';

                    /*
                    |--------------------------------------------------------------------------
                    | STATUS FINAL
                    |--------------------------------------------------------------------------
                    */
                    $finalLabel = match (true) {
                        $item->status === 'cancelled'
                            => 'Dibatalkan',

                        $kabagStatus === 'rejected'
                            => 'Ditolak ' . $firstApprovalLabel,

                        $hrdStatus === 'rejected'
                            => 'Ditolak HRD',

                        $kabagStatus === 'approved'
                            && $hrdStatus === 'approved'
                            => 'Disetujui',

                        $kabagStatus === 'approved'
                            => 'Menunggu HRD',

                        default
                            => 'Menunggu ' . $firstApprovalLabel,
                    };

                    $statusBadge = match (true) {
                        $item->status === 'cancelled'
                            => 'bg-slate-100 text-slate-700 border-slate-200',

                        $kabagStatus === 'rejected'
                            || $hrdStatus === 'rejected'
                            => 'bg-rose-50 text-rose-800 border-rose-200',

                        $kabagStatus === 'approved'
                            && $hrdStatus === 'approved'
                            => 'bg-emerald-50 text-emerald-800 border-emerald-200',

                        default
                            => 'bg-amber-50 text-amber-800 border-amber-200',
                    };


                    /*
                    |--------------------------------------------------------------------------
                    | JENIS
                    |--------------------------------------------------------------------------
                    */
                    $jenisBadge = match ($item->jenis) {
                        'cuti'
                            => 'bg-purple-50 text-purple-800 border-purple-200',

                        'sakit'
                            => 'bg-orange-50 text-orange-800 border-orange-200',

                        default
                            => 'bg-sky-50 text-sky-800 border-sky-200',
                    };
                @endphp


                <button
                    type="button"
                    data-open-employee-leave
                    data-modal="employeeLeaveModal-{{ $item->id }}"
                    data-box="employeeLeaveModalBox-{{ $item->id }}"
                    class="w-full text-left rounded-2xl border border-[#e2e8f0] bg-white p-5 transition duration-150 hover:border-slate-400 shadow-xs hover:shadow-sm focus:outline-none"
                >

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">

                        <div class="space-y-1.5 min-w-0">

                            <div class="flex flex-wrap items-center gap-2.5">

                                <span class="rounded-lg px-2.5 py-1 text-xs font-extrabold uppercase tracking-wider border {{ $jenisBadge }}">
                                    {{ $item->jenis_label }}
                                </span>

                                @if ($item->jenis === 'cuti')
                                    <span class="rounded-lg border border-violet-200 bg-violet-50 px-2.5 py-1 text-[10px] font-extrabold text-violet-700">
                                        @if ($item->leave_category === 'annual')
                                            Cuti Tahunan
                                        @elseif ($item->leave_category === 'special')
                                            {{ $item->specialLeaveType?->name ?? 'Cuti Khusus' }}
                                        @else
                                            Cuti
                                        @endif
                                    </span>
                                @endif

                                @if ($item->jenis === 'izin' && $item->permissionType)
                                    <span class="rounded-lg border border-sky-200 bg-sky-50 px-2.5 py-1 text-[10px] font-extrabold text-sky-700">
                                        {{ $item->permissionType->name }}
                                    </span>
                                @endif

                                <span class="text-sm sm:text-base font-extrabold text-slate-900">

                                    {{ $item->tanggal_mulai->format('d M Y') }}

                                    @if (
                                        $item->tanggal_mulai->toDateString()
                                        !==
                                        $item->tanggal_selesai->toDateString()
                                    )
                                        -
                                        {{ $item->tanggal_selesai->format('d M Y') }}
                                    @endif

                                </span>


                                @if ($item->durasi_type === 'hourly')

                                    <span class="text-xs sm:text-sm font-bold text-slate-500">

                                        (
                                        {{ substr($item->jam_mulai, 0, 5) }}
                                        -
                                        {{ substr($item->jam_selesai, 0, 5) }}
                                        )

                                    </span>

                                @endif

                            </div>


                            <p class="text-xs sm:text-sm text-slate-600 font-medium line-clamp-1 truncate max-w-xl">
                                {{ $item->alasan }}
                            </p>


                            {{-- STATUS MINI --}}
                            @if ($item->status !== 'cancelled')

                                <div class="flex items-center gap-2 pt-1">

                                    {{-- KABAG --}}
                                    <span
                                        class="inline-flex items-center gap-1 rounded-lg border px-2 py-0.5 text-[10px] font-extrabold
                                        {{
                                            $kabagStatus === 'approved'
                                                ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                                : (
                                                    $kabagStatus === 'rejected'
                                                        ? 'border-rose-200 bg-rose-50 text-rose-700'
                                                        : 'border-amber-200 bg-amber-50 text-amber-700'
                                                )
                                        }}"
                                    >

                                        @if ($kabagStatus === 'approved')
                                            ✓
                                        @elseif ($kabagStatus === 'rejected')
                                            ✕
                                        @else
                                            •
                                        @endif

                                        {{ $firstApprovalLabel }}

                                    </span>


                                    <span class="text-slate-300">
                                        →
                                    </span>


                                    {{-- HRD --}}
                                    <span
                                        class="inline-flex items-center gap-1 rounded-lg border px-2 py-0.5 text-[10px] font-extrabold
                                        {{
                                            $hrdStatus === 'approved'
                                                ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                                : (
                                                    $hrdStatus === 'rejected'
                                                        ? 'border-rose-200 bg-rose-50 text-rose-700'
                                                        : (
                                                            $hrdStatus === 'pending'
                                                                ? 'border-amber-200 bg-amber-50 text-amber-700'
                                                                : 'border-slate-200 bg-slate-50 text-slate-500'
                                                        )
                                                )
                                        }}"
                                    >

                                        @if ($hrdStatus === 'approved')
                                            ✓
                                        @elseif ($hrdStatus === 'rejected')
                                            ✕
                                        @elseif ($hrdStatus === 'pending')
                                            •
                                        @else
                                            —
                                        @endif

                                        HRD

                                    </span>

                                </div>

                            @endif

                        </div>


                        <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">

                            <span class="rounded-lg px-3 py-1 text-xs sm:text-sm font-extrabold border {{ $statusBadge }}">
                                {{ $finalLabel }}
                            </span>

                            <span class="text-slate-400 font-bold text-lg">
                                ›
                            </span>

                        </div>

                    </div>

                </button>


            @empty

                <div class="rounded-2xl border border-dashed border-[#d1d5db] p-8 text-center bg-white/70">

                    <p class="text-sm font-bold text-slate-700">
                        Belum ada riwayat pengajuan
                    </p>

                    <p class="text-xs text-slate-500 mt-1">
                        Tekan tombol di atas untuk mengajukan izin, cuti, atau sakit.
                    </p>

                </div>

            @endforelse

        </div>


        {{-- ========================================================= --}}
        {{-- PAGINATION --}}
        {{-- ========================================================= --}}
        @if ($items->hasPages())

            <div class="p-2">
                {{ $items->withQueryString()->links() }}
            </div>

        @endif

    </div>



    {{-- ============================================================= --}}
    {{-- MODAL BUAT PENGAJUAN --}}
    {{-- ============================================================= --}}
    <div
        id="createLeaveModal"
        class="fixed inset-0 z-[110] hidden h-[100dvh] min-h-0 items-start justify-center overflow-hidden bg-slate-900/50 px-3 backdrop-blur-xs sm:items-center sm:px-4"
        style="
            padding-top: max(12px, env(safe-area-inset-top));
            padding-bottom: max(12px, env(safe-area-inset-bottom));
        "
    >

        <div
            id="createLeaveModalBox"
            class="flex max-h-[calc(100dvh-24px)] min-h-0 w-full max-w-lg scale-95 flex-col overflow-hidden rounded-2xl border border-[#e2e8f0] bg-white opacity-0 shadow-2xl transition-all duration-200 sm:max-h-[92dvh]"
        >

            {{-- HEADER - SELALU TERLIHAT --}}
            <div class="z-20 flex shrink-0 items-center justify-between border-b border-[#e2e8f0] bg-white px-5 py-3.5 sm:py-4">

                <div>
                    <h3 class="text-base font-extrabold text-slate-900">
                        Buat Pengajuan
                    </h3>

                    <p class="text-xs text-slate-500 font-medium">
                        Izin, cuti, atau sakit.
                    </p>
                </div>

                <button
                    type="button"
                    data-close-create-leave
                    class="p-1 rounded-lg text-slate-400 hover:text-slate-700"
                >
                    ✕
                </button>

            </div>


            <form
                id="createLeaveForm"
                method="POST"
                action="{{ route('leave-requests.store') }}"
                enctype="multipart/form-data"
                class="flex min-h-0 flex-1 flex-col overflow-hidden"
            >

                @csrf


                {{-- HANYA ISI FORM YANG SCROLL --}}
                <div
                    id="createLeaveModalScroll"
                    class="min-h-0 flex-1 space-y-4 overflow-y-auto overscroll-contain p-4 sm:p-5"
                    style="-webkit-overflow-scrolling: touch;"
                >

                    {{-- INFO USER --}}
                    <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">

                        <div class="font-extrabold text-slate-900 text-sm sm:text-base">
                            {{ $employee->nama }}
                        </div>

                        <div class="text-xs text-slate-500 font-bold mt-0.5">
                            {{ $employee->employee_code }}
                            •
                            {{ $employee->jabatan ?? '-' }}
                        </div>

                    </div>


                    {{-- ERROR --}}
                    <div
                        id="createLeaveError"
                        class="hidden rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs sm:text-sm font-bold text-rose-800"
                    ></div>


                    {{-- JENIS --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                            Jenis Pengajuan
                        </label>


                        <div class="grid grid-cols-3 gap-2.5">

                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="jenis"
                                    value="izin"
                                    class="peer sr-only"
                                    required
                                >

                                <div class="rounded-xl border border-[#d1d5db] bg-white py-3 text-center text-sm font-extrabold text-slate-700 peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white transition">
                                    Izin
                                </div>

                            </label>


                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="jenis"
                                    value="cuti"
                                    class="peer sr-only"
                                >

                                <div class="rounded-xl border border-[#d1d5db] bg-white py-3 text-center text-sm font-extrabold text-slate-700 peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white transition">
                                    Cuti
                                </div>

                            </label>


                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="jenis"
                                    value="sakit"
                                    class="peer sr-only"
                                >

                                <div class="rounded-xl border border-[#d1d5db] bg-white py-3 text-center text-sm font-extrabold text-slate-700 peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white transition">
                                    Sakit
                                </div>

                            </label>

                        </div>

                    </div>


                    {{-- ===================================================== --}}
                    {{-- DETAIL JENIS IZIN --}}
                    {{-- ===================================================== --}}
                    <div
                        id="modalPermissionTypeFields"
                        class="hidden space-y-3 rounded-2xl border border-sky-200 bg-sky-50/50 p-4"
                    >
                        <div>
                            <div class="text-xs font-extrabold uppercase tracking-wider text-sky-700">
                                Jenis Izin
                            </div>

                            <div class="mt-1 text-xs font-medium text-slate-500">
                                Pilih jenis izin yang sesuai dengan pengajuan.
                            </div>
                        </div>


                        @if ($permissionTypes->isNotEmpty())

                            <select
                                name="permission_type_id"
                                id="modalPermissionType"
                                class="w-full rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:border-sky-500 focus:outline-none"
                            >
                                <option value="">
                                    -- Pilih Jenis Izin --
                                </option>

                                @foreach ($permissionTypes as $permissionType)
                                    <option
                                        value="{{ $permissionType->id }}"
                                        data-description="{{ $permissionType->description }}"
                                    >
                                        {{ $permissionType->name }}
                                    </option>
                                @endforeach
                            </select>


                            <div
                                id="modalPermissionTypeInfo"
                                class="hidden rounded-xl border border-sky-100 bg-white p-3 text-xs leading-5 text-slate-600"
                            ></div>

                        @else

                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs font-bold text-amber-700">
                                Belum ada master jenis izin aktif. Hubungi administrator/HRD.
                            </div>

                        @endif
                    </div>


                    {{-- ===================================================== --}}
                    {{-- DETAIL JENIS CUTI --}}
                    {{-- ===================================================== --}}
                    <div
                        id="modalLeaveCategoryFields"
                        class="hidden space-y-3 rounded-2xl border border-violet-200 bg-violet-50/50 p-4"
                    >

                        <div>
                            <div class="text-xs font-extrabold uppercase tracking-wider text-violet-700">
                                Jenis Cuti
                            </div>
                            <div class="mt-1 text-xs font-medium text-slate-500">
                                Cuti tahunan memakai saldo tahunan. Cuti khusus tidak memotong saldo tahunan.
                            </div>
                        </div>

                        <div class="space-y-2.5">

                            @if ($employee->isAsiaEmployee())
                                <label class="block cursor-pointer">
                                    <input
                                        type="radio"
                                        name="leave_category"
                                        value="annual"
                                        class="peer sr-only"
                                    >

                                    <div class="rounded-xl border border-slate-200 bg-white p-3.5 transition peer-checked:border-violet-500 peer-checked:ring-2 peer-checked:ring-violet-100">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <div class="text-sm font-extrabold text-slate-900">
                                                    Cuti Tahunan
                                                </div>
                                                <div class="mt-1 text-xs font-medium text-slate-500">
                                                    Mengurangi saldo setelah HRD menyetujui pengajuan.
                                                </div>
                                            </div>

                                            <div class="shrink-0 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-700">
                                                Sisa {{ $leaveBalance?->remaining ?? 0 }} hari
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @else
                                <div class="rounded-xl border border-slate-200 bg-white p-3.5">
                                    <div class="text-sm font-extrabold text-slate-500">
                                        Cuti Tahunan
                                    </div>
                                    <div class="mt-1 text-xs font-medium text-slate-500">
                                        Tidak tersedia untuk karyawan outsourcing.
                                    </div>
                                </div>
                            @endif


                            <label class="block cursor-pointer">
                                <input
                                    type="radio"
                                    name="leave_category"
                                    value="special"
                                    class="peer sr-only"
                                >

                                <div class="rounded-xl border border-slate-200 bg-white p-3.5 transition peer-checked:border-violet-500 peer-checked:ring-2 peer-checked:ring-violet-100">
                                    <div class="text-sm font-extrabold text-slate-900">
                                        Cuti Khusus
                                    </div>
                                    <div class="mt-1 text-xs font-medium text-slate-500">
                                        Tidak mengurangi saldo cuti tahunan.
                                    </div>
                                </div>
                            </label>

                        </div>


                        <div
                            id="modalSpecialLeaveTypeFields"
                            class="hidden"
                        >
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">
                                Jenis Cuti Khusus
                            </label>

                            <select
                                name="special_leave_type_id"
                                id="modalSpecialLeaveType"
                                class="w-full rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:border-violet-500 focus:outline-none"
                            >
                                <option value="">
                                    -- Pilih Cuti Khusus --
                                </option>

                                @foreach ($specialLeaveTypes as $specialLeaveType)
                                    <option
                                        value="{{ $specialLeaveType->id }}"
                                        data-default-days="{{ $specialLeaveType->default_days }}"
                                        data-description="{{ $specialLeaveType->description }}"
                                    >
                                        {{ $specialLeaveType->name }}
                                        ({{ $specialLeaveType->default_days }} hari)
                                    </option>
                                @endforeach
                            </select>

                            <div
                                id="modalSpecialLeaveInfo"
                                class="mt-2 hidden rounded-xl border border-violet-100 bg-white p-3 text-xs leading-5 text-slate-600"
                            ></div>
                        </div>


                        <div
                            id="modalAnnualLeaveInfo"
                            class="hidden rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs font-bold text-emerald-700"
                        >
                            Sisa cuti tahunan {{ now()->year }}:
                            {{ $leaveBalance?->remaining ?? 0 }} hari.
                            Saldo baru berkurang setelah HRD menyetujui.
                        </div>

                        <div
                            id="modalLeavePeriodInfo"
                            class="hidden rounded-xl border p-3 text-xs font-bold"
                        ></div>

                    </div>


                    {{-- DURASI --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Durasi
                        </label>

                        <select
                            name="durasi_type"
                            id="modalDurasiType"
                            class="w-full rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:border-slate-500 focus:outline-none"
                            required
                        >

                            <option value="full_day">
                                Sehari Penuh / Beberapa Hari
                            </option>

                            <option value="hourly">
                                Beberapa Jam
                            </option>

                        </select>

                    </div>


                    {{-- TANGGAL --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                        <div>

                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Tanggal Mulai
                            </label>

                            <input
                                type="date"
                                name="tanggal_mulai"
                                id="modalTanggalMulai"
                                class="w-full rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:border-slate-500 focus:outline-none font-medium"
                                required
                            >

                        </div>


                        <div>

                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Tanggal Selesai
                            </label>

                            <input
                                type="date"
                                name="tanggal_selesai"
                                id="modalTanggalSelesai"
                                class="w-full rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:border-slate-500 focus:outline-none font-medium"
                                required
                            >

                        </div>

                    </div>


                    {{-- JAM --}}
                    <div
                        id="modalHourlyFields"
                        class="hidden grid grid-cols-2 gap-3"
                    >

                        <div>

                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Jam Mulai
                            </label>

                            <input
                                type="time"
                                name="jam_mulai"
                                class="w-full rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:border-slate-500 focus:outline-none font-medium"
                            >

                        </div>


                        <div>

                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Jam Selesai
                            </label>

                            <input
                                type="time"
                                name="jam_selesai"
                                class="w-full rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:border-slate-500 focus:outline-none font-medium"
                            >

                        </div>

                    </div>


                    {{-- ALASAN --}}
                    <div>

                        <div class="flex justify-between items-center mb-1.5">

                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600">
                                Alasan
                            </label>

                            <span
                                id="leaveReasonCounter"
                                class="text-xs text-slate-400 font-bold"
                            >
                                0/2000
                            </span>

                        </div>


                        <textarea
                            name="alasan"
                            id="leaveReason"
                            rows="3"
                            maxlength="2000"
                            placeholder="Tulis alasan..."
                            class="w-full rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-slate-500 focus:outline-none font-medium"
                            required
                        ></textarea>

                    </div>


                    {{-- LAMPIRAN --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Lampiran (Opsional)
                        </label>


                        <label class="block cursor-pointer rounded-xl border border-dashed border-[#d1d5db] bg-[#f8fafc] p-3.5 hover:bg-slate-100 transition">

                            <input
                                type="file"
                                name="lampiran"
                                id="leaveAttachment"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="hidden"
                            >

                            <div class="flex items-center gap-3">

                                <span
                                    class="text-xs sm:text-sm font-bold text-slate-700"
                                    id="leaveAttachmentName"
                                >
                                    Pilih dokumen / foto
                                </span>

                                <span class="text-xs text-slate-400 font-semibold ml-auto">
                                    Maks 5MB
                                </span>

                            </div>

                        </label>

                    </div>

                </div>


                {{-- FOOTER - SELALU TERLIHAT --}}
                <div
                    class="z-20 flex shrink-0 justify-end gap-2 border-t border-[#e2e8f0] bg-white px-4 py-3 sm:px-5 sm:py-3.5"
                    style="padding-bottom: max(12px, env(safe-area-inset-bottom));"
                >

                    <button
                        type="button"
                        data-close-create-leave
                        class="rounded-xl border border-[#d1d5db] px-5 py-2 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50"
                    >
                        Batal
                    </button>


                    <button
                        type="submit"
                        id="submitLeaveRequest"
                        class="rounded-xl bg-slate-900 hover:bg-black px-6 py-2 text-xs sm:text-sm font-extrabold text-white transition"
                    >
                        Kirim Pengajuan
                    </button>

                </div>

            </form>

        </div>

    </div>



    {{-- ============================================================= --}}
    {{-- MODAL DETAIL PENGAJUAN --}}
    {{-- ============================================================= --}}
    @foreach ($items as $item)

        @php
            $modalId =
                'employeeLeaveModal-' . $item->id;

            $modalBoxId =
                'employeeLeaveModalBox-' . $item->id;

            $imgModalId =
                'employeeDocModal-' . $item->id;

            $imgModalBoxId =
                'employeeDocModalBox-' . $item->id;


            $kabagStatus =
                $item->kabag_status ?? 'pending';

            $hrdStatus =
                $item->hrd_status ?? 'waiting';


            $kabagLabel = match ($kabagStatus) {
                'approved'
                    => 'Disetujui ' . $firstApprovalLabel,

                'rejected'
                    => 'Ditolak ' . $firstApprovalLabel,

                default
                    => 'Menunggu ' . $firstApprovalLabel,
            };


            $hrdLabel = match ($hrdStatus) {
                'approved'
                    => 'Disetujui HRD',

                'rejected'
                    => 'Ditolak HRD',

                'pending'
                    => 'Menunggu HRD',

                default
                    => 'Belum Masuk HRD',
            };


            $finalLabel = match (true) {
                $item->status === 'cancelled'
                    => 'Dibatalkan',

                $kabagStatus === 'rejected'
                    => 'Ditolak ' . $firstApprovalLabel,

                $hrdStatus === 'rejected'
                    => 'Ditolak HRD',

                $kabagStatus === 'approved'
                    && $hrdStatus === 'approved'
                    => 'Disetujui',

                $kabagStatus === 'approved'
                    => 'Menunggu HRD',

                default
                    => 'Menunggu ' . $firstApprovalLabel,
            };


            $finalClass = match (true) {
                $item->status === 'cancelled'
                    => 'border-slate-200 bg-slate-100 text-slate-700',

                $kabagStatus === 'rejected'
                    || $hrdStatus === 'rejected'
                    => 'border-rose-200 bg-rose-50 text-rose-800',

                $kabagStatus === 'approved'
                    && $hrdStatus === 'approved'
                    => 'border-emerald-200 bg-emerald-50 text-emerald-800',

                default
                    => 'border-amber-200 bg-amber-50 text-amber-800',
            };


            $kabagClass = match ($kabagStatus) {
                'approved'
                    => 'border-emerald-200 bg-emerald-50 text-emerald-800',

                'rejected'
                    => 'border-rose-200 bg-rose-50 text-rose-800',

                default
                    => 'border-amber-200 bg-amber-50 text-amber-800',
            };


            $hrdClass = match ($hrdStatus) {
                'approved'
                    => 'border-emerald-200 bg-emerald-50 text-emerald-800',

                'rejected'
                    => 'border-rose-200 bg-rose-50 text-rose-800',

                'pending'
                    => 'border-amber-200 bg-amber-50 text-amber-800',

                default
                    => 'border-slate-200 bg-slate-50 text-slate-600',
            };


            $isPdf =
                $item->lampiran_path
                &&
                str_ends_with(
                    strtolower($item->lampiran_path),
                    '.pdf'
                );


            /*
            |--------------------------------------------------------------------------
            | URL LAMPIRAN
            |--------------------------------------------------------------------------
            */
            $lampiranPath =
                ltrim(
                    (string) $item->lampiran_path,
                    '/'
                );

            if (
                str_starts_with(
                    $lampiranPath,
                    'storage/'
                )
            ) {
                $lampiranPath =
                    substr(
                        $lampiranPath,
                        strlen('storage/')
                    );
            }

            if (
                str_starts_with(
                    $lampiranPath,
                    'uploads/'
                )
            ) {
                $lampiranPath =
                    substr(
                        $lampiranPath,
                        strlen('uploads/')
                    );
            }

            $lampiranUrl =
                $item->lampiran_path
                    ? asset(
                        'uploads/'
                        . $lampiranPath
                    )
                    : null;
        @endphp


        <div
            id="{{ $modalId }}"
            class="fixed inset-0 z-[110] hidden h-[100dvh] min-h-0 items-start justify-center overflow-hidden bg-slate-900/50 px-3 backdrop-blur-xs sm:items-center sm:px-4"
            style="
                padding-top: max(12px, env(safe-area-inset-top));
                padding-bottom: max(12px, env(safe-area-inset-bottom));
            "
        >

            <div
                id="{{ $modalBoxId }}"
                class="flex max-h-[calc(100dvh-24px)] min-h-0 w-full max-w-lg scale-95 flex-col overflow-hidden rounded-2xl border border-[#e2e8f0] bg-white opacity-0 shadow-2xl transition-all duration-200 sm:max-h-[92dvh]"
            >

                {{-- HEADER - SELALU TERLIHAT --}}
                <div class="z-20 flex shrink-0 items-center justify-between border-b border-[#e2e8f0] bg-white px-5 py-3.5 sm:py-4">

                    <div>

                        <h3 class="text-base font-extrabold text-slate-900">
                            Detail Pengajuan
                        </h3>

                        <p class="text-xs text-slate-500 font-bold mt-0.5">
                            {{ $item->uuid ?? 'Informasi cuti/izin' }}
                        </p>

                    </div>


                    <button
                        type="button"
                        data-close-detail
                        data-modal="{{ $modalId }}"
                        data-box="{{ $modalBoxId }}"
                        class="p-1 rounded-lg text-slate-400 hover:text-slate-700"
                    >
                        ✕
                    </button>

                </div>


                <div
                    class="min-h-0 flex-1 space-y-4 overflow-y-auto overscroll-contain p-4 sm:p-5"
                    style="-webkit-overflow-scrolling: touch;"
                >

                    {{-- ===================================================== --}}
                    {{-- STATUS FINAL --}}
                    {{-- ===================================================== --}}
                    <div class="rounded-2xl border p-4 {{ $finalClass }}">

                        <div class="text-[10px] font-extrabold uppercase tracking-wider opacity-70">
                            Status Pengajuan
                        </div>

                        <div class="mt-1 text-base font-extrabold">
                            {{ $finalLabel }}
                        </div>

                    </div>


                    {{-- ===================================================== --}}
                    {{-- PROGRESS APPROVAL --}}
                    {{-- ===================================================== --}}
                    @if ($item->status !== 'cancelled')

                        <div class="rounded-2xl border border-slate-200 bg-white p-4">

                            <div class="mb-3">

                                <div class="text-[10px] font-extrabold uppercase tracking-[0.15em] text-slate-400">
                                    Progress Persetujuan
                                </div>

                                <div class="mt-1 text-sm font-extrabold text-slate-900">
                                    {{ $firstApprovalLabel }} → HRD
                                </div>

                            </div>


                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                                {{-- KABAG --}}
                                <div class="rounded-xl border p-3 {{ $kabagClass }}">

                                    <div class="flex justify-between gap-3">

                                        <div>

                                            <div class="text-[10px] font-extrabold uppercase tracking-wider opacity-70">
                                                {{ $firstApprovalLabel }}
                                            </div>

                                            <div class="mt-1 text-sm font-extrabold">
                                                {{ $kabagLabel }}
                                            </div>

                                        </div>


                                        <div class="text-lg font-black">

                                            @if ($kabagStatus === 'approved')
                                                ✓
                                            @elseif ($kabagStatus === 'rejected')
                                                ✕
                                            @else
                                                …
                                            @endif

                                        </div>

                                    </div>


                                    @if ($kabagStatus === 'approved')

                                        <div class="mt-2 text-xs leading-5">

                                            <div>
                                                Oleh:
                                                <strong>
                                                    {{ $item->kabagApprovedBy?->name
                                                        ?? $item->kabag?->name
                                                        ?? $firstApprovalLabel }}
                                                </strong>
                                            </div>


                                            @if ($item->kabag_approved_at)

                                                <div>
                                                    {{ $item->kabag_approved_at
                                                        ->timezone('Asia/Jakarta')
                                                        ->format('d/m/Y H:i') }}
                                                </div>

                                            @endif

                                        </div>


                                    @elseif ($kabagStatus === 'rejected')

                                        <div class="mt-2 text-xs leading-5">

                                            <div>
                                                Oleh:
                                                <strong>
                                                    {{ $item->kabagRejectedBy?->name
                                                        ?? $item->kabag?->name
                                                        ?? $firstApprovalLabel }}
                                                </strong>
                                            </div>


                                            @if ($item->kabag_rejection_reason)

                                                <div class="mt-1">
                                                    Alasan:
                                                    <strong>
                                                        {{ $item->kabag_rejection_reason }}
                                                    </strong>
                                                </div>

                                            @endif

                                        </div>

                                    @endif

                                </div>


                                {{-- HRD --}}
                                <div class="rounded-xl border p-3 {{ $hrdClass }}">

                                    <div class="flex justify-between gap-3">

                                        <div>

                                            <div class="text-[10px] font-extrabold uppercase tracking-wider opacity-70">
                                                HRD
                                            </div>

                                            <div class="mt-1 text-sm font-extrabold">
                                                {{ $hrdLabel }}
                                            </div>

                                        </div>


                                        <div class="text-lg font-black">

                                            @if ($hrdStatus === 'approved')
                                                ✓
                                            @elseif ($hrdStatus === 'rejected')
                                                ✕
                                            @elseif ($hrdStatus === 'pending')
                                                …
                                            @else
                                                —
                                            @endif

                                        </div>

                                    </div>


                                    @if ($hrdStatus === 'approved')

                                        <div class="mt-2 text-xs leading-5">

                                            <div>
                                                Oleh:
                                                <strong>
                                                    {{ $item->hrdApprovedBy?->name
                                                        ?? $item->approvedBy?->name
                                                        ?? $item->external_approved_by_name
                                                        ?? 'HRD' }}
                                                </strong>
                                            </div>


                                            @if ($item->hrd_approved_at)

                                                <div>
                                                    {{ $item->hrd_approved_at
                                                        ->timezone('Asia/Jakarta')
                                                        ->format('d/m/Y H:i') }}
                                                </div>

                                            @endif


                                            @if ($item->hrd_action_source)

                                                <div>
                                                    Melalui:
                                                    <strong class="capitalize">
                                                        {{ $item->hrd_action_source === 'facelog'
                                                            ? 'FaceLog'
                                                            : 'Portal' }}
                                                    </strong>
                                                </div>

                                            @endif

                                        </div>


                                    @elseif ($hrdStatus === 'rejected')

                                        <div class="mt-2 text-xs leading-5">

                                            <div>
                                                Oleh:
                                                <strong>
                                                    {{ $item->hrdRejectedBy?->name
                                                        ?? $item->rejectedBy?->name
                                                        ?? $item->external_rejected_by_name
                                                        ?? 'HRD' }}
                                                </strong>
                                            </div>


                                            @if ($item->hrd_rejection_reason)

                                                <div class="mt-1">
                                                    Alasan:
                                                    <strong>
                                                        {{ $item->hrd_rejection_reason }}
                                                    </strong>
                                                </div>

                                            @endif

                                        </div>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @endif


                    {{-- ===================================================== --}}
                    {{-- INFO --}}
                    {{-- ===================================================== --}}
                    <div class="grid grid-cols-2 gap-2.5">

                        <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">

                            <span class="text-[11px] text-slate-500 font-bold uppercase block">
                                Jenis
                            </span>

                            <span class="text-slate-900 font-extrabold text-sm sm:text-base mt-1 block capitalize">
                                {{ $item->jenis_label }}
                            </span>

                        </div>


                        @if ($item->jenis === 'izin')
                            <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">

                                <span class="text-[11px] text-slate-500 font-bold uppercase block">
                                    Jenis Izin
                                </span>

                                <span class="text-slate-900 font-extrabold text-sm mt-1 block">
                                    {{ $item->permissionType?->name ?? '-' }}
                                </span>

                            </div>
                        @endif


                        @if ($item->jenis === 'cuti')
                            <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">

                                <span class="text-[11px] text-slate-500 font-bold uppercase block">
                                    Jenis Cuti
                                </span>

                                <span class="text-slate-900 font-extrabold text-sm mt-1 block">
                                    @if ($item->leave_category === 'annual')
                                        Cuti Tahunan
                                    @elseif ($item->leave_category === 'special')
                                        {{ $item->specialLeaveType?->name ?? 'Cuti Khusus' }}
                                    @else
                                        -
                                    @endif
                                </span>

                                @if ($item->leave_days)
                                    <span class="mt-1 block text-xs font-bold text-slate-500">
                                        {{ $item->leave_days }} hari
                                    </span>
                                @endif

                            </div>
                        @endif


                        <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">

                            <span class="text-[11px] text-slate-500 font-bold uppercase block">
                                Durasi
                            </span>

                            <span class="text-slate-800 font-bold text-xs sm:text-sm mt-1 block">

                                {{
                                    $item->durasi_type === 'hourly'
                                        ? substr($item->jam_mulai, 0, 5)
                                            . ' - '
                                            . substr($item->jam_selesai, 0, 5)
                                        : 'Sehari Penuh'
                                }}

                            </span>

                        </div>


                        <div class="col-span-2 p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">

                            <span class="text-[11px] text-slate-500 font-bold uppercase block">
                                Periode
                            </span>

                            <span class="text-slate-800 font-bold text-xs sm:text-sm mt-1 block">

                                {{ $item->tanggal_mulai->format('d/m/Y') }}

                                @if (
                                    $item->tanggal_mulai->toDateString()
                                    !==
                                    $item->tanggal_selesai->toDateString()
                                )

                                    -
                                    {{ $item->tanggal_selesai->format('d/m/Y') }}

                                @endif

                            </span>

                        </div>

                    </div>


                    {{-- ALASAN --}}
                    <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">

                        <span class="text-[11px] text-slate-500 font-bold uppercase block mb-1">
                            Alasan
                        </span>

                        <p class="text-slate-800 font-medium text-sm leading-relaxed">
                            {{ $item->alasan ?: '-' }}
                        </p>

                    </div>


                    {{-- LAMPIRAN --}}
                    @if ($item->lampiran_path)

                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">

                            <div class="truncate max-w-[230px]">

                                <span class="text-[11px] text-slate-500 font-bold uppercase block">
                                    Lampiran
                                </span>

                                <span class="text-slate-900 font-bold text-xs sm:text-sm truncate block">
                                    {{ $item->lampiran_original_name ?: 'Dokumen' }}
                                </span>

                            </div>


                            <button
                                type="button"
                                data-modal="{{ $imgModalId }}"
                                data-box="{{ $imgModalBoxId }}"
                                class="openDocModal px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white font-extrabold text-xs shadow-xs transition"
                            >
                                Lihat
                            </button>

                        </div>

                    @endif

                </div>


                {{-- FOOTER - SELALU TERLIHAT --}}
                <div
                    class="z-20 flex shrink-0 justify-between gap-2 border-t border-[#e2e8f0] bg-white px-4 py-3 sm:px-5 sm:py-3.5"
                    style="padding-bottom: max(12px, env(safe-area-inset-bottom));"
                >

                    <button
                        type="button"
                        data-close-detail
                        data-modal="{{ $modalId }}"
                        data-box="{{ $modalBoxId }}"
                        class="rounded-xl border border-[#d1d5db] px-5 py-2 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50"
                    >
                        Tutup
                    </button>


                    {{-- HANYA BISA BATAL SEBELUM KABAG MEMPROSES --}}
                    @if (
                        $item->status === 'pending'
                        &&
                        $kabagStatus === 'pending'
                    )

                        <form
                            method="POST"
                            action="{{ route(
                                'leave-requests.cancel',
                                $item
                            ) }}"
                        >

                            @csrf

                            <button
                                type="submit"
                                onclick="return confirm('Batalkan pengajuan ini?')"
                                class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-2 text-xs sm:text-sm font-extrabold text-rose-700 hover:bg-rose-100 transition"
                            >
                                Batalkan
                            </button>

                        </form>

                    @endif

                </div>

            </div>

        </div>



        {{-- ========================================================= --}}
        {{-- PREVIEW LAMPIRAN --}}
        {{-- ========================================================= --}}
        @if ($item->lampiran_path)

            <div
                id="{{ $imgModalId }}"
                class="fixed inset-0 z-[120] hidden h-[100dvh] min-h-0 items-start justify-center overflow-hidden bg-slate-950/75 px-3 backdrop-blur-sm sm:items-center sm:px-6"
                style="
                    padding-top: max(12px, env(safe-area-inset-top));
                    padding-bottom: max(12px, env(safe-area-inset-bottom));
                "
            >

                <div
                    id="{{ $imgModalBoxId }}"
                    class="relative flex max-h-[calc(100dvh-24px)] min-h-0 w-full max-w-2xl scale-95 flex-col overflow-hidden rounded-3xl bg-white opacity-0 shadow-2xl transition-all duration-200 sm:max-h-[92dvh]"
                >

                    <div class="flex items-center justify-between border-b border-[#e2e8f0] bg-white px-5 py-3.5">

                        <span class="text-sm font-bold text-slate-800 truncate max-w-[80%]">
                            {{ $item->lampiran_original_name ?: 'Lampiran Pengajuan' }}
                        </span>


                        <button
                            type="button"
                            data-modal="{{ $imgModalId }}"
                            data-box="{{ $imgModalBoxId }}"
                            class="closeDocModal h-8 w-8 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center text-sm font-bold"
                        >
                            ✕
                        </button>

                    </div>


                    <div class="flex-1 overflow-auto p-4 flex items-center justify-center bg-slate-50 min-h-[300px]">

                        @if ($isPdf)

                            <iframe
                                src="{{ $lampiranUrl }}"
                                class="w-full h-[70vh] rounded-xl border border-slate-200"
                            ></iframe>

                        @else

                            <img
                                src="{{ $lampiranUrl }}"
                                alt="Lampiran"
                                class="max-h-[75vh] w-auto max-w-full rounded-xl object-contain shadow-sm border border-slate-200"
                            >

                        @endif

                    </div>

                </div>

            </div>

        @endif

    @endforeach



    {{-- ============================================================= --}}
    {{-- TOAST --}}
    {{-- ============================================================= --}}
    <div
        id="employeePortalToast"
        class="pointer-events-none fixed right-4 top-4 z-[200] hidden max-w-sm rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-2xl transition duration-300"
    ></div>



    {{-- ============================================================= --}}
    {{-- SCRIPT --}}
    {{-- ============================================================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            /*
            |--------------------------------------------------------------------------
            | GENERIC MODAL
            |--------------------------------------------------------------------------
            */

            function openModal(modalId, boxId) {

                const modal =
                    document.getElementById(modalId);

                const box =
                    document.getElementById(boxId);

                if (!modal || !box) return;


                modal.classList.remove('hidden');

                modal.classList.add('flex');


                setTimeout(() => {

                    box.classList.remove(
                        'scale-95',
                        'opacity-0'
                    );

                }, 10);


                document.body.classList.add(
                    'overflow-hidden'
                );
            }


            function closeModal(modalId, boxId) {

                const modal =
                    document.getElementById(modalId);

                const box =
                    document.getElementById(boxId);

                if (!modal || !box) return;


                box.classList.add(
                    'scale-95',
                    'opacity-0'
                );


                setTimeout(() => {

                    modal.classList.remove('flex');

                    modal.classList.add('hidden');


                    const anyOpen =
                        document.querySelector(
                            '.fixed.inset-0.flex'
                        );


                    if (!anyOpen) {
                        document.body.classList.remove(
                            'overflow-hidden'
                        );
                    }

                }, 180);
            }



            /*
            |--------------------------------------------------------------------------
            | CREATE MODAL
            |--------------------------------------------------------------------------
            */

            const createModal =
                document.getElementById(
                    'createLeaveModal'
                );

            const createBox =
                document.getElementById(
                    'createLeaveModalBox'
                );


            document.addEventListener(
                'click',
                (e) => {

                    if (
                        e.target.closest(
                            '[data-open-create-leave]'
                        )
                    ) {
                        openModal(
                            'createLeaveModal',
                            'createLeaveModalBox'
                        );
                    }


                    if (
                        e.target.closest(
                            '[data-close-create-leave]'
                        )
                    ) {
                        closeModal(
                            'createLeaveModal',
                            'createLeaveModalBox'
                        );
                    }


                    if (
                        e.target === createModal
                    ) {
                        closeModal(
                            'createLeaveModal',
                            'createLeaveModalBox'
                        );
                    }

                }
            );



            /*
            |--------------------------------------------------------------------------
            | DETAIL MODAL
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                (e) => {

                    const trigger =
                        e.target.closest(
                            '[data-open-employee-leave]'
                        );


                    if (trigger) {

                        openModal(
                            trigger.dataset.modal,
                            trigger.dataset.box
                        );
                    }


                    const closeTrigger =
                        e.target.closest(
                            '[data-close-detail]'
                        );


                    if (closeTrigger) {

                        closeModal(
                            closeTrigger.dataset.modal,
                            closeTrigger.dataset.box
                        );
                    }


                    const openDoc =
                        e.target.closest(
                            '.openDocModal'
                        );


                    if (openDoc) {

                        openModal(
                            openDoc.dataset.modal,
                            openDoc.dataset.box
                        );
                    }


                    const closeDoc =
                        e.target.closest(
                            '.closeDocModal'
                        );


                    if (closeDoc) {

                        closeModal(
                            closeDoc.dataset.modal,
                            closeDoc.dataset.box
                        );
                    }

                }
            );



            /*
            |--------------------------------------------------------------------------
            | CREATE FORM
            |--------------------------------------------------------------------------
            */

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

            const submitBtn =
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

            const permissionTypeFields =
                document.getElementById(
                    'modalPermissionTypeFields'
                );

            const permissionTypeSelect =
                document.getElementById(
                    'modalPermissionType'
                );

            const permissionTypeInfo =
                document.getElementById(
                    'modalPermissionTypeInfo'
                );

            const leaveCategoryFields =
                document.getElementById(
                    'modalLeaveCategoryFields'
                );

            const specialLeaveFields =
                document.getElementById(
                    'modalSpecialLeaveTypeFields'
                );

            const specialLeaveSelect =
                document.getElementById(
                    'modalSpecialLeaveType'
                );

            const specialLeaveInfo =
                document.getElementById(
                    'modalSpecialLeaveInfo'
                );

            const annualLeaveInfo =
                document.getElementById(
                    'modalAnnualLeaveInfo'
                );

            const leavePeriodInfo =
                document.getElementById(
                    'modalLeavePeriodInfo'
                );

            const jenisRadios =
                document.querySelectorAll(
                    'input[name="jenis"]'
                );

            const leaveCategoryRadios =
                document.querySelectorAll(
                    'input[name="leave_category"]'
                );


            function parseDateOnly(value) {
                if (!value) return null;

                const parts = value.split('-').map(Number);

                if (parts.length !== 3) {
                    return null;
                }

                return new Date(
                    parts[0],
                    parts[1] - 1,
                    parts[2]
                );
            }


            function formatDateOnly(date) {
                if (!(date instanceof Date)) {
                    return '';
                }

                const year =
                    date.getFullYear();

                const month =
                    String(
                        date.getMonth() + 1
                    ).padStart(2, '0');

                const day =
                    String(
                        date.getDate()
                    ).padStart(2, '0');

                return `${year}-${month}-${day}`;
            }


            function addDaysToDate(
                dateString,
                days
            ) {
                const date =
                    parseDateOnly(
                        dateString
                    );

                if (!date) {
                    return '';
                }

                date.setDate(
                    date.getDate()
                    + Number(days || 0)
                );

                return formatDateOnly(
                    date
                );
            }


            function calculateInclusiveDays(
                startValue,
                endValue
            ) {
                const start =
                    parseDateOnly(
                        startValue
                    );

                const end =
                    parseDateOnly(
                        endValue
                    );

                if (!start || !end) {
                    return null;
                }

                const diff =
                    Math.round(
                        (
                            end.getTime()
                            -
                            start.getTime()
                        )
                        /
                        86400000
                    );

                return diff + 1;
            }


            function getSelectedSpecialMaxDays() {
                if (!specialLeaveSelect?.value) {
                    return null;
                }

                const option =
                    specialLeaveSelect
                        .options[
                            specialLeaveSelect.selectedIndex
                        ];

                const days =
                    Number(
                        option?.dataset
                            ?.defaultDays
                        || 0
                    );

                return days > 0
                    ? days
                    : null;
            }


            function applySpecialLeaveDateLimit(
                autoFix = false
            ) {
                const selectedJenis =
                    document.querySelector(
                        'input[name="jenis"]:checked'
                    )?.value;

                const selectedCategory =
                    document.querySelector(
                        'input[name="leave_category"]:checked'
                    )?.value;

                if (
                    selectedJenis !== 'cuti'
                    ||
                    selectedCategory !== 'special'
                    ||
                    !mulai?.value
                ) {
                    if (selesai) {
                        selesai.removeAttribute('max');
                    }

                    return;
                }

                const maxDays =
                    getSelectedSpecialMaxDays();

                if (!maxDays) {
                    selesai?.removeAttribute('max');
                    return;
                }

                const maxDate =
                    addDaysToDate(
                        mulai.value,
                        maxDays - 1
                    );

                if (selesai) {
                    selesai.min =
                        mulai.value;

                    selesai.max =
                        maxDate;

                    if (
                        !selesai.value
                        ||
                        selesai.value
                            <
                            mulai.value
                    ) {
                        selesai.value =
                            mulai.value;
                    }

                    if (
                        autoFix
                        &&
                        selesai.value
                            >
                            maxDate
                    ) {
                        selesai.value =
                            maxDate;
                    }

                    if (
                        maxDays === 1
                    ) {
                        selesai.value =
                            mulai.value;
                    }
                }
            }


            function refreshLeavePeriodInfo() {
                if (!leavePeriodInfo) {
                    return;
                }

                const selectedJenis =
                    document.querySelector(
                        'input[name="jenis"]:checked'
                    )?.value;

                const selectedCategory =
                    document.querySelector(
                        'input[name="leave_category"]:checked'
                    )?.value;

                if (
                    selectedJenis !== 'cuti'
                    ||
                    !mulai?.value
                    ||
                    !selesai?.value
                ) {
                    leavePeriodInfo.classList.add(
                        'hidden'
                    );

                    return;
                }

                const selectedDays =
                    calculateInclusiveDays(
                        mulai.value,
                        selesai.value
                    );

                if (
                    !selectedDays
                    ||
                    selectedDays < 1
                ) {
                    leavePeriodInfo.className =
                        'rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-bold text-rose-700';

                    leavePeriodInfo.textContent =
                        'Periode tanggal tidak valid.';

                    leavePeriodInfo.classList.remove(
                        'hidden'
                    );

                    return;
                }

                if (
                    selectedCategory === 'special'
                ) {
                    const maxDays =
                        getSelectedSpecialMaxDays();

                    if (!maxDays) {
                        leavePeriodInfo.classList.add(
                            'hidden'
                        );

                        return;
                    }

                    if (
                        selectedDays > maxDays
                    ) {
                        leavePeriodInfo.className =
                            'rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-bold text-rose-700';

                        leavePeriodInfo.innerHTML =
                            `Periode dipilih: <strong>${selectedDays} hari</strong>. `
                            +
                            `Melebihi batas maksimal <strong>${maxDays} hari</strong>.`;
                    } else {
                        leavePeriodInfo.className =
                            'rounded-xl border border-violet-200 bg-violet-50 p-3 text-xs font-bold text-violet-700';

                        leavePeriodInfo.innerHTML =
                            `Periode dipilih: <strong>${selectedDays} hari</strong> dari maksimal `
                            +
                            `<strong>${maxDays} hari</strong>.`;
                    }

                    leavePeriodInfo.classList.remove(
                        'hidden'
                    );

                    return;
                }

                if (
                    selectedCategory === 'annual'
                ) {
                    const remaining =
                        Number(
                            @json((int) ($leaveBalance?->remaining ?? 0))
                        );

                    if (
                        selectedDays > remaining
                    ) {
                        leavePeriodInfo.className =
                            'rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-bold text-rose-700';

                        leavePeriodInfo.innerHTML =
                            `Periode dipilih: <strong>${selectedDays} hari</strong>. `
                            +
                            `Sisa cuti tahunan hanya <strong>${remaining} hari</strong>.`;
                    } else {
                        leavePeriodInfo.className =
                            'rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs font-bold text-emerald-700';

                        leavePeriodInfo.innerHTML =
                            `Periode dipilih: <strong>${selectedDays} hari</strong>. `
                            +
                            `Sisa saldo saat ini <strong>${remaining} hari</strong>.`;
                    }

                    leavePeriodInfo.classList.remove(
                        'hidden'
                    );

                    return;
                }

                leavePeriodInfo.classList.add(
                    'hidden'
                );
            }


            function refreshPermissionTypeUI() {

                const selectedJenis =
                    document.querySelector(
                        'input[name="jenis"]:checked'
                    )?.value;


                if (
                    selectedJenis
                    === 'izin'
                ) {

                    permissionTypeFields
                        ?.classList
                        .remove(
                            'hidden'
                        );

                    if (
                        permissionTypeSelect
                    ) {
                        permissionTypeSelect.required =
                            true;
                    }

                } else {

                    permissionTypeFields
                        ?.classList
                        .add(
                            'hidden'
                        );

                    if (
                        permissionTypeSelect
                    ) {
                        permissionTypeSelect.required =
                            false;

                        permissionTypeSelect.value =
                            '';
                    }

                    if (
                        permissionTypeInfo
                    ) {
                        permissionTypeInfo.classList.add(
                            'hidden'
                        );

                        permissionTypeInfo.textContent =
                            '';
                    }
                }
            }


            function refreshLeaveCategoryUI() {

                const selectedJenis =
                    document.querySelector(
                        'input[name="jenis"]:checked'
                    )?.value;

                const selectedCategory =
                    document.querySelector(
                        'input[name="leave_category"]:checked'
                    )?.value;

                const hourlyOption =
                    durasi?.querySelector(
                        'option[value="hourly"]'
                    );


                if (selectedJenis === 'cuti') {

                    leaveCategoryFields?.classList.remove(
                        'hidden'
                    );

                    if (durasi) {
                        durasi.value = 'full_day';
                    }

                    hourly?.classList.add(
                        'hidden'
                    );

                    if (selesai) {
                        selesai.readOnly = false;
                    }

                    if (hourlyOption) {
                        hourlyOption.disabled = true;
                    }


                    if (!selectedCategory) {

                        const annualRadio =
                            document.querySelector(
                                'input[name="leave_category"][value="annual"]'
                            );

                        const specialRadio =
                            document.querySelector(
                                'input[name="leave_category"][value="special"]'
                            );

                        if (annualRadio) {
                            annualRadio.checked = true;
                        } else if (specialRadio) {
                            specialRadio.checked = true;
                        }
                    }

                } else {

                    leaveCategoryFields?.classList.add(
                        'hidden'
                    );

                    specialLeaveFields?.classList.add(
                        'hidden'
                    );

                    annualLeaveInfo?.classList.add(
                        'hidden'
                    );

                    leaveCategoryRadios.forEach(
                        radio => {
                            radio.checked = false;
                        }
                    );

                    if (specialLeaveSelect) {
                        specialLeaveSelect.value = '';
                        specialLeaveSelect.required = false;
                    }

                    if (hourlyOption) {
                        hourlyOption.disabled = false;
                    }
                }


                const currentCategory =
                    document.querySelector(
                        'input[name="leave_category"]:checked'
                    )?.value;


                if (
                    selectedJenis === 'cuti'
                    &&
                    currentCategory === 'special'
                ) {

                    specialLeaveFields?.classList.remove(
                        'hidden'
                    );

                    annualLeaveInfo?.classList.add(
                        'hidden'
                    );

                    if (specialLeaveSelect) {
                        specialLeaveSelect.required = true;
                    }

                } else if (
                    selectedJenis === 'cuti'
                    &&
                    currentCategory === 'annual'
                ) {

                    specialLeaveFields?.classList.add(
                        'hidden'
                    );

                    annualLeaveInfo?.classList.remove(
                        'hidden'
                    );

                    if (specialLeaveSelect) {
                        specialLeaveSelect.value = '';
                        specialLeaveSelect.required = false;
                    }

                } else {

                    specialLeaveFields?.classList.add(
                        'hidden'
                    );

                    annualLeaveInfo?.classList.add(
                        'hidden'
                    );

                    if (specialLeaveSelect) {
                        specialLeaveSelect.required = false;
                    }
                }

                applySpecialLeaveDateLimit(
                    true
                );

                refreshLeavePeriodInfo();
            }


            jenisRadios.forEach(
                radio => {
                    radio.addEventListener(
                        'change',
                        () => {
                            refreshPermissionTypeUI();
                            refreshPermissionTypeUI();
            refreshLeaveCategoryUI();
                        }
                    );
                }
            );


            leaveCategoryRadios.forEach(
                radio => {
                    radio.addEventListener(
                        'change',
                        refreshLeaveCategoryUI
                    );
                }
            );


            permissionTypeSelect?.addEventListener(
                'change',
                () => {

                    const option =
                        permissionTypeSelect
                            .options[
                                permissionTypeSelect.selectedIndex
                            ];

                    const description =
                        option?.dataset
                            ?.description;


                    if (
                        !permissionTypeSelect.value
                    ) {

                        permissionTypeInfo
                            ?.classList
                            .add(
                                'hidden'
                            );

                        if (
                            permissionTypeInfo
                        ) {
                            permissionTypeInfo.textContent =
                                '';
                        }

                        return;
                    }


                    if (
                        permissionTypeInfo
                    ) {

                        permissionTypeInfo.textContent =
                            description
                            || 'Jenis izin dipilih.';

                        permissionTypeInfo.classList.remove(
                            'hidden'
                        );
                    }

                }
            );


            specialLeaveSelect?.addEventListener(
                'change',
                () => {

                    const option =
                        specialLeaveSelect
                            .options[
                                specialLeaveSelect.selectedIndex
                            ];

                    const days =
                        option?.dataset
                            ?.defaultDays;

                    const description =
                        option?.dataset
                            ?.description;


                    if (
                        !specialLeaveSelect.value
                    ) {
                        specialLeaveInfo?.classList.add(
                            'hidden'
                        );

                        if (specialLeaveInfo) {
                            specialLeaveInfo.textContent = '';
                        }

                        selesai?.removeAttribute(
                            'max'
                        );

                        refreshLeavePeriodInfo();

                        return;
                    }


                    if (specialLeaveInfo) {

                        specialLeaveInfo.innerHTML =
                            `<strong>Maksimal ${days || '-'} hari.</strong>`
                            +
                            (
                                description
                                    ? ` ${description}`
                                    : ''
                            );

                        specialLeaveInfo.classList.remove(
                            'hidden'
                        );
                    }

                    applySpecialLeaveDateLimit(
                        true
                    );

                    refreshLeavePeriodInfo();

                }
            );


            durasi?.addEventListener(
                'change',
                () => {

                    const selectedJenis =
                        document.querySelector(
                            'input[name="jenis"]:checked'
                        )?.value;

                    if (
                        selectedJenis === 'cuti'
                    ) {
                        durasi.value = 'full_day';
                        hourly.classList.add('hidden');
                        selesai.readOnly = false;
                        return;
                    }

                    if (
                        durasi.value ===
                        'hourly'
                    ) {

                        hourly.classList.remove(
                            'hidden'
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

                        selesai.readOnly =
                            false;
                    }

                    applySpecialLeaveDateLimit(
                        true
                    );

                    refreshLeavePeriodInfo();

                }
            );


            mulai?.addEventListener(
                'change',
                () => {

                    selesai.min =
                        mulai.value;


                    if (
                        durasi.value ===
                        'hourly'
                    ) {
                        selesai.value =
                            mulai.value;
                    }


                    applySpecialLeaveDateLimit(
                        true
                    );

                    refreshLeavePeriodInfo();

                }
            );


            selesai?.addEventListener(
                'change',
                () => {

                    applySpecialLeaveDateLimit(
                        false
                    );

                    refreshLeavePeriodInfo();

                }
            );


            reason?.addEventListener(
                'input',
                () => {

                    reasonCounter.textContent =
                        `${reason.value.length}/2000`;

                }
            );


            attachment?.addEventListener(
                'change',
                () => {

                    attachmentName.textContent =
                        attachment.files?.[0]?.name
                        || 'Pilih dokumen / foto';

                }
            );



            refreshLeaveCategoryUI();


            /*
            |--------------------------------------------------------------------------
            | SUBMIT CREATE
            |--------------------------------------------------------------------------
            */

            form?.addEventListener(
                'submit',
                async (e) => {

                    e.preventDefault();


                    errorBox.classList.add(
                        'hidden'
                    );


                    const selectedJenis =
                        document.querySelector(
                            'input[name="jenis"]:checked'
                        )?.value;

                    const selectedCategory =
                        document.querySelector(
                            'input[name="leave_category"]:checked'
                        )?.value;

                    if (
                        selectedJenis === 'izin'
                        &&
                        (
                            !permissionTypeSelect
                            ||
                            !permissionTypeSelect.value
                        )
                    ) {

                        errorBox.innerHTML =
                            '<div>• Jenis izin wajib dipilih.</div>';

                        errorBox.classList.remove(
                            'hidden'
                        );

                        return;
                    }


                    if (
                        selectedJenis === 'cuti'
                        &&
                        selectedCategory === 'special'
                    ) {
                        const maxDays =
                            getSelectedSpecialMaxDays();

                        const selectedDays =
                            calculateInclusiveDays(
                                mulai?.value,
                                selesai?.value
                            );

                        if (
                            maxDays
                            &&
                            selectedDays
                            &&
                            selectedDays > maxDays
                        ) {
                            errorBox.innerHTML =
                                `<div>• Cuti khusus yang dipilih maksimal ${maxDays} hari. Periode yang dipilih adalah ${selectedDays} hari.</div>`;

                            errorBox.classList.remove(
                                'hidden'
                            );

                            refreshLeavePeriodInfo();

                            return;
                        }
                    }


                    const originalText =
                        submitBtn.innerHTML;


                    submitBtn.disabled =
                        true;

                    submitBtn.innerHTML =
                        'Mengirim...';


                    try {

                        const response =
                            await fetch(
                                form.action,
                                {
                                    method: 'POST',

                                    body:
                                        new FormData(
                                            form
                                        ),

                                    headers: {
                                        'Accept':
                                            'application/json',

                                        'X-Requested-With':
                                            'XMLHttpRequest'
                                    }
                                }
                            );


                        const data =
                            await response.json();


                        if (!response.ok) {

                            const messages =
                                data.errors

                                    ? Object.values(
                                        data.errors
                                    ).flat()

                                    : [
                                        data.message
                                        || 'Gagal mengirim pengajuan.'
                                    ];


                            errorBox.innerHTML =
                                messages
                                    .map(
                                        message =>
                                            `<div>• ${message}</div>`
                                    )
                                    .join('');


                            errorBox.classList.remove(
                                'hidden'
                            );


                            return;
                        }


                        sessionStorage.setItem(
                            'employeePortalToast',
                            data.message
                            || 'Pengajuan berhasil dikirim.'
                        );


                        window.location.reload();


                    } catch (err) {

                        errorBox.textContent =
                            'Terjadi kesalahan. Coba beberapa saat lagi.';


                        errorBox.classList.remove(
                            'hidden'
                        );


                    } finally {

                        submitBtn.disabled =
                            false;

                        submitBtn.innerHTML =
                            originalText;
                    }

                }
            );



            /*
            |--------------------------------------------------------------------------
            | TOAST
            |--------------------------------------------------------------------------
            */

            const toastMsg =
                sessionStorage.getItem(
                    'employeePortalToast'
                );


            if (toastMsg) {

                sessionStorage.removeItem(
                    'employeePortalToast'
                );


                const toast =
                    document.getElementById(
                        'employeePortalToast'
                    );


                toast.textContent =
                    toastMsg;


                toast.classList.remove(
                    'hidden'
                );


                setTimeout(
                    () => {
                        toast.classList.add(
                            'hidden'
                        );
                    },
                    3500
                );
            }

        });
    </script>

</x-app-layout>
