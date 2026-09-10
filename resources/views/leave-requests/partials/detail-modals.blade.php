@foreach ($requests as $item)
    @php
        $modalId = 'employeeLeaveModal-' . $item->id;
        $modalBoxId = 'employeeLeaveModalBox-' . $item->id;
        $imgModalId = 'employeeDocModal-' . $item->id;
        $imgModalBoxId = 'employeeDocModalBox-' . $item->id;

        $statusBadge = match($item->status) {
            'approved' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
            'cancelled' => 'bg-slate-100 text-slate-700 border-slate-200',
            default => 'bg-amber-50 text-amber-800 border-amber-200',
        };

        $isPdf = $item->lampiran_path && str_ends_with(strtolower($item->lampiran_path), '.pdf');
    @endphp

    {{-- MODAL DETAIL PENGAJUAN --}}
    <div id="{{ $modalId }}" class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-900/50 px-3 py-4 backdrop-blur-xs sm:px-4">
        <div id="{{ $modalBoxId }}" class="max-h-[92vh] w-full max-w-md scale-95 overflow-y-auto rounded-2xl border border-[#e2e8f0] bg-white opacity-0 shadow-2xl transition-all duration-200">
            
            {{-- HEADER --}}
            <div class="sticky top-0 z-20 flex items-center justify-between border-b border-[#e2e8f0] bg-white/95 px-5 py-4 backdrop-blur">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Detail Pengajuan</h3>
                    <p class="text-xs text-slate-500 font-bold mt-0.5">{{ $item->uuid ?? 'Informasi cuti/izin' }}</p>
                </div>
                <button type="button" data-close-detail data-modal="{{ $modalId }}" class="p-1 rounded-lg text-slate-400 hover:text-slate-700">✕</button>
            </div>

            <div class="p-5 space-y-4">
                
                {{-- STATUS & INFO GRID --}}
                <div class="grid grid-cols-2 gap-2.5">
                    <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                        <span class="text-[11px] text-slate-500 font-bold uppercase block">Status</span>
                        <span class="inline-block mt-1 rounded-md px-2.5 py-0.5 text-xs sm:text-sm font-extrabold border {{ $statusBadge }}">
                            {{ $item->status_label }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                        <span class="text-[11px] text-slate-500 font-bold uppercase block">Jenis</span>
                        <span class="text-slate-900 font-extrabold text-sm sm:text-base mt-1 block capitalize">{{ $item->jenis_label }}</span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                        <span class="text-[11px] text-slate-500 font-bold uppercase block">Periode</span>
                        <span class="text-slate-800 font-bold text-xs sm:text-sm mt-1 block">
                            {{ $item->tanggal_mulai->format('d/m/Y') }} 
                            @if($item->tanggal_mulai->toDateString() !== $item->tanggal_selesai->toDateString())
                                - {{ $item->tanggal_selesai->format('d/m/Y') }}
                            @endif
                        </span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                        <span class="text-[11px] text-slate-500 font-bold uppercase block">Durasi</span>
                        <span class="text-slate-800 font-bold text-xs sm:text-sm mt-1 block">
                            {{ $item->durasi_type === 'hourly' ? substr($item->jam_mulai,0,5).' - '.substr($item->jam_selesai,0,5) : 'Sehari Penuh' }}
                        </span>
                    </div>
                </div>

                {{-- ALASAN --}}
                <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                    <span class="text-[11px] text-slate-500 font-bold uppercase block mb-1">Alasan</span>
                    <p class="text-slate-800 font-medium text-sm leading-relaxed">{{ $item->alasan ?: '-' }}</p>
                </div>

                {{-- LAMPIRAN DENGAN TRIGGER MODAL PREVIEW --}}
                @if ($item->lampiran_path)
                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                        <div class="truncate max-w-[200px]">
                            <span class="text-[11px] text-slate-500 font-bold uppercase block">Lampiran</span>
                            <span class="text-slate-900 font-bold text-xs sm:text-sm truncate block">{{ $item->lampiran_original_name ?: 'Dokumen' }}</span>
                        </div>
                        <button type="button" 
                                data-modal="{{ $imgModalId }}" 
                                data-box="{{ $imgModalBoxId }}" 
                                class="openDocModal px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white font-extrabold text-xs shadow-xs transition">
                            Lihat
                        </button>
                    </div>
                @endif

                {{-- REJECTION REASON --}}
                @if ($item->status === 'rejected' && $item->rejection_reason)
                    <div class="p-4 rounded-xl border border-rose-200 bg-rose-50 text-sm font-semibold text-rose-900">
                        <span class="text-xs font-extrabold uppercase block text-rose-800 mb-1">Alasan Penolakan</span>
                        {{ $item->rejection_reason }}
                    </div>
                @endif
            </div>

            {{-- FOOTER --}}
            <div class="sticky bottom-0 z-20 flex justify-between gap-2 border-t border-[#e2e8f0] bg-white/95 px-5 py-3.5 backdrop-blur">
                <button type="button" data-close-detail data-modal="{{ $modalId }}" class="rounded-xl border border-[#d1d5db] px-5 py-2 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50">Tutup</button>
                
                @if ($item->status === 'pending')
                    <form method="POST" action="{{ route('leave-requests.cancel', $item) }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Batalkan pengajuan ini?')" class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-2 text-xs sm:text-sm font-extrabold text-rose-700 hover:bg-rose-100 transition">
                            Batalkan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL PREVIEW GAMBAR / PDF (POP-UP IN-PAGE DENGAN TOMBOL ✕) --}}
    @if ($item->lampiran_path)
        <div id="{{ $imgModalId }}" class="fixed inset-0 z-[120] hidden items-center justify-center bg-slate-950/75 p-3 backdrop-blur-sm sm:p-6">
            <div id="{{ $imgModalBoxId }}" class="relative max-h-[92vh] w-full max-w-2xl scale-95 overflow-hidden rounded-3xl bg-white opacity-0 shadow-2xl transition-all duration-200 flex flex-col">
                
                <div class="flex items-center justify-between border-b border-[#e2e8f0] bg-white px-5 py-3.5">
                    <span class="text-sm font-bold text-slate-800 truncate max-w-[80%]">{{ $item->lampiran_original_name ?: 'Lampiran Pengajuan' }}</span>
                    <button type="button" data-modal="{{ $imgModalId }}" data-box="{{ $imgModalBoxId }}" class="closeDocModal h-8 w-8 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center text-sm font-bold">
                        ✕
                    </button>
                </div>

                <div class="flex-1 overflow-auto p-4 flex items-center justify-center bg-slate-50 min-h-[300px]">
                    @php
                        $lampiranUrl = asset('uploads/' . ltrim($item->lampiran_path, '/'));
                    @endphp

                    @if ($isPdf)
                        <iframe
                            src="{{ $lampiranUrl }}"
                            class="w-full h-[70vh] rounded-xl border border-slate-200">
                        </iframe>
                    @else
                        <img
                            src="{{ $lampiranUrl }}"
                            alt="Lampiran"
                            class="max-h-[75vh] w-auto max-w-full rounded-xl object-contain shadow-sm border border-slate-200">
                    @endif
                </div>
            </div>
        </div>
    @endif
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', () => {
        function openDetailModal(modalId, boxId) {
            const modal = document.getElementById(modalId);
            const box = document.getElementById(boxId);
            if (!modal || !box) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => box.classList.remove('scale-95', 'opacity-0'), 10);
            document.body.classList.add('overflow-hidden');
        }

        function closeDetailModal(modalId) {
            const modal = document.getElementById(modalId);
            const box = modal?.querySelector('div[id$="Box-' + modalId.split('-')[1] + '"]') || modal?.firstElementChild;
            if (!modal) return;
            if (box) box.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                const anyOpen = document.querySelector('.fixed.inset-0.flex');
                if (!anyOpen) document.body.classList.remove('overflow-hidden');
            }, 180);
        }

        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-open-employee-leave]');
            if (trigger) openDetailModal(trigger.dataset.modal, trigger.dataset.box);

            const closeTrigger = e.target.closest('[data-close-detail]');
            if (closeTrigger) closeDetailModal(closeTrigger.dataset.modal);

            // Preview Doc Triggers
            const openDoc = e.target.closest('.openDocModal');
            if (openDoc) openDetailModal(openDoc.dataset.modal, openDoc.dataset.box);

            const closeDoc = e.target.closest('.closeDocModal');
            if (closeDoc) closeDetailModal(closeDoc.dataset.modal);
        });
    });
</script>
