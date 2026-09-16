<x-app-layout>

    <x-slot name="headerTitle">
        <div>
            <h1 class="text-lg font-extrabold text-slate-900">
                Kredensial Karyawan
            </h1>
            <p class="text-xs font-medium text-slate-500">
                Reset massal dan export password sementara.
            </p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5">

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">
                {{ session('warning') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <div class="font-extrabold">Proses belum dapat dijalankan.</div>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-xs font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-slate-400">
                    Master Admin
                </p>
                <h1 class="mt-1 text-xl font-extrabold text-slate-900 sm:text-2xl">
                    Kredensial Login Karyawan
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Password di sini hanya password sementara yang dibuat admin.
                </p>
            </div>

            <a
                href="{{ route('master.employee-credentials.export', array_filter($filters, fn ($value) => $value !== '')) }}"
                class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-emerald-700"
            >
                Export Kredensial Excel
            </a>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                    Hasil Filter
                </div>
                <div class="mt-1 text-2xl font-black text-slate-900">
                    {{ number_format($summary['matching_employees'] ?? 0) }}
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-amber-600">
                    Wajib Ganti Password
                </div>
                <div class="mt-1 text-2xl font-black text-amber-800">
                    {{ number_format($summary['must_change_password'] ?? 0) }}
                </div>
            </div>

            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 shadow-sm">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-sky-600">
                    Password Sementara Tersimpan
                </div>
                <div class="mt-1 text-2xl font-black text-sky-800">
                    {{ number_format($summary['valid_credentials'] ?? 0) }}
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- SINGLE CARD: FILTER + PILIH + RESET --}}
        {{-- ========================================================= --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="text-sm font-extrabold text-slate-900">
                            Pilih & Reset Karyawan
                        </div>
                        <div class="mt-0.5 text-xs leading-5 text-slate-500">
                            Cari, filter, pilih karyawan, lalu reset password atau ID Login + password dari satu tempat.
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-500">
                            Tampil:
                            <span id="liveEmployeeVisibleCount" class="font-black text-slate-900">
                                {{ $employees->count() }}
                            </span>
                        </div>

                        <div class="rounded-xl border border-blue-200 bg-blue-50 px-3.5 py-2 text-xs font-bold text-blue-700">
                            Dipilih:
                            <span id="selectedEmployeeResetCount" class="font-black">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
                <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">

                    <div class="lg:col-span-5">
                        <label for="liveEmployeeSearch" class="mb-1.5 block text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                            Live Search
                        </label>

                        <input
                            type="text"
                            id="liveEmployeeSearch"
                            placeholder="Cari nama / ID / login / jabatan..."
                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm focus:border-slate-500 focus:outline-none"
                            autocomplete="off"
                        >
                    </div>

                    <form
                        method="GET"
                        action="{{ route('master.employee-credentials.index') }}"
                        id="credentialFilterForm"
                        class="contents"
                    >
                        <div class="lg:col-span-2">
                            <label class="mb-1.5 block text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                                Area
                            </label>

                            <select
                                name="area"
                                onchange="this.form.submit()"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm focus:border-slate-500 focus:outline-none"
                            >
                                <option value="">Semua Area</option>
                                <option value="52" @selected(($filters['area'] ?? '') === '52')>Area 52</option>
                                <option value="27" @selected(($filters['area'] ?? '') === '27')>Area 27</option>
                                <option value="other" @selected(($filters['area'] ?? '') === 'other')>Area Lain</option>
                            </select>
                        </div>

                        <div class="lg:col-span-3">
                            <label class="mb-1.5 block text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                                Bagian
                            </label>

                            <select
                                name="category"
                                onchange="this.form.submit()"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm focus:border-slate-500 focus:outline-none"
                            >
                                <option value="">Semua Bagian</option>

                                @foreach ($categories as $categoryOption)
                                    <option
                                        value="{{ $categoryOption }}"
                                        @selected(($filters['category'] ?? '') === $categoryOption)
                                    >
                                        {{ $categoryOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end lg:col-span-2">
                            <a
                                href="{{ route('master.employee-credentials.index') }}"
                                class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-extrabold text-slate-600 transition hover:bg-slate-100"
                            >
                                Reset Filter
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="text-xs font-semibold text-slate-500">
                    Centang karyawan yang ingin direset. Live search hanya menyaring tampilan.
                </div>

                <button
                    type="button"
                    id="toggleAllEmployees"
                    class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-extrabold text-slate-700 transition hover:bg-slate-50"
                >
                    Pilih Semua Tampil
                </button>
            </div>

            <form
                method="POST"
                action="{{ route('master.employee-credentials.mass-reset') }}"
                id="selectedEmployeeResetForm"
            >
                @csrf

                <input type="hidden" name="area" value="{{ $filters['area'] ?? '' }}">
                <input type="hidden" name="category" value="{{ $filters['category'] ?? '' }}">

                <div class="max-h-[560px] overflow-auto">
                    <table class="min-w-full">

                        <thead class="sticky top-0 z-10 bg-slate-50 shadow-sm">
                            <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                                <th class="w-12 px-5 py-4">
                                    <input
                                        type="checkbox"
                                        id="employeeResetHeaderCheckbox"
                                        class="h-4.5 w-4.5 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                                    >
                                </th>

                                <th class="px-5 py-4 font-extrabold">Karyawan</th>
                                <th class="px-5 py-4 font-extrabold">Area</th>
                                <th class="px-5 py-4 font-extrabold">Bagian</th>
                                <th class="px-5 py-4 font-extrabold">Status</th>
                            </tr>
                        </thead>

                        <tbody id="employeeResetTableBody" class="divide-y divide-slate-100">
                            @forelse ($employees as $employee)
                                @php
                                    $employeeUser = $employee->user;

                                    $areaLabel = match ((int) $employee->source_device_id) {
                                        1, 2 => '52',
                                        3 => '27',
                                        default => 'Lain',
                                    };

                                    $liveSearchText = strtolower(
                                        trim(
                                            ($employee->nama ?? '')
                                            . ' '
                                            . ($employee->employee_code ?? '')
                                            . ' '
                                            . ($employee->jabatan ?? '')
                                            . ' '
                                            . ($employee->source_kategori_karyawan_name ?? '')
                                            . ' '
                                            . ($employeeUser?->username ?? '')
                                        )
                                    );
                                @endphp

                                <tr
                                    class="employeeResetRow hover:bg-slate-50/70"
                                    data-search="{{ $liveSearchText }}"
                                >
                                    <td class="px-5 py-4 align-top">
                                        <input
                                            type="checkbox"
                                            name="employee_ids[]"
                                            value="{{ $employee->id }}"
                                            class="employeeResetCheckbox h-4.5 w-4.5 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                                        >
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="font-extrabold text-slate-900">
                                            {{ $employee->nama }}
                                        </div>

                                        <div class="mt-0.5 text-xs font-bold text-slate-500">
                                            {{ $employee->employee_code ?? '-' }}
                                            @if ($employee->jabatan)
                                                • {{ $employee->jabatan }}
                                            @endif
                                        </div>

                                        <div class="mt-1 text-[11px] font-semibold text-slate-500">
                                            Login aktif:
                                            <span class="font-mono font-extrabold text-slate-800">
                                                {{ $employeeUser?->username ?? '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="rounded-lg border border-cyan-200 bg-cyan-50 px-2 py-1 text-[10px] font-extrabold text-cyan-700">
                                            AREA {{ $areaLabel }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="rounded-lg border border-blue-200 bg-blue-50 px-2 py-1 text-[10px] font-extrabold text-blue-700">
                                            {{ $employee->source_kategori_karyawan_name ?: '-' }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex flex-col gap-1">
                                            @if ($employeeUser?->must_change_username)
                                                <span class="rounded-lg border border-violet-200 bg-violet-50 px-2.5 py-1 text-[10px] font-extrabold text-violet-700">
                                                    Wajib Ganti ID
                                                </span>
                                            @else
                                                <span class="rounded-lg border border-sky-200 bg-sky-50 px-2.5 py-1 text-[10px] font-extrabold text-sky-700">
                                                    ID Aktif
                                                </span>
                                            @endif

                                            @if ($employeeUser?->must_change_password)
                                                <span class="rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1 text-[10px] font-extrabold text-amber-700">
                                                    Wajib Ganti PW
                                                </span>
                                            @else
                                                <span class="rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[10px] font-extrabold text-emerald-700">
                                                    Password Aktif
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-sm font-bold text-slate-500">
                                        Tidak ada karyawan pada hasil filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <div class="border-t border-slate-200 bg-slate-50/80 px-5 py-4 sm:px-6">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">

                        <div>
                            <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                                Mode Reset Karyawan Terpilih
                            </div>

                            <div class="mt-2 flex flex-wrap gap-4 text-xs font-bold text-slate-700">
                                <label class="inline-flex cursor-pointer items-center gap-2">
                                    <input
                                        type="radio"
                                        name="reset_mode"
                                        value="password_only"
                                        checked
                                        class="border-slate-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    Password saja
                                </label>

                                <label class="inline-flex cursor-pointer items-center gap-2">
                                    <input
                                        type="radio"
                                        name="reset_mode"
                                        value="login_and_password"
                                        class="border-slate-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    ID Login + Password
                                </label>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row">
                            <button
                                type="submit"
                                id="resetSelectedEmployeesButton"
                                disabled
                                class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-40"
                            >
                                Reset Karyawan Terpilih
                            </button>

                            <button
                                type="button"
                                id="openMassResetModal"
                                class="rounded-xl border border-amber-300 bg-amber-100 px-5 py-3 text-sm font-extrabold text-amber-900 shadow-sm transition hover:bg-amber-200"
                            >
                                Reset Semua Hasil Filter
                            </button>
                        </div>

                    </div>
                </div>
            </form>

        </div>


        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <div class="text-sm font-extrabold text-slate-900">
                    Password Sementara Tersimpan
                </div>
                <div class="mt-0.5 text-xs text-slate-500">
                    Hanya credential yang belum kedaluwarsa dan akun masih wajib ganti password.
                </div>
            </div>

            <div class="max-h-[520px] overflow-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                            <th class="px-5 py-4 font-extrabold">Karyawan</th>
                            <th class="px-5 py-4 font-extrabold">Area / Bagian</th>
                            <th class="px-5 py-4 font-extrabold">ID Login</th>
                            <th class="px-5 py-4 font-extrabold">Password Sementara</th>
                            <th class="px-5 py-4 font-extrabold">Dibuat</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($credentials as $credential)
                            @php
                                $employee = $credential->employee;
                                $user = $credential->user;
                                $areaLabel = match ((int) ($employee?->source_device_id ?? 0)) {
                                    1, 2 => '52',
                                    3 => '27',
                                    default => 'Lain',
                                };
                                $passwordId = 'credentialPassword-' . $credential->id;
                            @endphp

                            <tr>
                                <td class="px-5 py-4">
                                    <div class="font-extrabold text-slate-900">
                                        {{ $employee?->nama ?? '-' }}
                                    </div>
                                    <div class="mt-0.5 text-xs font-bold text-slate-500">
                                        {{ $employee?->employee_code ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        <span class="rounded-lg border border-cyan-200 bg-cyan-50 px-2 py-0.5 text-[10px] font-extrabold text-cyan-700">
                                            AREA {{ $areaLabel }}
                                        </span>
                                        <span class="rounded-lg border border-blue-200 bg-blue-50 px-2 py-0.5 text-[10px] font-extrabold text-blue-700">
                                            {{ $employee?->source_kategori_karyawan_name ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-5 py-4 font-mono text-sm font-extrabold text-slate-800">
                                    {{ $user?->username ?? '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <span
                                            id="{{ $passwordId }}"
                                            class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-1.5 font-mono text-sm font-black tracking-wider text-slate-900"
                                        >
                                            {{ $credential->password_encrypted }}
                                        </span>

                                        <button
                                            type="button"
                                            data-copy-target="{{ $passwordId }}"
                                            class="copyCredentialButton rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-extrabold text-white"
                                        >
                                            Salin
                                        </button>
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-xs font-semibold text-slate-600">
                                    {{ $credential->generated_at?->format('d/m/Y H:i') ?? '-' }}
                                    @if ($credential->expires_at)
                                        <div class="mt-1 text-[10px] text-slate-400">
                                            Exp: {{ $credential->expires_at->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-sm font-bold text-slate-500">
                                    Belum ada password sementara yang tersimpan untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div
        id="massResetModal"
        class="fixed inset-0 z-[140] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
    >
        <div
            id="massResetBox"
            class="w-full max-w-lg scale-95 rounded-3xl bg-white p-5 opacity-0 shadow-2xl transition-all duration-200"
        >
            <h3 class="text-lg font-extrabold text-slate-900">
                Reset Password Massal?
            </h3>

            <p class="mt-2 text-sm leading-6 text-slate-600">
                Tindakan ini akan mengganti password
                <strong>{{ number_format($summary['matching_employees'] ?? 0) }} akun</strong>
                sesuai filter saat ini.
            </p>

            <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs leading-5 text-amber-800">
                <div>
                    Area:
                    <strong>
                        {{ ($filters['area'] ?? '') !== '' ? 'Area ' . $filters['area'] : 'Semua Area' }}
                    </strong>
                </div>
                <div>
                    Bagian:
                    <strong>
                        {{ ($filters['category'] ?? '') !== '' ? $filters['category'] : 'Semua Bagian' }}
                    </strong>
                </div>
                <div>
                    Search:
                    <strong>
                        {{ ($filters['search'] ?? '') !== '' ? $filters['search'] : '-' }}
                    </strong>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('master.employee-credentials.mass-reset') }}"
                id="massResetForm"
                class="mt-5"
            >
                @csrf

                <input type="hidden" name="search" value="{{ $filters['search'] ?? '' }}">
                <input type="hidden" name="area" value="{{ $filters['area'] ?? '' }}">
                <input type="hidden" name="category" value="{{ $filters['category'] ?? '' }}">


                <div class="mb-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">

                    <div class="text-xs font-extrabold uppercase tracking-wider text-slate-500">
                        Yang Akan Direset
                    </div>

                    <div class="mt-3 space-y-2">

                        <label class="flex cursor-pointer items-start gap-3">
                            <input
                                type="radio"
                                name="reset_mode"
                                value="password_only"
                                checked
                                class="mt-0.5 border-slate-300 text-blue-600 focus:ring-blue-500"
                            >

                            <span>
                                <span class="block text-sm font-extrabold text-slate-800">
                                    Password saja
                                </span>
                                <span class="block text-xs text-slate-500">
                                    ID Login aktif tetap dipertahankan.
                                </span>
                            </span>
                        </label>


                        <label class="flex cursor-pointer items-start gap-3">
                            <input
                                type="radio"
                                name="reset_mode"
                                value="login_and_password"
                                class="mt-0.5 border-slate-300 text-blue-600 focus:ring-blue-500"
                            >

                            <span>
                                <span class="block text-sm font-extrabold text-slate-800">
                                    ID Login + Password
                                </span>
                                <span class="block text-xs text-slate-500">
                                    ID Login dikembalikan ke ID Karyawan dan user wajib membuat ID Login baru lagi.
                                </span>
                            </span>
                        </label>

                    </div>

                </div>


                @if (
                    ($filters['search'] ?? '') === ''
                    && ($filters['area'] ?? '') === ''
                    && ($filters['category'] ?? '') === ''
                )
                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4">
                        <input
                            type="checkbox"
                            name="allow_all"
                            value="1"
                            required
                            class="mt-0.5 h-5 w-5 rounded border-rose-300 text-rose-600"
                        >
                        <span class="text-xs font-bold leading-5 text-rose-800">
                            Saya memahami bahwa tanpa filter, seluruh akun karyawan aktif akan direset.
                        </span>
                    </label>
                @endif

                <div class="mt-5 flex justify-end gap-3">
                    <button
                        type="button"
                        id="cancelMassReset"
                        class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-extrabold text-slate-700"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        id="confirmMassReset"
                        class="rounded-xl bg-rose-600 px-5 py-3 text-sm font-extrabold text-white hover:bg-rose-700"
                    >
                        Ya, Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- AJAX BATCH RESET PROGRESS --}}
    {{-- ========================================================= --}}
    <div
        id="batchResetProgressModal"
        class="fixed inset-0 z-[170] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm"
    >
        <div class="w-full max-w-xl rounded-3xl bg-white p-6 shadow-2xl">

            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">
                        Memproses Reset Akun
                    </h3>

                    <p
                        id="batchResetProgressText"
                        class="mt-1 text-sm font-semibold text-slate-500"
                    >
                        Menyiapkan proses...
                    </p>
                </div>

                <div
                    id="batchResetProgressPercent"
                    class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-black text-slate-800"
                >
                    0%
                </div>
            </div>


            <div class="mt-5 h-3 overflow-hidden rounded-full bg-slate-100">
                <div
                    id="batchResetProgressBar"
                    class="h-full w-0 rounded-full bg-blue-600 transition-all duration-300"
                ></div>
            </div>


            <div class="mt-5 grid grid-cols-3 gap-3">

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 text-center">
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        Diproses
                    </div>
                    <div
                        id="batchProcessedCount"
                        class="mt-1 text-xl font-black text-slate-900"
                    >
                        0
                    </div>
                </div>

                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-3 text-center">
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600">
                        Berhasil
                    </div>
                    <div
                        id="batchSuccessCount"
                        class="mt-1 text-xl font-black text-emerald-800"
                    >
                        0
                    </div>
                </div>

                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-3 text-center">
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-rose-600">
                        Gagal
                    </div>
                    <div
                        id="batchFailedCount"
                        class="mt-1 text-xl font-black text-rose-800"
                    >
                        0
                    </div>
                </div>

            </div>


            <div
                id="batchFailedListBox"
                class="mt-4 hidden max-h-44 overflow-y-auto rounded-2xl border border-rose-200 bg-rose-50 p-3"
            >
                <div class="mb-2 text-xs font-extrabold uppercase tracking-wider text-rose-700">
                    Akun Gagal / Terkunci
                </div>

                <div
                    id="batchFailedList"
                    class="space-y-1 text-xs font-semibold text-rose-800"
                ></div>
            </div>


            <div
                id="batchResetDoneActions"
                class="mt-5 hidden justify-end gap-2"
            >
                <button
                    type="button"
                    id="batchResetCloseButton"
                    class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 hover:bg-slate-50"
                >
                    Tutup
                </button>

                <button
                    type="button"
                    id="batchResetReloadButton"
                    class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white hover:bg-black"
                >
                    Refresh Data
                </button>
            </div>

        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('massResetModal');
            const box = document.getElementById('massResetBox');
            const openButton = document.getElementById('openMassResetModal');
            const cancelButton = document.getElementById('cancelMassReset');
            const form = document.getElementById('massResetForm');
            const confirmButton = document.getElementById('confirmMassReset');

            function openModal() {
                modal?.classList.remove('hidden');
                modal?.classList.add('flex');
                document.body.classList.add('overflow-hidden');

                setTimeout(function () {
                    box?.classList.remove('scale-95', 'opacity-0');
                    box?.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            function closeModal() {
                box?.classList.remove('scale-100', 'opacity-100');
                box?.classList.add('scale-95', 'opacity-0');

                setTimeout(function () {
                    modal?.classList.remove('flex');
                    modal?.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }, 180);
            }

            openButton?.addEventListener('click', openModal);
            cancelButton?.addEventListener('click', closeModal);

            modal?.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            /*
            |--------------------------------------------------------------------------
            | LIVE SEARCH + CHECKBOX RESET KARYAWAN
            |--------------------------------------------------------------------------
            */

            const liveEmployeeSearch =
                document.getElementById(
                    'liveEmployeeSearch'
                );

            const liveEmployeeVisibleCount =
                document.getElementById(
                    'liveEmployeeVisibleCount'
                );

            const employeeRows =
                Array.from(
                    document.querySelectorAll(
                        '.employeeResetRow'
                    )
                );

            const employeeResetCheckboxes =
                Array.from(
                    document.querySelectorAll(
                        '.employeeResetCheckbox'
                    )
                );

            const employeeResetHeaderCheckbox =
                document.getElementById(
                    'employeeResetHeaderCheckbox'
                );

            const toggleAllEmployees =
                document.getElementById(
                    'toggleAllEmployees'
                );

            const selectedEmployeeResetCount =
                document.getElementById(
                    'selectedEmployeeResetCount'
                );

            const resetSelectedEmployeesButton =
                document.getElementById(
                    'resetSelectedEmployeesButton'
                );

            const selectedEmployeeResetForm =
                document.getElementById(
                    'selectedEmployeeResetForm'
                );


            function getVisibleCheckboxes() {

                return employeeRows
                    .filter(
                        row =>
                            ! row.classList.contains(
                                'hidden'
                            )
                    )
                    .map(
                        row =>
                            row.querySelector(
                                '.employeeResetCheckbox'
                            )
                    )
                    .filter(Boolean);
            }


            function refreshEmployeeResetSelection() {

                const selectedCount =
                    employeeResetCheckboxes
                        .filter(
                            checkbox =>
                                checkbox.checked
                        )
                        .length;

                const visibleCheckboxes =
                    getVisibleCheckboxes();

                const visibleSelectedCount =
                    visibleCheckboxes
                        .filter(
                            checkbox =>
                                checkbox.checked
                        )
                        .length;


                if (selectedEmployeeResetCount) {
                    selectedEmployeeResetCount.textContent =
                        selectedCount;
                }


                if (resetSelectedEmployeesButton) {
                    resetSelectedEmployeesButton.disabled =
                        selectedCount === 0;
                }


                if (employeeResetHeaderCheckbox) {

                    employeeResetHeaderCheckbox.checked =
                        visibleCheckboxes.length > 0
                        &&
                        visibleSelectedCount
                        ===
                        visibleCheckboxes.length;

                    employeeResetHeaderCheckbox.indeterminate =
                        visibleSelectedCount > 0
                        &&
                        visibleSelectedCount
                        <
                        visibleCheckboxes.length;
                }


                if (toggleAllEmployees) {

                    toggleAllEmployees.textContent =
                        visibleCheckboxes.length > 0
                        &&
                        visibleSelectedCount
                        ===
                        visibleCheckboxes.length
                            ? 'Hapus Semua Tampil'
                            : 'Pilih Semua Tampil';
                }
            }


            function applyLiveEmployeeSearch() {

                const keyword =
                    (
                        liveEmployeeSearch
                            ?.value
                        ?? ''
                    )
                        .trim()
                        .toLowerCase();

                let visibleCount = 0;


                employeeRows.forEach(
                    function (row) {

                        const searchable =
                            (
                                row.dataset.search
                                ?? ''
                            )
                                .toLowerCase();

                        const isVisible =
                            keyword === ''
                            ||
                            searchable.includes(
                                keyword
                            );


                        row.classList.toggle(
                            'hidden',
                            ! isVisible
                        );


                        if (isVisible) {
                            visibleCount++;
                        }
                    }
                );


                if (liveEmployeeVisibleCount) {
                    liveEmployeeVisibleCount.textContent =
                        visibleCount;
                }


                refreshEmployeeResetSelection();
            }


            liveEmployeeSearch
                ?.addEventListener(
                    'input',
                    applyLiveEmployeeSearch
                );


            employeeResetCheckboxes.forEach(
                function (checkbox) {

                    checkbox.addEventListener(
                        'change',
                        refreshEmployeeResetSelection
                    );
                }
            );


            employeeResetHeaderCheckbox
                ?.addEventListener(
                    'change',
                    function () {

                        getVisibleCheckboxes()
                            .forEach(
                                checkbox => {
                                    checkbox.checked =
                                        employeeResetHeaderCheckbox.checked;
                                }
                            );

                        refreshEmployeeResetSelection();
                    }
                );


            toggleAllEmployees
                ?.addEventListener(
                    'click',
                    function () {

                        const visibleCheckboxes =
                            getVisibleCheckboxes();

                        const shouldCheck =
                            visibleCheckboxes.some(
                                checkbox =>
                                    ! checkbox.checked
                            );


                        visibleCheckboxes.forEach(
                            checkbox => {
                                checkbox.checked =
                                    shouldCheck;
                            }
                        );


                        refreshEmployeeResetSelection();
                    }
                );



            /*
            |--------------------------------------------------------------------------
            | AJAX BATCH RESET - ANTI 504
            |--------------------------------------------------------------------------
            */

            const batchResetUrl =
                @json(
                    route(
                        'master.employee-credentials.mass-reset-batch'
                    )
                );

            const batchSize =
                10;

            const batchProgressModal =
                document.getElementById(
                    'batchResetProgressModal'
                );

            const batchProgressText =
                document.getElementById(
                    'batchResetProgressText'
                );

            const batchProgressPercent =
                document.getElementById(
                    'batchResetProgressPercent'
                );

            const batchProgressBar =
                document.getElementById(
                    'batchResetProgressBar'
                );

            const batchProcessedCount =
                document.getElementById(
                    'batchProcessedCount'
                );

            const batchSuccessCount =
                document.getElementById(
                    'batchSuccessCount'
                );

            const batchFailedCount =
                document.getElementById(
                    'batchFailedCount'
                );

            const batchFailedListBox =
                document.getElementById(
                    'batchFailedListBox'
                );

            const batchFailedList =
                document.getElementById(
                    'batchFailedList'
                );

            const batchDoneActions =
                document.getElementById(
                    'batchResetDoneActions'
                );

            const batchCloseButton =
                document.getElementById(
                    'batchResetCloseButton'
                );

            const batchReloadButton =
                document.getElementById(
                    'batchResetReloadButton'
                );


            let batchIsRunning =
                false;


            function chunkIds(
                ids,
                size
            ) {
                const chunks =
                    [];

                for (
                    let i = 0;
                    i < ids.length;
                    i += size
                ) {
                    chunks.push(
                        ids.slice(
                            i,
                            i + size
                        )
                    );
                }

                return chunks;
            }


            function showBatchProgress() {

                batchProgressModal
                    ?.classList
                    .remove(
                        'hidden'
                    );

                batchProgressModal
                    ?.classList
                    .add(
                        'flex'
                    );

                document
                    .body
                    .classList
                    .add(
                        'overflow-hidden'
                    );
            }


            function hideBatchProgress() {

                if (
                    batchIsRunning
                ) {
                    return;
                }

                batchProgressModal
                    ?.classList
                    .remove(
                        'flex'
                    );

                batchProgressModal
                    ?.classList
                    .add(
                        'hidden'
                    );

                document
                    .body
                    .classList
                    .remove(
                        'overflow-hidden'
                    );
            }


            function resetBatchProgressUi(
                total
            ) {
                if (
                    batchProgressText
                ) {
                    batchProgressText.textContent =
                        '0 dari '
                        + total
                        + ' akun diproses...';
                }

                if (
                    batchProgressPercent
                ) {
                    batchProgressPercent.textContent =
                        '0%';
                }

                if (
                    batchProgressBar
                ) {
                    batchProgressBar.style.width =
                        '0%';
                }

                if (
                    batchProcessedCount
                ) {
                    batchProcessedCount.textContent =
                        '0';
                }

                if (
                    batchSuccessCount
                ) {
                    batchSuccessCount.textContent =
                        '0';
                }

                if (
                    batchFailedCount
                ) {
                    batchFailedCount.textContent =
                        '0';
                }

                if (
                    batchFailedList
                ) {
                    batchFailedList.innerHTML =
                        '';
                }

                batchFailedListBox
                    ?.classList
                    .add(
                        'hidden'
                    );

                batchDoneActions
                    ?.classList
                    .remove(
                        'flex'
                    );

                batchDoneActions
                    ?.classList
                    .add(
                        'hidden'
                    );
            }


            function updateBatchProgressUi(
                processed,
                total,
                successCount,
                failedCount
            ) {
                const percent =
                    total > 0
                        ? Math.round(
                            (
                                processed
                                /
                                total
                            )
                            *
                            100
                        )
                        : 100;

                if (
                    batchProgressText
                ) {
                    batchProgressText.textContent =
                        processed
                        + ' dari '
                        + total
                        + ' akun selesai diproses.';
                }

                if (
                    batchProgressPercent
                ) {
                    batchProgressPercent.textContent =
                        percent
                        + '%';
                }

                if (
                    batchProgressBar
                ) {
                    batchProgressBar.style.width =
                        percent
                        + '%';
                }

                if (
                    batchProcessedCount
                ) {
                    batchProcessedCount.textContent =
                        processed;
                }

                if (
                    batchSuccessCount
                ) {
                    batchSuccessCount.textContent =
                        successCount;
                }

                if (
                    batchFailedCount
                ) {
                    batchFailedCount.textContent =
                        failedCount;
                }
            }


            function appendBatchFailures(
                failures
            ) {
                if (
                    ! Array.isArray(
                        failures
                    )
                    ||
                    failures.length
                    === 0
                ) {
                    return;
                }

                batchFailedListBox
                    ?.classList
                    .remove(
                        'hidden'
                    );

                failures.forEach(
                    function (
                        failure
                    ) {
                        const row =
                            document.createElement(
                                'div'
                            );

                        row.textContent =
                            (
                                failure.nama
                                ?? 'Karyawan'
                            )
                            +
                            ' ('
                            +
                            (
                                failure.employee_code
                                ?? '-'
                            )
                            +
                            ') - '
                            +
                            (
                                failure.message
                                ?? 'Gagal'
                            );

                        batchFailedList
                            ?.appendChild(
                                row
                            );
                    }
                );
            }


            async function runBatchReset(
                employeeIds,
                resetMode
            ) {
                if (
                    batchIsRunning
                    ||
                    employeeIds.length
                    === 0
                ) {
                    return;
                }

                batchIsRunning =
                    true;

                const chunks =
                    chunkIds(
                        employeeIds,
                        batchSize
                    );

                const total =
                    employeeIds.length;

                let processed =
                    0;

                let successCount =
                    0;

                let failedCount =
                    0;

                resetBatchProgressUi(
                    total
                );

                showBatchProgress();


                for (
                    let index = 0;
                    index < chunks.length;
                    index++
                ) {
                    const ids =
                        chunks[
                            index
                        ];

                    if (
                        batchProgressText
                    ) {
                        batchProgressText.textContent =
                            'Batch '
                            +
                            (
                                index
                                +
                                1
                            )
                            +
                            ' dari '
                            +
                            chunks.length
                            +
                            ' sedang diproses...';
                    }

                    try {
                        const response =
                            await fetch(
                                batchResetUrl,
                                {
                                    method:
                                        'POST',

                                    headers: {
                                        'Content-Type':
                                            'application/json',

                                        'Accept':
                                            'application/json',

                                        'X-CSRF-TOKEN':
                                            @json(
                                                csrf_token()
                                            ),

                                        'X-Requested-With':
                                            'XMLHttpRequest',
                                    },

                                    body:
                                        JSON.stringify({
                                            employee_ids:
                                                ids,

                                            reset_mode:
                                                resetMode,
                                        }),
                                }
                            );

                        const data =
                            await response.json();


                        if (
                            ! response.ok
                        ) {
                            throw new Error(
                                data?.message
                                ??
                                'Batch gagal diproses.'
                            );
                        }


                        successCount +=
                            Number(
                                data.success_count
                                ??
                                0
                            );

                        failedCount +=
                            Number(
                                data.failed_count
                                ??
                                0
                            );

                        processed +=
                            Number(
                                data.processed
                                ??
                                ids.length
                            );

                        appendBatchFailures(
                            data.failed
                            ??
                            []
                        );

                    }
                    catch (
                        error
                    ) {
                        /*
                        |--------------------------------------------------------------------------
                        | Jika satu REQUEST batch gagal total, hanya 10 akun batch itu
                        | yang dianggap gagal. Batch berikutnya tetap dilanjutkan.
                        |--------------------------------------------------------------------------
                        */

                        processed +=
                            ids.length;

                        failedCount +=
                            ids.length;

                        appendBatchFailures(
                            ids.map(
                                function (
                                    employeeId
                                ) {
                                    const checkbox =
                                        document.querySelector(
                                            '.employeeResetCheckbox[value="'
                                            +
                                            employeeId
                                            +
                                            '"]'
                                        );

                                    const row =
                                        checkbox
                                            ?.closest(
                                                '.employeeResetRow'
                                            );

                                    const name =
                                        row
                                            ?.querySelector(
                                                'td:nth-child(2) .font-extrabold'
                                            )
                                            ?.textContent
                                            ?.trim()
                                        ??
                                        'Karyawan';

                                    return {
                                        nama:
                                            name,

                                        employee_code:
                                            '#'
                                            +
                                            employeeId,

                                        message:
                                            error.message
                                            ??
                                            'Request batch gagal.',
                                    };
                                }
                            )
                        );
                    }


                    updateBatchProgressUi(
                        processed,
                        total,
                        successCount,
                        failedCount
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | JEDA KECIL AGAR SERVER TIDAK DITEMBAK TERUS-MENERUS
                    |--------------------------------------------------------------------------
                    */
                    await new Promise(
                        resolve =>
                            setTimeout(
                                resolve,
                                150
                            )
                    );
                }


                batchIsRunning =
                    false;


                if (
                    batchProgressText
                ) {
                    batchProgressText.textContent =
                        failedCount > 0
                            ? 'Selesai. '
                                +
                                successCount
                                +
                                ' berhasil, '
                                +
                                failedCount
                                +
                                ' gagal.'
                            : 'Selesai. Semua '
                                +
                                successCount
                                +
                                ' akun berhasil direset.';
                }


                batchDoneActions
                    ?.classList
                    .remove(
                        'hidden'
                    );

                batchDoneActions
                    ?.classList
                    .add(
                        'flex'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | RESET KARYAWAN TERPILIH
            |--------------------------------------------------------------------------
            */

            selectedEmployeeResetForm
                ?.addEventListener(
                    'submit',
                    async function (
                        event
                    ) {
                        event.preventDefault();

                        const selected =
                            employeeResetCheckboxes
                                .filter(
                                    checkbox =>
                                        checkbox.checked
                                );

                        if (
                            selected.length
                            === 0
                        ) {
                            alert(
                                'Pilih minimal satu karyawan.'
                            );

                            return;
                        }


                        const resetMode =
                            selectedEmployeeResetForm
                                .querySelector(
                                    'input[name="reset_mode"]:checked'
                                )
                                ?.value
                            ??
                            'password_only';


                        const confirmed =
                            confirm(
                                'Reset '
                                +
                                selected.length
                                +
                                ' karyawan terpilih?'
                            );


                        if (
                            ! confirmed
                        ) {
                            return;
                        }


                        await runBatchReset(
                            selected.map(
                                checkbox =>
                                    Number(
                                        checkbox.value
                                    )
                            ),
                            resetMode
                        );
                    }
                );


            /*
            |--------------------------------------------------------------------------
            | RESET MASSAL SESUAI FILTER SERVER
            |--------------------------------------------------------------------------
            */

            form?.addEventListener(
                'submit',
                async function (
                    event
                ) {
                    event.preventDefault();


                    const allowAll =
                        form.querySelector(
                            'input[name="allow_all"]'
                        );

                    if (
                        allowAll
                        &&
                        ! allowAll.checked
                    ) {
                        alert(
                            'Centang konfirmasi reset seluruh akun terlebih dahulu.'
                        );

                        return;
                    }


                    const resetMode =
                        form.querySelector(
                            'input[name="reset_mode"]:checked'
                        )
                        ?.value
                    ??
                    'password_only';


                    /*
                    |--------------------------------------------------------------------------
                    | Semua checkbox yang ada di halaman adalah hasil filter
                    | server Area/Bagian saat ini. Live search tidak mengubah
                    | target mass reset.
                    |--------------------------------------------------------------------------
                    */

                    const ids =
                        employeeResetCheckboxes
                            .map(
                                checkbox =>
                                    Number(
                                        checkbox.value
                                    )
                            );


                    if (
                        ids.length
                        === 0
                    ) {
                        alert(
                            'Tidak ada akun pada hasil filter.'
                        );

                        return;
                    }


                    const confirmed =
                        confirm(
                            'Reset massal '
                            +
                            ids.length
                            +
                            ' akun? Proses akan dibagi menjadi batch kecil.'
                        );


                    if (
                        ! confirmed
                    ) {
                        return;
                    }


                    closeModal();


                    await runBatchReset(
                        ids,
                        resetMode
                    );
                }
            );


            batchCloseButton
                ?.addEventListener(
                    'click',
                    hideBatchProgress
                );


            batchReloadButton
                ?.addEventListener(
                    'click',
                    function () {
                        window.location.reload();
                    }
                );


            applyLiveEmployeeSearch();


            document.querySelectorAll('.copyCredentialButton').forEach(function (button) {
                button.addEventListener('click', async function () {
                    const target = document.getElementById(button.dataset.copyTarget);
                    if (!target) return;

                    try {
                        await navigator.clipboard.writeText(target.textContent.trim());

                        const oldText = button.textContent;
                        button.textContent = 'Tersalin!';

                        setTimeout(function () {
                            button.textContent = oldText;
                        }, 1200);
                    } catch (error) {}
                });
            });
        });
    </script>

</x-app-layout>
