<x-app-layout>

    <div class="max-w-6xl mx-auto space-y-5">

        <div>
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-slate-400">
                Master Data
            </p>

            <h1 class="mt-1 text-2xl font-extrabold text-slate-900">
                Mapping Kabag
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Pilih Kabag untuk melihat dan mengatur karyawan bawahannya.
            </p>
        </div>


        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            <div class="p-5 border-b border-slate-200">

                <form method="GET"
                      class="flex gap-3">

                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari Kabag..."
                           class="flex-1 rounded-xl border border-slate-300 px-4 py-3 text-sm">

                    <button
                        class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white">
                        Cari
                    </button>

                </form>

            </div>


            <div class="divide-y divide-slate-100">

                @forelse ($kabags as $kabag)

                    <a href="{{ route(
                            'master.kabag-mapping.show',
                            $kabag
                        ) }}"
                       class="flex items-center justify-between p-5 transition hover:bg-slate-50">

                        <div>

                            <div class="font-extrabold text-slate-900">
                                {{ $kabag->name }}
                            </div>

                            <div class="mt-1 text-xs text-slate-500">
                                {{ $kabag->email }}
                            </div>

                        </div>


                        <div class="flex items-center gap-4">

                            <span class="rounded-xl bg-blue-50 px-3 py-1.5 text-xs font-extrabold text-blue-700">
                                {{ $kabag->managed_employees_count }}
                                Karyawan
                            </span>

                            <span class="text-xl text-slate-400">
                                ›
                            </span>

                        </div>

                    </a>

                @empty

                    <div class="p-10 text-center text-sm font-bold text-slate-500">
                        Belum ada Kabag.
                    </div>

                @endforelse

            </div>

        </div>

        {{ $kabags->links() }}

    </div>

</x-app-layout>
