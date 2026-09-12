<x-app-layout>

    <div class="max-w-5xl mx-auto space-y-3 sm:space-y-4">

        @if (session('success'))
            <div
                id="successFlash"
                class="rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-sm font-bold text-emerald-800"
            >
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-sm font-bold text-rose-800"
            >
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-3">

            @forelse ($items as $item)

                @php
                    $kabagStatus = $item->kabag_status ?? 'pending';
                    $hrdStatus = $item->hrd_status ?? 'waiting';

                    $finalLabel = match (true) {
                        $item->status === 'cancelled'
                            => 'Dibatalkan',

                        $kabagStatus === 'rejected'
                            => 'Ditolak Kabag',

                        $hrdStatus === 'rejected'
                            => 'Ditolak HRD',

                        $kabagStatus === 'approved'
                            && $hrdStatus === 'approved'
                            => 'Disetujui',

                        $kabagStatus === 'approved'
                            && $hrdStatus === 'pending'
                            => 'Menunggu HRD',

                        default
                            => 'Menunggu Kabag',
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


                    $jenisBadge = match ($item->jenis) {
                        'cuti'
                            => 'bg-purple-50 text-purple-800 border-purple-200',

                        'sakit'
                            => 'bg-orange-50 text-orange-800 border-orange-200',

                        default
                            => 'bg-sky-50 text-sky-800 border-sky-200',
                    };


                    $modalId =
                        'leaveModal-' . $item->id;

                    $modalBoxId =
                        'leaveModalBox-' . $item->id;
                @endphp


                <button
                    type="button"
                    data-modal="{{ $modalId }}"
                    data-box="{{ $modalBoxId }}"
                    class="openLeaveModal w-full text-left rounded-2xl border border-[#e2e8f0] bg-white p-5 transition duration-150 hover:border-slate-400 shadow-xs hover:shadow-sm focus:outline-none"
                >

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">

                        {{-- KIRI --}}
                        <div class="space-y-2 min-w-0">

                            <div class="flex flex-wrap items-center gap-2">

                                <span class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug">
                                    {{ $item->employee?->nama ?? '-' }}
                                </span>

                                <span class="text-xs sm:text-sm text-slate-500 font-bold">
                                    ({{ $item->employee?->employee_code ?? '-' }})
                                </span>

                                <span class="rounded-lg px-2.5 py-0.5 text-xs font-extrabold uppercase tracking-wider border {{ $jenisBadge }}">
                                    {{ $item->jenis_label }}
                                </span>

                            </div>


                            <p class="text-xs sm:text-sm text-slate-600 font-medium">

                                <span class="font-bold text-slate-800">
                                    {{ $item->tanggal_mulai->format('d/m/Y') }}

                                    @if (
                                        $item->tanggal_mulai->toDateString()
                                        !==
                                        $item->tanggal_selesai->toDateString()
                                    )
                                        –
                                        {{ $item->tanggal_selesai->format('d/m/Y') }}
                                    @endif
                                </span>

                                •
                                {{ $item->alasan }}

                            </p>


                            {{-- MINI PROGRESS --}}
                            @if ($item->status !== 'cancelled')

                                <div class="flex flex-wrap items-center gap-2">

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

                                        Kabag

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


                        {{-- KANAN --}}
                        <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-[#e2e8f0]">

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
                        Belum ada pengajuan yang menunggu HRD.
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        Pengajuan akan muncul setelah disetujui oleh Kabag.
                    </p>

                </div>

            @endforelse

        </div>


        {{-- PAGINATION --}}
        @if ($items->hasPages())

            <div class="p-2">
                {{ $items->withQueryString()->links() }}
            </div>

        @endif

    </div>



    {{-- ============================================================= --}}
    {{-- MODAL DETAIL --}}
    {{-- ============================================================= --}}
    @foreach ($items as $item)

        @php
            $modalId =
                'leaveModal-' . $item->id;

            $modalBoxId =
                'leaveModalBox-' . $item->id;

            $imgModalId =
                'imagePreviewModal-' . $item->id;

            $imgModalBoxId =
                'imagePreviewModalBox-' . $item->id;


            $kabagStatus =
                $item->kabag_status ?? 'pending';

            $hrdStatus =
                $item->hrd_status ?? 'waiting';


            $kabagLabel = match ($kabagStatus) {
                'approved'
                    => 'Disetujui Kabag',

                'rejected'
                    => 'Ditolak Kabag',

                default
                    => 'Menunggu Kabag',
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
                    => 'Ditolak Kabag',

                $hrdStatus === 'rejected'
                    => 'Ditolak HRD',

                $kabagStatus === 'approved'
                    && $hrdStatus === 'approved'
                    => 'Disetujui',

                $kabagStatus === 'approved'
                    => 'Menunggu HRD',

                default
                    => 'Menunggu Kabag',
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
            class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-900/50 px-3 py-4 backdrop-blur-xs sm:px-4"
        >

            <div
                id="{{ $modalBoxId }}"
                class="max-h-[92vh] w-full max-w-lg scale-95 overflow-y-auto rounded-2xl border border-[#e2e8f0] bg-white opacity-0 shadow-2xl transition-all duration-200"
            >

                {{-- HEADER --}}
                <div class="sticky top-0 z-20 flex items-center justify-between border-b border-[#e2e8f0] bg-white/95 px-5 py-4 backdrop-blur">

                    <div>

                        <h3 class="text-base font-extrabold text-slate-900">
                            Review Pengajuan
                        </h3>

                        <p class="text-xs sm:text-sm text-slate-500 font-bold mt-0.5">
                            {{ $item->employee?->nama ?? '-' }}
                            ({{ $item->employee?->employee_code ?? '-' }})
                        </p>

                    </div>


                    <button
                        type="button"
                        data-modal="{{ $modalId }}"
                        data-box="{{ $modalBoxId }}"
                        class="closeLeaveModal p-1 rounded-lg text-slate-400 hover:text-slate-700"
                    >
                        ✕
                    </button>

                </div>


                <div class="p-5 space-y-4">

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
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-4">

                        <div class="mb-3">

                            <div class="text-[10px] font-extrabold uppercase tracking-[0.15em] text-slate-400">
                                Progress Persetujuan
                            </div>

                            <div class="mt-1 text-sm font-extrabold text-slate-900">
                                Kabag → HRD
                            </div>

                        </div>


                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                            {{-- ======================== --}}
                            {{-- KABAG --}}
                            {{-- ======================== --}}
                            <div class="rounded-xl border p-3 {{ $kabagClass }}">

                                <div class="flex items-start justify-between gap-3">

                                    <div>

                                        <div class="text-[10px] font-extrabold uppercase tracking-wider opacity-70">
                                            Kabag
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
                                                    ?? 'Kabag' }}
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
                                                    ?? 'Kabag' }}
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


                            {{-- ======================== --}}
                            {{-- HRD --}}
                            {{-- ======================== --}}
                            <div class="rounded-xl border p-3 {{ $hrdClass }}">

                                <div class="flex items-start justify-between gap-3">

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
                                                <strong>
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
                                                    ?? 'HRD' }}
                                            </strong>
                                        </div>


                                        @if ($item->hrd_rejected_at)

                                            <div>
                                                {{ $item->hrd_rejected_at
                                                    ->timezone('Asia/Jakarta')
                                                    ->format('d/m/Y H:i') }}
                                            </div>

                                        @endif


                                        @if ($item->hrd_rejection_reason)

                                            <div class="mt-1">
                                                Alasan:
                                                <strong>
                                                    {{ $item->hrd_rejection_reason }}
                                                </strong>
                                            </div>

                                        @endif


                                        @if ($item->hrd_action_source)

                                            <div class="mt-1">
                                                Melalui:
                                                <strong>
                                                    {{ $item->hrd_action_source === 'facelog'
                                                        ? 'FaceLog'
                                                        : 'Portal' }}
                                                </strong>
                                            </div>

                                        @endif

                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- ===================================================== --}}
                    {{-- INFO PENGAJUAN --}}
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

                                    –
                                    {{ $item->tanggal_selesai->format('d/m/Y') }}

                                @endif

                            </span>

                        </div>

                    </div>


                    {{-- ALASAN --}}
                    <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">

                        <span class="text-[11px] text-slate-500 font-bold uppercase block mb-1">
                            Alasan Pengajuan
                        </span>

                        <p class="text-slate-800 font-medium text-sm leading-relaxed">
                            {{ $item->alasan ?: '-' }}
                        </p>

                    </div>


                    {{-- ===================================================== --}}
                    {{-- LAMPIRAN --}}
                    {{-- ===================================================== --}}
                    @if ($item->lampiran_path)

                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">

                            <div class="min-w-0">

                                <span class="text-[11px] text-slate-500 font-bold uppercase block">
                                    Lampiran
                                </span>

                                <span class="text-slate-800 font-bold text-xs sm:text-sm truncate block max-w-[230px]">
                                    {{ $item->lampiran_original_name ?: 'Dokumen Lampiran' }}
                                </span>

                            </div>


                            <button
                                type="button"
                                data-modal="{{ $imgModalId }}"
                                data-box="{{ $imgModalBoxId }}"
                                class="openImageModal px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white font-extrabold text-xs shadow-xs transition"
                            >
                                Buka Dokumen
                            </button>

                        </div>

                    @endif


                    {{-- ===================================================== --}}
                    {{-- ACTION HRD --}}
                    {{-- ===================================================== --}}
                    @if (
                        $kabagStatus === 'approved'
                        &&
                        $hrdStatus === 'pending'
                        &&
                        $item->status === 'pending'
                    )

                        <div class="pt-3 border-t border-[#e2e8f0] space-y-3">

                            <div
                                id="actionError-{{ $item->id }}"
                                class="hidden rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs sm:text-sm font-bold text-rose-800"
                            ></div>


                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs font-semibold leading-5 text-amber-800">

                                Pengajuan ini sudah disetujui Kabag dan sekarang
                                menunggu keputusan final HRD.

                            </div>


                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                                {{-- REJECT --}}
                                <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#e2e8f0] space-y-2.5">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'hrd.leave-requests.reject',
                                            $item
                                        ) }}"
                                        data-leave-action-form
                                        data-type="reject"
                                        data-item="{{ $item->id }}"
                                    >

                                        @csrf


                                        <textarea
                                            name="rejection_reason"
                                            rows="2"
                                            placeholder="Alasan jika ditolak..."
                                            class="w-full rounded-xl border border-[#d1d5db] bg-white px-3 py-2 text-sm text-slate-800 focus:border-rose-400 focus:outline-none font-medium"
                                        ></textarea>


                                        <button
                                            type="submit"
                                            data-submit-button
                                            class="w-full mt-2 rounded-xl bg-rose-50 border border-rose-200 py-2.5 text-xs sm:text-sm font-extrabold text-rose-800 hover:bg-rose-100 transition"
                                        >
                                            Tolak
                                        </button>

                                    </form>

                                </div>


                                {{-- APPROVE --}}
                                <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#e2e8f0] flex flex-col justify-between">

                                    <p class="text-xs sm:text-sm text-slate-500 font-medium leading-5">
                                        Periksa data pengajuan dan persetujuan Kabag sebelum
                                        memberikan persetujuan final.
                                    </p>


                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'hrd.leave-requests.approve',
                                            $item
                                        ) }}"
                                        data-leave-action-form
                                        data-type="approve"
                                        data-item="{{ $item->id }}"
                                    >

                                        @csrf


                                        <button
                                            type="submit"
                                            data-submit-button
                                            class="w-full mt-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 py-2.5 text-xs sm:text-sm font-extrabold text-white transition shadow-xs"
                                        >
                                            Setujui
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    @elseif (
                        $kabagStatus === 'approved'
                        &&
                        $hrdStatus === 'approved'
                    )

                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">

                            <div class="font-extrabold">
                                Pengajuan telah mendapat persetujuan final HRD.
                            </div>

                            <div class="mt-1 text-xs leading-5">

                                Oleh:
                                <strong>
                                    {{ $item->hrdApprovedBy?->name
                                        ?? $item->approvedBy?->name
                                        ?? 'HRD' }}
                                </strong>

                                @if ($item->hrd_approved_at)

                                    •
                                    {{ $item->hrd_approved_at
                                        ->timezone('Asia/Jakarta')
                                        ->format('d/m/Y H:i') }}

                                @endif

                                @if ($item->hrd_action_source)

                                    •
                                    {{ $item->hrd_action_source === 'facelog'
                                        ? 'FaceLog'
                                        : 'Portal' }}

                                @endif

                            </div>

                        </div>


                    @elseif (
                        $kabagStatus === 'approved'
                        &&
                        $hrdStatus === 'rejected'
                    )

                        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900">

                            <div class="font-extrabold">
                                Pengajuan ditolak oleh HRD.
                            </div>

                            @if ($item->hrd_rejection_reason)

                                <div class="mt-1 text-xs leading-5">
                                    Alasan:
                                    <strong>
                                        {{ $item->hrd_rejection_reason }}
                                    </strong>
                                </div>

                            @endif

                        </div>

                    @endif

                </div>


                {{-- FOOTER --}}
                <div class="sticky bottom-0 z-20 flex justify-end border-t border-[#e2e8f0] bg-white/95 px-5 py-3.5 backdrop-blur">

                    <button
                        type="button"
                        data-modal="{{ $modalId }}"
                        data-box="{{ $modalBoxId }}"
                        class="closeLeaveModal rounded-xl border border-[#d1d5db] px-5 py-2 text-xs sm:text-sm font-extrabold text-slate-700 hover:bg-slate-50"
                    >
                        Tutup
                    </button>

                </div>

            </div>

        </div>



        {{-- ========================================================= --}}
        {{-- MODAL PREVIEW LAMPIRAN --}}
        {{-- ========================================================= --}}
        @if ($item->lampiran_path)

            <div
                id="{{ $imgModalId }}"
                class="fixed inset-0 z-[120] hidden items-center justify-center bg-slate-950/75 p-3 backdrop-blur-sm sm:p-6"
            >

                <div
                    id="{{ $imgModalBoxId }}"
                    class="relative max-h-[92vh] w-full max-w-3xl scale-95 overflow-hidden rounded-3xl bg-white opacity-0 shadow-2xl transition-all duration-200 flex flex-col"
                >

                    {{-- TOP BAR --}}
                    <div class="flex items-center justify-between border-b border-[#e2e8f0] bg-white px-5 py-3.5">

                        <span class="text-sm font-bold text-slate-800 truncate max-w-[80%]">
                            {{ $item->lampiran_original_name ?: 'Lampiran Pengajuan' }}
                        </span>


                        <button
                            type="button"
                            data-modal="{{ $imgModalId }}"
                            data-box="{{ $imgModalBoxId }}"
                            class="closeImageModal h-8 w-8 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center text-sm font-bold"
                        >
                            ✕
                        </button>

                    </div>


                    {{-- CONTENT --}}
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
    {{-- MODAL KONFIRMASI --}}
    {{-- ============================================================= --}}
    <div
        id="confirmActionModal"
        class="fixed inset-0 z-[150] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-xs"
    >

        <div
            id="confirmActionBox"
            class="w-full max-w-sm scale-95 overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 opacity-0 shadow-2xl transition-all duration-200"
        >

            <div class="flex flex-col items-center text-center">

                <div
                    id="confirmIconContainer"
                    class="flex h-12 w-12 items-center justify-center rounded-2xl"
                ></div>


                <h4
                    id="confirmModalTitle"
                    class="mt-4 text-base font-extrabold text-slate-900"
                >
                    Konfirmasi Tindakan
                </h4>


                <p
                    id="confirmModalMessage"
                    class="mt-1.5 text-xs sm:text-sm font-medium text-slate-600 leading-relaxed"
                ></p>

            </div>


            <div class="mt-6 flex items-center gap-2.5">

                <button
                    type="button"
                    id="cancelConfirmBtn"
                    class="w-1/2 rounded-xl border border-slate-200 bg-white py-2.5 text-xs sm:text-sm font-extrabold text-slate-700 transition hover:bg-slate-50 focus:outline-none"
                >
                    Batal
                </button>


                <button
                    type="button"
                    id="acceptConfirmBtn"
                    class="w-1/2 rounded-xl py-2.5 text-xs sm:text-sm font-extrabold text-white transition shadow-xs focus:outline-none"
                >
                    Ya, Lanjutkan
                </button>

            </div>

        </div>

    </div>



    {{-- ============================================================= --}}
    {{-- SCRIPT --}}
    {{-- ============================================================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            function openGenericModal(modalId, boxId) {

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


            function closeGenericModal(modalId, boxId) {

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

                    modal.classList.remove(
                        'flex'
                    );

                    modal.classList.add(
                        'hidden'
                    );


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

            function showConfirmDialog({
                title,
                message,
                type
            }) {

                return new Promise((resolve) => {

                    const modal =
                        document.getElementById(
                            'confirmActionModal'
                        );

                    const titleEl =
                        document.getElementById(
                            'confirmModalTitle'
                        );

                    const msgEl =
                        document.getElementById(
                            'confirmModalMessage'
                        );

                    const iconBox =
                        document.getElementById(
                            'confirmIconContainer'
                        );

                    const acceptBtn =
                        document.getElementById(
                            'acceptConfirmBtn'
                        );

                    const cancelBtn =
                        document.getElementById(
                            'cancelConfirmBtn'
                        );


                    titleEl.textContent =
                        title;

                    msgEl.textContent =
                        message;


                    if (
                        type === 'approve'
                    ) {

                        iconBox.className =
                            'flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600';


                        iconBox.innerHTML = `
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        `;


                        acceptBtn.className =
                            'w-1/2 rounded-xl bg-emerald-600 hover:bg-emerald-700 py-2.5 text-xs sm:text-sm font-extrabold text-white transition shadow-xs focus:outline-none';


                        acceptBtn.textContent =
                            'Ya, Setujui';

                    } else {

                        iconBox.className =
                            'flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600';


                        iconBox.innerHTML = `
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        `;


                        acceptBtn.className =
                            'w-1/2 rounded-xl bg-rose-600 hover:bg-rose-700 py-2.5 text-xs sm:text-sm font-extrabold text-white transition shadow-xs focus:outline-none';


                        acceptBtn.textContent =
                            'Ya, Tolak';
                    }


                    openGenericModal(
                        'confirmActionModal',
                        'confirmActionBox'
                    );


                    function handleAccept() {

                        cleanup();

                        closeGenericModal(
                            'confirmActionModal',
                            'confirmActionBox'
                        );

                        resolve(true);
                    }


                    function handleCancel() {

                        cleanup();

                        closeGenericModal(
                            'confirmActionModal',
                            'confirmActionBox'
                        );

                        resolve(false);
                    }


                    function cleanup() {

                        acceptBtn.removeEventListener(
                            'click',
                            handleAccept
                        );

                        cancelBtn.removeEventListener(
                            'click',
                            handleCancel
                        );
                    }


                    acceptBtn.addEventListener(
                        'click',
                        handleAccept
                    );

                    cancelBtn.addEventListener(
                        'click',
                        handleCancel
                    );

                });
            }

            document.addEventListener(
                'click',
                function (e) {

                    const openBtn =
                        e.target.closest(
                            '.openLeaveModal'
                        );


                    if (openBtn) {

                        openGenericModal(
                            openBtn.dataset.modal,
                            openBtn.dataset.box
                        );
                    }


                    const closeBtn =
                        e.target.closest(
                            '.closeLeaveModal'
                        );


                    if (closeBtn) {

                        closeGenericModal(
                            closeBtn.dataset.modal,
                            closeBtn.dataset.box
                        );
                    }


                    const openImg =
                        e.target.closest(
                            '.openImageModal'
                        );


                    if (openImg) {

                        openGenericModal(
                            openImg.dataset.modal,
                            openImg.dataset.box
                        );
                    }


                    const closeImg =
                        e.target.closest(
                            '.closeImageModal'
                        );


                    if (closeImg) {

                        closeGenericModal(
                            closeImg.dataset.modal,
                            closeImg.dataset.box
                        );
                    }

                }
            );

            document.addEventListener(
                'submit',
                async function (e) {

                    const form =
                        e.target.closest(
                            '[data-leave-action-form]'
                        );


                    if (!form) return;


                    e.preventDefault();


                    const type =
                        form.dataset.type;

                    const itemId =
                        form.dataset.item;

                    const submitBtn =
                        form.querySelector(
                            '[data-submit-button]'
                        );

                    const errorBox =
                        document.getElementById(
                            `actionError-${itemId}`
                        );

                    if (
                        type === 'reject'
                    ) {

                        const textarea =
                            form.querySelector(
                                '[name="rejection_reason"]'
                            );


                        if (
                            !textarea
                            ||
                            !textarea.value.trim()
                        ) {

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


                    const confirmed =
                        await showConfirmDialog({

                            title:
                                type === 'approve'
                                    ? 'Setujui Pengajuan?'
                                    : 'Tolak Pengajuan?',


                            message:
                                type === 'approve'

                                    ? 'Pengajuan ini akan mendapat persetujuan final HRD.'

                                    : 'Pengajuan ini akan ditolak oleh HRD sesuai alasan yang telah dimasukkan.',


                            type:
                                type,
                        });


                    if (!confirmed) {
                        return;
                    }


                    errorBox?.classList.add(
                        'hidden'
                    );


                    if (submitBtn) {

                        submitBtn.disabled =
                            true;

                        submitBtn.innerHTML =
                            'Memproses...';
                    }

                    form.submit();
                }
            );

        });
    </script>

</x-app-layout>
