<x-app-layout>

    <div class="mx-auto max-w-7xl space-y-5">

        {{-- HEADER --}}
        <div>
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-slate-400">
                Master Data
            </p>

            <h1 class="mt-1 text-xl font-extrabold text-slate-900 sm:text-2xl">
                Mapping Kabag
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Satu karyawan dapat ditangani oleh lebih dari satu Kabag.
            </p>
        </div>


        {{-- FLASH --}}
        @if (session('success'))
            <div
                id="successFlash"
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800"
            >
                {{ session('success') }}
            </div>
        @endif


        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">

                <div class="font-extrabold">
                    Data belum dapat diproses.
                </div>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-xs font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif


        {{-- SUMMARY --}}
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                    Total Kabag
                </div>
                <div class="mt-1 text-2xl font-black text-slate-900">
                    {{ $summary['total_kabag'] ?? 0 }}
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                    Total Karyawan
                </div>
                <div class="mt-1 text-2xl font-black text-slate-900">
                    {{ $summary['total_employee'] ?? 0 }}
                </div>
            </div>

            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 shadow-sm">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-sky-500">
                    Kabag Ini
                </div>
                <div class="mt-1 text-2xl font-black text-sky-800">
                    {{ $summary['mapped_to_selected'] ?? 0 }}
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-amber-600">
                    Belum Punya Kabag Sama Sekali
                </div>
                <div class="mt-1 text-2xl font-black text-amber-800">
                    {{ $summary['unmapped_all'] ?? 0 }}
                </div>
            </div>

        </div>


        {{-- PILIH KABAG --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">

            <form
                method="GET"
                action="{{ route('master.kabag-mapping.index') }}"
                class="grid grid-cols-1 gap-4 sm:grid-cols-2"
            >

                <div>
                    <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                        Pilih Kabag
                    </label>

                    <select
                        name="kabag_id"
                        onchange="this.form.submit()"
                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-800 shadow-sm focus:border-blue-500 focus:outline-none"
                    >
                        @forelse ($kabags as $kabag)

                            <option
                                value="{{ $kabag->id }}"
                                @selected(
                                    $selectedKabag
                                    && $selectedKabag->id === $kabag->id
                                )
                            >
                                {{ $kabag->name }}
                                ({{ $kabag->managed_employees_count }} karyawan)
                            </option>

                        @empty

                            <option value="">
                                Belum ada user role Kabag
                            </option>

                        @endforelse
                    </select>
                </div>


                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">

                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-blue-500">
                        Konsep Multi-Kabag
                    </div>

                    <div class="mt-1 text-xs leading-5 text-blue-800">
                        Karyawan boleh masuk ke beberapa Kabag sekaligus.
                        Menambahkan atau melepas mapping di sini hanya memengaruhi
                        Kabag yang sedang dipilih.
                    </div>

                </div>

            </form>

        </div>


        @if ($selectedKabag)

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">

                {{-- ===================================================== --}}
                {{-- SUDAH MASUK --}}
                {{-- ===================================================== --}}
                <div class="overflow-hidden rounded-3xl border border-emerald-200 bg-white shadow-sm">

                    <div class="flex items-center justify-between gap-3 border-b border-emerald-100 bg-emerald-50 px-5 py-4">

                        <div>
                            <div class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-emerald-600">
                                Sudah Masuk
                            </div>

                            <div class="mt-1 text-sm font-extrabold text-emerald-900">
                                Ditangani {{ $selectedKabag->name }}
                            </div>
                        </div>


                        <span class="inline-flex min-w-9 items-center justify-center rounded-full bg-emerald-600 px-3 py-1.5 text-xs font-black text-white">
                            {{ $mappedEmployees->count() }}
                        </span>

                    </div>


                    <div class="max-h-[680px] divide-y divide-slate-100 overflow-y-auto">

                        @forelse ($mappedEmployees as $employee)

                            @php
                                $otherKabags =
                                    $employee
                                        ->kabags
                                        ->filter(
                                            fn ($kabag) =>
                                                $kabag->id
                                                !==
                                                $selectedKabag->id
                                        );
                            @endphp


                            <div class="flex items-start gap-3 px-4 py-4">

                                <div class="min-w-0 flex-1">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <div class="font-extrabold text-slate-900">
                                            {{ $employee->nama }}
                                        </div>

                                        <span class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-0.5 text-[10px] font-bold text-slate-500">
                                            {{ $employee->employee_code ?? '-' }}
                                        </span>

                                        @if ($employee->source_kategori_karyawan_name)

                                            <span class="rounded-lg border border-blue-200 bg-blue-50 px-2 py-0.5 text-[10px] font-extrabold text-blue-700">
                                                {{ $employee->source_kategori_karyawan_name }}
                                            </span>

                                        @endif

                                    </div>


                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $employee->jabatan ?: 'Jabatan belum diisi' }}
                                    </div>


                                    @if ($otherKabags->isNotEmpty())

                                        <div class="mt-2 flex flex-wrap items-center gap-1.5">

                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                                Juga ditangani:
                                            </span>

                                            @foreach ($otherKabags as $otherKabag)

                                                <span class="rounded-lg border border-violet-200 bg-violet-50 px-2 py-0.5 text-[10px] font-bold text-violet-700">
                                                    {{ $otherKabag->name }}
                                                </span>

                                            @endforeach

                                        </div>

                                    @endif

                                </div>


                                <form
                                    method="POST"
                                    action="{{ route(
                                        'master.kabag-mapping.remove',
                                        [
                                            'kabag' => $selectedKabag,
                                            'employee' => $employee,
                                        ]
                                    ) }}"
                                    onsubmit="return confirm('Lepas {{ $employee->nama }} dari {{ $selectedKabag->name }}?')"
                                    class="shrink-0"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-[11px] font-extrabold text-rose-700 transition hover:bg-rose-100"
                                    >
                                        Lepas
                                    </button>
                                </form>

                            </div>

                        @empty

                            <div class="px-5 py-12 text-center">

                                <div class="text-sm font-extrabold text-slate-700">
                                    Belum ada karyawan.
                                </div>

                                <div class="mt-1 text-xs text-slate-500">
                                    Tambahkan karyawan dari card sebelah kanan.
                                </div>

                            </div>

                        @endforelse

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- BELUM MASUK KE KABAG INI --}}
                {{-- ===================================================== --}}
                <div class="overflow-hidden rounded-3xl border border-amber-200 bg-white shadow-sm">

                    <div class="border-b border-amber-100 bg-amber-50 px-5 py-4">

                        <div class="flex items-center justify-between gap-3">

                            <div>
                                <div class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-amber-600">
                                    Belum Masuk
                                </div>

                                <div class="mt-1 text-sm font-extrabold text-amber-900">
                                    Tambahkan ke {{ $selectedKabag->name }}
                                </div>
                            </div>


                            <span class="inline-flex min-w-9 items-center justify-center rounded-full bg-amber-500 px-3 py-1.5 text-xs font-black text-white">
                                {{ $availableEmployees->count() }}
                            </span>

                        </div>


                        {{-- SEARCH KHUSUS BELUM MASUK --}}
                        <form
                            method="GET"
                            action="{{ route('master.kabag-mapping.index') }}"
                            class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-12"
                        >

                            <input
                                type="hidden"
                                name="kabag_id"
                                value="{{ $selectedKabag->id }}"
                            >


                            <div class="sm:col-span-6">

                                <input
                                    type="text"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Cari nama / ID / jabatan..."
                                    class="w-full rounded-xl border border-amber-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-amber-400 focus:outline-none"
                                >

                            </div>


                            <div class="sm:col-span-4">

                                <select
                                    name="category"
                                    class="w-full rounded-xl border border-amber-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-amber-400 focus:outline-none"
                                >
                                    <option value="">
                                        Semua Bagian
                                    </option>

                                    @foreach ($categories as $categoryOption)

                                        <option
                                            value="{{ $categoryOption }}"
                                            @selected(
                                                $category === $categoryOption
                                            )
                                        >
                                            {{ $categoryOption }}
                                        </option>

                                    @endforeach
                                </select>

                            </div>


                            <div class="flex gap-2 sm:col-span-2">

                                <button
                                    type="submit"
                                    class="flex-1 rounded-xl bg-amber-500 px-3 py-2.5 text-xs font-extrabold text-white transition hover:bg-amber-600"
                                >
                                    Cari
                                </button>

                                @if (
                                    $search !== ''
                                    ||
                                    $category !== ''
                                )

                                    <a
                                        href="{{ route(
                                            'master.kabag-mapping.index',
                                            [
                                                'kabag_id' =>
                                                    $selectedKabag->id,
                                            ]
                                        ) }}"
                                        class="rounded-xl border border-amber-200 bg-white px-3 py-2.5 text-xs font-extrabold text-amber-700 transition hover:bg-amber-100"
                                    >
                                        ×
                                    </a>

                                @endif

                            </div>

                        </form>

                    </div>


                    <form
                        method="POST"
                        action="{{ route(
                            'master.kabag-mapping.assign',
                            $selectedKabag
                        ) }}"
                        id="assignEmployeesForm"
                    >
                        @csrf


                        <div class="max-h-[570px] divide-y divide-slate-100 overflow-y-auto">

                            @forelse ($availableEmployees as $employee)

                                <label class="flex cursor-pointer items-start gap-3 px-4 py-4 transition hover:bg-amber-50/40">

                                    <div class="pt-1">
                                        <input
                                            type="checkbox"
                                            name="employee_ids[]"
                                            value="{{ $employee->id }}"
                                            class="availableEmployeeCheckbox h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                        >
                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <div class="flex flex-wrap items-center gap-2">

                                            <div class="font-extrabold text-slate-900">
                                                {{ $employee->nama }}
                                            </div>

                                            <span class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-0.5 text-[10px] font-bold text-slate-500">
                                                {{ $employee->employee_code ?? '-' }}
                                            </span>

                                            @if ($employee->source_kategori_karyawan_name)

                                                <span class="rounded-lg border border-blue-200 bg-blue-50 px-2 py-0.5 text-[10px] font-extrabold text-blue-700">
                                                    {{ $employee->source_kategori_karyawan_name }}
                                                </span>

                                            @endif

                                        </div>


                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $employee->jabatan ?: 'Jabatan belum diisi' }}
                                        </div>


                                        @if ($employee->kabags->isNotEmpty())

                                            <div class="mt-2 flex flex-wrap items-center gap-1.5">

                                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                                    Sudah ditangani:
                                                </span>

                                                @foreach ($employee->kabags as $otherKabag)

                                                    <span class="rounded-lg border border-violet-200 bg-violet-50 px-2 py-0.5 text-[10px] font-bold text-violet-700">
                                                        {{ $otherKabag->name }}
                                                    </span>

                                                @endforeach

                                                <span class="rounded-lg border border-blue-200 bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700">
                                                    Boleh ditambah juga
                                                </span>

                                            </div>

                                        @else

                                            <div class="mt-2">
                                                <span class="rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700">
                                                    Belum punya Kabag
                                                </span>
                                            </div>

                                        @endif

                                    </div>

                                </label>

                            @empty

                                <div class="px-5 py-12 text-center">

                                    <div class="text-sm font-extrabold text-slate-700">
                                        Tidak ada karyawan.
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        Coba ubah pencarian/filter atau semua karyawan sudah masuk ke Kabag ini.
                                    </div>

                                </div>

                            @endforelse

                        </div>


                        @if ($availableEmployees->isNotEmpty())

                            <div class="border-t border-amber-100 bg-amber-50/60 px-4 py-4">

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                    <div class="flex items-center gap-2">

                                        <button
                                            type="button"
                                            id="selectAllAvailable"
                                            class="rounded-xl border border-amber-200 bg-white px-3 py-2 text-xs font-extrabold text-amber-700 hover:bg-amber-100"
                                        >
                                            Pilih Semua Hasil
                                        </button>

                                        <div class="text-xs font-semibold text-slate-500">
                                            Dipilih:
                                            <span
                                                id="selectedAvailableCount"
                                                class="font-black text-slate-900"
                                            >
                                                0
                                            </span>
                                        </div>

                                    </div>


                                    <button
                                        type="submit"
                                        id="assignEmployeesButton"
                                        class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-extrabold text-white shadow-sm transition hover:bg-blue-700"
                                    >
                                        Tambahkan ke Kabag
                                    </button>

                                </div>

                            </div>

                        @endif

                    </form>

                </div>

            </div>

        @else

            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">

                <div class="font-extrabold text-slate-700">
                    Belum ada user dengan role Kabag.
                </div>

            </div>

        @endif

    </div>


    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const availableCheckboxes =
                    Array.from(
                        document.querySelectorAll(
                            '.availableEmployeeCheckbox'
                        )
                    );

                const countElement =
                    document.getElementById(
                        'selectedAvailableCount'
                    );

                const selectAllButton =
                    document.getElementById(
                        'selectAllAvailable'
                    );

                const assignForm =
                    document.getElementById(
                        'assignEmployeesForm'
                    );

                const assignButton =
                    document.getElementById(
                        'assignEmployeesButton'
                    );


                function refreshSelectedCount() {

                    if (!countElement) {
                        return;
                    }

                    countElement.textContent =
                        availableCheckboxes
                            .filter(
                                checkbox =>
                                    checkbox.checked
                            )
                            .length;
                }


                availableCheckboxes.forEach(
                    function (checkbox) {

                        checkbox.addEventListener(
                            'change',
                            refreshSelectedCount
                        );
                    }
                );


                selectAllButton?.addEventListener(
                    'click',
                    function () {

                        const shouldCheck =
                            availableCheckboxes
                                .some(
                                    checkbox =>
                                        ! checkbox.checked
                                );


                        availableCheckboxes.forEach(
                            function (checkbox) {

                                checkbox.checked =
                                    shouldCheck;
                            }
                        );


                        selectAllButton.textContent =
                            shouldCheck
                                ? 'Hapus Semua Pilihan'
                                : 'Pilih Semua Hasil';


                        refreshSelectedCount();
                    }
                );


                assignForm?.addEventListener(
                    'submit',
                    function (event) {

                        const selected =
                            availableCheckboxes
                                .filter(
                                    checkbox =>
                                        checkbox.checked
                                );


                        if (selected.length === 0) {

                            event.preventDefault();

                            alert(
                                'Pilih minimal satu karyawan.'
                            );

                            return;
                        }


                        if (assignButton) {

                            assignButton.disabled =
                                true;

                            assignButton.textContent =
                                'Menambahkan...';

                            assignButton.classList.add(
                                'opacity-70',
                                'cursor-not-allowed'
                            );
                        }
                    }
                );


                const successFlash =
                    document.getElementById(
                        'successFlash'
                    );

                if (successFlash) {

                    setTimeout(
                        function () {

                            successFlash.style.transition =
                                'opacity .4s ease, transform .4s ease';

                            successFlash.style.opacity =
                                '0';

                            successFlash.style.transform =
                                'translateY(-6px)';


                            setTimeout(
                                function () {
                                    successFlash.remove();
                                },
                                400
                            );

                        },
                        3500
                    );
                }

            }
        );
    </script>

</x-app-layout>
