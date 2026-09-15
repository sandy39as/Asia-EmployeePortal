<x-app-layout>

    <div class="mx-auto max-w-7xl space-y-5">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">

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

        </div>


        {{-- ========================================================= --}}
        {{-- FLASH --}}
        {{-- ========================================================= --}}
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
                    Mapping belum dapat disimpan.
                </div>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-xs font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- SUMMARY --}}
        {{-- ========================================================= --}}
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
                    Ditangani Kabag Ini
                </div>
                <div class="mt-1 text-2xl font-black text-sky-800">
                    {{ $summary['mapped_to_selected'] ?? 0 }}
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-amber-600">
                    Belum Punya Kabag
                </div>
                <div class="mt-1 text-2xl font-black text-amber-800">
                    {{ $summary['unmapped'] ?? 0 }}
                </div>
            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- PILIH KABAG --}}
        {{-- ========================================================= --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">

            <form
                method="GET"
                action="{{ route('master.kabag-mapping.index') }}"
                class="grid grid-cols-1 gap-4 lg:grid-cols-12"
            >

                <div class="lg:col-span-4">
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
                            </option>
                        @empty
                            <option value="">
                                Belum ada user role Kabag
                            </option>
                        @endforelse
                    </select>
                </div>


                <div class="lg:col-span-4">
                    <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                        Cari Karyawan
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Nama / ID / jabatan / bagian..."
                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:outline-none"
                    >
                </div>


                <div class="lg:col-span-3">
                    <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                        Bagian
                    </label>

                    <select
                        name="category"
                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:outline-none"
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


                <div class="flex items-end gap-2 lg:col-span-1">
                    <button
                        type="submit"
                        class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-extrabold text-white transition hover:bg-black"
                    >
                        Cari
                    </button>
                </div>

            </form>

        </div>


        {{-- ========================================================= --}}
        {{-- MAPPING --}}
        {{-- ========================================================= --}}
        @if ($selectedKabag)

            <form
                method="POST"
                action="{{ route('master.kabag-mapping.update', $selectedKabag) }}"
                id="kabagMappingForm"
            >
                @csrf
                @method('PUT')


                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                    {{-- HEADER MAPPING --}}
                    <div class="border-b border-slate-100 px-5 py-4">

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                            <div>
                                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">
                                    Mapping Untuk
                                </div>

                                <div class="mt-1 text-lg font-extrabold text-slate-900">
                                    {{ $selectedKabag->name }}
                                </div>
                            </div>


                            <div class="flex flex-wrap gap-2">

                                <button
                                    type="button"
                                    id="checkAllVisible"
                                    class="rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-extrabold text-sky-700 transition hover:bg-sky-100"
                                >
                                    Centang Semua Tampil
                                </button>

                                <button
                                    type="button"
                                    id="uncheckAllVisible"
                                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-extrabold text-slate-600 transition hover:bg-slate-50"
                                >
                                    Hapus Centang Tampil
                                </button>

                            </div>

                        </div>

                    </div>


                    {{-- EMPLOYEE LIST --}}
                    <div class="divide-y divide-slate-100">

                        @forelse ($employees as $employee)

                            @php
                                $isSelected =
                                    $selectedEmployeeIds
                                        ->contains(
                                            $employee->id
                                        );

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


                            <label
                                class="employeeMappingRow flex cursor-pointer items-start gap-4 px-5 py-4 transition hover:bg-slate-50"
                                data-category="{{ $employee->source_kategori_karyawan_name }}"
                            >

                                <div class="pt-1">
                                    <input
                                        type="checkbox"
                                        name="employee_ids[]"
                                        value="{{ $employee->id }}"
                                        @checked($isSelected)
                                        class="employeeMappingCheckbox h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
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


                                <div class="shrink-0">

                                    @if ($isSelected)
                                        <span class="rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[10px] font-extrabold text-emerald-700">
                                            Terpilih
                                        </span>
                                    @endif

                                </div>

                            </label>

                        @empty

                            <div class="px-5 py-12 text-center">

                                <div class="text-sm font-extrabold text-slate-700">
                                    Tidak ada karyawan.
                                </div>

                                <div class="mt-1 text-xs text-slate-500">
                                    Coba ubah pencarian atau filter bagian.
                                </div>

                            </div>

                        @endforelse

                    </div>


                    {{-- FOOTER --}}
                    <div class="sticky bottom-0 border-t border-slate-200 bg-white/95 px-5 py-4 backdrop-blur">

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                            <div class="text-xs font-semibold text-slate-500">
                                Dipilih:
                                <span
                                    id="selectedEmployeeCount"
                                    class="font-black text-slate-900"
                                >
                                    {{ $selectedEmployeeIds->count() }}
                                </span>
                                karyawan
                            </div>


                            <button
                                type="submit"
                                id="saveMappingButton"
                                class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-blue-700"
                            >
                                Simpan Mapping
                            </button>

                        </div>

                    </div>

                </div>

            </form>

        @else

            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <div class="font-extrabold text-slate-700">
                    Belum ada user dengan role Kabag.
                </div>

                <div class="mt-1 text-sm text-slate-500">
                    Buat atau ubah user menjadi role Kabag terlebih dahulu.
                </div>
            </div>

        @endif

    </div>


    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const checkboxes =
                    Array.from(
                        document.querySelectorAll(
                            '.employeeMappingCheckbox'
                        )
                    );

                const countElement =
                    document.getElementById(
                        'selectedEmployeeCount'
                    );

                const checkAllButton =
                    document.getElementById(
                        'checkAllVisible'
                    );

                const uncheckAllButton =
                    document.getElementById(
                        'uncheckAllVisible'
                    );

                const saveButton =
                    document.getElementById(
                        'saveMappingButton'
                    );

                const form =
                    document.getElementById(
                        'kabagMappingForm'
                    );


                function refreshCount() {

                    if (!countElement) {
                        return;
                    }

                    countElement.textContent =
                        checkboxes
                            .filter(
                                checkbox =>
                                    checkbox.checked
                            )
                            .length;
                }


                checkboxes.forEach(
                    function (checkbox) {

                        checkbox.addEventListener(
                            'change',
                            refreshCount
                        );
                    }
                );


                checkAllButton?.addEventListener(
                    'click',
                    function () {

                        checkboxes.forEach(
                            function (checkbox) {

                                checkbox.checked =
                                    true;
                            }
                        );

                        refreshCount();
                    }
                );


                uncheckAllButton?.addEventListener(
                    'click',
                    function () {

                        checkboxes.forEach(
                            function (checkbox) {

                                checkbox.checked =
                                    false;
                            }
                        );

                        refreshCount();
                    }
                );


                form?.addEventListener(
                    'submit',
                    function () {

                        if (!saveButton) {
                            return;
                        }

                        saveButton.disabled =
                            true;

                        saveButton.textContent =
                            'Menyimpan...';

                        saveButton.classList.add(
                            'opacity-70',
                            'cursor-not-allowed'
                        );
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
