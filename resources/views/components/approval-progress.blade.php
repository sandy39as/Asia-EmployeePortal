@props([
    'item',
    'compact' => false,
])

@php
    $kabagStatus = $item->kabag_status ?? 'pending';
    $hrdStatus = $item->hrd_status ?? 'waiting';

    $kabagLabel = match ($kabagStatus) {
        'approved' => 'Disetujui Kabag',
        'rejected' => 'Ditolak Kabag',
        default => 'Menunggu Kabag',
    };

    $hrdLabel = match ($hrdStatus) {
        'approved' => 'Disetujui HRD',
        'rejected' => 'Ditolak HRD',
        'pending' => 'Menunggu HRD',
        default => 'Belum Masuk HRD',
    };

    $kabagClass = match ($kabagStatus) {
        'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'rejected' => 'border-rose-200 bg-rose-50 text-rose-800',
        default => 'border-amber-200 bg-amber-50 text-amber-800',
    };

    $hrdClass = match ($hrdStatus) {
        'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'rejected' => 'border-rose-200 bg-rose-50 text-rose-800',
        'pending' => 'border-amber-200 bg-amber-50 text-amber-800',
        default => 'border-slate-200 bg-slate-50 text-slate-600',
    };

    $finalLabel = match (true) {
        $kabagStatus === 'rejected' => 'Ditolak Kabag',
        $hrdStatus === 'rejected' => 'Ditolak HRD',
        $kabagStatus === 'approved' && $hrdStatus === 'approved' => 'Disetujui',
        $kabagStatus === 'approved' => 'Menunggu HRD',
        default => 'Menunggu Kabag',
    };

    $finalClass = match (true) {
        $kabagStatus === 'rejected' || $hrdStatus === 'rejected'
            => 'border-rose-200 bg-rose-50 text-rose-800',

        $kabagStatus === 'approved' && $hrdStatus === 'approved'
            => 'border-emerald-200 bg-emerald-50 text-emerald-800',

        default
            => 'border-amber-200 bg-amber-50 text-amber-800',
    };
@endphp

<div class="rounded-2xl border border-slate-200 bg-white {{ $compact ? 'p-3' : 'p-4' }}">

    <div class="mb-3 flex items-center justify-between gap-3">
        <div>
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                Progress Persetujuan
            </div>

            <div class="mt-1 text-sm font-extrabold text-slate-900">
                {{ $finalLabel }}
            </div>
        </div>

        <span class="rounded-xl border px-3 py-1 text-xs font-extrabold {{ $finalClass }}">
            {{ $finalLabel }}
        </span>
    </div>


    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

        {{-- KABAG --}}
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

                <div class="mt-2 text-xs font-medium leading-5">

                    <div>
                        Oleh:
                        <span class="font-extrabold">
                            {{ $item->kabagApprovedBy?->name
                                ?? $item->kabag?->name
                                ?? 'Kabag' }}
                        </span>
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

                <div class="mt-2 text-xs font-medium leading-5">

                    <div>
                        Oleh:
                        <span class="font-extrabold">
                            {{ $item->kabagRejectedBy?->name
                                ?? $item->kabag?->name
                                ?? 'Kabag' }}
                        </span>
                    </div>

                    @if ($item->kabag_rejected_at)
                        <div>
                            {{ $item->kabag_rejected_at
                                ->timezone('Asia/Jakarta')
                                ->format('d/m/Y H:i') }}
                        </div>
                    @endif

                    @if ($item->kabag_rejection_reason)
                        <div class="mt-1">
                            Alasan:
                            <span class="font-bold">
                                {{ $item->kabag_rejection_reason }}
                            </span>
                        </div>
                    @endif

                </div>

            @endif

        </div>


        {{-- HRD --}}
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

                <div class="mt-2 text-xs font-medium leading-5">

                    <div>
                        Oleh:
                        <span class="font-extrabold">
                            {{ $item->hrdApprovedBy?->name
                                ?? $item->approvedBy?->name
                                ?? 'HRD' }}
                        </span>
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
                            <span class="font-extrabold capitalize">
                                {{ $item->hrd_action_source }}
                            </span>
                        </div>
                    @endif

                </div>

            @elseif ($hrdStatus === 'rejected')

                <div class="mt-2 text-xs font-medium leading-5">

                    <div>
                        Oleh:
                        <span class="font-extrabold">
                            {{ $item->hrdRejectedBy?->name
                                ?? $item->rejectedBy?->name
                                ?? 'HRD' }}
                        </span>
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
                            <span class="font-bold">
                                {{ $item->hrd_rejection_reason }}
                            </span>
                        </div>
                    @endif

                </div>

            @endif

        </div>

    </div>

</div>
