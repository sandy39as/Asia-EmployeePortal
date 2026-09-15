<x-app-layout>
    <x-slot name="headerTitle">
        <div>
            <h1 class="text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">Data Karyawan</h1>
            <p class="text-xs font-medium text-slate-500">Manajemen akun dan hak akses Employee Portal.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-3 px-2 sm:space-y-4 sm:px-4">

        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <div id="successFlash" class="rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-xs font-bold text-emerald-800 transition-all">
                {{ session('success') }}
            </div>
        @endif

        {{-- FILTER & SUMMARY PILLS --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <span class="rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-700 shadow-xs">
                    Total: <strong class="font-extrabold text-slate-900">{{ number_format($summary['total'] ?? 0) }}</strong>
                </span>

                <a href="{{ route('hrd.employees.index', ['status' => 'active', 'search' => $search, 'leave_year' => $leaveYear]) }}" 
                   class="rounded-xl border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-xs font-bold text-emerald-800 transition hover:bg-emerald-100 {{ ($status ?? '') === 'active' ? 'ring-2 ring-emerald-500' : '' }}">
                    Aktif: {{ number_format($summary['active'] ?? 0) }}
                </a>

                <a href="{{ route('hrd.employees.index', ['status' => 'inactive', 'search' => $search, 'leave_year' => $leaveYear]) }}" 
                   class="rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50 {{ ($status ?? '') === 'inactive' ? 'ring-2 ring-slate-400' : '' }}">
                    Nonaktif: {{ number_format($summary['inactive'] ?? 0) }}
                </a>

                <span class="rounded-xl border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-xs font-bold text-amber-800">
                    Wajib Ganti PW: {{ number_format($summary['must_change_password'] ?? 0) }}
                </span>

                <span class="rounded-xl border border-sky-200 bg-sky-50 px-2.5 py-1.5 text-xs font-bold text-sky-800">
                    ASIA: {{ number_format($summary['asia'] ?? 0) }}
                </span>

                <span class="rounded-xl border border-orange-200 bg-orange-50 px-2.5 py-1.5 text-xs font-bold text-orange-800">
                    Outsourcing: {{ number_format($summary['outsourcing'] ?? 0) }}
                </span>
            </div>

            {{-- FORM PENCARIAN --}}
            <form method="GET" action="{{ route('hrd.employees.index') }}" class="flex flex-wrap items-center gap-1.5 sm:flex-nowrap">
                <input type="text"
                       name="search"
                       value="{{ $search ?? '' }}"
                       placeholder="Cari nama / kode ID..."
                       class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 placeholder:text-slate-400 focus:border-slate-500 focus:outline-none sm:w-56">

                <select name="leave_year"
                        class="w-28 rounded-xl border border-slate-300 bg-white px-2.5 py-2 text-xs font-bold text-slate-700 focus:border-slate-500 focus:outline-none"
                        onchange="this.form.submit()">
                    @for ($year = now()->year; $year >= now()->year - 3; $year--)
                        <option value="{{ $year }}" @selected(($leaveYear ?? now()->year) == $year)>
                            {{ $year }}
                        </option>
                    @endfor
                </select>

                <select name="status" class="w-32 rounded-xl border border-slate-300 bg-white px-2.5 py-2 text-xs font-bold text-slate-700 focus:border-slate-500 focus:outline-none">
                    <option value="">Semua</option>
                    <option value="active" @selected(($status ?? '') === 'active')>Aktif</option>
                    <option value="inactive" @selected(($status ?? '') === 'inactive')>Nonaktif</option>
                </select>

                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-extrabold text-white transition hover:bg-black">
                    Cari
                </button>

                @if(($search ?? '') !== '' || ($status ?? '') !== '')
                    <a href="{{ route('hrd.employees.index', ['leave_year' => $leaveYear]) }}" class="flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50" title="Reset Filter">
                        ✕
                    </a>
                @endif
            </form>
        </div>

        {{-- MOBILE CARDS VIEW --}}
        <div class="space-y-2.5 lg:hidden">
            @forelse ($employees as $employee)
                @php
                    $hasUser = (bool) $employee->user;
                    $mustChangePassword = (bool) ($employee->user?->must_change_password ?? false);
                    $annualBalance = $employee->employment_group === 'asia' ? $employee->leaveBalances->first() : null;
                @endphp

                <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-xs transition hover:border-slate-300">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h3 class="text-sm font-extrabold text-slate-900 truncate">{{ $employee->nama }}</h3>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                {{ $employee->employee_code }} • {{ $employee->jabatan ?: '-' }}
                            </p>
                            <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                <span class="text-[10px] font-bold text-slate-500 truncate max-w-[160px]">
                                    {{ $employee->source_kategori_karyawan_name ?: 'Belum Sync' }}
                                </span>
                                @if ($employee->employment_group === 'asia')
                                    <span class="rounded border border-sky-200 bg-sky-50 px-1.5 py-0.5 text-[9px] font-extrabold text-sky-700">ASIA</span>
                                @elseif ($employee->employment_group === 'outsourcing')
                                    <span class="rounded border border-orange-200 bg-orange-50 px-1.5 py-0.5 text-[9px] font-extrabold text-orange-700">OUTSOURCING</span>
                                @endif
                            </div>
                        </div>

                        <span class="shrink-0 rounded-lg border px-2 py-0.5 text-[10px] font-extrabold {{ $employee->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-100 text-slate-500' }}">
                            {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    @if ($employee->employment_group === 'asia')
                        <div class="mt-2.5 flex items-center justify-between rounded-lg border border-emerald-100 bg-emerald-50/60 px-3 py-2 text-xs">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800">Cuti {{ $leaveYear }}</span>
                                <div class="text-[11px] text-emerald-700">Jatah: {{ $annualBalance?->entitlement ?? 12 }} • Terpakai: {{ $annualBalance?->used ?? 0 }}</div>
                            </div>
                            <div class="text-right font-black text-emerald-800">
                                <span class="text-base">{{ $annualBalance?->remaining ?? 12 }}</span>
                                <span class="text-[10px] font-bold">sisa</span>
                            </div>
                        </div>
                    @endif

                    <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-2.5">
                        <span id="mobilePasswordBadge-{{ $employee->id }}" class="rounded border px-2 py-0.5 text-[10px] font-extrabold {{ !$hasUser ? 'border-rose-200 bg-rose-50 text-rose-700' : ($mustChangePassword ? 'border-amber-200 bg-amber-50 text-amber-800' : 'border-emerald-200 bg-emerald-50 text-emerald-700') }}">
                            {{ !$hasUser ? 'Akun Tidak Ada' : ($mustChangePassword ? 'Wajib Ganti PW' : 'PW Aktif') }}
                        </span>

                        <button type="button"
                                class="openEmployeeModalBtn rounded-lg border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-bold text-slate-800 transition hover:bg-slate-100"
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
                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-6 text-center">
                    <p class="text-xs font-bold text-slate-500">Data karyawan tidak ditemukan.</p>
                </div>
            @endforelse
        </div>

        {{-- DESKTOP TABLE VIEW --}}
        <div class="hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs lg:block">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Karyawan</th>
                        <th class="px-4 py-3 text-left">Jabatan</th>
                        <th class="px-4 py-3 text-left">Kategori / Group</th>
                        <th class="px-4 py-3 text-left">Login (ID)</th>
                        <th class="px-4 py-3 text-left">Status Password</th>
                        <th class="px-4 py-3 text-left">Saldo Cuti {{ $leaveYear }}</th>
                        <th class="px-4 py-3 text-left">Keaktifan</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($employees as $employee)
                        @php
                            $hasUser = (bool) $employee->user;
                            $mustChangePassword = (bool) ($employee->user?->must_change_password ?? false);
                            $annualBalance = $employee->employment_group === 'asia' ? $employee->leaveBalances->first() : null;
                        @endphp
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $employee->nama }}</div>
                                <div class="font-mono text-[11px] font-bold text-slate-400">{{ $employee->employee_code }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600 font-medium">{{ $employee->jabatan ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="text-[11px] font-bold text-slate-700">{{ $employee->source_kategori_karyawan_name ?: '-' }}</div>
                                <div class="mt-0.5">
                                    @if ($employee->employment_group === 'asia')
                                        <span class="rounded border border-sky-200 bg-sky-50 px-1.5 py-0.5 text-[9px] font-extrabold text-sky-700">ASIA</span>
                                    @elseif ($employee->employment_group === 'outsourcing')
                                        <span class="rounded border border-orange-200 bg-orange-50 px-1.5 py-0.5 text-[9px] font-extrabold text-orange-700">OUTSOURCING</span>
                                    @else
                                        <span class="rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[9px] font-bold text-slate-400">Belum Sync</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-slate-800">{{ $employee->user?->username ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <span id="passwordBadge-{{ $employee->id }}" class="rounded border px-2 py-0.5 text-[10px] font-extrabold {{ !$hasUser ? 'border-rose-200 bg-rose-50 text-rose-700' : ($mustChangePassword ? 'border-amber-200 bg-amber-50 text-amber-800' : 'border-emerald-200 bg-emerald-50 text-emerald-700') }}">
                                    {{ !$hasUser ? 'Akun Kosong' : ($mustChangePassword ? 'Wajib Ganti' : 'Aktif') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($employee->employment_group === 'asia')
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-sm font-black text-emerald-700">{{ $annualBalance?->remaining ?? 12 }}</span>
                                        <span class="text-[10px] font-bold text-slate-400">sisa</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400">Jatah: {{ $annualBalance?->entitlement ?? 12 }} • Pakai: {{ $annualBalance?->used ?? 0 }}</div>
                                @else
                                    <span class="text-[11px] font-medium text-slate-400">Tidak ada annual</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded border px-2 py-0.5 text-[10px] font-extrabold {{ $employee->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-100 text-slate-500' }}">
                                    {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button type="button"
                                        class="openEmployeeModalBtn rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-bold text-slate-700 transition hover:bg-slate-100"
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
                            <td colspan="8" class="px-4 py-8 text-center text-xs font-bold text-slate-400">Data karyawan tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($employees->hasPages())
            <div class="pt-2">
                {{ $employees->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL GLOBAL DETAIL KARYAWAN --}}
    {{-- ========================================================= --}}
    <div id="globalEmployeeModal" class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-950/50 px-3 py-4 backdrop-blur-xs">
        <div id="globalEmployeeModalBox" class="max-h-[92vh] w-full max-w-md scale-95 overflow-y-auto rounded-2xl border border-slate-200 bg-white opacity-0 shadow-2xl transition-all duration-200">
            {{-- Header --}}
            <div class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-100 bg-white/95 px-4 py-3 backdrop-blur">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Detail Karyawan</h3>
                    <p id="modalEmployeeCodeHeader" class="text-xs font-mono text-slate-400">-</p>
                </div>
                <button type="button" id="closeGlobalEmployeeModal" class="rounded-lg p-1 text-slate-400 hover:text-slate-700">✕</button>
            </div>

            <div class="p-4 space-y-3 text-xs">
                {{-- Info Utama --}}
                <div class="rounded-xl bg-slate-50 p-3">
                    <div id="modalNama" class="text-sm font-extrabold text-slate-900 leading-tight">-</div>
                    <div id="modalJabatanCode" class="mt-0.5 text-xs text-slate-500 font-medium">-</div>
                </div>

                {{-- Grid Detail Singkat --}}
                <div class="grid grid-cols-2 gap-2">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-2.5">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">ID Login</span>
                        <span id="modalUsername" class="mt-0.5 block font-mono font-extrabold text-slate-800">-</span>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-2.5">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Keaktifan</span>
                        <span id="modalActiveStatus" class="mt-0.5 block font-bold">-</span>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-2.5">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">ID FaceLog</span>
                        <span id="modalFacelogId" class="mt-0.5 block font-mono font-bold text-slate-700">-</span>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-2.5">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal Masuk</span>
                        <span id="modalTanggalMasuk" class="mt-0.5 block font-bold text-slate-700">-</span>
                    </div>
                </div>

                {{-- Kategori & Group --}}
                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-2.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Kategori & Group</span>
                    <div class="mt-1 flex items-center justify-between gap-2">
                        <span id="modalKategori" class="font-bold text-slate-800">-</span>
                        <span id="modalGroupBadge">-</span>
                    </div>
                </div>

                {{-- Saldo Cuti (Khusus ASIA) --}}
                <div id="modalLeaveSection" class="hidden rounded-xl border border-emerald-100 bg-emerald-50/60 p-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800">Saldo Cuti <span id="modalLeaveYear"></span></span>
                        <span id="modalRemainingDays" class="text-lg font-black text-emerald-800">12</span>
                    </div>
                    <div class="mt-1.5 grid grid-cols-3 gap-1.5 text-center">
                        <div class="rounded-lg bg-white/80 p-1.5">
                            <span class="block text-[9px] text-slate-400">Jatah</span>
                            <span id="modalEntitlementDays" class="block font-bold text-slate-800">-</span>
                        </div>
                        <div class="rounded-lg bg-white/80 p-1.5">
                            <span class="block text-[9px] text-slate-400">Terpakai</span>
                            <span id="modalUsedDays" class="block font-bold text-amber-700">-</span>
                        </div>
                        <div class="rounded-lg bg-white/80 p-1.5">
                            <span class="block text-[9px] text-slate-400">Sisa</span>
                            <span id="modalRemainingDaysBottom" class="block font-bold text-emerald-700">-</span>
                        </div>
                    </div>
                </div>

                {{-- Reset Password Section --}}
                <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Password</span>
                        <span id="modalPasswordStatusBadge" class="mt-0.5 inline-block rounded px-2 py-0.5 text-[10px] font-extrabold">-</span>
                    </div>
                    <button type="button" id="modalResetPasswordBtn" class="rounded-lg border border-amber-300 bg-amber-100 px-3 py-1.5 text-xs font-extrabold text-amber-900 hover:bg-amber-200">
                        Reset Password
                    </button>
                </div>

                {{-- Kotak Hasil Reset Password --}}
                <div id="modalResetResultBox" class="hidden space-y-1.5 rounded-xl border border-sky-200 bg-sky-50 p-3">
                    <span class="text-[11px] font-bold text-sky-900">Password Sementara Baru:</span>
                    <div class="flex items-center justify-between rounded-lg border border-sky-200 bg-white p-2 font-mono">
                        <span id="modalNewPasswordText" class="text-sm font-black tracking-widest text-slate-900">-</span>
                        <button type="button" id="modalCopyPasswordBtn" class="rounded bg-slate-900 px-2.5 py-1 text-[10px] font-bold text-white hover:bg-black">
                            Salin
                        </button>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 bg-slate-50 px-4 py-2.5 text-right">
                <button type="button" id="closeGlobalEmployeeModalBottom" class="rounded-xl border border-slate-300 bg-white px-4 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL KONFIRMASI RESET PASSWORD --}}
    {{-- ========================================================= --}}
    <div id="resetPasswordConfirmModal" class="fixed inset-0 z-[130] hidden items-center justify-center bg-slate-950/60 px-4 backdrop-blur-xs">
        <div id="resetPasswordConfirmBox" class="w-full max-w-sm scale-95 rounded-2xl border border-slate-200 bg-white p-4 opacity-0 shadow-2xl transition-all duration-200 text-xs">
            <h3 class="text-sm font-extrabold text-slate-900">Reset Password Karyawan?</h3>
            <p class="mt-1.5 text-slate-600 leading-relaxed">
                Password untuk <strong id="resetEmployeeName" class="text-slate-900">-</strong> akan diganti dengan 6 digit password acak baru.
            </p>

            <div id="resetPasswordError" class="mt-2.5 hidden rounded-lg border border-rose-200 bg-rose-50 p-2 text-rose-700 font-medium"></div>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" id="cancelResetPassword" class="rounded-lg border border-slate-300 px-3.5 py-1.5 font-bold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="button" id="confirmResetPassword" class="rounded-lg bg-amber-600 px-3.5 py-1.5 font-extrabold text-white transition hover:bg-amber-700">Ya, Reset</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let activeEmployee = null;

            // DOM Elements Global Modal
            const globalModal = document.getElementById('globalEmployeeModal');
            const globalBox = document.getElementById('globalEmployeeModalBox');
            const closeBtnTop = document.getElementById('closeGlobalEmployeeModal');
            const closeBtnBottom = document.getElementById('closeGlobalEmployeeModalBottom');

            // DOM Confirm Modal
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

            // Event Klik Tombol Detail
            document.querySelectorAll('.openEmployeeModalBtn').forEach(btn => {
                btn.addEventListener('click', () => {
                    activeEmployee = JSON.parse(btn.dataset.employee);

                    // Isi data modal
                    document.getElementById('modalEmployeeCodeHeader').textContent = `ID: ${activeEmployee.code}`;
                    document.getElementById('modalNama').textContent = activeEmployee.nama;
                    document.getElementById('modalJabatanCode').textContent = `${activeEmployee.jabatan} • ${activeEmployee.code}`;
                    document.getElementById('modalUsername').textContent = activeEmployee.username;
                    
                    const activeEl = document.getElementById('modalActiveStatus');
                    activeEl.textContent = activeEmployee.is_active ? 'Aktif' : 'Nonaktif';
                    activeEl.className = activeEmployee.is_active ? 'mt-0.5 block font-bold text-emerald-700' : 'mt-0.5 block font-bold text-slate-500';

                    document.getElementById('modalFacelogId').textContent = activeEmployee.facelog_id;
                    document.getElementById('modalTanggalMasuk').textContent = activeEmployee.tanggal_masuk;
                    document.getElementById('modalKategori').textContent = activeEmployee.kategori;

                    // Group Badge
                    const groupBadge = document.getElementById('modalGroupBadge');
                    if (activeEmployee.group === 'asia') {
                        groupBadge.className = 'rounded border border-sky-200 bg-sky-50 px-1.5 py-0.5 text-[10px] font-extrabold text-sky-700';
                        groupBadge.textContent = 'ASIA';
                    } else if (activeEmployee.group === 'outsourcing') {
                        groupBadge.className = 'rounded border border-orange-200 bg-orange-50 px-1.5 py-0.5 text-[10px] font-extrabold text-orange-700';
                        groupBadge.textContent = 'OUTSOURCING';
                    } else {
                        groupBadge.className = 'rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] font-bold text-slate-500';
                        groupBadge.textContent = 'Belum Sync';
                    }

                    // Saldo Cuti
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

                    // Password Status Badge
                    const pwBadge = document.getElementById('modalPasswordStatusBadge');
                    if (!activeEmployee.has_user) {
                        pwBadge.className = 'mt-0.5 inline-block rounded border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-extrabold text-rose-700';
                        pwBadge.textContent = 'Akun Kosong';
                    } else if (activeEmployee.must_change_password) {
                        pwBadge.className = 'mt-0.5 inline-block rounded border border-amber-200 bg-amber-50 px-2 py-0.5 text-[10px] font-extrabold text-amber-800';
                        pwBadge.textContent = 'Wajib Ganti';
                    } else {
                        pwBadge.className = 'mt-0.5 inline-block rounded border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-extrabold text-emerald-700';
                        pwBadge.textContent = 'Aktif';
                    }

                    // Tombol Reset PW
                    document.getElementById('modalResetPasswordBtn').style.display = activeEmployee.has_user ? 'inline-block' : 'none';
                    document.getElementById('modalResetResultBox').classList.add('hidden');

                    toggleModal(globalModal, globalBox, true);
                });
            });

            closeBtnTop?.addEventListener('click', () => toggleModal(globalModal, globalBox, false));
            closeBtnBottom?.addEventListener('click', () => toggleModal(globalModal, globalBox, false));
            globalModal?.addEventListener('click', (e) => { if (e.target === globalModal) toggleModal(globalModal, globalBox, false); });

            // Buka Modal Konfirmasi Reset PW
            document.getElementById('modalResetPasswordBtn')?.addEventListener('click', () => {
                if (!activeEmployee) return;
                resetNameText.textContent = activeEmployee.nama;
                resetError?.classList.add('hidden');
                toggleModal(confirmModal, confirmBox, true);
            });

            cancelResetBtn?.addEventListener('click', () => toggleModal(confirmModal, confirmBox, false));
            confirmModal?.addEventListener('click', (e) => { if (e.target === confirmModal) toggleModal(confirmModal, confirmBox, false); });

            // Eksekusi Reset Password via Fetch
            confirmResetBtn?.addEventListener('click', async () => {
                if (!activeEmployee) return;
                const originalText = confirmResetBtn.innerHTML;
                confirmResetBtn.disabled = true;
                confirmResetBtn.innerHTML = 'Memproses...';

                try {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('leave_year', activeEmployee.leave_year);

                    const response = await fetch(activeEmployee.reset_url, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Reset password gagal.');

                    // Tampilkan password baru di modal detail
                    document.getElementById('modalNewPasswordText').textContent = data.data.password;
                    document.getElementById('modalResetResultBox').classList.remove('hidden');

                    // Update badge di tabel dan mobile card
                    const pwId = `passwordBadge-${activeEmployee.id}`;
                    const mobId = `mobilePasswordBadge-${activeEmployee.id}`;
                    [pwId, mobId].forEach(id => {
                        const el = document.getElementById(id);
                        if (el) {
                            el.textContent = id.startsWith('passwordBadge-') ? 'Wajib Ganti' : 'Wajib Ganti PW';
                            el.className = 'rounded border border-amber-200 bg-amber-50 px-2 py-0.5 text-[10px] font-extrabold text-amber-800';
                        }
                    });

                    // Update badge di dalam modal
                    const pwModalBadge = document.getElementById('modalPasswordStatusBadge');
                    pwModalBadge.textContent = 'Wajib Ganti';
                    pwModalBadge.className = 'mt-0.5 inline-block rounded border border-amber-200 bg-amber-50 px-2 py-0.5 text-[10px] font-extrabold text-amber-800';

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

            // Tombol Salin Password
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

            // Flash timeout
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
