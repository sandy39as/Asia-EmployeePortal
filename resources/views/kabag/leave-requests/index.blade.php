<x-app-layout>
    {{-- State Manajemen Modal Global Menggunakan Alpine.js --}}
    <div 
        x-data="{
            approveModalOpen: false,
            rejectModalOpen: false,
            actionUrl: '',
            targetName: '',
            rejectionReason: '',

            openApprove(url, name) {
                this.actionUrl = url;
                this.targetName = name;
                this.approveModalOpen = true;
            },
            openReject(url, name) {
                this.actionUrl = url;
                this.targetName = name;
                this.rejectionReason = '';
                this.rejectModalOpen = true;
            }
        }"
        class="mx-auto max-w-4xl space-y-4 px-3 sm:px-6 py-2"
    >
        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-slate-400">Kabag Portal</p>
                <h1 class="text-lg font-extrabold text-slate-900 sm:text-xl">Persetujuan Pengajuan</h1>
                <p class="text-xs text-slate-500">Karyawan di bawah tanggung jawab: <span class="font-bold text-slate-700">{{ $kabag->name }}</span></p>
            </div>
            <div class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg w-fit">
                Total: {{ $items->total() ?? $items->count() }} Pengajuan
            </div>
        </div>

        {{-- FLASH MESSAGES --}}
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-xs font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-xs font-bold text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        {{-- LIST PENGAJUAN --}}
        <div class="space-y-3">
            @forelse ($items as $item)
                @php
                    $kabagStatus = $item->kabag_status ?? 'pending';
                    $hrdStatus = $item->hrd_status ?? 'waiting';

                    $kabagLabel = match ($kabagStatus) {
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => 'Menunggu',
                    };

                    $hrdLabel = match ($hrdStatus) {
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'pending' => 'Menunggu',
                        default => 'Belum Masuk',
                    };

                    $finalLabel = match (true) {
                        $item->status === 'cancelled' => 'Dibatalkan',
                        $kabagStatus === 'rejected' => 'Ditolak Kabag',
                        $hrdStatus === 'rejected' => 'Ditolak HRD',
                        $kabagStatus === 'approved' && $hrdStatus === 'approved' => 'Disetujui Penuh',
                        $kabagStatus === 'approved' => 'Menunggu HRD',
                        default => 'Menunggu Kabag',
                    };

                    $finalClass = match (true) {
                        $item->status === 'cancelled' => 'border-slate-200 bg-slate-100 text-slate-600',
                        $kabagStatus === 'rejected' || $hrdStatus === 'rejected' => 'border-rose-200 bg-rose-50 text-rose-700',
                        $kabagStatus === 'approved' && $hrdStatus === 'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                        default => 'border-amber-200 bg-amber-50 text-amber-700',
                    };

                    $badgeKabagClass = match ($kabagStatus) {
                        'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                        'rejected' => 'border-rose-200 bg-rose-50 text-rose-700',
                        default => 'border-amber-200 bg-amber-50 text-amber-700',
                    };

                    $badgeHrdClass = match ($hrdStatus) {
                        'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                        'rejected' => 'border-rose-200 bg-rose-50 text-rose-700',
                        'pending' => 'border-amber-200 bg-amber-50 text-amber-700',
                        default => 'border-slate-200 bg-slate-50 text-slate-500',
                    };
                @endphp

                <div class="rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-slate-300">
                    <div class="p-3.5 sm:p-4 space-y-3">
                        {{-- BARIS 1: NAMA, TIPE PENGAJUAN, DAN STATUS AKHIR --}}
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="text-sm font-extrabold text-slate-900 truncate">
                                        {{ $item->employee?->nama ?? '-' }}
                                    </span>
                                    <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold text-slate-500">
                                        {{ $item->employee?->employee_code ?? '-' }}
                                    </span>
                                    
                                    {{-- Kategori / Sub-tipe Tag --}}
                                    @if ($item->jenis === 'izin')
                                        <span class="rounded border border-sky-200 bg-sky-50 px-1.5 py-0.5 text-[10px] font-extrabold text-sky-700">
                                            Izin: {{ $item->permissionType?->name ?? 'Umum' }}
                                        </span>
                                    @elseif ($item->jenis === 'cuti')
                                        <span class="rounded border border-violet-200 bg-violet-50 px-1.5 py-0.5 text-[10px] font-extrabold text-violet-700">
                                            @if ($item->leave_category === 'annual') Cuti Tahunan
                                            @elseif ($item->leave_category === 'special') {{ $item->specialLeaveType?->name ?? 'Cuti Khusus' }}
                                            @else Cuti @endif
                                        </span>
                                    @else
                                        <span class="rounded border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-extrabold uppercase text-blue-700">
                                            {{ $item->jenis_label }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Rentang Tanggal --}}
                                <div class="mt-1 flex items-center gap-1.5 text-xs font-bold text-slate-700">
                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>
                                        {{ $item->tanggal_mulai->format('d M Y') }}
                                        @if ($item->tanggal_mulai->toDateString() !== $item->tanggal_selesai->toDateString())
                                            — {{ $item->tanggal_selesai->format('d M Y') }}
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- Badge Status Utama --}}
                            <span class="shrink-0 rounded-lg border px-2 py-1 text-[10px] font-black uppercase tracking-wider {{ $finalClass }}">
                                {{ $finalLabel }}
                            </span>
                        </div>

                        {{-- BARIS 2: ALASAN PENGAJUAN --}}
                        <div class="rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-600">
                            <span class="font-bold text-slate-700">Alasan:</span> {{ $item->alasan ?: '-' }}
                        </div>

                        {{-- BARIS 3: TRACKING APPROVAL MINI (KABAG & HRD) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 border-t border-slate-100 pt-2.5 text-xs">
                            {{-- Step Kabag --}}
                            <div class="rounded-lg border p-2 flex items-start justify-between gap-2 {{ $badgeKabagClass }}">
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-wide opacity-75">1. Persetujuan Kabag</div>
                                    <div class="font-extrabold text-xs mt-0.5">{{ $kabagLabel }}</div>
                                    @if ($kabagStatus === 'approved')
                                        <div class="text-[10px] opacity-80 mt-0.5">
                                            Oleh: {{ $item->kabagApprovedBy?->name ?? $kabag->name }}
                                            @if($item->kabag_approved_at) • {{ $item->kabag_approved_at->timezone('Asia/Jakarta')->format('d/m H:i') }} @endif
                                        </div>
                                    @elseif ($kabagStatus === 'rejected')
                                        <div class="text-[10px] opacity-90 mt-0.5 font-medium">
                                            Alasan: "{{ $item->kabag_rejection_reason }}"
                                        </div>
                                    @endif
                                </div>
                                <span class="font-black text-sm">
                                    @if($kabagStatus === 'approved') ✓ @elseif($kabagStatus === 'rejected') ✕ @else … @endif
                                </span>
                            </div>

                            {{-- Step HRD --}}
                            <div class="rounded-lg border p-2 flex items-start justify-between gap-2 {{ $badgeHrdClass }}">
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-wide opacity-75">2. HRD Kantor</div>
                                    <div class="font-extrabold text-xs mt-0.5">{{ $hrdLabel }}</div>
                                    @if ($hrdStatus === 'approved')
                                        <div class="text-[10px] opacity-80 mt-0.5 truncate max-w-[180px]">
                                            Oleh: {{ $item->hrdApprovedBy?->name ?? $item->approvedBy?->name ?? 'HRD' }}
                                        </div>
                                    @elseif ($hrdStatus === 'rejected')
                                        <div class="text-[10px] opacity-90 mt-0.5 font-medium">
                                            Alasan: "{{ $item->hrd_rejection_reason }}"
                                        </div>
                                    @endif
                                </div>
                                <span class="font-black text-sm">
                                    @if($hrdStatus === 'approved') ✓ @elseif($hrdStatus === 'rejected') ✕ @elseif($hrdStatus === 'pending') … @else — @endif
                                </span>
                            </div>
                        </div>

                        {{-- BARIS 4: TOMBOL AKSI JIKA MASIH PENDING --}}
                        @if ($kabagStatus === 'pending')
                            <div class="border-t border-slate-100 pt-2.5 flex items-center justify-end gap-2">
                                <button
                                    type="button"
                                    @click="openReject('{{ route('kabag.leave-requests.reject', $item) }}', '{{ $item->employee?->nama }}')"
                                    class="flex-1 sm:flex-none rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100"
                                >
                                    Tolak
                                </button>
                                <button
                                    type="button"
                                    @click="openApprove('{{ route('kabag.leave-requests.approve', $item) }}', '{{ $item->employee?->nama }}')"
                                    class="flex-1 sm:flex-none rounded-lg bg-emerald-600 px-4 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700"
                                >
                                    Setujui
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
                    <div class="text-sm font-bold text-slate-700">Belum ada pengajuan.</div>
                    <div class="mt-1 text-xs text-slate-400">Pengajuan bawahan yang butuh persetujuan akan tampil di sini.</div>
                </div>
            @endforelse
        </div>

        @if ($items->hasPages())
            <div class="pt-2">
                {{ $items->links() }}
            </div>
        @endif

        {{-- ========================================================= --}}
        {{-- MODAL KONFIRMASI APPROVE --}}
        {{-- ========================================================= --}}
        <div 
            x-show="approveModalOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
        >
            <div 
                @click.outside="approveModalOpen = false"
                class="w-full max-w-sm rounded-2xl bg-white p-5 shadow-xl transition-all"
            >
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Setujui Pengajuan?</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pengajuan <span class="font-bold text-slate-800" x-text="targetName"></span> akan diteruskan ke HRD.</p>
                    </div>
                </div>

                <form :action="actionUrl" method="POST" class="mt-5 flex gap-2">
                    @csrf
                    <button 
                        type="button" 
                        @click="approveModalOpen = false"
                        class="w-1/2 rounded-xl border border-slate-200 bg-white py-2 text-xs font-bold text-slate-700 hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="w-1/2 rounded-xl bg-emerald-600 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700"
                    >
                        Ya, Setujui
                    </button>
                </form>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL KONFIRMASI REJECT --}}
        {{-- ========================================================= --}}
        <div 
            x-show="rejectModalOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
        >
            <div 
                @click.outside="rejectModalOpen = false"
                class="w-full max-w-sm rounded-2xl bg-white p-5 shadow-xl transition-all"
            >
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Tolak Pengajuan?</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Karyawan: <span class="font-bold text-slate-800" x-text="targetName"></span></p>
                    </div>
                </div>

                <form :action="actionUrl" method="POST" class="mt-4">
                    @csrf
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea 
                        name="rejection_reason" 
                        x-model="rejectionReason"
                        rows="3" 
                        required 
                        placeholder="Tulis alasan penolakan untuk karyawan..."
                        class="w-full rounded-xl border border-slate-300 p-2.5 text-xs focus:border-rose-500 focus:outline-none focus:ring-1 focus:ring-rose-500"
                    ></textarea>

                    <div class="mt-4 flex gap-2">
                        <button 
                            type="button" 
                            @click="rejectModalOpen = false"
                            class="w-1/2 rounded-xl border border-slate-200 bg-white py-2 text-xs font-bold text-slate-700 hover:bg-slate-50"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            :disabled="!rejectionReason.trim()"
                            class="w-1/2 rounded-xl bg-rose-600 py-2 text-xs font-bold text-white shadow-sm hover:bg-rose-700 disabled:opacity-50"
                        >
                            Tolak Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
