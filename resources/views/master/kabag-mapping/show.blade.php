<x-app-layout>

    <div class="max-w-7xl mx-auto space-y-5">

        <div class="flex items-center justify-between">

            <div>

                <a href="{{ route(
                        'master.kabag-mapping.index'
                    ) }}"
                   class="text-xs font-bold text-slate-500">
                    ← Mapping Kabag
                </a>

                <h1 class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ $kabag->name }}
                </h1>

                <p class="text-sm text-slate-500">
                    {{ $kabag->email }}
                </p>

            </div>

        </div>


        @if (session('success'))

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>

        @endif


        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

            {{-- KARYAWAN KABAG --}}
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">

                <div class="border-b border-slate-200 p-5">

                    <h2 class="font-extrabold text-slate-900">
                        Karyawan Kabag Ini
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Karyawan yang pengajuannya akan masuk ke {{ $kabag->name }}.
                    </p>

                </div>


                <div class="divide-y divide-slate-100">

                    @forelse ($assignedEmployees as $employee)

                        <div class="flex items-center justify-between gap-4 p-4">

                            <div class="min-w-0">

                                <div class="font-extrabold text-slate-900 truncate">
                                    {{ $employee->nama }}
                                </div>

                                <div class="mt-1 flex flex-wrap gap-2 text-xs text-slate-500">

                                    <span>
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


                            <form method="POST"
                                  action="{{ route(
                                      'master.kabag-mapping.remove',
                                      [
                                          $kabag,
                                          $employee
                                      ]
                                  ) }}">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    onclick="return confirm('Keluarkan karyawan ini dari Kabag?')"
                                    class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-extrabold text-rose-700 hover:bg-rose-100">
                                    Hapus
                                </button>

                            </form>

                        </div>

                    @empty

                        <div class="p-10 text-center">

                            <p class="font-bold text-slate-600">
                                Belum ada karyawan.
                            </p>

                        </div>

                    @endforelse

                </div>


                @if ($assignedEmployees->hasPages())

                    <div class="border-t border-slate-200 p-4">
                        {{ $assignedEmployees->links() }}
                    </div>

                @endif

            </div>


            {{-- TAMBAH KARYAWAN --}}
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">

                <div class="border-b border-slate-200 p-5">

                    <h2 class="font-extrabold text-slate-900">
                        Tambah Karyawan
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Cari kemudian tambahkan satu karyawan ke Kabag ini.
                    </p>

                </div>


                <div class="p-5 border-b border-slate-100">

                    <form method="GET">

                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Cari nama, kode, atau jabatan..."
                               class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">

                    </form>

                </div>


                <div class="divide-y divide-slate-100 max-h-[600px] overflow-y-auto">

                    @forelse ($availableEmployees as $employee)

                        <div class="flex items-center justify-between gap-4 p-4">

                            <div class="min-w-0">

                                <div class="font-extrabold text-slate-900 truncate">
                                    {{ $employee->nama }}
                                </div>

                                <div class="mt-1 text-xs text-slate-500">
                                    {{ $employee->employee_code }}

                                    @if ($employee->jabatan)
                                        • {{ $employee->jabatan }}
                                    @endif
                                </div>

                            </div>


                            <form method="POST"
                                  action="{{ route(
                                      'master.kabag-mapping.assign',
                                      $kabag
                                  ) }}">

                                @csrf

                                <input type="hidden"
                                       name="employee_id"
                                       value="{{ $employee->id }}">

                                <button
                                    type="submit"
                                    class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-extrabold text-white hover:bg-black">
                                    + Tambah
                                </button>

                            </form>

                        </div>

                    @empty

                        <div class="p-10 text-center text-sm font-bold text-slate-500">
                            Tidak ada karyawan ditemukan.
                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
