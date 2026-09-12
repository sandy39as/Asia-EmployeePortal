<x-app-layout>

    <div class="max-w-6xl mx-auto space-y-5">

        {{-- HEADER --}}
        <div>
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-slate-400">
                Kabag
            </p>

            <h1 class="mt-1 text-xl sm:text-2xl font-extrabold text-slate-900">
                Persetujuan Pengajuan
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Pengajuan karyawan yang berada di bawah tanggung jawab
                {{ $kabag->name }}.
            </p>
        </div>


        {{-- FLASH --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-800">
                {{ session('error') }}
            </div>
        @endif


        {{-- LIST --}}
        <div class="space-y-3">

            @forelse ($items as $item)

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

                    $finalLabel = match (true) {
                        $item->status === 'cancelled' => 'Dibatalkan',
                        $kabagStatus === 'rejected' => 'Ditolak Kabag',
                        $hrdStatus === 'rejected' => 'Ditolak HRD',
                        $kabagStatus === 'approved' && $hrdStatus === 'approved' => 'Disetujui',
                        $kabagStatus === 'approved' => 'Menunggu HRD',
                        default => 'Menunggu Kabag',
                    };

                    $finalClass = match (true) {
                        $item->status === 'cancelled'
                            => 'border-slate-200 bg-slate-100 text-slate-700',

                        $kabagStatus === 'rejected' || $hrdStatus === 'rejected'
                            => 'border-rose-200 bg-rose-50 text-rose-800',

                        $kabagStatus === 'approved' && $hrdStatus === 'approved'
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
                @endphp


                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

                    <div class="p-5 space-y-4">

                        {{-- TOP --}}
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                            {{-- EMPLOYEE --}}
                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">

                                    <span class="text-base font-extrabold text-slate-900">
                                        {{ $item->employee?->nama ?? '-' }}
                                    </span>

                                    <span class="text-xs font-bold text-slate-500">
                                        ({{ $item->employee?->employee_code ?? '-' }})
                                    </span>

                                    <span class="rounded-lg border border-blue-200 bg-blue-50 px-2 py-0.5 text-[11px] font-extrabold uppercase text-blue-700">
                                        {{ $item->jenis_label }}
                                    </span>

                                </div>


                                <div class="mt-2 text-sm font-bold text-slate-700">

                                    {{ $item->tanggal_mulai->format('d/m/Y') }}

                                    @if (
                                        $item->tanggal_mulai->toDateString()
                                        !==
                                        $item->tanggal_selesai->toDateString()
                                    )
                                        –
                                        {{ $item->tanggal_selesai->format('d/m/Y') }}
                                    @endif

                                </div>


                                <div class="mt-1 text-sm text-slate-500">
                                    {{ $item->alasan ?: '-' }}
                                </div>

                            </div>


                            {{-- FINAL STATUS --}}
                            <div>

                                <span class="inline-flex rounded-xl border px-3 py-1.5 text-xs font-extrabold {{ $finalClass }}">
                                    {{ $finalLabel }}
                                </span>

                            </div>

                        </div>


                        {{-- PROGRESS APPROVAL --}}
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

                                        <div class="mt-2 text-xs leading-5">

                                            <div>
                                                Oleh:
                                                <strong>
                                                    {{ $item->kabagApprovedBy?->name ?? $kabag->name }}
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
                                                    {{ $item->kabagRejectedBy?->name ?? $kabag->name }}
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


                        {{-- APPROVAL ACTION --}}
                        @if ($kabagStatus === 'pending')

                            <div class="border-t border-slate-100 pt-4">

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                                    {{-- REJECT --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'kabag.leave-requests.reject',
                                            $item
                                        ) }}"
                                        class="rounded-xl border border-rose-100 bg-rose-50/40 p-3"
                                    >

                                        @csrf

                                        <textarea
                                            name="rejection_reason"
                                            rows="2"
                                            required
                                            placeholder="Alasan penolakan..."
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm"
                                        ></textarea>

                                        <button
                                            type="submit"
                                            onclick="return confirm('Tolak pengajuan ini?')"
                                            class="mt-2 w-full rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-extrabold text-rose-700 hover:bg-rose-100"
                                        >
                                            Tolak
                                        </button>

                                    </form>


                                    {{-- APPROVE --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'kabag.leave-requests.approve',
                                            $item
                                        ) }}"
                                        class="flex flex-col justify-between rounded-xl border border-emerald-100 bg-emerald-50/40 p-3"
                                    >

                                        @csrf

                                        <p class="text-xs leading-5 text-slate-500">
                                            Setujui jika data pengajuan karyawan sudah sesuai.
                                            Setelah disetujui, pengajuan akan diteruskan ke HRD.
                                        </p>

                                        <button
                                            type="submit"
                                            onclick="return confirm('Setujui pengajuan ini?')"
                                            class="mt-3 w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-extrabold text-white hover:bg-emerald-700"
                                        >
                                            Setujui
                                        </button>

                                    </form>

                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            @empty

                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">

                    <div class="font-extrabold text-slate-700">
                        Belum ada pengajuan.
                    </div>

                    <div class="mt-1 text-sm text-slate-500">
                        Pengajuan karyawan yang kamu tangani akan muncul di sini.
                    </div>

                </div>

            @endforelse

        </div>


        @if ($items->hasPages())
            <div>
                {{ $items->links() }}
            </div>
        @endif

    </div>

</x-app-layout>
