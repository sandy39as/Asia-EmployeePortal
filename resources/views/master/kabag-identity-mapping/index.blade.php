<x-app-layout>

    <div class="mx-auto max-w-6xl space-y-5">

        <div>
            <h1 class="text-xl font-extrabold text-slate-900 sm:text-2xl">
                Mapping Identitas Kabag
            </h1>

            <p class="mt-1 max-w-3xl text-xs font-medium leading-5 text-slate-500 sm:text-sm">
                Hubungkan akun Kabag dengan data dirinya sebagai karyawan.
                Gunakan Employee Code, Device, Group, Kategori, dan Source ID untuk memastikan record yang dipilih benar.
                PIN Fingerspot tidak digunakan sebagai identitas karena dapat sama antar device.
            </p>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
            <label for="kabagIdentitySearch" class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                Cari Kabag
            </label>

            <input
                id="kabagIdentitySearch"
                type="search"
                autocomplete="off"
                placeholder="Cari nama Kabag, username, atau email..."
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-800 outline-none focus:border-slate-500"
            >
        </div>

        <div id="kabagIdentityList" class="space-y-4">

            @foreach ($kabags as $kabag)

                @php
                    $current = $kabag->selfEmployee;

                    $searchText = strtolower(
                        implode(' ', [
                            $kabag->name,
                            $kabag->username,
                            $kabag->email,
                        ])
                    );
                @endphp

                <form
                    method="POST"
                    action="{{ route('master.kabag-identity-mapping.update', $kabag) }}"
                    class="kabag-identity-card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs"
                    data-search="{{ $searchText }}"
                >
                    @csrf

                    <div class="grid grid-cols-1 gap-4 p-4 sm:p-5 lg:grid-cols-[minmax(0,280px)_1fr_auto] lg:items-end">

                        <div class="min-w-0">
                            <div class="text-base font-extrabold text-slate-900">
                                {{ $kabag->name }}
                            </div>

                            <div class="mt-1 text-xs font-bold text-slate-500">
                                {{ $kabag->username ?? '-' }}
                            </div>

                            <div class="mt-1 truncate text-[11px] font-semibold text-slate-400">
                                {{ $kabag->email ?? '-' }}
                            </div>

                            @if ($current)
                                <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs leading-5 text-emerald-800">
                                    <div class="font-extrabold">Saat ini terhubung:</div>
                                    <div class="mt-1">
                                        {{ $current->employee_code }} • {{ $current->nama }}
                                    </div>
                                    <div>
                                        Device {{ $current->source_device_id ?? '-' }}
                                        • {{ strtoupper($current->employment_group ?? '-') }}
                                    </div>
                                    <div class="break-words">
                                        {{ $current->source_kategori_karyawan_name ?? '-' }}
                                        • Source ID {{ $current->source_karyawan_id }}
                                    </div>
                                </div>
                            @else
                                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs font-bold text-amber-800">
                                    Belum terhubung ke data karyawan.
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Data Karyawan
                            </label>

                            <select
                                name="self_employee_id"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-semibold text-slate-800 outline-none focus:border-slate-500"
                            >
                                <option value="">
                                    -- Belum / Lepas Mapping --
                                </option>

                                @foreach ($employees as $employee)
                                    @php
                                        $area = in_array((int) $employee->source_device_id, [1, 2], true)
                                            ? 'Area 52'
                                            : ((int) $employee->source_device_id === 3 ? 'Area 27' : 'Area Lain');
                                    @endphp

                                    <option
                                        value="{{ $employee->id }}"
                                        @selected((int) $kabag->self_employee_id === (int) $employee->id)
                                    >
                                        {{ $employee->employee_code }}
                                        | {{ $employee->nama }}
                                        | {{ $area }} / Device {{ $employee->source_device_id ?? '-' }}
                                        | {{ strtoupper($employee->employment_group ?? '-') }}
                                        | {{ $employee->source_kategori_karyawan_name ?? '-' }}
                                        | Source {{ $employee->source_karyawan_id }}
                                    </option>
                                @endforeach
                            </select>

                            <p class="mt-1.5 text-[11px] font-medium text-slate-400">
                                Jika ada nama sama, bedakan dari Employee Code, Device, Group, Kategori, dan Source ID.
                            </p>
                        </div>

                        <div>
                            <button
                                type="submit"
                                class="w-full rounded-xl bg-slate-950 px-5 py-2.5 text-sm font-extrabold text-white transition hover:bg-black lg:w-auto"
                            >
                                Simpan
                            </button>
                        </div>

                    </div>
                </form>

            @endforeach

        </div>

        <div
            id="kabagIdentityEmpty"
            class="hidden rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm font-bold text-slate-600"
        >
            Kabag tidak ditemukan.
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('kabagIdentitySearch');
            const cards = Array.from(document.querySelectorAll('.kabag-identity-card'));
            const empty = document.getElementById('kabagIdentityEmpty');

            function filterCards() {
                const keyword = (input?.value || '').trim().toLowerCase();
                let visible = 0;

                cards.forEach(card => {
                    const show =
                        keyword === ''
                        ||
                        (card.dataset.search || '')
                            .toLowerCase()
                            .includes(keyword);

                    card.classList.toggle('hidden', !show);

                    if (show) {
                        visible++;
                    }
                });

                empty?.classList.toggle('hidden', visible !== 0);
            }

            input?.addEventListener('input', filterCards);
        });
    </script>

</x-app-layout>
