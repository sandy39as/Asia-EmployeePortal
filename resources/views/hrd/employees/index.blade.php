<x-app-layout>
    <x-slot name="headerTitle">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Data Karyawan</h1>
            <p class="text-sm font-medium text-slate-500">Manajemen akun dan hak akses Employee Portal.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-4 px-2 sm:px-4">

        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <div id="successFlash" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800 transition-all">
                {{ session('success') }}
            </div>
        @endif

        {{-- FILTER & SUMMARY PILLS --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-xs">
                    Total: <strong class="font-extrabold text-slate-900">{{ number_format($summary['total'] ?? 0) }}</strong>
                </span>

                <a href="{{ route('hrd.employees.index', ['status' => 'active', 'search' => $search, 'leave_year' => $leaveYear]) }}" 
                   class="rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-sm font-bold text-emerald-800 transition hover:bg-emerald-100 {{ ($status ?? '') === 'active' ? 'ring-2 ring-emerald-500' : '' }}">
                    Aktif: {{ number_format($summary['active'] ?? 0) }}
                </a>

                <a href="{{ route('hrd.employees.index', ['status' => 'inactive', 'search' => $search, 'leave_year' => $leaveYear]) }}" 
                   class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 {{ ($status ?? '') === 'inactive' ? 'ring-2 ring-slate-400' : '' }}">
                    Nonaktif: {{ number_format($summary['inactive'] ?? 0) }}
                </a>

                <span class="rounded-xl border border-violet-200 bg-violet-50 px-3.5 py-2 text-sm font-bold text-violet-800">
                    Wajib Ganti ID: {{ number_format($summary['must_change_username'] ?? 0) }}
                </span>

                <span class="rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-2 text-sm font-bold text-amber-800">
                    Wajib Ganti PW: {{ number_format($summary['must_change_password'] ?? 0) }}
                </span>

                <span class="rounded-xl border border-sky-200 bg-sky-50 px-3.5 py-2 text-sm font-bold text-sky-800">
                    ASIA: {{ number_format($summary['asia'] ?? 0) }}
                </span>

                <span class="rounded-xl border border-orange-200 bg-orange-50 px-3.5 py-2 text-sm font-bold text-orange-800">
                    Outsourcing: {{ number_format($summary['outsourcing'] ?? 0) }}
                </span>
            </div>

            {{-- FORM PENCARIAN --}}
            <form method="GET" action="{{ route('hrd.employees.index') }}" class="flex flex-wrap items-center gap-2 sm:flex-nowrap">
                <input type="text"
                       name="search"
                       value="{{ $search ?? '' }}"
                       placeholder="Cari nama / kode ID..."
                       class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-500 focus:outline-none sm:w-60">

                <select name="leave_year"
                        class="w-32 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-bold text-slate-700 focus:border-slate-500 focus:outline-none"
                        onchange="this.form.submit()">
                    @for ($year = now()->year; $year >= now()->year - 3; $year--)
                        <option value="{{ $year }}" @selected(($leaveYear ?? now()->year) == $year)>
                            {{ $year }}
                        </option>
                    @endfor
                </select>

                <select name="status" class="w-36 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-bold text-slate-700 focus:border-slate-500 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(($status ?? '') === 'active')>Aktif</option>
                    <option value="inactive" @selected(($status ?? '') === 'inactive')>Nonaktif</option>
                </select>

                <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-extrabold text-white transition hover:bg-black">
                    Cari
                </button>

                @if(($search ?? '') !== '' || ($status ?? '') !== '')
                    <a href="{{ route('hrd.employees.index', ['leave_year' => $leaveYear]) }}" class="flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50" title="Reset Filter">
                        ✕
                    </a>
                @endif
            </form>
        </div>

        {{-- MOBILE VIEW --}}
        <div class="space-y-3 lg:hidden">
            @forelse ($employees as $employee)
                @php
                    $hasUser = (bool) $employee->user;
                    $mustChangeUsername = (bool) ($employee->user?->must_change_username ?? false);
                    $mustChangePassword = (bool) ($employee->user?->must_change_password ?? false);
                    $annualBalance = $employee->employment_group === 'asia' ? $employee->leaveBalances->first() : null;
                @endphp

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs transition hover:border-slate-300 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-base font-extrabold text-slate-900 leading-snug">{{ $employee->nama }}</h3>
                            <p class="mt-1 text-sm text-slate-600 font-bold">
                                {{ $employee->employee_code }} • {{ $employee->jabatan ?: '-' }}
                            </p>
                            <div class="mt-1.5 flex flex-wrap items-center gap-2">
                                <span class="text-xs font-bold text-slate-500">
                                    {{ $employee->source_kategori_karyawan_name ?: 'Belum Sync' }}
                                </span>
                                @if ($employee->employment_group === 'asia')
                                    <span class="rounded-lg border border-sky-200 bg-sky-50 px-2 py-0.5 text-xs font-extrabold text-sky-700">ASIA</span>
                                @elseif ($employee->employment_group === 'outsourcing')
                                    <span class="rounded-lg border border-orange-200 bg-orange-50 px-2 py-0.5 text-xs font-extrabold text-orange-700">OUTSOURCING</span>
                                @endif
                            </div>
                        </div>

                        <span class="shrink-0 rounded-lg border px-2.5 py-1 text-xs font-extrabold {{ $employee->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-100 text-slate-500' }}">
                            {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    @if ($employee->employment_group === 'asia')
                        <div class="flex items-center justify-between rounded-xl border border-emerald-100 bg-emerald-50/70 p-3 text-sm">
                            <div>
                                <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-800">Cuti {{ $leaveYear }}</span>
                                <div class="text-xs text-emerald-700 mt-0.5">Jatah: {{ $annualBalance?->entitlement ?? 12 }} • Terpakai: {{ $annualBalance?->used ?? 0 }}</div>
                            </div>
                            <div class="text-right font-black text-emerald-800">
                                <span class="text-xl">{{ $annualBalance?->remaining ?? 12 }}</span>
                                <span class="text-xs font-bold">hari sisa</span>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-between gap-3 border-t border-slate-100 pt-3">
                        <div class="flex flex-wrap gap-1.5">
                            @if (!$hasUser)
                                <span class="rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-extrabold text-rose-700">
                                    Akun Tidak Ada
                                </span>
                            @else
                                <span id="mobileUsernameBadge-{{ $employee->id }}"
                                      class="rounded-lg border px-2.5 py-1 text-xs font-extrabold {{ $mustChangeUsername ? 'border-violet-200 bg-violet-50 text-violet-800' : 'border-sky-200 bg-sky-50 text-sky-700' }}">
                                    {{ $mustChangeUsername ? 'Wajib Ganti ID' : 'ID Aktif' }}
                                </span>

                                <span id="mobilePasswordBadge-{{ $employee->id }}"
                                      class="rounded-lg border px-2.5 py-1 text-xs font-extrabold {{ $mustChangePassword ? 'border-amber-200 bg-amber-50 text-amber-800' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}">
                                    {{ $mustChangePassword ? 'Wajib Ganti PW' : 'Password Aktif' }}
                                </span>
                            @endif
                        </div>

                        <button type="button"
                                class="openEmployeeModalBtn rounded-xl border border-slate-300 bg-slate-50 px-4 py-1.5 text-sm font-extrabold text-slate-800 transition hover:bg-slate-100"
                                data-employee="{{ json_encode([
                                    'id' => $employee->id,
                                    'nama' => $employee->nama,
                                    'code' => $employee->employee_code,
                                    'jabatan' => $employee->jabatan ?: '-',
                                    'username' => $employee->user?->username ?: '-',
                                    'is_active' => $employee->is_active,
                                    'facelog_id' => $employee->source_karyawan_id ?? '-',
                                    'tanggal_masuk' => $employee->tanggal_masuk ? \Carbon\Carbon::parse($employee->tanggal_masuk)->format('d/m/Y') : '-',
                                    'kategori' => $employee->source_kategori_karyawan_name ?: '-',
                                    'group' => $employee->employment_group,
                                    'has_user' => $hasUser,
                                    'must_change_username' => $mustChangeUsername,
                                    'must_change_password' => $mustChangePassword,
                                    'entitlement' => $annualBalance?->entitlement ?? 12,
                                    'used' => $annualBalance?->used ?? 0,
                                    'remaining' => $annualBalance?->remaining ?? 12,
                                    'leave_year' => $leaveYear,
                                    'reset_url' => route('hrd.employees.reset-password', $employee)
                                ]) }}">
                            Detail
                        </button>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
                    <p class="text-sm font-bold text-slate-500">Data karyawan tidak ditemukan.</p>
                </div>
            @endforelse
        </div>

        {{-- DESKTOP TABLE VIEW --}}
        <div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs lg:block">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-xs font-extrabold uppercase tracking-wider text-slate-600">
                    <tr>
                        <th class="px-5 py-4 text-left">Karyawan</th>
                        <th class="px-5 py-4 text-left">Jabatan</th>
                        <th class="px-5 py-4 text-left">Kategori / Group</th>
                        <th class="px-5 py-4 text-left">Login (ID)</th>
                        <th class="px-5 py-4 text-left">Status Password</th>
                        <th class="px-5 py-4 text-left">Saldo Cuti {{ $leaveYear }}</th>
                        <th class="px-5 py-4 text-left">Keaktifan</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($employees as $employee)
                        @php
                            $hasUser = (bool) $employee->user;
                            $mustChangeUsername = (bool) ($employee->user?->must_change_username ?? false);
                            $mustChangePassword = (bool) ($employee->user?->must_change_password ?? false);
                            $annualBalance = $employee->employment_group === 'asia' ? $employee->leaveBalances->first() : null;
                        @endphp
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900 text-base leading-snug">{{ $employee->nama }}</div>
                                <div class="font-mono text-xs font-bold text-slate-400 mt-0.5">{{ $employee->employee_code }}</div>
                            </td>
                            <td class="px-5 py-4 text-slate-700 font-medium">{{ $employee->jabatan ?: '-' }}</td>
                            <td class="px-5 py-4">
                                <div class="text-xs font-bold text-slate-800">{{ $employee->source_kategori_karyawan_name ?: '-' }}</div>
                                <div class="mt-1">
                                    @if ($employee->employment_group === 'asia')
                                        <span class="rounded-lg border border-sky-200 bg-sky-50 px-2 py-0.5 text-xs font-extrabold text-sky-700">ASIA</span>
                                    @elseif ($employee->employment_group === 'outsourcing')
                                        <span class="rounded-lg border border-orange-200 bg-orange-50 px-2 py-0.5 text-xs font-extrabold text-orange-700">OUTSOURCING</span>
                                    @else
                                        <span class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-0.5 text-xs font-bold text-slate-400">Belum Sync</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-mono font-bold text-slate-800">
                                    {{ $employee->user?->username ?: '-' }}
                                </div>

                                @if ($hasUser)
                                    <div class="mt-1.5">
                                        <span id="usernameBadge-{{ $employee->id }}"
                                              class="rounded-lg border px-2 py-0.5 text-[11px] font-extrabold {{ $mustChangeUsername ? 'border-violet-200 bg-violet-50 text-violet-800' : 'border-sky-200 bg-sky-50 text-sky-700' }}">
                                            {{ $mustChangeUsername ? 'Wajib Ganti ID' : 'ID Aktif' }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span id="passwordBadge-{{ $employee->id }}" class="rounded-lg border px-2.5 py-1 text-xs font-extrabold {{ !$hasUser ? 'border-rose-200 bg-rose-50 text-rose-700' : ($mustChangePassword ? 'border-amber-200 bg-amber-50 text-amber-800' : 'border-emerald-200 bg-emerald-50 text-emerald-700') }}">
                                    {{ !$hasUser ? 'Akun Kosong' : ($mustChangePassword ? 'Wajib Ganti' : 'Aktif') }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if ($employee->employment_group === 'asia')
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-base font-black text-emerald-700">{{ $annualBalance?->remaining ?? 12 }}</span>
                                        <span class="text-xs font-bold text-slate-400">hari sisa</span>
                                    </div>
                                    <div class="text-xs text-slate-400 mt-0.5">Jatah: {{ $annualBalance?->entitlement ?? 12 }} • Pakai: {{ $annualBalance?->used ?? 0 }}</div>
                                @else
                                    <span class="text-xs font-medium text-slate-400">Tidak ada annual</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-lg border px-2.5 py-1 text-xs font-extrabold {{ $employee->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-100 text-slate-500' }}">
                                    {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button type="button"
                                        class="openEmployeeModalBtn rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-1.5 text-xs font-extrabold text-slate-800 transition hover:bg-slate-200"
                                        data-employee="{{ json_encode([
                                            'id' => $employee->id,
                                            'nama' => $employee->nama,
                                            'code' => $employee->employee_code,
                                            'jabatan' => $employee->jabatan ?: '-',
                                            'username' => $employee->user?->username ?: '-',
                                            'is_active' => $employee->is_active,
                                            'facelog_id' => $employee->source_karyawan_id ?? '-',
                                            'tanggal_masuk' => $employee->tanggal_masuk ? \Carbon\Carbon::parse($employee->tanggal_masuk)->format('d/m/Y') : '-',
                                            'kategori' => $employee->source_kategori_karyawan_name ?: '-',
                                            'group' => $employee->employment_group,
                                            'has_user' => $hasUser,
                                            'must_change_username' => $mustChangeUsername,
                                            'must_change_password' => $mustChangePassword,
                                            'entitlement' => $annualBalance?->entitlement ?? 12,
                                            'used' => $annualBalance?->used ?? 0,
                                            'remaining' => $annualBalance?->remaining ?? 12,
                                            'leave_year' => $leaveYear,
                                            'reset_url' => route('hrd.employees.reset-password', $employee)
                                        ]) }}">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-sm font-bold text-slate-400">Data karyawan tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ========================================================= --}}
        {{-- PAGINATION JELAS & LEGA --}}
        {{-- ========================================================= --}}
        @if ($employees->hasPages())
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    {{-- Info Halaman --}}
                    <div class="text-sm font-semibold text-slate-600">
                        Menampilkan <span class="font-extrabold text-slate-900">{{ $employees->firstItem() }}</span>
                        sampai <span class="font-extrabold text-slate-900">{{ $employees->lastItem() }}</span>
                        dari <span class="font-black text-slate-900">{{ $employees->total() }}</span> karyawan
                    </div>

                    {{-- Tombol Navigasi Pagination --}}
                    <div class="flex items-center gap-1.5 overflow-x-auto">
                        {{-- Tombol Previous --}}
                        @if ($employees->onFirstPage())
                            <span class="inline-flex cursor-not-allowed items-center rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm font-bold text-slate-400 opacity-60">
                                &laquo; Prev
                            </span>
                        @else
                            <a href="{{ $employees->previousPageUrl() }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm font-extrabold text-slate-700 transition hover:bg-slate-100 hover:text-slate-900">
                                &laquo; Prev
                            </a>
                        @endif

                        {{-- Deretan Nomor Halaman --}}
                        @foreach ($employees->getUrlRange(max(1, $employees->currentPage() - 2), min($employees->lastPage(), $employees->currentPage() + 2)) as $page => $url)
                            @if ($page == $employees->currentPage())
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-sm font-black text-white shadow-xs">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-300 bg-white text-sm font-extrabold text-slate-700 transition hover:bg-slate-100 hover:text-slate-900">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Tombol Next --}}
                        @if ($employees->hasMorePages())
                            <a href="{{ $employees->nextPageUrl() }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm font-extrabold text-slate-700 transition hover:bg-slate-100 hover:text-slate-900">
                                Next &raquo;
                            </a>
                        @else
                            <span class="inline-flex cursor-not-allowed items-center rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm font-bold text-slate-400 opacity-60">
                                Next &raquo;
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- ========================================================= --}}
    {{-- MODAL DETAIL KARYAWAN --}}
    {{-- ========================================================= --}}
    <div id="globalEmployeeModal" class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-950/60 px-3 py-4 backdrop-blur-xs">
        <div id="globalEmployeeModalBox" class="max-h-[92vh] w-full max-w-lg scale-95 overflow-y-auto rounded-3xl border border-slate-200 bg-white opacity-0 shadow-2xl transition-all duration-200">
            {{-- Header Modal --}}
            <div class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-100 bg-white/95 px-5 py-4 backdrop-blur">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Detail Karyawan</h3>
                    <p id="modalEmployeeCodeHeader" class="text-xs font-mono font-bold text-slate-400">-</p>
                </div>
                <button type="button" id="closeGlobalEmployeeModal" class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">✕</button>
            </div>

            <div class="p-5 space-y-4 text-sm">
                {{-- Info Nama --}}
                <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                    <div id="modalNama" class="text-base font-black text-slate-900 leading-snug">-</div>
                    <div id="modalJabatanCode" class="mt-1 text-xs text-slate-600 font-bold">-</div>
                </div>

                {{-- Grid Detail --}}
                <div class="grid grid-cols-2 gap-2.5">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">ID Login Aktif</span>
                        <span id="modalUsername" class="mt-1 block break-all font-mono font-black text-slate-900 text-sm">-</span>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Status ID Login</span>
                        <span id="modalUsernameStatusBadge" class="mt-1 inline-block rounded-lg px-2.5 py-1 text-xs font-extrabold">-</span>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Keaktifan</span>
                        <span id="modalActiveStatus" class="mt-1 block font-extrabold text-sm">-</span>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">ID FaceLog</span>
                        <span id="modalFacelogId" class="mt-1 block font-mono font-bold text-slate-800 text-sm">-</span>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Tanggal Masuk</span>
                        <span id="modalTanggalMasuk" class="mt-1 block font-bold text-slate-800 text-sm">-</span>
                    </div>
                </div>

                {{-- Kategori & Group --}}
                <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Kategori & Group</span>
                    <div class="mt-1.5 flex items-center justify-between gap-2">
                        <span id="modalKategori" class="font-bold text-slate-800">-</span>
                        <span id="modalGroupBadge">-</span>
                    </div>
                </div>

                {{-- Saldo Cuti (Khusus ASIA) --}}
                <div id="modalLeaveSection" class="hidden rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-800">Saldo Cuti <span id="modalLeaveYear"></span></span>
                        <span id="modalRemainingDays" class="text-2xl font-black text-emerald-800">12</span>
                    </div>
                    <div class="mt-2.5 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-xl bg-white/90 p-2 border border-emerald-100">
                            <span class="block text-[11px] font-bold text-slate-400">Jatah</span>
                            <span id="modalEntitlementDays" class="block font-black text-slate-800 text-sm mt-0.5">-</span>
                        </div>
                        <div class="rounded-xl bg-white/90 p-2 border border-emerald-100">
                            <span class="block text-[11px] font-bold text-slate-400">Terpakai</span>
                            <span id="modalUsedDays" class="block font-black text-amber-700 text-sm mt-0.5">-</span>
                        </div>
                        <div class="rounded-xl bg-white/90 p-2 border border-emerald-100">
                            <span class="block text-[11px] font-bold text-slate-400">Sisa</span>
                            <span id="modalRemainingDaysBottom" class="block font-black text-emerald-700 text-sm mt-0.5">-</span>
                        </div>
                    </div>
                </div>

                {{-- Reset Akun Card --}}
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Status Password</span>
                            <span id="modalPasswordStatusBadge" class="mt-1 inline-block rounded-lg px-2.5 py-1 text-xs font-extrabold">-</span>
                        </div>

                        <button type="button"
                                id="modalResetPasswordBtn"
                                class="rounded-xl border border-amber-300 bg-amber-100 px-4 py-2 text-xs font-black text-amber-900 transition hover:bg-amber-200">
                            Reset Akun
                        </button>
                    </div>

                    <p class="mt-2 text-[11px] leading-5 text-slate-500">
                        Bisa reset password saja atau mengembalikan ID Login ke ID karyawan sekaligus membuat password sementara baru.
                    </p>
                </div>

                {{-- Kotak Hasil Reset --}}
                <div id="modalResetResultBox" class="hidden space-y-3 rounded-2xl border border-sky-200 bg-sky-50 p-3.5">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-sky-700">
                            ID Login Setelah Reset
                        </span>
                        <div id="modalNewUsernameText" class="mt-1 break-all rounded-xl border border-sky-200 bg-white px-3 py-2 font-mono text-sm font-black text-slate-900">
                            -
                        </div>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-sky-700">
                            Password Sementara Baru
                        </span>

                        <div class="mt-1 flex items-center justify-between rounded-xl border border-sky-200 bg-white p-2.5 font-mono">
                            <span id="modalNewPasswordText" class="text-base font-black tracking-widest text-slate-900">-</span>

                            <button type="button"
                                    id="modalCopyPasswordBtn"
                                    class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-extrabold text-white transition hover:bg-black">
                                Salin
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 bg-slate-50 px-5 py-3 text-right">
                <button type="button" id="closeGlobalEmployeeModalBottom" class="rounded-xl border border-slate-300 bg-white px-5 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-100">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI RESET PASSWORD --}}
    <div id="resetPasswordConfirmModal" class="fixed inset-0 z-[130] hidden items-center justify-center bg-slate-950/60 px-4 backdrop-blur-xs">
        <div id="resetPasswordConfirmBox" class="w-full max-w-sm scale-95 rounded-2xl border border-slate-200 bg-white p-5 opacity-0 shadow-2xl transition-all duration-200 text-sm">
            <h3 class="text-base font-extrabold text-slate-900">Reset Password Karyawan?</h3>
            <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                Password untuk <strong id="resetEmployeeName" class="text-slate-900 font-bold">-</strong> akan diganti dengan 6 digit password acak baru.
            </p>

            <div id="resetPasswordError" class="mt-3 hidden rounded-xl border border-rose-200 bg-rose-50 p-2.5 text-xs text-rose-700 font-medium"></div>

            <div class="mt-5 flex justify-end gap-2">
                <button type="button" id="cancelResetPassword" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="button" id="confirmResetPassword" class="rounded-xl bg-amber-600 px-4 py-2 text-xs font-extrabold text-white transition hover:bg-amber-700">Ya, Reset</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let activeEmployee = null;

            const globalModal = document.getElementById('globalEmployeeModal');
            const globalBox = document.getElementById('globalEmployeeModalBox');
            const closeBtnTop = document.getElementById('closeGlobalEmployeeModal');
            const closeBtnBottom = document.getElementById('closeGlobalEmployeeModalBottom');

            const confirmModal = document.getElementById('resetPasswordConfirmModal');
            const confirmBox = document.getElementById('resetPasswordConfirmBox');
            const confirmResetBtn = document.getElementById('confirmResetPassword');
            const cancelResetBtn = document.getElementById('cancelResetPassword');
            const resetNameText = document.getElementById('resetEmployeeName');
            const resetError = document.getElementById('resetPasswordError');

            const toggleModal = (modal, box, show) => {
                if (!modal || !box) return;
                if (show) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    setTimeout(() => box.classList.remove('scale-95', 'opacity-0'), 10);
                    document.body.classList.add('overflow-hidden');
                } else {
                    box.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        modal.classList.remove('flex');
                        modal.classList.add('hidden');
                        if (!document.querySelector('.fixed.flex:not(#globalEmployeeModal):not(#resetPasswordConfirmModal)')) {
                            document.body.classList.remove('overflow-hidden');
                        }
                    }, 180);
                }
            };

            document.querySelectorAll('.openEmployeeModalBtn').forEach(btn => {
                btn.addEventListener('click', () => {
                    activeEmployee = JSON.parse(btn.dataset.employee);

                    document.getElementById('modalEmployeeCodeHeader').textContent = `ID: ${activeEmployee.code}`;
                    document.getElementById('modalNama').textContent = activeEmployee.nama;
                    document.getElementById('modalJabatanCode').textContent = `${activeEmployee.jabatan} • ${activeEmployee.code}`;
                    document.getElementById('modalUsername').textContent = activeEmployee.username;
                    
                    const activeEl = document.getElementById('modalActiveStatus');
                    activeEl.textContent = activeEmployee.is_active ? 'Aktif' : 'Nonaktif';
                    activeEl.className = activeEmployee.is_active ? 'mt-1 block font-bold text-emerald-700 text-sm' : 'mt-1 block font-bold text-slate-500 text-sm';

                    document.getElementById('modalFacelogId').textContent = activeEmployee.facelog_id;
                    document.getElementById('modalTanggalMasuk').textContent = activeEmployee.tanggal_masuk;
                    document.getElementById('modalKategori').textContent = activeEmployee.kategori;

                    const groupBadge = document.getElementById('modalGroupBadge');
                    if (activeEmployee.group === 'asia') {
                        groupBadge.className = 'rounded-lg border border-sky-200 bg-sky-50 px-2.5 py-0.5 text-xs font-extrabold text-sky-700';
                        groupBadge.textContent = 'ASIA';
                    } else if (activeEmployee.group === 'outsourcing') {
                        groupBadge.className = 'rounded-lg border border-orange-200 bg-orange-50 px-2.5 py-0.5 text-xs font-extrabold text-orange-700';
                        groupBadge.textContent = 'OUTSOURCING';
                    } else {
                        groupBadge.className = 'rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-xs font-bold text-slate-500';
                        groupBadge.textContent = 'Belum Sync';
                    }

                    const leaveSection = document.getElementById('modalLeaveSection');
                    if (activeEmployee.group === 'asia') {
                        leaveSection.classList.remove('hidden');
                        document.getElementById('modalLeaveYear').textContent = activeEmployee.leave_year;
                        document.getElementById('modalRemainingDays').textContent = activeEmployee.remaining;
                        document.getElementById('modalEntitlementDays').textContent = activeEmployee.entitlement;
                        document.getElementById('modalUsedDays').textContent = activeEmployee.used;
                        document.getElementById('modalRemainingDaysBottom').textContent = activeEmployee.remaining;
                    } else {
                        leaveSection.classList.add('hidden');
                    }

                    const pwBadge = document.getElementById('modalPasswordStatusBadge');
                    if (!activeEmployee.has_user) {
                        pwBadge.className = 'mt-1 inline-block rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-0.5 text-xs font-extrabold text-rose-700';
                        pwBadge.textContent = 'Akun Kosong';
                    } else if (activeEmployee.must_change_password) {
                        pwBadge.className = 'mt-1 inline-block rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-0.5 text-xs font-extrabold text-amber-800';
                        pwBadge.textContent = 'Wajib Ganti Password';
                    } else {
                        pwBadge.className = 'mt-1 inline-block rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-extrabold text-emerald-700';
                        pwBadge.textContent = 'Aktif';
                    }

                    document.getElementById('modalResetPasswordBtn').style.display = activeEmployee.has_user ? 'inline-block' : 'none';
                    document.getElementById('modalResetResultBox').classList.add('hidden');

                    toggleModal(globalModal, globalBox, true);
                });
            });

            closeBtnTop?.addEventListener('click', () => toggleModal(globalModal, globalBox, false));
            closeBtnBottom?.addEventListener('click', () => toggleModal(globalModal, globalBox, false));
            globalModal?.addEventListener('click', (e) => { if (e.target === globalModal) toggleModal(globalModal, globalBox, false); });

            document.getElementById('modalResetPasswordBtn')?.addEventListener('click', () => {
                if (!activeEmployee) return;
                resetNameText.textContent = activeEmployee.nama;
                resetError?.classList.add('hidden');

                const defaultMode = document.querySelector(
                    'input[name="employee_reset_mode"][value="password_only"]'
                );

                if (defaultMode) {
                    defaultMode.checked = true;
                }

                toggleModal(confirmModal, confirmBox, true);
            });

            cancelResetBtn?.addEventListener('click', () => toggleModal(confirmModal, confirmBox, false));
            confirmModal?.addEventListener('click', (e) => { if (e.target === confirmModal) toggleModal(confirmModal, confirmBox, false); });

            confirmResetBtn?.addEventListener('click', async () => {
                if (!activeEmployee) return;
                const originalText = confirmResetBtn.innerHTML;
                confirmResetBtn.disabled = true;
                confirmResetBtn.innerHTML = 'Memproses...';

                try {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('leave_year', activeEmployee.leave_year);

                    const selectedResetMode =
                        document.querySelector(
                            'input[name="employee_reset_mode"]:checked'
                        )?.value
                        ?? 'password_only';

                    formData.append('reset_mode', selectedResetMode);

                    const response = await fetch(activeEmployee.reset_url, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Reset password gagal.');

                    const resetMode = data.data.reset_mode || 'password_only';
                    const newUsername = data.data.username || activeEmployee.username;

                    document.getElementById('modalNewUsernameText').textContent = newUsername;
                    document.getElementById('modalNewPasswordText').textContent = data.data.password;
                    document.getElementById('modalResetResultBox').classList.remove('hidden');

                    activeEmployee.username = newUsername;
                    activeEmployee.must_change_password = true;

                    if (resetMode === 'login_and_password') {
                        activeEmployee.must_change_username = true;
                    }

                    document.getElementById('modalUsername').textContent = newUsername;

                    const pwId = `passwordBadge-${activeEmployee.id}`;
                    const mobPwId = `mobilePasswordBadge-${activeEmployee.id}`;

                    [pwId, mobPwId].forEach(id => {
                        const el = document.getElementById(id);

                        if (el) {
                            el.textContent = id.startsWith('passwordBadge-')
                                ? 'Wajib Ganti'
                                : 'Wajib Ganti PW';

                            el.className =
                                'rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-extrabold text-amber-800';
                        }
                    });

                    const pwModalBadge = document.getElementById('modalPasswordStatusBadge');
                    pwModalBadge.textContent = 'Wajib Ganti Password';
                    pwModalBadge.className =
                        'mt-1 inline-block rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-extrabold text-amber-800';

                    if (resetMode === 'login_and_password') {
                        const usernameId = `usernameBadge-${activeEmployee.id}`;
                        const mobileUsernameId = `mobileUsernameBadge-${activeEmployee.id}`;

                        [usernameId, mobileUsernameId].forEach(id => {
                            const el = document.getElementById(id);

                            if (el) {
                                el.textContent = 'Wajib Ganti ID';
                                el.className =
                                    'rounded-lg border border-violet-200 bg-violet-50 px-2.5 py-1 text-xs font-extrabold text-violet-800';
                            }
                        });

                        const usernameModalBadge = document.getElementById('modalUsernameStatusBadge');
                        usernameModalBadge.textContent = 'Wajib Ganti ID';
                        usernameModalBadge.className =
                            'mt-1 inline-block rounded-lg border border-violet-200 bg-violet-50 px-2.5 py-1 text-xs font-extrabold text-violet-800';
                    }

                    toggleModal(confirmModal, confirmBox, false);
                } catch (err) {
                    if (resetError) {
                        resetError.textContent = err.message || 'Terjadi kesalahan.';
                        resetError.classList.remove('hidden');
                    }
                } finally {
                    confirmResetBtn.disabled = false;
                    confirmResetBtn.innerHTML = originalText;
                }
            });

            document.getElementById('modalCopyPasswordBtn')?.addEventListener('click', async function () {
                const text = document.getElementById('modalNewPasswordText').textContent.trim();
                if (!text || text === '-') return;
                try {
                    await navigator.clipboard.writeText(text);
                    const original = this.textContent;
                    this.textContent = 'Disalin!';
                    setTimeout(() => this.textContent = original, 1200);
                } catch (e) {}
            });

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
