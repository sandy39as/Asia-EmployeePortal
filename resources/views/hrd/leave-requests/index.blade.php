<x-app-layout>
    <div class="max-w-5xl mx-auto space-y-3 sm:space-y-4">

        @if (session('success'))
            <div id="successFlash" class="rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- DAFTAR KARTU PENGAJUAN --}}
        <div class="space-y-3">
            @forelse ($items as $item)
                @php
                    $statusBadge = match($item->status) {
                        'approved' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                        'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
                        'cancelled' => 'bg-slate-100 text-slate-700 border-slate-200',
                        default => 'bg-amber-50 text-amber-800 border-amber-200',
                    };

                    $jenisBadge = match($item->jenis) {
                        'cuti' => 'bg-purple-50 text-purple-800 border-purple-200',
                        'sakit' => 'bg-orange-50 text-orange-800 border-orange-200',
                        default => 'bg-sky-50 text-sky-800 border-sky-200',
                    };

                    $modalId = 'leaveModal-' . $item->id;
                    $modalBoxId = 'leaveModalBox-' . $item->id;
                @endphp

                <button type="button"
                        data-modal="{{ $modalId }}"
                        data-box="{{ $modalBoxId }}"
                        class="openLeaveModal w-full text-left rounded-2xl border border-[#e2e8f0] bg-white p-5 transition duration-150 hover:border-slate-400 shadow-xs hover:shadow-sm focus:outline-none">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                        <div class="space-y-1.5 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug">{{ $item->employee?->nama ?? '-' }}</span>
                                <span class="text-xs sm:text-sm text-slate-500 font-bold">({{ $item->employee?->employee_code ?? '-' }})</span>
                                <span class="rounded-lg px-2.5 py-0.5 text-xs font-extrabold uppercase tracking-wider border {{ $jenisBadge }}">
                                    {{ $item->jenis_label }}
                                </span>
                            </div>

                            <p class="text-xs sm:text-sm text-slate-600 font-medium">
                                <span class="font-bold text-slate-800">
                                    {{ $item->tanggal_mulai->format('d/m/Y') }}@if ($item->tanggal_mulai->toDateString() !== $item->tanggal_selesai->toDateString()) – {{ $item->tanggal_selesai->format('d/m/Y') }}@endif
                                </span>
                                • {{ $item->alasan }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-[#e2e8f0]">
                            <span class="rounded-lg px-3 py-1 text-xs sm:text-sm font-extrabold capitalize border {{ $statusBadge }}">
                                {{ $item->status_label }}
                            </span>

                            <span class="text-slate-400 font-bold text-lg">›</span>
                        </div>
                    </div>
                </button>
            @empty
                <div class="rounded-2xl border border-dashed border-[#d1d5db] p-8 text-center bg-white/70">
                    <p class="text-sm font-bold text-slate-700">Belum ada data pengajuan karyawan.</p>
                </div>
            @endforelse
        </div>

        @if ($items->hasPages())
            <div class="p-2">
                {{ $items->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL DETAIL & REVIEW HRD --}}
    @foreach ($items as $item)
        @php
            $modalId = 'leaveModal-' . $item->id;
            $modalBoxId = 'leaveModalBox-' . $item->id;
            $imgModalId = 'imagePreviewModal-' . $item->id;
            $imgModalBoxId = 'imagePreviewModalBox-' . $item->id;

            $statusBadge = match($item->status) {
                'approved' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
                'cancelled' => 'bg-slate-100 text-slate-700 border-slate-200',
                default => 'bg-amber-50 text-amber-800 border-amber-200',
            };

            $isPdf = $item->lampiran_path && str_ends_with(strtolower($item->lampiran_path), '.pdf');
        @endphp

        <div id="{{ $modalId }}" class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-900/50 px-3 py-4 backdrop-blur-xs sm:px-4">
            <div id="{{ $modalBoxId }}" class="max-h-[92vh] w-full max-w-lg scale-95 overflow-y-auto rounded-2xl border border-[#e2e8f0] bg-white opacity-0 shadow-2xl transition-all duration-200">
                
                {{-- HEADER --}}
                <div class="sticky top-0 z-20 flex items-center justify-between border-b border-[#e2e8f0] bg-white/95 px-5 py-4 backdrop-blur">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Review Pengajuan</h3>
                        <p class="text-xs sm:text-sm text-slate-500 font-bold mt-0.5">{{ $item->employee?->nama ?? '-' }} ({{ $item->employee?->employee_code ?? '-' }})</p>
                    </div>
                    <button type="button" data-modal="{{ $modalId }}" data-box="{{ $modalBoxId }}" class="closeLeaveModal p-1 rounded-lg text-slate-400 hover:text-slate-700">✕</button>
                </div>

                <div class="p-5 space-y-4">
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
                                    – {{ $item->tanggal_selesai->format('d/m/Y') }}
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

                    <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                        <span class="text-[11px] text-slate-500 font-bold uppercase block mb-1">Alasan Pengajuan</span>
                        <p class="text-slate-800 font-medium text-sm leading-relaxed">{{ $item->alasan ?: '-' }}</p>
                    </div>

                    {{-- LAMPIRAN DENGAN PREVIEW MODAL --}}
                    @if ($item->lampiran_path)
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                            <span class="text-slate-800 font-bold text-xs sm:text-sm truncate max-w-[200px]">{{ $item->lampiran_original_name ?: 'Dokumen Lampiran' }}</span>
                            <button type="button" 
                                    data-modal="{{ $imgModalId }}" 
                                    data-box="{{ $imgModalBoxId }}" 
                                    class="openImageModal px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white font-extrabold text-xs shadow-xs transition">
                                Buka Dokumen
                            </button>
                        </div>
                    @endif

                    @if ($item->status === 'approved')
                        <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-sm font-semibold text-emerald-900">
                            Disetujui oleh: <strong class="font-extrabold">{{ $item->approvedBy?->name ?? 'HRD' }}</strong>
                            @if($item->approved_at) pada {{ $item->approved_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} @endif
                        </div>
                    @elseif ($item->status === 'rejected')
                        <div class="p-4 rounded-xl border border-rose-200 bg-rose-50 text-sm font-semibold text-rose-900">
                            Ditolak oleh: <strong class="font-extrabold">{{ $item->rejectedBy?->name ?? 'HRD' }}</strong><br>
                            Alasan: {{ $item->rejection_reason ?: '-' }}
                        </div>
                    @endif

                    {{-- FORM APPROVE / REJECT HRD --}}
                    @if ($item->status === 'pending')
                        <div class="pt-3 border-t border-[#e2e8f0] space-y-3">
                            <div id="actionError-{{ $item->id }}" class="hidden rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs sm:text-sm font-bold text-rose-800"></div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#e2e8f0] space-y-2.5">
                                    <form method="POST" action="{{ route('hrd.leave-requests.reject', $item) }}" data-leave-action-form data-type="reject" data-item="{{ $item->id }}">
                                        @csrf
                                        <textarea name="rejection_reason" rows="2" placeholder="Alasan jika ditolak..." class="w-full rounded-xl border border-[#d1d5db] bg-white px-3 py-2 text-sm text-slate-800 focus:border-rose-400 focus:outline-none font-medium"></textarea>
                                        <button type="submit" data-submit-button class="w-full mt-2 rounded-xl bg-rose-50 border border-rose-200 py-2.5 text-xs sm:text-sm font-extrabold text-rose-800 hover:bg-rose-100 transition">
                                            Tolak
                                        </button>
                                    </form>
                                </div>

                                <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#e2e8f0] flex flex-col justify-between">
                                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Periksa keabsahan data cuti/izin sebelum memberikan persetujuan.</p>
                                    <form method="POST" action="{{ route('hrd.leave-requests.approve', $item) }}" data-leave-action-form data-type="approve" data-item="{{ $item->id }}">
                                        @csrf
                                        <button type="submit" data-submit-button class="w-full mt-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 py-2.5 text-xs sm:text-sm font-extrabold text-white transition shadow-xs">
                                            Setujui
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                <div class="sticky bottom-0 z-20 flex justify-end border-t border-[#e2e8f0] bg-white/95 px-5 py-3.5 backdrop-blur">
                    <button type="button" data-modal="{{ $modalId }}" data-box="{{ $modalBoxId }}" class="closeLeaveModal rounded-xl border border-[#d1d5db] px-5 py-2 text-xs sm:text-sm font-extrabold text-slate-700 hover:bg-slate-50">
                        Tutup
                    </button>
                </div>

            </div>
        </div>

        {{-- MODAL KHUSUS PREVIEW GAMBAR / DOKUMEN (POP-UP DALAM HALAMAN DENGAN TOMBOL X) --}}
        @if ($item->lampiran_path)
            <div id="{{ $imgModalId }}" class="fixed inset-0 z-[120] hidden items-center justify-center bg-slate-950/75 p-3 backdrop-blur-sm sm:p-6">
                <div id="{{ $imgModalBoxId }}" class="relative max-h-[92vh] w-full max-w-3xl scale-95 overflow-hidden rounded-3xl bg-white opacity-0 shadow-2xl transition-all duration-200 flex flex-col">
                    
                    {{-- TOP BAR MODAL GAMBAR --}}
                    <div class="flex items-center justify-between border-b border-[#e2e8f0] bg-white px-5 py-3.5">
                        <span class="text-sm font-bold text-slate-800 truncate max-w-[80%]">{{ $item->lampiran_original_name ?: 'Lampiran Pengajuan' }}</span>
                        <button type="button" data-modal="{{ $imgModalId }}" data-box="{{ $imgModalBoxId }}" class="closeImageModal h-8 w-8 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center text-sm font-bold">
                            ✕
                        </button>
                    </div>

                    {{-- CONTENT GAMBAR / PDF --}}
                    <div class="flex-1 overflow-auto p-4 flex items-center justify-center bg-slate-50 min-h-[300px]">
                        @if ($isPdf)
                            <iframe src="{{ \Illuminate\Support\Facades\Storage::url($item->lampiran_path) }}" class="w-full h-[70vh] rounded-xl border border-slate-200"></iframe>
                        @else
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($item->lampiran_path) }}" 
                                 alt="Lampiran" 
                                 class="max-h-[75vh] w-auto max-w-full rounded-xl object-contain shadow-sm border border-slate-200">
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function openGenericModal(modalId, boxId) {
                const modal = document.getElementById(modalId);
                const box = document.getElementById(boxId);
                if (!modal || !box) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => box.classList.remove('scale-95', 'opacity-0'), 10);
                document.body.classList.add('overflow-hidden');
            }

            function closeGenericModal(modalId, boxId) {
                const modal = document.getElementById(modalId);
                const box = document.getElementById(boxId);
                if (!modal || !box) return;
                box.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                    const anyOpen = document.querySelector('.fixed.inset-0.flex');
                    if (!anyOpen) document.body.classList.remove('overflow-hidden');
                }, 180);
            }

            document.addEventListener('click', function (e) {
                const openBtn = e.target.closest('.openLeaveModal');
                if (openBtn) openGenericModal(openBtn.dataset.modal, openBtn.dataset.box);

                const closeBtn = e.target.closest('.closeLeaveModal');
                if (closeBtn) closeGenericModal(closeBtn.dataset.modal, closeBtn.dataset.box);

                // Preview Lampiran Trigger
                const openImg = e.target.closest('.openImageModal');
                if (openImg) openGenericModal(openImg.dataset.modal, openImg.dataset.box);

                const closeImg = e.target.closest('.closeImageModal');
                if (closeImg) closeGenericModal(closeImg.dataset.modal, closeImg.dataset.box);
            });

            document.addEventListener('submit', async function (e) {
                const form = e.target.closest('[data-leave-action-form]');
                if (!form) return;
                e.preventDefault();

                const type = form.dataset.type;
                const itemId = form.dataset.item;
                const submitBtn = form.querySelector('[data-submit-button]');
                const errorBox = document.getElementById(`actionError-${itemId}`);

                if (type === 'reject') {
                    const textarea = form.querySelector('[name="rejection_reason"]');
                    if (!textarea?.value.trim()) {
                        if (errorBox) {
                            errorBox.textContent = 'Alasan penolakan wajib diisi.';
                            errorBox.classList.remove('hidden');
                        }
                        textarea?.focus();
                        return;
                    }
                }

                if (!confirm(type === 'approve' ? 'Setujui pengajuan ini?' : 'Tolak pengajuan ini?')) return;

                errorBox?.classList.add('hidden');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Memproses...';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const data = await response.json();

                    if (!response.ok) throw new Error(data.message || 'Gagal memproses pengajuan.');

                    window.location.reload();
                } catch (err) {
                    if (errorBox) {
                        errorBox.textContent = err.message || 'Terjadi kesalahan.';
                        errorBox.classList.remove('hidden');
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        });
    </script>
</x-app-layout>
