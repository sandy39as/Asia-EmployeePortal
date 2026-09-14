<x-app-layout>
    <x-slot name="headerTitle">
        <div>
            <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Data Karyawan</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium">Manajemen akun Employee Portal.</p>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4">

        @if (session('success'))
            <div id="successFlash" class="rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- FILTER & SUMMARY PILLS --}}
        <div class="flex flex-col lg:flex-row gap-3 lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-xl border border-[#e2e8f0] bg-white px-3 py-1.5 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs">
                    Total: <strong class="text-slate-900 font-extrabold">{{ number_format($summary['total'] ?? 0) }}</strong>
                </span>
                <a href="{{ route('hrd.employees.index', ['status' => 'active']) }}" 
                   class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs sm:text-sm font-bold text-emerald-800 hover:bg-emerald-100 transition {{ ($status ?? '') === 'active' ? 'ring-2 ring-emerald-500' : '' }}">
                    Aktif: {{ number_format($summary['active'] ?? 0) }}
                </a>
                <a href="{{ route('hrd.employees.index', ['status' => 'inactive']) }}" 
                   class="rounded-xl border border-[#e2e8f0] bg-white px-3 py-1.5 text-xs sm:text-sm font-bold text-slate-600 hover:bg-slate-50 transition {{ ($status ?? '') === 'inactive' ? 'ring-2 ring-slate-400' : '' }}">
                    Nonaktif: {{ number_format($summary['inactive'] ?? 0) }}
                </a>
                <span class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs sm:text-sm font-bold text-amber-800">
                    Wajib Ganti PW: {{ number_format($summary['must_change_password'] ?? 0) }}
                </span>

                <span class="rounded-xl border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs sm:text-sm font-bold text-sky-800">
                    ASIA: {{ number_format($summary['asia'] ?? 0) }}
                </span>

                <span class="rounded-xl border border-orange-200 bg-orange-50 px-3 py-1.5 text-xs sm:text-sm font-bold text-orange-800">
                    Outsourcing: {{ number_format($summary['outsourcing'] ?? 0) }}
                </span>
            </div>

            {{-- SEARCH FORM --}}
            <form method="GET" action="{{ route('hrd.employees.index') }}" class="flex flex-wrap sm:flex-nowrap gap-2">
                <input type="text"
                       name="search"
                       value="{{ $search ?? '' }}"
                       placeholder="Cari nama / ID..."
                       class="w-full sm:w-60 rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 shadow-xs focus:border-slate-500 focus:outline-none">

                <select
                    name="leave_year"
                    class="w-full sm:w-32 rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm font-bold text-slate-700 shadow-xs focus:border-slate-500 focus:outline-none"
                    onchange="this.form.submit()"
                >
                    @for ($year = now()->year; $year >= now()->year - 3; $year--)
                        <option value="{{ $year }}" @selected(($leaveYear ?? now()->year) == $year)>
                            {{ $year }}
                        </option>
                    @endfor
                </select>

                <select name="status" class="w-full sm:w-40 rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm font-bold text-slate-700 shadow-xs focus:border-slate-500 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(($status ?? '') === 'active')>Aktif</option>
                    <option value="inactive" @selected(($status ?? '') === 'inactive')>Nonaktif</option>
                </select>

                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-sm font-extrabold shadow-xs transition">
                    Cari
                </button>
                <a href="{{ route('hrd.employees.index') }}" class="px-4 py-2.5 rounded-xl border border-[#d1d5db] bg-white text-slate-700 hover:bg-slate-50 text-sm font-bold flex items-center justify-center">
                    Reset
                </a>
            </form>
        </div>

        {{-- MOBILE VIEW --}}
        <div class="space-y-3 lg:hidden">
            @forelse ($employees as $employee)
                @php
                    $hasUser = (bool) $employee->user;
                    $mustChangePassword = (bool) ($employee->user?->must_change_password ?? false);
                    $modalId = 'employeeModal-' . $employee->id;
                    $modalBoxId = 'employeeModalBox-' . $employee->id;
                @endphp

                <div class="rounded-2xl border border-[#e2e8f0] bg-white p-5 space-y-3.5 shadow-xs">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            {{-- FONT NAMA DIPERBESAR --}}
                            <h3 class="text-base font-extrabold text-slate-900 leading-snug">{{ $employee->nama }}</h3>
                            <p class="text-xs sm:text-sm text-slate-500 font-bold mt-1">{{ $employee->employee_code }} • {{ $employee->jabatan ?: '-' }}</p>
                                <div class="mt-2 flex flex-wrap items-center gap-2">

                                    <span class="text-[11px] font-bold text-slate-500">
                                        {{ $employee->source_kategori_karyawan_name ?: 'Kategori belum tersinkron' }}
                                    </span>

                                    @if ($employee->employment_group === 'asia')

                                        <span class="rounded-lg border border-sky-200 bg-sky-50 px-2 py-0.5 text-[10px] font-extrabold text-sky-700">
                                            ASIA
                                        </span>

                                    @elseif ($employee->employment_group === 'outsourcing')

                                        <span class="rounded-lg border border-orange-200 bg-orange-50 px-2 py-0.5 text-[10px] font-extrabold text-orange-700">
                                            OUTSOURCING
                                        </span>

                                    @else

                                        <span class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-0.5 text-[10px] font-bold text-slate-500">
                                            Belum Sync
                                        </span>

                                    @endif

                                </div>
                        </div>

                        <span class="rounded-lg px-2.5 py-1 text-xs font-extrabold border {{ $employee->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                            {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    @php
                        $annualBalance =
                            $employee->employment_group === 'asia'
                                ? $employee->leaveBalances->first()
                                : null;
                    @endphp

                    @if ($employee->employment_group === 'asia')
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50/70 p-3.5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700">
                                        Saldo Cuti {{ $leaveYear }}
                                    </div>
                                    <div class="mt-1 text-xs font-medium text-emerald-700">
                                        Cuti tahunan karyawan ASIA
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div class="text-2xl font-black text-emerald-800">
                                        {{ $annualBalance?->remaining ?? 12 }}
                                    </div>
                                    <div class="text-[10px] font-bold text-emerald-600">
                                        hari tersisa
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 grid grid-cols-3 gap-2">
                                <div class="rounded-lg bg-white/80 p-2 text-center">
                                    <div class="text-[10px] font-bold text-slate-400">Jatah</div>
                                    <div class="mt-0.5 text-sm font-black text-slate-800">
                                        {{ $annualBalance?->entitlement ?? 12 }}
                                    </div>
                                </div>

                                <div class="rounded-lg bg-white/80 p-2 text-center">
                                    <div class="text-[10px] font-bold text-slate-400">Terpakai</div>
                                    <div class="mt-0.5 text-sm font-black text-amber-700">
                                        {{ $annualBalance?->used ?? 0 }}
                                    </div>
                                </div>

                                <div class="rounded-lg bg-white/80 p-2 text-center">
                                    <div class="text-[10px] font-bold text-slate-400">Sisa</div>
                                    <div class="mt-0.5 text-sm font-black text-emerald-700">
                                        {{ $annualBalance?->remaining ?? 12 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-3 border-t border-[#e2e8f0]">
                        <span id="mobilePasswordBadge-{{ $employee->id }}" class="rounded-lg px-2.5 py-1 text-xs font-extrabold border {{ !$hasUser ? 'bg-rose-50 text-rose-700 border-rose-200' : ($mustChangePassword ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }}">
                            {{ !$hasUser ? 'Akun Tidak Ada' : ($mustChangePassword ? 'Wajib Ganti Password' : 'Password Aktif') }}
                        </span>

                        <button type="button"
                                data-modal="{{ $modalId }}"
                                data-box="{{ $modalBoxId }}"
                                class="openEmployeeModal px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-900 font-extrabold text-xs sm:text-sm border border-[#d1d5db] transition">
                            Detail
                        </button>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-[#d1d5db] p-8 text-center bg-white">
                    <p class="text-sm font-bold text-slate-500">Data karyawan tidak ditemukan.</p>
                </div>
            @endforelse
        </div>

        {{-- DESKTOP TABLE VIEW --}}
        <div class="hidden lg:block overflow-hidden rounded-2xl border border-[#e2e8f0] bg-white shadow-xs">
            <table class="min-w-full divide-y divide-[#e2e8f0] text-sm">
                <thead class="bg-[#f8fafc] text-slate-600 font-bold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-5 py-3.5 text-left">Karyawan</th>
                        <th class="px-5 py-3.5 text-left">Jabatan</th>
                        <th class="px-5 py-3.5 text-left">Kategori / Group</th>
                        <th class="px-5 py-3.5 text-left">Login (ID)</th>
                        <th class="px-5 py-3.5 text-left">Status Password</th>
                        <th class="px-5 py-3.5 text-left">Saldo Cuti {{ $leaveYear }}</th>
                        <th class="px-5 py-3.5 text-left">Keaktifan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e2e8f0]">
                    @forelse ($employees as $employee)
                        @php
                            $hasUser = (bool) $employee->user;
                            $mustChangePassword = (bool) ($employee->user?->must_change_password ?? false);
                            $modalId = 'employeeModal-' . $employee->id;
                            $modalBoxId = 'employeeModalBox-' . $employee->id;
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-4 font-semibold text-slate-800">
                                <div>
                                    {{-- FONT NAMA TABEL DIPERBESAR --}}
                                    <div class="text-slate-900 font-extrabold text-base">{{ $employee->nama }}</div>
                                    <div class="text-xs text-slate-500 font-bold mt-0.5">{{ $employee->employee_code }}</div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-slate-700 font-medium">{{ $employee->jabatan ?: '-' }}</td>

                            <td class="px-5 py-4">

                                <div class="space-y-1.5">

                                    <div class="text-xs font-bold text-slate-700">
                                        {{ $employee->source_kategori_karyawan_name ?: '-' }}
                                    </div>

                                    @if ($employee->employment_group === 'asia')

                                        <span class="inline-flex rounded-lg border border-sky-200 bg-sky-50 px-2.5 py-1 text-[11px] font-extrabold text-sky-700">
                                            ASIA
                                        </span>

                                    @elseif ($employee->employment_group === 'outsourcing')

                                        <span class="inline-flex rounded-lg border border-orange-200 bg-orange-50 px-2.5 py-1 text-[11px] font-extrabold text-orange-700">
                                            OUTSOURCING
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-bold text-slate-500">
                                            Belum Sync
                                        </span>

                                    @endif

                                </div>

                            </td>

                            <td class="px-5 py-4 text-slate-900 font-mono font-bold">{{ $employee->user?->username ?: '-' }}</td>
                            <td class="px-5 py-4">
                                <span id="passwordBadge-{{ $employee->id }}" class="rounded-lg px-2.5 py-1 text-xs font-extrabold border {{ !$hasUser ? 'bg-rose-50 text-rose-700 border-rose-200' : ($mustChangePassword ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }}">
                                    {{ !$hasUser ? 'Akun Tidak Ada' : ($mustChangePassword ? 'Wajib Ganti' : 'Aktif') }}
                                </span>
                            </td>
                            @php
                                $annualBalance =
                                    $employee->employment_group === 'asia'
                                        ? $employee->leaveBalances->first()
                                        : null;
                            @endphp

                            <td class="px-5 py-4">
                                @if ($employee->employment_group === 'asia')
                                    <div class="min-w-[150px]">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg font-black text-emerald-700">
                                                {{ $annualBalance?->remaining ?? 12 }}
                                            </span>
                                            <span class="text-[11px] font-bold text-slate-500">
                                                hari sisa
                                            </span>
                                        </div>

                                        <div class="mt-1 text-[10px] font-semibold text-slate-400">
                                            Jatah {{ $annualBalance?->entitlement ?? 12 }}
                                            •
                                            Terpakai {{ $annualBalance?->used ?? 0 }}
                                        </div>
                                    </div>
                                @elseif ($employee->employment_group === 'outsourcing')
                                    <span class="text-xs font-bold text-slate-400">
                                        Tidak ada annual
                                    </span>
                                @else
                                    <span class="text-xs font-bold text-slate-400">
                                        -
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <span class="rounded-lg px-2.5 py-1 text-xs font-extrabold border {{ $employee->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                    {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button type="button"
                                        data-modal="{{ $modalId }}"
                                        data-box="{{ $modalBoxId }}"
                                        class="openEmployeeModal px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-900 font-extrabold text-xs sm:text-sm border border-[#d1d5db] transition">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-500 font-medium">Data karyawan tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($employees->hasPages())
            <div class="p-2">
                {{ $employees->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL DETAIL KARYAWAN --}}
    @foreach ($employees as $employee)
        @php
            $hasUser = (bool) $employee->user;
            $mustChangePassword = (bool) ($employee->user?->must_change_password ?? false);
            $modalId = 'employeeModal-' . $employee->id;
            $modalBoxId = 'employeeModalBox-' . $employee->id;
        @endphp

        <div id="{{ $modalId }}" class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-900/50 px-3 py-4 backdrop-blur-xs sm:px-4">
            <div id="{{ $modalBoxId }}" class="max-h-[92vh] w-full max-w-lg scale-95 overflow-y-auto rounded-2xl border border-[#e2e8f0] bg-white opacity-0 shadow-2xl transition-all duration-200">
                
                {{-- HEADER --}}
                <div class="sticky top-0 z-20 flex items-center justify-between border-b border-[#e2e8f0] bg-white/95 px-5 py-4 backdrop-blur">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Detail Karyawan</h3>
                        <p class="text-xs text-slate-500 font-bold">ID: {{ $employee->employee_code }}</p>
                    </div>
                    <button type="button" data-modal="{{ $modalId }}" data-box="{{ $modalBoxId }}" class="closeEmployeeModal p-1 rounded-lg text-slate-400 hover:text-slate-700">✕</button>
                </div>

                <div class="p-5 space-y-4">
                    <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                        <div class="text-base font-extrabold text-slate-900">{{ $employee->nama }}</div>
                        <div class="text-xs text-slate-600 font-bold mt-0.5">{{ $employee->jabatan ?: 'Staff' }} • {{ $employee->employee_code }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                            <span class="text-[11px] text-slate-500 font-bold uppercase block">ID Login</span>
                            <span class="text-slate-900 font-mono font-extrabold text-sm mt-1 block">{{ $employee->user?->username ?: '-' }}</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                            <span class="text-[11px] text-slate-500 font-bold uppercase block">Keaktifan</span>
                            <span class="inline-block mt-1 text-xs font-extrabold {{ $employee->is_active ? 'text-emerald-700' : 'text-slate-500' }}">
                                {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                            <span class="text-[11px] text-slate-500 font-bold uppercase block">ID FaceLog</span>
                            <span class="text-slate-800 font-bold text-xs mt-1 block">{{ $employee->source_karyawan_id ?? '-' }}</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                            <span class="text-[11px] text-slate-500 font-bold uppercase block">Tanggal Masuk</span>
                            <span class="text-slate-800 font-bold text-xs mt-1 block">
                                {{ $employee->tanggal_masuk ? \Carbon\Carbon::parse($employee->tanggal_masuk)->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">

                        <span class="text-[11px] text-slate-500 font-bold uppercase block">
                            Kategori FaceLog
                        </span>

                        <span class="text-slate-800 font-bold text-xs mt-1 block">
                            {{ $employee->source_kategori_karyawan_name ?: '-' }}
                        </span>

                        @if ($employee->source_kategori_karyawan_id)
                            <span class="mt-0.5 block text-[10px] font-semibold text-slate-400">
                                ID Kategori:
                                {{ $employee->source_kategori_karyawan_id }}
                            </span>
                        @endif

                    </div>


                    <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">

                        <span class="text-[11px] text-slate-500 font-bold uppercase block">
                            Group Karyawan
                        </span>

                        <div class="mt-1">

                            @if ($employee->employment_group === 'asia')

                                <span class="inline-flex rounded-lg border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-extrabold text-sky-700">
                                    ASIA
                                </span>

                            @elseif ($employee->employment_group === 'outsourcing')

                                <span class="inline-flex rounded-lg border border-orange-200 bg-orange-50 px-2.5 py-1 text-xs font-extrabold text-orange-700">
                                    OUTSOURCING
                                </span>

                            @else

                                <span class="inline-flex rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-bold text-slate-500">
                                    Belum Sync
                                </span>

                            @endif

                        </div>

                    </div>

                    @php
                        $annualBalance =
                            $employee->employment_group === 'asia'
                                ? $employee->leaveBalances->first()
                                : null;
                    @endphp

                    {{-- SALDO CUTI TAHUNAN --}}
                    <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4">

                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="block text-[11px] font-bold uppercase text-slate-500">
                                    Saldo Cuti Tahunan {{ $leaveYear }}
                                </span>

                                @if ($employee->employment_group === 'asia')
                                    <span class="mt-1 block text-xs font-medium text-slate-500">
                                        Berlaku untuk karyawan ASIA.
                                    </span>
                                @elseif ($employee->employment_group === 'outsourcing')
                                    <span class="mt-1 block text-xs font-medium text-orange-600">
                                        Karyawan outsourcing tidak memiliki saldo cuti tahunan.
                                    </span>
                                @else
                                    <span class="mt-1 block text-xs font-medium text-slate-400">
                                        Group karyawan belum tersinkron.
                                    </span>
                                @endif
                            </div>

                            @if ($employee->employment_group === 'asia')
                                <div class="text-right">
                                    <div class="text-3xl font-black text-emerald-700">
                                        {{ $annualBalance?->remaining ?? 12 }}
                                    </div>
                                    <div class="text-[10px] font-bold text-emerald-600">
                                        HARI TERSISA
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if ($employee->employment_group === 'asia')
                            <div class="mt-4 grid grid-cols-3 gap-2">

                                <div class="rounded-xl border border-slate-200 bg-white p-3 text-center">
                                    <div class="text-[10px] font-bold uppercase text-slate-400">
                                        Jatah
                                    </div>
                                    <div class="mt-1 text-lg font-black text-slate-900">
                                        {{ $annualBalance?->entitlement ?? 12 }}
                                    </div>
                                    <div class="text-[10px] font-semibold text-slate-400">
                                        hari
                                    </div>
                                </div>

                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-center">
                                    <div class="text-[10px] font-bold uppercase text-amber-600">
                                        Terpakai
                                    </div>
                                    <div class="mt-1 text-lg font-black text-amber-700">
                                        {{ $annualBalance?->used ?? 0 }}
                                    </div>
                                    <div class="text-[10px] font-semibold text-amber-500">
                                        hari
                                    </div>
                                </div>

                                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-center">
                                    <div class="text-[10px] font-bold uppercase text-emerald-600">
                                        Sisa
                                    </div>
                                    <div class="mt-1 text-lg font-black text-emerald-700">
                                        {{ $annualBalance?->remaining ?? 12 }}
                                    </div>
                                    <div class="text-[10px] font-semibold text-emerald-500">
                                        hari
                                    </div>
                                </div>

                            </div>
                        @endif

                    </div>

                    {{-- RESET PASSWORD SECTION --}}
                    <div class="p-4 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] flex items-center justify-between gap-3">
                        <div>
                            <span class="text-[11px] text-slate-500 font-bold uppercase block">Status Password</span>
                            <span id="modalPasswordBadge-{{ $employee->id }}" class="inline-block mt-1 rounded-md px-2.5 py-1 text-xs font-extrabold border {{ !$hasUser ? 'bg-rose-50 text-rose-700 border-rose-200' : ($mustChangePassword ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }}">
                                {{ !$hasUser ? 'Akun Tidak Tersedia' : ($mustChangePassword ? 'Wajib Ganti Password' : 'Password Aktif') }}
                            </span>
                        </div>

                        @if ($hasUser)
                            <button type="button"
                                    data-reset-open
                                    data-form="resetForm-{{ $employee->id }}"
                                    data-employee-id="{{ $employee->id }}"
                                    data-employee-name="{{ $employee->nama }}"
                                    class="rounded-xl border border-amber-300 bg-amber-100 hover:bg-amber-200 text-amber-900 px-4 py-2 text-xs font-extrabold transition">
                                Reset Password
                            </button>

                            <form id="resetForm-{{ $employee->id }}" action="{{ route('hrd.employees.reset-password', $employee) }}" method="POST" class="hidden">
                                @csrf
                                <input type="hidden" name="leave_year" value="{{ $leaveYear }}">
                            </form>
                        @endif
                    </div>

                    {{-- RESET RESULT --}}
                    <div id="passwordResult-{{ $employee->id }}" class="hidden p-4 rounded-xl border border-sky-200 bg-sky-50 space-y-2">
                        <div class="text-xs font-extrabold text-sky-900">Password Baru Sementara:</div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-white border border-sky-200 font-mono">
                            <span id="resultPassword-{{ $employee->id }}" class="text-base font-black text-slate-900 tracking-wider">-</span>
                            <button type="button" data-copy-password="{{ $employee->id }}" class="px-3.5 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-extrabold">
                                Salin
                            </button>
                        </div>
                    </div>
                </div>

                <div class="sticky bottom-0 z-20 flex justify-end border-t border-[#e2e8f0] bg-white/95 px-5 py-3.5 backdrop-blur">
                    <button type="button" data-modal="{{ $modalId }}" data-box="{{ $modalBoxId }}" class="closeEmployeeModal rounded-xl border border-[#d1d5db] px-5 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-50">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endforeach

    {{-- MODAL CONFIRM RESET PASSWORD --}}
    <div id="resetPasswordConfirmModal" class="fixed inset-0 z-[130] hidden items-center justify-center bg-slate-900/50 px-4 backdrop-blur-xs">
        <div id="resetPasswordConfirmBox" class="w-full max-w-sm scale-95 rounded-2xl border border-[#e2e8f0] bg-white p-5 opacity-0 shadow-2xl transition-all duration-200">
            <h3 class="text-base font-extrabold text-slate-900">Reset Password Karyawan?</h3>
            <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                Password untuk <strong id="resetEmployeeName" class="text-slate-900 font-bold">-</strong> akan diganti dengan password sementara baru.
            </p>

            <div id="resetPasswordError" class="mt-3 hidden rounded-lg border border-rose-200 bg-rose-50 p-2.5 text-xs text-rose-700"></div>

            <div class="mt-5 flex justify-end gap-2">
                <button type="button" id="cancelResetPassword" class="rounded-xl border border-[#d1d5db] px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="button" id="confirmResetPassword" class="rounded-xl bg-amber-600 hover:bg-amber-700 px-4 py-2 text-xs font-extrabold text-white transition">Ya, Reset</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let pendingResetForm = null;
            let pendingEmployeeId = null;

            const resetModal = document.getElementById('resetPasswordConfirmModal');
            const resetBox = document.getElementById('resetPasswordConfirmBox');
            const resetEmployeeName = document.getElementById('resetEmployeeName');
            const resetError = document.getElementById('resetPasswordError');
            const confirmResetButton = document.getElementById('confirmResetPassword');
            const cancelResetButton = document.getElementById('cancelResetPassword');

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
                    document.body.classList.remove('overflow-hidden');
                }, 180);
            }

            document.addEventListener('click', function (e) {
                const openBtn = e.target.closest('.openEmployeeModal');
                if (openBtn) openGenericModal(openBtn.dataset.modal, openBtn.dataset.box);

                const closeBtn = e.target.closest('.closeEmployeeModal');
                if (closeBtn) closeGenericModal(closeBtn.dataset.modal, closeBtn.dataset.box);

                const resetTrigger = e.target.closest('[data-reset-open]');
                if (resetTrigger) {
                    pendingResetForm = document.getElementById(resetTrigger.dataset.form);
                    pendingEmployeeId = resetTrigger.dataset.employeeId;
                    if (resetEmployeeName) resetEmployeeName.textContent = resetTrigger.dataset.employeeName;
                    resetError?.classList.add('hidden');
                    openGenericModal('resetPasswordConfirmModal', 'resetPasswordConfirmBox');
                }
            });

            cancelResetButton?.addEventListener('click', () => closeGenericModal('resetPasswordConfirmModal', 'resetPasswordConfirmBox'));

            confirmResetButton?.addEventListener('click', async function () {
                if (!pendingResetForm || !pendingEmployeeId) return;
                const originalText = confirmResetButton.innerHTML;
                confirmResetButton.disabled = true;
                confirmResetButton.innerHTML = 'Memproses...';

                try {
                    const response = await fetch(pendingResetForm.action, {
                        method: 'POST',
                        body: new FormData(pendingResetForm),
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const data = await response.json();

                    if (!response.ok) throw new Error(data.message || 'Reset password gagal.');

                    const result = document.getElementById(`passwordResult-${pendingEmployeeId}`);
                    const passwordEl = document.getElementById(`resultPassword-${pendingEmployeeId}`);
                    if (passwordEl) passwordEl.textContent = data.data.password;
                    result?.classList.remove('hidden');

                    [`passwordBadge-${pendingEmployeeId}`, `mobilePasswordBadge-${pendingEmployeeId}`, `modalPasswordBadge-${pendingEmployeeId}`].forEach(id => {
                        const badge = document.getElementById(id);
                        if (badge) {
                            badge.textContent = id.startsWith('passwordBadge-') ? 'Wajib Ganti' : 'Wajib Ganti Password';
                            badge.className = 'rounded-md px-2.5 py-1 text-xs font-extrabold border bg-amber-50 text-amber-800 border-amber-200';
                        }
                    });

                    closeGenericModal('resetPasswordConfirmModal', 'resetPasswordConfirmBox');
                } catch (err) {
                    if (resetError) {
                        resetError.textContent = err.message || 'Terjadi kesalahan.';
                        resetError.classList.remove('hidden');
                    }
                } finally {
                    confirmResetButton.disabled = false;
                    confirmResetButton.innerHTML = originalText;
                }
            });

            document.addEventListener('click', async function (e) {
                const copyBtn = e.target.closest('[data-copy-password]');
                if (!copyBtn) return;
                const employeeId = copyBtn.dataset.copyPassword;
                const passwordEl = document.getElementById(`resultPassword-${employeeId}`);
                if (!passwordEl) return;
                try {
                    await navigator.clipboard.writeText(passwordEl.textContent.trim());
                    const original = copyBtn.textContent;
                    copyBtn.textContent = 'Tersalin!';
                    setTimeout(() => copyBtn.textContent = original, 1200);
                } catch (e) {}
            });
        });
    </script>
</x-app-layout>
