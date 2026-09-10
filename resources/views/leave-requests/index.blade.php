<x-app-layout>
    <div class="max-w-5xl mx-auto space-y-3 sm:space-y-4">

        {{-- TOMBOL BUAT PENGAJUAN (LEBAR PENUH & JELAS) --}}
        <div>
            <button type="button" 
                    data-open-create-leave 
                    class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-950 hover:bg-black py-3.5 px-4 text-sm sm:text-base font-extrabold text-white shadow-xs transition active:scale-[0.99]">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="h-5 w-5 shrink-0 text-white" 
                     fill="none" 
                     viewBox="0 0 24 24" 
                     stroke="currentColor" 
                     stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat Pengajuan</span>
            </button>
        </div>

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
                @endphp

                <button type="button"
                        data-open-employee-leave
                        data-modal="employeeLeaveModal-{{ $item->id }}"
                        data-box="employeeLeaveModalBox-{{ $item->id }}"
                        class="w-full text-left rounded-2xl border border-[#e2e8f0] bg-white p-5 transition duration-150 hover:border-slate-400 shadow-xs hover:shadow-sm focus:outline-none">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                        <div class="space-y-1.5 min-w-0">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="rounded-lg px-2.5 py-1 text-xs font-extrabold uppercase tracking-wider border {{ $jenisBadge }}">
                                    {{ $item->jenis_label }}
                                </span>
                                
                                <span class="text-sm sm:text-base font-extrabold text-slate-900">
                                    {{ $item->tanggal_mulai->format('d M Y') }}
                                    @if ($item->tanggal_mulai->toDateString() !== $item->tanggal_selesai->toDateString())
                                        - {{ $item->tanggal_selesai->format('d M Y') }}
                                    @endif
                                </span>

                                @if ($item->durasi_type === 'hourly')
                                    <span class="text-xs sm:text-sm font-bold text-slate-500">
                                        ({{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }})
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs sm:text-sm text-slate-600 font-medium line-clamp-1 truncate max-w-xl">
                                {{ $item->alasan }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                            <span class="rounded-lg px-3 py-1 text-xs sm:text-sm font-extrabold capitalize border {{ $statusBadge }}">
                                {{ $item->status_label }}
                            </span>
                            
                            <span class="text-slate-400 font-bold text-lg">›</span>
                        </div>
                    </div>
                </button>
            @empty
                <div class="rounded-2xl border border-dashed border-[#d1d5db] p-8 text-center bg-white/70">
                    <p class="text-sm font-bold text-slate-700">Belum ada riwayat pengajuan</p>
                    <p class="text-xs text-slate-500 mt-1">Tekan tombol di atas untuk mengajukan izin, cuti, atau sakit.</p>
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

    {{-- MODAL --}}
    @include('leave-requests.partials.create-modal', ['employee' => $employee])
    @include('leave-requests.partials.detail-modals', ['employee' => $employee, 'requests' => $items])
</x-app-layout>
