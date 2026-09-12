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

                    $badge = match ($kabagStatus) {
                        'approved' =>
                            'bg-emerald-50 text-emerald-700 border-emerald-200',

                        'rejected' =>
                            'bg-rose-50 text-rose-700 border-rose-200',

                        default =>
                            'bg-amber-50 text-amber-700 border-amber-200',
                    };

                    $statusLabel = match ($kabagStatus) {
                        'approved' => 'Disetujui Kabag',
                        'rejected' => 'Ditolak Kabag',
                        default => 'Menunggu Kabag',
                    };
                @endphp


                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

                    <div class="p-5">

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


                            {{-- STATUS --}}
                            <div>

                                <span class="inline-flex rounded-xl border px-3 py-1.5 text-xs font-extrabold {{ $badge }}">
                                    {{ $statusLabel }}
                                </span>

                            </div>

                        </div>


                        {{-- APPROVAL --}}
                        @if ($kabagStatus === 'pending')

                            <div class="mt-5 border-t border-slate-100 pt-4">

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
                                            Pengajuan akan diteruskan ke HRD untuk persetujuan final.
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

                        @elseif ($kabagStatus === 'approved')

                            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs font-bold text-emerald-800">

                                Disetujui oleh
                                {{ $item->kabagApprovedBy?->name ?? $kabag->name }}

                                @if ($item->kabag_approved_at)
                                    pada
                                    {{ $item->kabag_approved_at
                                        ->timezone('Asia/Jakarta')
                                        ->format('d/m/Y H:i') }}
                                @endif

                            </div>

                        @elseif ($kabagStatus === 'rejected')

                            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-bold text-rose-800">

                                <div>
                                    Pengajuan ditolak.
                                </div>

                                <div class="mt-1 font-medium">
                                    Alasan:
                                    {{ $item->kabag_rejection_reason ?: '-' }}
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
