<x-app-layout>

    <div class="mx-auto max-w-6xl space-y-5">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

            <div>
                <h1 class="text-xl font-extrabold text-slate-900 sm:text-2xl">
                    Mapping Atasan Kabag
                </h1>

                <p class="mt-1 max-w-2xl text-xs font-medium leading-5 text-slate-500 sm:text-sm">
                    Tentukan siapa yang menyetujui pengajuan milik Kabag sebelum diteruskan ke HRD.
                    Atasan dapat berupa Kabag lain.
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-500 shadow-xs">
                {{ $kabags->count() }} Kabag aktif
            </div>

        </div>


        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif


        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-800">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif


        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs sm:p-5">

            <label for="kabagSupervisorSearch" class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                Cari Kabag
            </label>

            <input
                id="kabagSupervisorSearch"
                type="search"
                autocomplete="off"
                placeholder="Cari nama, username, ID karyawan, jabatan..."
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 outline-none transition focus:border-slate-500"
            >

        </div>


        <div id="kabagSupervisorList" class="grid grid-cols-1 gap-4 xl:grid-cols-2">

            @forelse ($kabags as $kabag)

                @php
                    $selectedSupervisorIds =
                        $kabag
                            ->supervisors
                            ->pluck('id')
                            ->map(fn ($id) => (int) $id)
                            ->all();

                    $searchText =
                        strtolower(
                            implode(
                                ' ',
                                [
                                    $kabag->name,
                                    $kabag->username,
                                    $kabag->email,
                                    $kabag->selfEmployee?->employee_code,
                                    $kabag->selfEmployee?->nama,
                                    $kabag->selfEmployee?->jabatan,
                                ]
                            )
                        );
                @endphp

                <form
                    method="POST"
                    action="{{ route('master.kabag-supervisor-mapping.update', $kabag) }}"
                    class="kabag-supervisor-card flex min-h-full flex-col rounded-2xl border border-slate-200 bg-white shadow-xs"
                    data-search="{{ $searchText }}"
                >
                    @csrf

                    <div class="border-b border-slate-200 px-4 py-4 sm:px-5">

                        <div class="flex items-start justify-between gap-4">

                            <div class="min-w-0">
                                <div class="truncate text-base font-extrabold text-slate-900">
                                    {{ $kabag->name }}
                                </div>

                                <div class="mt-1 text-xs font-bold text-slate-500">
                                    {{ $kabag->selfEmployee?->employee_code ?? '-' }}
                                    •
                                    {{ $kabag->selfEmployee?->jabatan ?? 'Kabag' }}
                                </div>

                                <div class="mt-1 truncate font-mono text-[11px] font-bold text-slate-400">
                                    {{ $kabag->username ?? '-' }}
                                </div>
                            </div>

                            <div class="shrink-0 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                                {{ count($selectedSupervisorIds) }} Atasan
                            </div>

                        </div>

                    </div>


                    <div class="flex-1 p-4 sm:p-5">

                        <div class="mb-3">
                            <div class="text-xs font-extrabold uppercase tracking-wider text-slate-600">
                                Pilih Atasan
                            </div>

                            <div class="mt-1 text-xs font-medium text-slate-500">
                                Boleh lebih dari satu. Yang memproses pertama akan tercatat sebagai approver.
                            </div>
                        </div>


                        <div class="max-h-64 space-y-2 overflow-y-auto pr-1">

                            @foreach ($kabags as $candidate)

                                @continue($candidate->id === $kabag->id)

                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3 transition hover:bg-slate-50">

                                    <input
                                        type="checkbox"
                                        name="supervisor_ids[]"
                                        value="{{ $candidate->id }}"
                                        @checked(in_array((int) $candidate->id, $selectedSupervisorIds, true))
                                        class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                                    >

                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-sm font-extrabold text-slate-900">
                                            {{ $candidate->name }}
                                        </div>

                                        <div class="mt-0.5 truncate text-[11px] font-semibold text-slate-500">
                                            {{ $candidate->selfEmployee?->employee_code ?? '-' }}
                                            •
                                            {{ $candidate->selfEmployee?->jabatan ?? 'Kabag' }}
                                        </div>
                                    </div>

                                </label>

                            @endforeach

                        </div>

                    </div>


                    <div class="border-t border-slate-200 bg-slate-50/70 px-4 py-3 sm:px-5">

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-extrabold text-white transition hover:bg-black"
                        >
                            Simpan Mapping Atasan
                        </button>

                    </div>

                </form>

            @empty

                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
                    <div class="text-sm font-extrabold text-slate-700">
                        Belum ada akun Kabag aktif.
                    </div>
                </div>

            @endforelse

        </div>


        <div
            id="kabagSupervisorEmptySearch"
            class="hidden rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center"
        >
            <div class="text-sm font-extrabold text-slate-700">
                Kabag tidak ditemukan.
            </div>
        </div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const search =
                document.getElementById(
                    'kabagSupervisorSearch'
                );

            const cards =
                Array.from(
                    document.querySelectorAll(
                        '.kabag-supervisor-card'
                    )
                );

            const empty =
                document.getElementById(
                    'kabagSupervisorEmptySearch'
                );


            function filterCards() {

                const keyword =
                    (search?.value || '')
                        .trim()
                        .toLowerCase();

                let visible = 0;


                cards.forEach(
                    card => {

                        const haystack =
                            (
                                card.dataset.search
                                || ''
                            )
                                .toLowerCase();

                        const show =
                            keyword === ''
                            ||
                            haystack.includes(
                                keyword
                            );


                        card.classList.toggle(
                            'hidden',
                            !show
                        );


                        if (show) {
                            visible++;
                        }
                    }
                );


                empty?.classList.toggle(
                    'hidden',
                    visible !== 0
                );
            }


            search?.addEventListener(
                'input',
                filterCards
            );

        });
    </script>

</x-app-layout>
