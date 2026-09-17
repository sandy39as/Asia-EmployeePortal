<x-app-layout>
    @php
        $resolveArea = function ($deviceId) {
            return match ((int) $deviceId) {
                1, 2 => ['label' => 'Area 52', 'badge' => 'border-cyan-200 bg-cyan-50 text-cyan-700'],
                3 => ['label' => 'Area 27', 'badge' => 'border-indigo-200 bg-indigo-50 text-indigo-700'],
                default => ['label' => 'Area Lain', 'badge' => 'border-slate-200 bg-slate-50 text-slate-600'],
            };
        };
    @endphp

    <div class="mx-auto max-w-7xl space-y-4 sm:space-y-5">
        {{-- HEADER --}}
        <div>
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-slate-400">Master Data</p>
            <h1 class="mt-1 text-xl font-extrabold text-slate-900 sm:text-2xl">Mapping Kabag</h1>
        </div>

        {{-- FLASH MESSAGES --}}
        @if (session('success'))
            <div id="successFlash" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800 transition-all">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <div class="font-extrabold">Data belum dapat diproses.</div>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-xs font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- COMPACT SUMMARY --}}
        <div class="grid grid-cols-2 gap-2 sm:gap-3 sm:grid-cols-3 lg:grid-cols-6">
            <div class="rounded-xl border border-slate-200 bg-white p-2.5 sm:p-3 shadow-sm">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 truncate">Total Kabag</div>
                <div class="mt-0.5 text-lg font-bold text-slate-900 sm:text-xl">{{ $summary['total_kabag'] ?? 0 }}</div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-2.5 sm:p-3 shadow-sm">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 truncate">Total Karyawan</div>
                <div class="mt-0.5 text-lg font-bold text-slate-900 sm:text-xl">{{ $summary['total_employee'] ?? 0 }}</div>
            </div>

            <div class="rounded-xl border border-sky-200 bg-sky-50/70 p-2.5 sm:p-3 shadow-sm">
                <div class="text-[10px] font-bold uppercase tracking-wider text-sky-600 truncate">Kabag Ini</div>
                <div class="mt-0.5 text-lg font-bold text-sky-800 sm:text-xl">{{ $summary['mapped_to_selected'] ?? 0 }}</div>
            </div>

            <div class="rounded-xl border border-cyan-200 bg-cyan-50/70 p-2.5 sm:p-3 shadow-sm">
                <div class="text-[10px] font-bold uppercase tracking-wider text-cyan-600 truncate">Area 52</div>
                <div class="mt-0.5 text-lg font-bold text-cyan-800 sm:text-xl">{{ $summary['area_52'] ?? 0 }}</div>
            </div>

            <div class="rounded-xl border border-indigo-200 bg-indigo-50/70 p-2.5 sm:p-3 shadow-sm">
                <div class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 truncate">Area 27</div>
                <div class="mt-0.5 text-lg font-bold text-indigo-800 sm:text-xl">{{ $summary['area_27'] ?? 0 }}</div>
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-2.5 sm:p-3 shadow-sm">
                <div class="text-[10px] font-bold uppercase tracking-wider text-amber-700 truncate" title="Belum Punya Kabag Sama Sekali">Tanpa Kabag</div>
                <div class="mt-0.5 text-lg font-bold text-amber-800 sm:text-xl">{{ $summary['unmapped_all'] ?? 0 }}</div>
            </div>
        </div>

        {{-- PILIH KABAG --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="grid grid-cols-1 items-center gap-4 sm:grid-cols-2">
        
                {{-- Custom Dropdown (Alpine.js) --}}
                <div 
                    x-data="{ 
                        open: false,
                        selectedId: '{{ $selectedKabag?->id ?? '' }}',
                        selectKabag(id) {
                            if (id !== this.selectedId) {
                                window.location.href = '{{ route('master.kabag-mapping.index') }}?kabag_id=' + id;
                            }
                            this.open = false;
                        }
                    }" 
                    class="relative"
                    @click.outside="open = false"
                >
                    <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                        Pilih Kabag
                    </label>

                    {{-- Tombol Pemicu Dropdown --}}
                    <button
                        type="button"
                        @click="open = !open"
                        class="flex w-full items-center justify-between rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-left text-sm font-bold text-slate-800 shadow-sm transition hover:border-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                        <div class="flex items-center gap-2 truncate">
                            <span class="truncate">{{ $selectedKabag?->name ?? 'Pilih salah satu kabag...' }}</span>
                            @if($selectedKabag)
                                <span class="shrink-0 rounded-md bg-blue-50 px-2 py-0.5 text-[11px] font-extrabold text-blue-700">
                                    {{ $selectedKabag->managed_employees_count }} karyawan
                                </span>
                            @endif
                        </div>

                        <svg 
                            class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-200" 
                            :class="open ? 'rotate-180 text-blue-600' : ''"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Daftar Menu Dropdown --}}
                    <div
                        x-show="open"
                        x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute left-0 top-full z-50 mt-1.5 max-h-72 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl"
                    >
                        @forelse ($kabags as $kabag)
                            @php $isActive = $selectedKabag && $selectedKabag->id === $kabag->id; @endphp
                            <button
                                type="button"
                                @click="selectKabag('{{ $kabag->id }}')"
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-xs font-bold transition {{ $isActive ? 'bg-blue-50 text-blue-800' : 'text-slate-700 hover:bg-slate-100' }}"
                            >
                                <span class="truncate">{{ $kabag->name }}</span>
                                <span class="ml-2 shrink-0 rounded-full px-2 py-0.5 text-[10px] font-extrabold {{ $isActive ? 'bg-blue-200 text-blue-900' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $kabag->managed_employees_count }} karyawan
                                </span>
                            </button>
                        @empty
                            <div class="px-3 py-3 text-center text-xs text-slate-400">
                                Belum ada user role Kabag
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Info Panel Kanan --}}
                <div class="flex items-center rounded-xl border border-blue-100 bg-blue-50/60 p-3.5">
                    <p class="text-xs leading-relaxed text-blue-800">
                        Karyawan dapat dimasukkan ke beberapa Kabag sekaligus. Perubahan mapping pada halaman ini hanya berlaku untuk Kabag yang sedang aktif dipilih.
                    </p>
                </div>

            </div>
        </div>

        @if ($selectedKabag)
            <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
                {{-- KOLOM: SUDAH MASUK --}}
                <div class="overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-3 border-b border-emerald-100 bg-emerald-50/70 px-4 py-3 sm:px-5">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-emerald-600">Sudah Masuk</span>
                            <div class="text-sm font-extrabold text-emerald-900">Ditangani {{ $selectedKabag->name }}</div>
                        </div>
                        <span class="inline-flex min-w-8 items-center justify-center rounded-full bg-emerald-600 px-2.5 py-1 text-xs font-black text-white">
                            {{ $mappedEmployees->count() }}
                        </span>
                    </div>

                    <div class="max-h-[620px] divide-y divide-slate-100 overflow-y-auto">
                        @forelse ($mappedEmployees as $employee)
                            @php
                                $otherKabags = $employee->kabags->filter(fn ($k) => $k->id !== $selectedKabag->id);
                                $areaMeta = $resolveArea($employee->source_device_id);
                            @endphp
                            <div class="flex items-start gap-3 px-4 py-3.5 hover:bg-slate-50/50">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                        <span class="font-extrabold text-slate-900">{{ $employee->nama }}</span>
                                        <span class="rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] font-bold text-slate-500">
                                            {{ $employee->employee_code ?? '-' }}
                                        </span>
                                        @if ($employee->source_kategori_karyawan_name)
                                            <span class="rounded border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-extrabold text-blue-700">
                                                {{ $employee->source_kategori_karyawan_name }}
                                            </span>
                                        @endif
                                        <span class="rounded border px-1.5 py-0.5 text-[10px] font-extrabold {{ $areaMeta['badge'] }}">
                                            {{ $areaMeta['label'] }}
                                        </span>
                                    </div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $employee->jabatan ?: 'Jabatan belum diisi' }}
                                    </div>
                                    @if ($otherKabags->isNotEmpty())
                                        <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Juga ditangani:</span>
                                            @foreach ($otherKabags as $otherKabag)
                                                <span class="rounded border border-violet-200 bg-violet-50 px-1.5 py-0.5 text-[10px] font-bold text-violet-700">
                                                    {{ $otherKabag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <form
                                    method="POST"
                                    action="{{ route('master.kabag-mapping.remove', ['kabag' => $selectedKabag, 'employee' => $employee]) }}"
                                    onsubmit="return confirm('Lepas {{ $employee->nama }} dari {{ $selectedKabag->name }}?')"
                                    class="shrink-0"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-[11px] font-extrabold text-rose-700 hover:bg-rose-100">
                                        Lepas
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="px-5 py-12 text-center">
                                <div class="text-sm font-extrabold text-slate-700">Belum ada karyawan.</div>
                                <div class="mt-1 text-xs text-slate-500">Pilih dan tambahkan karyawan dari daftar di sebelah kanan.</div>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- KOLOM: BELUM MASUK --}}
                <div class="overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm">
                    <div class="border-b border-amber-100 bg-amber-50/70 p-4 sm:p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-amber-600">Belum Masuk</span>
                                <div class="text-sm font-extrabold text-amber-900">Tambahkan ke {{ $selectedKabag->name }}</div>
                            </div>
                            <span class="inline-flex min-w-8 items-center justify-center rounded-full bg-amber-500 px-2.5 py-1 text-xs font-black text-white">
                                {{ $availableEmployees->count() }}
                            </span>
                        </div>

                        {{-- FILTER + LIVE SEARCH --}}
                        <div class="mt-3 space-y-2">
                            <input
                                type="text"
                                id="liveAvailableSearch"
                                value="{{ $search }}"
                                placeholder="Cari nama / ID / jabatan..."
                                autocomplete="off"
                                class="w-full rounded-xl border border-amber-200 bg-white px-3 py-2 text-xs text-slate-800 placeholder:text-slate-400 focus:border-amber-400 focus:outline-none"
                            >

                            <form
                                method="GET"
                                action="{{ route('master.kabag-mapping.index') }}"
                                id="availableEmployeeFilterForm"
                                class="grid grid-cols-1 gap-2 sm:grid-cols-12"
                            >
                                <input type="hidden" name="kabag_id" value="{{ $selectedKabag->id }}">

                                <div class="sm:col-span-5">
                                    <select
                                        name="category"
                                        onchange="this.form.submit()"
                                        class="w-full rounded-xl border border-amber-200 bg-white px-3 py-2 text-xs text-slate-800 focus:border-amber-400 focus:outline-none"
                                    >
                                        <option value="">Semua Bagian</option>
                                        @foreach ($categories as $categoryOption)
                                            <option value="{{ $categoryOption }}" @selected($category === $categoryOption)>{{ $categoryOption }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="sm:col-span-5">
                                    <select
                                        name="area"
                                        onchange="this.form.submit()"
                                        class="w-full rounded-xl border border-amber-200 bg-white px-3 py-2 text-xs text-slate-800 focus:border-amber-400 focus:outline-none"
                                    >
                                        <option value="">Semua Area</option>
                                        <option value="52" @selected(($area ?? '') === '52')>Area 52</option>
                                        <option value="27" @selected(($area ?? '') === '27')>Area 27</option>
                                        <option value="other" @selected(($area ?? '') === 'other')>Area Lain</option>
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <a
                                        href="{{ route('master.kabag-mapping.index', ['kabag_id' => $selectedKabag->id]) }}"
                                        class="flex h-full w-full items-center justify-center rounded-xl border border-amber-200 bg-white px-3 py-2 text-xs font-extrabold text-amber-700 hover:bg-amber-100"
                                    >
                                        Reset
                                    </a>
                                </div>
                            </form>

                            <div class="flex items-center justify-between rounded-xl border border-amber-100 bg-white/70 px-3 py-2 text-[11px] text-slate-500">
                                <span>
                                    Tampil:
                                    <strong id="liveAvailableVisibleCount" class="font-black text-slate-900">
                                        {{ $availableEmployees->count() }}
                                    </strong>
                                    karyawan
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- CHECKBOX LIST ASSIGN --}}
                    <form method="POST" action="{{ route('master.kabag-mapping.assign', $selectedKabag) }}" id="assignEmployeesForm">
                        @csrf
                        <div class="max-h-[520px] divide-y divide-slate-100 overflow-y-auto">
                            @forelse ($availableEmployees as $employee)
                                @php $areaMeta = $resolveArea($employee->source_device_id); @endphp
                                @php
                                    $availableSearchText = strtolower(
                                        trim(
                                            ($employee->nama ?? '')
                                            . ' '
                                            . ($employee->employee_code ?? '')
                                            . ' '
                                            . ($employee->jabatan ?? '')
                                            . ' '
                                            . ($employee->source_kategori_karyawan_name ?? '')
                                        )
                                    );
                                @endphp
                                <label
                                    class="availableEmployeeRow flex cursor-pointer items-start gap-3 px-4 py-3 hover:bg-amber-50/40"
                                    data-search="{{ $availableSearchText }}"
                                >
                                    <div class="pt-0.5">
                                        <input
                                            type="checkbox"
                                            name="employee_ids[]"
                                            value="{{ $employee->id }}"
                                            class="availableEmployeeCheckbox h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                        >
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                            <span class="font-extrabold text-slate-900">{{ $employee->nama }}</span>
                                            <span class="rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] font-bold text-slate-500">
                                                {{ $employee->employee_code ?? '-' }}
                                            </span>
                                            @if ($employee->source_kategori_karyawan_name)
                                                <span class="rounded border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-extrabold text-blue-700">
                                                    {{ $employee->source_kategori_karyawan_name }}
                                                </span>
                                            @endif
                                            <span class="rounded border px-1.5 py-0.5 text-[10px] font-extrabold {{ $areaMeta['badge'] }}">
                                                {{ $areaMeta['label'] }}
                                            </span>
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $employee->jabatan ?: 'Jabatan belum diisi' }}
                                        </div>

                                        @if ($employee->kabags->isNotEmpty())
                                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sudah ditangani:</span>
                                                @foreach ($employee->kabags as $otherKabag)
                                                    <span class="rounded border border-violet-200 bg-violet-50 px-1.5 py-0.5 text-[10px] font-bold text-violet-700">
                                                        {{ $otherKabag->name }}
                                                    </span>
                                                @endforeach
                                                <span class="rounded border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-bold text-blue-700">
                                                    Boleh ditambah
                                                </span>
                                            </div>
                                        @else
                                            <div class="mt-2">
                                                <span class="rounded border border-amber-200 bg-amber-50 px-1.5 py-0.5 text-[10px] font-bold text-amber-700">
                                                    Belum punya Kabag
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @empty
                                <div class="px-5 py-12 text-center">
                                    <div class="text-sm font-extrabold text-slate-700">Tidak ada karyawan.</div>
                                    <div class="mt-1 text-xs text-slate-500">Semua karyawan sudah masuk ke Kabag ini atau filter tidak menemukan hasil.</div>
                                </div>
                            @endforelse
                        </div>

                        @if ($availableEmployees->isNotEmpty())
                            <div class="border-t border-amber-100 bg-amber-50/50 p-3.5 sm:p-4 space-y-3">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            id="selectAllAvailable"
                                            class="rounded-xl border border-amber-200 bg-white px-3 py-1.5 text-xs font-extrabold text-amber-700 hover:bg-amber-100"
                                        >
                                            Pilih Semua Hasil
                                        </button>
                                        <span class="text-xs text-slate-500">
                                            Dipilih: <strong id="selectedAvailableCount" class="font-black text-slate-900">0</strong>
                                        </span>
                                    </div>

                                    <button
                                        type="submit"
                                        id="assignEmployeesButton"
                                        class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-extrabold text-white shadow-sm hover:bg-blue-700"
                                    >
                                        Tambahkan yang Dipilih
                                    </button>
                                </div>

                                <div class="border-t border-amber-200/60 pt-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-amber-900">Bulk berdasarkan filter aktif</div>
                                        <div class="text-[11px] text-amber-700">Tambahkan semua hasil tanpa centang satu per satu (wajib pilih Area / Bagian).</div>
                                    </div>

                                    <button
                                        type="button"
                                        id="openBulkAssignConfirm"
                                        data-count="{{ $availableEmployees->count() }}"
                                        data-enabled="{{ (($area ?? '') !== '' || $category !== '') ? '1' : '0' }}"
                                        class="shrink-0 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-extrabold text-white shadow-sm hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                                        @disabled(($area ?? '') === '' && $category === '')
                                    >
                                        Bulk Assign ({{ $availableEmployees->count() }})
                                    </button>
                                </div>
                            </div>
                        @endif
                    </form>

                    {{-- HIDDEN BULK ASSIGN FORM --}}
                    <form
                        method="POST"
                        action="{{ route('master.kabag-mapping.assign-filtered', $selectedKabag) }}"
                        id="bulkAssignFilteredForm"
                        class="hidden"
                    >
                        @csrf
                        <input type="hidden" name="search" id="bulkAssignSearch" value="{{ $search }}">
                        <input type="hidden" name="category" value="{{ $category }}">
                        <input type="hidden" name="area" value="{{ $area ?? '' }}">
                    </form>
                </div>
            </div>
        @else
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <div class="font-extrabold text-slate-700">Belum ada user dengan role Kabag.</div>
            </div>
        @endif
    </div>

    {{-- MODAL KONFIRMASI BULK --}}
    <div id="bulkAssignConfirmModal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
        <div id="bulkAssignConfirmBox" class="w-full max-w-md scale-95 rounded-2xl border border-slate-200 bg-white p-5 opacity-0 shadow-2xl transition-all duration-200">
            <h3 class="text-lg font-extrabold text-slate-900">Tambahkan Semua Hasil Filter?</h3>
            <p class="mt-2 text-xs leading-relaxed text-slate-600">
                Semua karyawan yang sesuai dengan kriteria filter saat ini akan dipetakan ke <strong>{{ $selectedKabag?->name }}</strong>.
            </p>

            <div class="mt-3 space-y-1 rounded-xl bg-slate-50 p-3 text-xs text-slate-600">
                <div>Area: <strong>{{ ($area ?? '') !== '' ? 'Area ' . $area : 'Semua Area' }}</strong></div>
                <div>Bagian: <strong>{{ $category !== '' ? $category : 'Semua Bagian' }}</strong></div>
                <div>Pencarian: <strong>{{ $search !== '' ? $search : '-' }}</strong></div>
                <div class="pt-1 text-emerald-700 font-bold">Total target: {{ $availableEmployees->count() }} karyawan</div>
            </div>

            <div class="mt-5 flex gap-2.5">
                <button type="button" id="cancelBulkAssign" class="w-1/2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" id="confirmBulkAssign" class="w-1/2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700">
                    Ya, Tambahkan
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const availableCheckboxes = Array.from(document.querySelectorAll('.availableEmployeeCheckbox'));
            const availableRows = Array.from(document.querySelectorAll('.availableEmployeeRow'));
            const liveAvailableSearch = document.getElementById('liveAvailableSearch');
            const liveAvailableVisibleCount = document.getElementById('liveAvailableVisibleCount');
            const bulkAssignSearch = document.getElementById('bulkAssignSearch');

            const countElement = document.getElementById('selectedAvailableCount');
            const selectAllButton = document.getElementById('selectAllAvailable');
            const assignForm = document.getElementById('assignEmployeesForm');
            const assignButton = document.getElementById('assignEmployeesButton');

            const openBulkBtn = document.getElementById('openBulkAssignConfirm');
            const bulkModal = document.getElementById('bulkAssignConfirmModal');
            const bulkBox = document.getElementById('bulkAssignConfirmBox');
            const cancelBulkBtn = document.getElementById('cancelBulkAssign');
            const confirmBulkBtn = document.getElementById('confirmBulkAssign');
            const bulkForm = document.getElementById('bulkAssignFilteredForm');

            const toggleModal = (show) => {
                if (!bulkModal || !bulkBox) return;
                if (show) {
                    bulkModal.classList.remove('hidden');
                    bulkModal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                    setTimeout(() => bulkBox.classList.replace('opacity-0', 'opacity-100'), 10);
                    setTimeout(() => bulkBox.classList.replace('scale-95', 'scale-100'), 10);
                } else {
                    bulkBox.classList.replace('opacity-100', 'opacity-0');
                    bulkBox.classList.replace('scale-100', 'scale-95');
                    setTimeout(() => {
                        bulkModal.classList.replace('flex', 'hidden');
                        document.body.classList.remove('overflow-hidden');
                    }, 180);
                }
            };

            const getVisibleCheckboxes = () => {
                return availableRows
                    .filter(row => !row.classList.contains('hidden'))
                    .map(row => row.querySelector('.availableEmployeeCheckbox'))
                    .filter(Boolean);
            };

            const refreshCount = () => {
                const selectedCount = availableCheckboxes.filter(c => c.checked).length;

                if (countElement) {
                    countElement.textContent = selectedCount;
                }

                const visibleCheckboxes = getVisibleCheckboxes();
                const allVisibleSelected =
                    visibleCheckboxes.length > 0
                    && visibleCheckboxes.every(cb => cb.checked);

                if (selectAllButton) {
                    selectAllButton.textContent =
                        allVisibleSelected
                            ? 'Hapus Pilihan Tampil'
                            : 'Pilih Semua Tampil';
                }
            };

            const applyLiveAvailableSearch = () => {
                const keyword = (liveAvailableSearch?.value ?? '')
                    .trim()
                    .toLowerCase();

                let visibleCount = 0;

                availableRows.forEach(row => {
                    const haystack = (row.dataset.search ?? '').toLowerCase();
                    const visible = keyword === '' || haystack.includes(keyword);

                    row.classList.toggle('hidden', !visible);

                    if (visible) {
                        visibleCount++;
                    }
                });

                if (liveAvailableVisibleCount) {
                    liveAvailableVisibleCount.textContent = visibleCount;
                }

                if (bulkAssignSearch) {
                    bulkAssignSearch.value = liveAvailableSearch?.value?.trim() ?? '';
                }

                refreshCount();
            };

            liveAvailableSearch?.addEventListener('input', applyLiveAvailableSearch);

            availableCheckboxes.forEach(cb => cb.addEventListener('change', refreshCount));

            selectAllButton?.addEventListener('click', () => {
                const visibleCheckboxes = getVisibleCheckboxes();
                const shouldCheck = visibleCheckboxes.some(cb => !cb.checked);

                visibleCheckboxes.forEach(cb => {
                    cb.checked = shouldCheck;
                });

                refreshCount();
            });

            assignForm?.addEventListener('submit', (e) => {
                if (!availableCheckboxes.some(cb => cb.checked)) {
                    e.preventDefault();
                    alert('Pilih minimal satu karyawan.');
                    return;
                }
                if (assignButton) {
                    assignButton.disabled = true;
                    assignButton.textContent = 'Menambahkan...';
                    assignButton.classList.add('opacity-70', 'cursor-not-allowed');
                }
            });

            openBulkBtn?.addEventListener('click', function () {
                if (this.dataset.enabled !== '1' || parseInt(this.dataset.count || '0', 10) <= 0) return;
                toggleModal(true);
            });

            cancelBulkBtn?.addEventListener('click', () => toggleModal(false));
            bulkModal?.addEventListener('click', (e) => { if (e.target === bulkModal) toggleModal(false); });

            confirmBulkAssign?.addEventListener('click', function () {
                if (!bulkForm) return;
                this.disabled = true;
                this.textContent = 'Menambahkan...';
                bulkForm.submit();
            });

            applyLiveAvailableSearch();

            const flash = document.getElementById('successFlash');
            if (flash) {
                setTimeout(() => {
                    flash.style.transition = 'opacity .4s ease, transform .4s ease';
                    flash.style.opacity = '0';
                    flash.style.transform = 'translateY(-6px)';
                    setTimeout(() => flash.remove(), 400);
                }, 3500);
            }
        });
    </script>
</x-app-layout>
