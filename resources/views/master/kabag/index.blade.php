<x-app-layout>

    <div class="max-w-6xl mx-auto space-y-5">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-slate-400">
                    Master Data
                </p>

                <h1 class="mt-1 text-xl sm:text-2xl font-extrabold text-slate-900">
                    Data Kabag
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola akun Kabag yang akan melakukan persetujuan pengajuan karyawan.
                </p>
            </div>

            <a href="{{ route('master.kabag.create') }}"
               class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-black">
                + Tambah Kabag
            </a>
        </div>


        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif


        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            <div class="border-b border-slate-200 p-4 sm:p-5">

                <form method="GET"
                      action="{{ route('master.kabag.index') }}"
                      class="flex flex-col sm:flex-row gap-3">

                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari nama, email, username..."
                           class="flex-1 rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:ring-4 focus:ring-slate-100">

                    <button type="submit"
                            class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white hover:bg-black">
                        Cari
                    </button>

                    @if ($search !== '')
                        <a href="{{ route('master.kabag.index') }}"
                           class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-bold text-slate-700 hover:bg-slate-50">
                            Reset
                        </a>
                    @endif

                </form>

            </div>


            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                            <th class="px-5 py-4 font-extrabold">Kabag</th>
                            <th class="px-5 py-4 font-extrabold">Username</th>
                            <th class="px-5 py-4 font-extrabold">Karyawan</th>
                            <th class="px-5 py-4 font-extrabold">Status</th>
                            <th class="px-5 py-4 text-right font-extrabold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse ($kabags as $kabag)

                            <tr class="hover:bg-slate-50/70">

                                <td class="px-5 py-4">

                                    <div class="font-extrabold text-slate-900">
                                        {{ $kabag->name }}
                                    </div>

                                    <div class="mt-0.5 text-xs font-medium text-slate-500">
                                        {{ $kabag->email ?: '-' }}
                                    </div>

                                </td>


                                <td class="px-5 py-4 text-sm font-bold text-slate-700">
                                    {{ $kabag->username ?: '-' }}
                                </td>


                                <td class="px-5 py-4">

                                    <span class="inline-flex rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-extrabold text-blue-700">
                                        {{ $kabag->managed_employees_count }} karyawan
                                    </span>

                                </td>


                                <td class="px-5 py-4">

                                    @if ($kabag->is_active)
                                        <span class="inline-flex rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-700">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1 text-xs font-extrabold text-slate-600">
                                            Nonaktif
                                        </span>
                                    @endif

                                </td>


                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-end gap-2">

                                        <a href="{{ route('master.kabag-mapping.index', [
                                                'kabag_id' => $kabag->id
                                            ]) }}"
                                           class="rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-extrabold text-blue-700 hover:bg-blue-100">
                                            Mapping
                                        </a>

                                        <a href="{{ route('master.kabag.edit', $kabag) }}"
                                           class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-50">
                                            Edit
                                        </a>

                                        <form method="POST"
                                              action="{{ route('master.kabag.destroy', $kabag) }}"
                                              onsubmit="return confirm('Hapus akun Kabag {{ $kabag->name }}?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-extrabold text-rose-700 hover:bg-rose-100">
                                                Hapus
                                            </button>
                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5"
                                    class="px-5 py-12 text-center text-sm font-bold text-slate-500">
                                    Belum ada akun Kabag.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            @if ($kabags->hasPages())
                <div class="border-t border-slate-200 p-4">
                    {{ $kabags->links() }}
                </div>
            @endif

        </div>

    </div>

</x-app-layout>
