<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="text-xl font-bold text-gray-900">
                    Pengajuan Karyawan
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Izin, cuti, dan sakit seluruh karyawan.
                </p>

            </div>

            <a href="{{ route('hrd.dashboard') }}"
               class="text-sm font-semibold text-gray-600">
                ← Dashboard
            </a>

        </div>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">


            @if (session('success'))

                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700">
                    {{ session('success') }}
                </div>

            @endif


            {{-- FILTER --}}
            <form method="GET"
                  class="mb-6 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-200">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">


                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Nama / ID Karyawan"
                           class="rounded-2xl border-gray-300">


                    <select name="jenis"
                            class="rounded-2xl border-gray-300">

                        <option value="">
                            Semua Jenis
                        </option>

                        <option value="izin" @selected($jenis === 'izin')>
                            Izin
                        </option>

                        <option value="cuti" @selected($jenis === 'cuti')>
                            Cuti
                        </option>

                        <option value="sakit" @selected($jenis === 'sakit')>
                            Sakit
                        </option>

                    </select>


                    <select name="status"
                            class="rounded-2xl border-gray-300">

                        <option value="">
                            Semua Status
                        </option>

                        <option value="pending" @selected($status === 'pending')>
                            Menunggu
                        </option>

                        <option value="approved" @selected($status === 'approved')>
                            Disetujui
                        </option>

                        <option value="rejected" @selected($status === 'rejected')>
                            Ditolak
                        </option>

                        <option value="cancelled" @selected($status === 'cancelled')>
                            Dibatalkan
                        </option>

                    </select>


                    <input type="date"
                           name="start_date"
                           value="{{ $startDate }}"
                           class="rounded-2xl border-gray-300">


                    <input type="date"
                           name="end_date"
                           value="{{ $endDate }}"
                           class="rounded-2xl border-gray-300">

                </div>


                <div class="mt-4 flex flex-wrap justify-end gap-3">

                    <a href="{{ route('hrd.leave-requests.index') }}"
                       class="rounded-2xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-600">

                        Reset

                    </a>

                    <button type="submit"
                            class="rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white">

                        Filter

                    </button>

                </div>

            </form>


            {{-- LIST --}}
            <div class="space-y-4">

                @forelse ($items as $item)

                    @php
                        $statusClass = match($item->status) {
                            'approved' => 'bg-emerald-100 text-emerald-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            'cancelled' => 'bg-gray-100 text-gray-600',
                            default => 'bg-amber-100 text-amber-700',
                        };
                    @endphp


                    <a href="{{ route('hrd.leave-requests.show', $item) }}"
                       class="block rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-200 transition hover:shadow-md">

                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">


                            <div>

                                <div class="flex flex-wrap items-center gap-2">

                                    <div class="text-lg font-bold text-gray-900">
                                        {{ $item->employee?->nama ?? '-' }}
                                    </div>

                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                        {{ $item->employee?->employee_code ?? '-' }}
                                    </span>

                                </div>


                                <div class="mt-2 text-sm text-gray-500">

                                    {{ $item->jenis_label }}

                                    •

                                    {{ $item->tanggal_mulai->format('d-m-Y') }}

                                    @if (
                                        $item->tanggal_mulai->toDateString()
                                        !==
                                        $item->tanggal_selesai->toDateString()
                                    )

                                        -
                                        {{ $item->tanggal_selesai->format('d-m-Y') }}

                                    @endif

                                    @if ($item->durasi_type === 'hourly')

                                        •

                                        {{ substr($item->jam_mulai, 0, 5) }}
                                        -
                                        {{ substr($item->jam_selesai, 0, 5) }}

                                    @endif

                                </div>


                                <div class="mt-2 line-clamp-1 text-sm text-gray-600">
                                    {{ $item->alasan }}
                                </div>

                            </div>


                            <span class="w-fit rounded-full px-3 py-1.5 text-xs font-bold {{ $statusClass }}">
                                {{ $item->status_label }}
                            </span>

                        </div>

                    </a>

                @empty

                    <div class="rounded-3xl bg-white py-12 text-center text-gray-500 shadow-sm ring-1 ring-gray-200">
                        Tidak ada pengajuan.
                    </div>

                @endforelse

            </div>


            <div class="mt-6">
                {{ $items->links() }}
            </div>

        </div>

    </div>

</x-app-layout>
