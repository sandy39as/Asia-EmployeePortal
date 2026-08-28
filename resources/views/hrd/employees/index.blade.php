<x-app-layout>

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <x-slot name="header">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.8">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />

                    </svg>

                </div>


                <div>

                    <h2 class="text-xl font-black tracking-tight text-slate-900 dark:text-slate-100">
                        Data Karyawan
                    </h2>

                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                        Manajemen akun Employee Portal.
                    </p>

                </div>

            </div>


            <a href="{{ route('hrd.dashboard') }}"
               class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M15 19l-7-7 7-7" />

                </svg>

                Dashboard

            </a>

        </div>

    </x-slot>


    <div class="min-h-screen bg-slate-50 py-5 sm:py-6 dark:bg-slate-950">

        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-8">


            {{-- ================================================= --}}
            {{-- FLASH --}}
            {{-- ================================================= --}}

            @if (session('success'))

                <div id="successFlash"
                     class="mb-5 rounded-3xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-700 shadow-sm dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300">

                    {{ session('success') }}

                </div>

            @endif


            @if (session('error'))

                <div class="mb-5 rounded-3xl border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-700 shadow-sm dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300">

                    {{ session('error') }}

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- HERO --}}
            {{-- ================================================= --}}

            <div class="relative overflow-hidden rounded-[30px] bg-gradient-to-br from-[#1D4ED8] via-[#1E40AF] to-[#172554] p-5 text-white shadow-xl shadow-blue-900/10 sm:p-7">

                <div class="pointer-events-none absolute -right-20 -top-24 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>

                <div class="pointer-events-none absolute -bottom-24 left-1/3 h-52 w-52 rounded-full bg-sky-400/10 blur-3xl"></div>


                <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">


                    <div>

                        <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold text-blue-100 ring-1 ring-white/10">

                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>

                            Employee Management

                        </div>


                        <h1 class="mt-4 text-2xl font-black sm:text-3xl">
                            Data Karyawan
                        </h1>


                        <p class="mt-2 max-w-xl text-sm leading-6 text-blue-100/80">
                            Cari akun karyawan, lihat informasi Employee Portal, dan reset password tanpa berpindah halaman.
                        </p>

                    </div>


                    <div class="rounded-2xl bg-white/10 px-5 py-4 ring-1 ring-white/10 backdrop-blur">

                        <div class="text-xs font-bold uppercase tracking-wider text-blue-100/60">
                            Total Karyawan
                        </div>

                        <div class="mt-1 text-3xl font-black">
                            {{ number_format($summary['total'] ?? 0) }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- SUMMARY --}}
            {{-- ================================================= --}}

            <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4">


                <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5 dark:border-slate-800 dark:bg-slate-900">

                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total
                    </div>

                    <div class="mt-2 text-3xl font-black text-slate-900 dark:text-white">
                        {{ number_format($summary['total'] ?? 0) }}
                    </div>

                </div>


                <a href="{{ route('hrd.employees.index', ['status' => 'active']) }}"
                   class="rounded-3xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-5 dark:border-emerald-900/40 dark:bg-emerald-950/20">

                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-600">
                        Aktif
                    </div>

                    <div class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300">
                        {{ number_format($summary['active'] ?? 0) }}
                    </div>

                </a>


                <a href="{{ route('hrd.employees.index', ['status' => 'inactive']) }}"
                   class="rounded-3xl border border-slate-200 bg-slate-100 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-5 dark:border-slate-700 dark:bg-slate-800">

                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500">
                        Nonaktif
                    </div>

                    <div class="mt-2 text-3xl font-black text-slate-700 dark:text-slate-200">
                        {{ number_format($summary['inactive'] ?? 0) }}
                    </div>

                </a>


                <div class="rounded-3xl border border-amber-200 bg-amber-50 p-4 shadow-sm sm:p-5 dark:border-amber-900/40 dark:bg-amber-950/20">

                    <div class="text-xs font-bold uppercase tracking-wider text-amber-600">
                        Wajib Ganti Password
                    </div>

                    <div class="mt-2 text-3xl font-black text-amber-700 dark:text-amber-300">
                        {{ number_format($summary['must_change_password'] ?? 0) }}
                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- FILTER --}}
            {{-- ================================================= --}}

            <div class="mt-5 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">


                <div class="border-b border-slate-100 p-5 dark:border-slate-800">

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <div class="flex items-center gap-2">

                                <div class="h-5 w-1 rounded-full bg-blue-600"></div>

                                <h3 class="font-black text-slate-900 dark:text-slate-100">
                                    Cari Karyawan
                                </h3>

                            </div>

                            <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
                                Cari berdasarkan nama, ID, username, atau jabatan.
                            </p>

                        </div>


                        <div class="text-xs font-semibold text-slate-400">

                            Hasil:

                            <span class="font-black text-slate-700 dark:text-slate-200">
                                {{ number_format($employees->total()) }}
                            </span>

                        </div>

                    </div>

                </div>


                <form method="GET"
                      action="{{ route('hrd.employees.index') }}"
                      class="p-5">


                    <div class="grid grid-cols-1 gap-3 lg:grid-cols-[1fr_220px_auto]">


                        <div>

                            <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Pencarian
                            </label>

                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Nama / A0001 / jabatan..."
                                   autocomplete="off"
                                   class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-blue-900/30">

                        </div>


                        <div>

                            <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Status
                            </label>

                            <select name="status"
                                    class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-blue-900/30">

                                <option value="">
                                    Semua Status
                                </option>

                                <option value="active"
                                        @selected($status === 'active')>
                                    Aktif
                                </option>

                                <option value="inactive"
                                        @selected($status === 'inactive')>
                                    Nonaktif
                                </option>

                            </select>

                        </div>


                        <div class="flex items-end gap-2">

                            <a href="{{ route('hrd.employees.index') }}"
                               class="inline-flex h-[46px] items-center justify-center rounded-2xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300 dark:hover:bg-slate-800">

                                Reset

                            </a>


                            <button type="submit"
                                    class="inline-flex h-[46px] flex-1 items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700 lg:flex-none">

                                Cari

                            </button>

                        </div>

                    </div>

                </form>

            </div>


            {{-- ================================================= --}}
            {{-- MOBILE --}}
            {{-- ================================================= --}}

            <div class="mt-5 space-y-3 lg:hidden">


                @forelse ($employees as $employee)


                    @php

                        $hasUser =
                            (bool) $employee->user;

                        $mustChangePassword =
                            (bool) (
                                $employee->user
                                    ?->must_change_password
                                ?? false
                            );

                        $modalId =
                            'employeeModal-' .
                            $employee->id;

                        $modalBoxId =
                            'employeeModalBox-' .
                            $employee->id;

                    @endphp


                    <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">


                        <div class="flex items-start justify-between gap-3">


                            <div class="flex min-w-0 gap-3">


                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 text-sm font-black text-white">

                                    {{ strtoupper(
                                        substr(
                                            (string) $employee->nama,
                                            0,
                                            1
                                        )
                                    ) }}

                                </div>


                                <div class="min-w-0">

                                    <div class="truncate font-black text-slate-900 dark:text-slate-100">
                                        {{ $employee->nama }}
                                    </div>

                                    <div class="mt-1 text-xs font-black text-blue-600 dark:text-blue-400">
                                        {{ $employee->employee_code }}
                                    </div>

                                </div>

                            </div>


                            @if ($employee->is_active)

                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-black text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                                    Aktif
                                </span>

                            @else

                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-black text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    Nonaktif
                                </span>

                            @endif

                        </div>


                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">


                            <div>

                                <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400">
                                    Jabatan
                                </div>

                                <div class="mt-1 truncate font-semibold text-slate-700 dark:text-slate-200">
                                    {{ $employee->jabatan ?: '-' }}
                                </div>

                            </div>


                            <div>

                                <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400">
                                    Login
                                </div>

                                <div class="mt-1 truncate font-semibold text-slate-700 dark:text-slate-200">
                                    {{ $employee->user?->username ?: '-' }}
                                </div>

                            </div>

                        </div>


                        <div class="mt-4 flex items-center justify-between gap-3">


                            <span id="mobilePasswordBadge-{{ $employee->id }}"
                                  class="rounded-full px-3 py-1 text-[11px] font-black
                                    {{ ! $hasUser
                                        ? 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-300'
                                        : (
                                            $mustChangePassword
                                                ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300'
                                                : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300'
                                        )
                                    }}">

                                @if (! $hasUser)
                                    Akun Tidak Ada
                                @elseif ($mustChangePassword)
                                    Wajib Ganti Password
                                @else
                                    Password Aktif
                                @endif

                            </span>


                            <button type="button"
                                    data-modal="{{ $modalId }}"
                                    data-box="{{ $modalBoxId }}"
                                    class="openEmployeeModal inline-flex items-center justify-center rounded-2xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-black text-blue-700 transition hover:bg-blue-100 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-300">

                                Detail

                            </button>

                        </div>

                    </div>


                @empty


                    <div class="rounded-3xl border border-slate-200 bg-white py-14 text-center text-sm font-semibold text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                        Data karyawan tidak ditemukan.
                    </div>


                @endforelse

            </div>


            {{-- ================================================= --}}
            {{-- DESKTOP --}}
            {{-- ================================================= --}}

            <div class="mt-5 hidden overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm lg:block dark:border-slate-800 dark:bg-slate-900">


                <div class="overflow-x-auto">


                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">


                        <thead class="bg-slate-50 dark:bg-slate-800/80">


                            <tr>

                                <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Karyawan
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Jabatan
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Login
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Password
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-right text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">


                            @forelse ($employees as $employee)


                                @php

                                    $hasUser =
                                        (bool) $employee->user;

                                    $mustChangePassword =
                                        (bool) (
                                            $employee->user
                                                ?->must_change_password
                                            ?? false
                                        );

                                    $modalId =
                                        'employeeModal-' .
                                        $employee->id;

                                    $modalBoxId =
                                        'employeeModalBox-' .
                                        $employee->id;

                                @endphp


                                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">


                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-3">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 text-sm font-black text-white">

                                                {{ strtoupper(
                                                    substr(
                                                        (string) $employee->nama,
                                                        0,
                                                        1
                                                    )
                                                ) }}

                                            </div>


                                            <div>

                                                <div class="font-black text-slate-900 dark:text-slate-100">
                                                    {{ $employee->nama }}
                                                </div>

                                                <div class="mt-1 text-xs font-black text-blue-600 dark:text-blue-400">
                                                    {{ $employee->employee_code }}
                                                </div>

                                            </div>

                                        </div>

                                    </td>


                                    <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        {{ $employee->jabatan ?: '-' }}
                                    </td>


                                    <td class="px-5 py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                        {{ $employee->user?->username ?: '-' }}
                                    </td>


                                    <td class="px-5 py-4">

                                        <span id="passwordBadge-{{ $employee->id }}"
                                              class="rounded-full px-3 py-1 text-xs font-black
                                                {{ ! $hasUser
                                                    ? 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-300'
                                                    : (
                                                        $mustChangePassword
                                                            ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300'
                                                            : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300'
                                                    )
                                                }}">

                                            @if (! $hasUser)
                                                Akun Tidak Ada
                                            @elseif ($mustChangePassword)
                                                Wajib Ganti
                                            @else
                                                Aktif
                                            @endif

                                        </span>

                                    </td>


                                    <td class="px-5 py-4">

                                        @if ($employee->is_active)

                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                                                Aktif
                                            </span>

                                        @else

                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                                Nonaktif
                                            </span>

                                        @endif

                                    </td>


                                    <td class="px-5 py-4 text-right">

                                        <button type="button"
                                                data-modal="{{ $modalId }}"
                                                data-box="{{ $modalBoxId }}"
                                                class="openEmployeeModal inline-flex items-center justify-center gap-2 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-xs font-black text-blue-700 transition hover:bg-blue-100 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-300">

                                            Detail

                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="h-4 w-4"
                                                 fill="none"
                                                 viewBox="0 0 24 24"
                                                 stroke="currentColor"
                                                 stroke-width="2">

                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      d="M9 5l7 7-7 7" />

                                            </svg>

                                        </button>

                                    </td>

                                </tr>


                            @empty


                                <tr>

                                    <td colspan="6"
                                        class="px-5 py-14 text-center text-sm text-slate-500 dark:text-slate-400">

                                        Data karyawan tidak ditemukan.

                                    </td>

                                </tr>


                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- PAGINATION --}}
            {{-- ================================================= --}}

            @if ($employees->hasPages())

                <div class="mt-5 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">

                    {{ $employees->withQueryString()->links() }}

                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- EMPLOYEE DETAIL MODALS --}}
    {{-- ========================================================= --}}

    @foreach ($employees as $employee)


        @php

            $hasUser =
                (bool) $employee->user;

            $mustChangePassword =
                (bool) (
                    $employee->user
                        ?->must_change_password
                    ?? false
                );

            $modalId =
                'employeeModal-' .
                $employee->id;

            $modalBoxId =
                'employeeModalBox-' .
                $employee->id;

        @endphp


        <div id="{{ $modalId }}"
             class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-950/65 px-3 py-4 backdrop-blur-sm sm:px-4 sm:py-8">


            <div id="{{ $modalBoxId }}"
                 class="max-h-[94vh] w-full max-w-3xl scale-95 overflow-y-auto rounded-[28px] border border-slate-200 bg-white opacity-0 shadow-2xl transition-all duration-200 dark:border-slate-800 dark:bg-slate-900">


                {{-- HEADER --}}
                <div class="sticky top-0 z-20 flex items-start justify-between gap-4 border-b border-slate-100 bg-white/95 p-5 backdrop-blur dark:border-slate-800 dark:bg-slate-900/95">


                    <div>

                        <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">
                            Detail Karyawan
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Informasi akun Employee Portal.
                        </p>

                    </div>


                    <button type="button"
                            data-modal="{{ $modalId }}"
                            data-box="{{ $modalBoxId }}"
                            class="closeEmployeeModal inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300">

                        ✕

                    </button>

                </div>


                <div class="p-4 sm:p-6">


                    {{-- HERO EMPLOYEE --}}
                    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#1D4ED8] via-[#1E40AF] to-[#172554] p-5 text-white">


                        <div class="absolute -right-16 -top-20 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>


                        <div class="relative flex items-center gap-4">


                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-xl font-black ring-1 ring-white/20">

                                {{ strtoupper(
                                    substr(
                                        (string) $employee->nama,
                                        0,
                                        1
                                    )
                                ) }}

                            </div>


                            <div class="min-w-0">

                                <div class="truncate text-lg font-black">
                                    {{ $employee->nama }}
                                </div>

                                <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-blue-100">

                                    <span class="font-bold">
                                        {{ $employee->employee_code }}
                                    </span>

                                    @if ($employee->jabatan)

                                        <span>•</span>

                                        <span>
                                            {{ $employee->jabatan }}
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- DETAILS --}}
                    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">


                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                ID Login
                            </div>

                            <div class="mt-2 text-sm font-black text-blue-700 dark:text-blue-300">
                                {{ $employee->user?->username ?: '-' }}
                            </div>

                        </div>


                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                Jabatan
                            </div>

                            <div class="mt-2 text-sm font-black text-slate-800 dark:text-slate-100">
                                {{ $employee->jabatan ?: '-' }}
                            </div>

                        </div>


                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                Status
                            </div>

                            <div class="mt-2">

                                @if ($employee->is_active)

                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                                        Aktif
                                    </span>

                                @else

                                    <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-black text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                        Nonaktif
                                    </span>

                                @endif

                            </div>

                        </div>


                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                ID FaceLog
                            </div>

                            <div class="mt-2 text-sm font-black text-slate-800 dark:text-slate-100">
                                {{ $employee->source_karyawan_id ?? '-' }}
                            </div>

                        </div>


                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                Device
                            </div>

                            <div class="mt-2 text-sm font-black text-slate-800 dark:text-slate-100">
                                {{ $employee->source_device_id ?? '-' }}
                            </div>

                        </div>


                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">

                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                Tanggal Masuk
                            </div>

                            <div class="mt-2 text-sm font-black text-slate-800 dark:text-slate-100">

                                {{ $employee->tanggal_masuk
                                    ? \Carbon\Carbon::parse(
                                        $employee->tanggal_masuk
                                    )->format('d-m-Y')
                                    : '-' }}

                            </div>

                        </div>

                    </div>


                    {{-- PASSWORD STATUS --}}
                    <div class="mt-4 rounded-2xl border border-slate-200 p-4 dark:border-slate-800">


                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">


                            <div>

                                <div class="text-xs font-black uppercase tracking-wide text-slate-400">
                                    Status Password
                                </div>

                                <div class="mt-2">

                                    <span id="modalPasswordBadge-{{ $employee->id }}"
                                          class="rounded-full px-3 py-1 text-xs font-black
                                            {{ ! $hasUser
                                                ? 'bg-red-100 text-red-700'
                                                : (
                                                    $mustChangePassword
                                                        ? 'bg-amber-100 text-amber-700'
                                                        : 'bg-emerald-100 text-emerald-700'
                                                )
                                            }}">

                                        @if (! $hasUser)
                                            Akun Tidak Tersedia
                                        @elseif ($mustChangePassword)
                                            Wajib Ganti Password
                                        @else
                                            Password Aktif
                                        @endif

                                    </span>

                                </div>

                            </div>


                            @if ($hasUser)

                                <button type="button"
                                        data-reset-open
                                        data-form="resetForm-{{ $employee->id }}"
                                        data-employee-id="{{ $employee->id }}"
                                        data-employee-name="{{ $employee->nama }}"
                                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-amber-50 px-4 py-2.5 text-sm font-black text-amber-700 ring-1 ring-amber-200 transition hover:bg-amber-100 dark:bg-amber-950/30 dark:text-amber-300 dark:ring-amber-900/50">

                                    Reset Password

                                </button>


                                <form id="resetForm-{{ $employee->id }}"
                                      action="{{ route('hrd.employees.reset-password', $employee) }}"
                                      method="POST"
                                      class="hidden">

                                    @csrf

                                </form>

                            @endif

                        </div>

                    </div>


                    {{-- RESET RESULT --}}
                    <div id="passwordResult-{{ $employee->id }}"
                         class="mt-4 hidden rounded-3xl border border-blue-200 bg-blue-50 p-5 dark:border-blue-900/50 dark:bg-blue-950/20">


                        <div class="flex items-start gap-3">


                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300">

                                ✓

                            </div>


                            <div class="min-w-0 flex-1">

                                <div class="font-black text-blue-800 dark:text-blue-200">
                                    Password Sementara Baru
                                </div>

                                <div class="mt-1 text-xs leading-5 text-blue-600 dark:text-blue-400">
                                    Password hanya ditampilkan setelah proses reset ini.
                                </div>

                            </div>

                        </div>


                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">


                            <div class="rounded-2xl bg-white p-4 dark:bg-slate-900">

                                <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400">
                                    ID Login
                                </div>

                                <div id="resultUsername-{{ $employee->id }}"
                                     class="mt-2 font-black text-blue-700 dark:text-blue-300">
                                    -
                                </div>

                            </div>


                            <div class="rounded-2xl bg-white p-4 dark:bg-slate-900">

                                <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400">
                                    Password
                                </div>


                                <div class="mt-2 flex items-center justify-between gap-3">

                                    <span id="resultPassword-{{ $employee->id }}"
                                          class="text-xl font-black tracking-widest text-slate-900 dark:text-white">
                                        -
                                    </span>


                                    <button type="button"
                                            data-copy-password="{{ $employee->id }}"
                                            class="rounded-xl bg-blue-600 px-3 py-2 text-xs font-black text-white transition hover:bg-blue-700">

                                        Salin

                                    </button>

                                </div>

                            </div>

                        </div>


                        <div class="mt-3 text-xs font-semibold text-blue-700 dark:text-blue-300">
                            Karyawan wajib membuat password baru pada login berikutnya.
                        </div>

                    </div>


                    {{-- FOOTER --}}
                    <div class="mt-6 flex justify-end border-t border-slate-100 pt-5 dark:border-slate-800">

                        <button type="button"
                                data-modal="{{ $modalId }}"
                                data-box="{{ $modalBoxId }}"
                                class="closeEmployeeModal rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-black text-white transition hover:bg-slate-800 dark:bg-slate-700">

                            Tutup

                        </button>

                    </div>

                </div>

            </div>

        </div>

    @endforeach


    {{-- ========================================================= --}}
    {{-- RESET PASSWORD CONFIRM MODAL --}}
    {{-- ========================================================= --}}

    <div id="resetPasswordConfirmModal"
         class="fixed inset-0 z-[130] hidden items-center justify-center bg-slate-950/70 px-4 backdrop-blur-sm">


        <div id="resetPasswordConfirmBox"
             class="w-full max-w-md scale-95 rounded-3xl border border-slate-200 bg-white p-5 opacity-0 shadow-2xl transition-all duration-200 sm:p-6 dark:border-slate-800 dark:bg-slate-900">


            <div class="flex items-start gap-4">


                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.8">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 15.75h.008v.008H12v-.008zM12 6.75v6m8.25-.75a8.25 8.25 0 11-16.5 0 8.25 8.25 0 0116.5 0z" />

                    </svg>

                </div>


                <div>

                    <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">
                        Reset Password?
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">

                        Password akun

                        <span id="resetEmployeeName"
                              class="font-black text-slate-800 dark:text-slate-200">
                            -
                        </span>

                        akan diganti dengan password sementara baru.

                    </p>

                </div>

            </div>


            <div id="resetPasswordError"
                 class="mt-4 hidden rounded-2xl border border-red-200 bg-red-50 p-3 text-sm font-semibold text-red-700 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300">
            </div>


            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">


                <button type="button"
                        id="cancelResetPassword"
                        class="rounded-2xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300">

                    Batal

                </button>


                <button type="button"
                        id="confirmResetPassword"
                        class="rounded-2xl bg-amber-600 px-5 py-2.5 text-sm font-black text-white shadow-sm transition hover:bg-amber-700">

                    Ya, Reset Password

                </button>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            let pendingResetForm = null;
            let pendingEmployeeId = null;

            const resetModal =
                document.getElementById(
                    'resetPasswordConfirmModal'
                );

            const resetBox =
                document.getElementById(
                    'resetPasswordConfirmBox'
                );

            const resetEmployeeName =
                document.getElementById(
                    'resetEmployeeName'
                );

            const resetError =
                document.getElementById(
                    'resetPasswordError'
                );

            const confirmResetButton =
                document.getElementById(
                    'confirmResetPassword'
                );

            const cancelResetButton =
                document.getElementById(
                    'cancelResetPassword'
                );


            /*
            |--------------------------------------------------------------------------
            | GENERIC DETAIL MODAL
            |--------------------------------------------------------------------------
            */

            function openModal(modalId, boxId) {

                const modal =
                    document.getElementById(modalId);

                const box =
                    document.getElementById(boxId);

                if (!modal || !box) {
                    return;
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                document.body.classList.add(
                    'overflow-hidden'
                );

                setTimeout(function () {

                    box.classList.remove(
                        'scale-95',
                        'opacity-0'
                    );

                    box.classList.add(
                        'scale-100',
                        'opacity-100'
                    );

                }, 10);
            }


            function closeModal(modalId, boxId) {

                const modal =
                    document.getElementById(modalId);

                const box =
                    document.getElementById(boxId);

                if (!modal || !box) {
                    return;
                }

                box.classList.remove(
                    'scale-100',
                    'opacity-100'
                );

                box.classList.add(
                    'scale-95',
                    'opacity-0'
                );

                setTimeout(function () {

                    modal.classList.remove('flex');
                    modal.classList.add('hidden');

                    syncBodyScroll();

                }, 200);
            }


            function syncBodyScroll() {

                const opened =
                    document.querySelector(
                        '.fixed.inset-0.flex'
                    );

                if (opened) {

                    document.body.classList.add(
                        'overflow-hidden'
                    );

                } else {

                    document.body.classList.remove(
                        'overflow-hidden'
                    );

                }
            }


            /*
            |--------------------------------------------------------------------------
            | DETAIL BUTTON
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function (event) {

                    const openButton =
                        event.target.closest(
                            '.openEmployeeModal'
                        );

                    if (openButton) {

                        openModal(
                            openButton.dataset.modal,
                            openButton.dataset.box
                        );

                        return;
                    }


                    const closeButton =
                        event.target.closest(
                            '.closeEmployeeModal'
                        );

                    if (closeButton) {

                        closeModal(
                            closeButton.dataset.modal,
                            closeButton.dataset.box
                        );

                        return;
                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | OPEN RESET CONFIRM
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function (event) {

                    const button =
                        event.target.closest(
                            '[data-reset-open]'
                        );

                    if (!button) {
                        return;
                    }

                    pendingResetForm =
                        document.getElementById(
                            button.dataset.form
                        );

                    pendingEmployeeId =
                        button.dataset.employeeId;

                    if (resetEmployeeName) {
                        resetEmployeeName.textContent =
                            button.dataset.employeeName;
                    }

                    if (resetError) {
                        resetError.classList.add(
                            'hidden'
                        );

                        resetError.textContent = '';
                    }

                    openModal(
                        'resetPasswordConfirmModal',
                        'resetPasswordConfirmBox'
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | CANCEL RESET
            |--------------------------------------------------------------------------
            */

            cancelResetButton?.addEventListener(
                'click',
                function () {

                    closeModal(
                        'resetPasswordConfirmModal',
                        'resetPasswordConfirmBox'
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | CONFIRM RESET AJAX
            |--------------------------------------------------------------------------
            */

            confirmResetButton?.addEventListener(
                'click',
                async function () {

                    if (
                        !pendingResetForm ||
                        !pendingEmployeeId
                    ) {
                        return;
                    }

                    const originalText =
                        confirmResetButton.innerHTML;

                    confirmResetButton.disabled =
                        true;

                    confirmResetButton.innerHTML =
                        'Memproses...';

                    confirmResetButton.classList.add(
                        'opacity-70',
                        'cursor-not-allowed'
                    );


                    if (resetError) {

                        resetError.classList.add(
                            'hidden'
                        );

                        resetError.textContent = '';

                    }


                    try {

                        const response =
                            await fetch(
                                pendingResetForm.action,
                                {
                                    method: 'POST',

                                    body:
                                        new FormData(
                                            pendingResetForm
                                        ),

                                    headers: {
                                        'X-Requested-With':
                                            'XMLHttpRequest',

                                        'Accept':
                                            'application/json',
                                    },
                                }
                            );


                        const data =
                            await response.json();


                        if (!response.ok) {

                            throw new Error(
                                data.message
                                ||
                                'Reset password gagal.'
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | RESULT
                        |--------------------------------------------------------------------------
                        */

                        const result =
                            document.getElementById(
                                `passwordResult-${pendingEmployeeId}`
                            );

                        const username =
                            document.getElementById(
                                `resultUsername-${pendingEmployeeId}`
                            );

                        const password =
                            document.getElementById(
                                `resultPassword-${pendingEmployeeId}`
                            );


                        if (username) {
                            username.textContent =
                                data.data.username;
                        }


                        if (password) {
                            password.textContent =
                                data.data.password;
                        }


                        if (result) {
                            result.classList.remove(
                                'hidden'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE BADGES
                        |--------------------------------------------------------------------------
                        */

                        [
                            `passwordBadge-${pendingEmployeeId}`,
                            `mobilePasswordBadge-${pendingEmployeeId}`,
                            `modalPasswordBadge-${pendingEmployeeId}`,
                        ].forEach(function (id) {

                            const badge =
                                document.getElementById(id);

                            if (!badge) {
                                return;
                            }

                            badge.textContent =
                                id.startsWith(
                                    'passwordBadge-'
                                )
                                    ? 'Wajib Ganti'
                                    : 'Wajib Ganti Password';

                            badge.className =
                                'rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-700 dark:bg-amber-950/50 dark:text-amber-300';

                        });


                        closeModal(
                            'resetPasswordConfirmModal',
                            'resetPasswordConfirmBox'
                        );


                    } catch (error) {

                        if (resetError) {

                            resetError.textContent =
                                error.message
                                ||
                                'Terjadi kesalahan.';

                            resetError.classList.remove(
                                'hidden'
                            );

                        }

                    } finally {

                        confirmResetButton.disabled =
                            false;

                        confirmResetButton.innerHTML =
                            originalText;

                        confirmResetButton.classList.remove(
                            'opacity-70',
                            'cursor-not-allowed'
                        );

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | COPY PASSWORD
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                async function (event) {

                    const button =
                        event.target.closest(
                            '[data-copy-password]'
                        );

                    if (!button) {
                        return;
                    }

                    const employeeId =
                        button.dataset.copyPassword;

                    const passwordElement =
                        document.getElementById(
                            `resultPassword-${employeeId}`
                        );

                    if (!passwordElement) {
                        return;
                    }

                    try {

                        await navigator.clipboard.writeText(
                            passwordElement
                                .textContent
                                .trim()
                        );

                        const original =
                            button.textContent;

                        button.textContent =
                            'Tersalin';

                        setTimeout(function () {
                            button.textContent =
                                original;
                        }, 1200);

                    } catch (error) {
                        //
                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | BACKDROP
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function (event) {

                    const employeeBackdrop =
                        event.target.closest(
                            '[id^="employeeModal-"]'
                        );

                    if (
                        employeeBackdrop &&
                        event.target === employeeBackdrop
                    ) {

                        const modalId =
                            employeeBackdrop.id;

                        const boxId =
                            modalId.replace(
                                'employeeModal-',
                                'employeeModalBox-'
                            );

                        closeModal(
                            modalId,
                            boxId
                        );

                        return;
                    }


                    if (
                        event.target === resetModal
                    ) {

                        closeModal(
                            'resetPasswordConfirmModal',
                            'resetPasswordConfirmBox'
                        );

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function (event) {

                    if (event.key !== 'Escape') {
                        return;
                    }


                    if (
                        resetModal &&
                        resetModal.classList.contains(
                            'flex'
                        )
                    ) {

                        closeModal(
                            'resetPasswordConfirmModal',
                            'resetPasswordConfirmBox'
                        );

                        return;
                    }


                    const employeeModal =
                        document.querySelector(
                            '[id^="employeeModal-"].flex'
                        );

                    if (!employeeModal) {
                        return;
                    }


                    closeModal(
                        employeeModal.id,
                        employeeModal.id.replace(
                            'employeeModal-',
                            'employeeModalBox-'
                        )
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | FLASH AUTO HIDE
            |--------------------------------------------------------------------------
            */

            const successFlash =
                document.getElementById(
                    'successFlash'
                );

            if (successFlash) {

                setTimeout(function () {

                    successFlash.style.transition =
                        'opacity .4s ease, transform .4s ease';

                    successFlash.style.opacity =
                        '0';

                    successFlash.style.transform =
                        'translateY(-8px)';

                    setTimeout(function () {
                        successFlash.remove();
                    }, 400);

                }, 3500);

            }

        });

    </script>

</x-app-layout>
