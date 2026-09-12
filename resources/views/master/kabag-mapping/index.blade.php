<x-app-layout>

    <div class="max-w-7xl mx-auto space-y-5">

        {{-- HEADER --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-blue-600">
                        Master Data
                    </p>

                    <h1 class="mt-1 text-xl sm:text-2xl font-extrabold text-slate-900">
                        Mapping Kabag & Karyawan
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Atur karyawan yang menjadi tanggung jawab masing-masing Kabag.
                    </p>
                </div>

                <a
                    href="{{ route('master.kabag.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50"
                >
                    Kelola Kabag
                </a>

            </div>
        </div>


        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif


        {{-- PILIH KABAG --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">

            <form
                method="GET"
                action="{{ route('master.kabag-mapping.index') }}"
                class="grid grid-cols-1 gap-4 sm:grid-cols-3"
            >

                <div class="sm:col-span-2">

                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Pilih Kabag
                    </label>

                    <select
                        name="kabag_id"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        required
                    >

                        <option value="">
                            -- Pilih Kabag --
                        </option>

                        @foreach ($kabags as $kabag)

                            <option
                                value="{{ $kabag->id }}"
                                @selected(
                                    optional($selectedKabag)->id
                                    === $kabag->id
                                )
                            >
                                {{ $kabag->name }}

                                @if ($kabag->email)
                                    - {{ $kabag->email }}
                                @endif
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="flex items-end">

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-blue-600 px-5 py-3 text-sm font-extrabold text-white hover:bg-blue-700"
                    >
                        Tampilkan Karyawan
                    </button>

                </div>

            </form>

        </div>


        @if ($selectedKabag)

            {{-- INFO KABAG --}}
            <div class="rounded-3xl border border-blue-200 bg-blue-50 p-5">

                <div class="text-xs font-extrabold uppercase tracking-wider text-blue-600">
                    Kabag Terpilih
                </div>

                <div class="mt-1 text-lg font-extrabold text-slate-900">
                    {{ $selectedKabag->name }}
                </div>

                <div class="text-sm font-medium text-slate-500">
                    {{ $selectedKabag->email }}
                </div>

            </div>


            {{-- SEARCH --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">

                <form
                    method="GET"
                    action="{{ route('master.kabag-mapping.index') }}"
                    class="flex flex-col gap-3 sm:flex-row"
                >

                    <input
                        type="hidden"
                        name="kabag_id"
                        value="{{ $selectedKabag->id }}"
                    >

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari nama, kode karyawan, atau jabatan..."
                        class="flex-1 rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    >

                    <button
                        type="submit"
                        class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white hover:bg-black"
                    >
                        Cari
                    </button>

                    @if ($search !== '')

                        <a
                            href="{{ route('master.kabag-mapping.index', [
                                'kabag_id' => $selectedKabag->id
                            ]) }}"
                            class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-bold text-slate-700 hover:bg-slate-50"
                        >
                            Reset
                        </a>

                    @endif

                </form>

            </div>


            {{-- LIST KARYAWAN --}}
            <form
                method="POST"
                action="{{ route(
                    'master.kabag-mapping.update',
                    $selectedKabag
                ) }}"
            >

                @csrf
                @method('PUT')


                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-200 px-5 py-4">

                        <div class="flex items-center justify-between">

                            <div>
                                <h2 class="font-extrabold text-slate-900">
                                    Daftar Karyawan
                                </h2>

                                <p class="mt-1 text-xs text-slate-500">
                                    Centang karyawan yang menjadi tanggung jawab Kabag ini.
                                </p>
                            </div>

                            <div class="text-xs font-bold text-slate-500">

                                {{ count($mappedEmployeeIds) }}
                                karyawan ter-mapping

                            </div>

                        </div>

                    </div>


                    <div class="divide-y divide-slate-100">

                        @forelse ($employees as $employee)

                            <label
                                class="flex cursor-pointer items-center gap-4 px-5 py-4 transition hover:bg-slate-50"
                            >

                                <input
                                    type="checkbox"
                                    name="employee_ids[]"
                                    value="{{ $employee->id }}"
                                    @checked(
                                        in_array(
                                            $employee->id,
                                            $mappedEmployeeIds,
                                            true
                                        )
                                    )
                                    class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                >


                                <div class="min-w-0 flex-1">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <span class="font-extrabold text-slate-900">
                                            {{ $employee->nama }}
                                        </span>

                                        <span class="rounded-lg bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-600">
                                            {{ $employee->employee_code }}
                                        </span>

                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">

                                        {{ $employee->jabatan ?: 'Jabatan belum diisi' }}

                                    </div>

                                </div>

                            </label>

                        @empty

                            <div class="p-10 text-center">

                                <p class="font-bold text-slate-700">
                                    Tidak ada karyawan ditemukan.
                                </p>

                            </div>

                        @endforelse

                    </div>


                    @if (
                        $employees instanceof
                        \Illuminate\Pagination\LengthAwarePaginator
                    )

                        <div class="border-t border-slate-200 p-4">

                            {{ $employees->links() }}

                        </div>

                    @endif


                    <div class="sticky bottom-0 border-t border-slate-200 bg-white/95 p-4 backdrop-blur">

                        <div class="flex justify-end">

                            <button
                                type="submit"
                                class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-extrabold text-white shadow-sm hover:bg-blue-700"
                            >
                                Simpan Mapping
                            </button>

                        </div>

                    </div>

                </div>

            </form>

        @else

            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">

                <p class="font-extrabold text-slate-700">
                    Pilih Kabag terlebih dahulu.
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    Setelah memilih Kabag, daftar karyawan akan ditampilkan.
                </p>

            </div>

        @endif

    </div>

</x-app-layout>
