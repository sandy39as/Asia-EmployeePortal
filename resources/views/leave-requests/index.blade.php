<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <h2 class="text-xl font-bold text-gray-900">
                Pengajuan Saya
            </h2>

            <a href="{{ route('leave-requests.create') }}"
               class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white">

                + Pengajuan

            </a>

        </div>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">


            @if (session('success'))

                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700">

                    {{ session('success') }}

                </div>

            @endif


            <form method="GET"
                  class="mb-5 grid grid-cols-1 gap-3 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-gray-200 sm:grid-cols-2">

                <select name="jenis"
                        class="rounded-2xl border-gray-300"
                        onchange="this.form.submit()">

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
                        class="rounded-2xl border-gray-300"
                        onchange="this.form.submit()">

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

            </form>


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


                    <a href="{{ route('leave-requests.show', $item) }}"
                       class="block rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-200 transition hover:shadow">

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <div class="text-lg font-bold text-gray-900">
                                    {{ $item->jenis_label }}
                                </div>

                                <div class="mt-1 text-sm text-gray-500">

                                    {{ $item->tanggal_mulai->format('d-m-Y') }}

                                    @if (
                                        $item->tanggal_mulai->toDateString()
                                        !==
                                        $item->tanggal_selesai->toDateString()
                                    )
                                        -
                                        {{ $item->tanggal_selesai->format('d-m-Y') }}
                                    @endif

                                </div>

                            </div>


                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">

                                {{ $item->status_label }}

                            </span>

                        </div>


                        <p class="mt-4 line-clamp-2 text-sm text-gray-600">
                            {{ $item->alasan }}
                        </p>

                    </a>

                @empty

                    <div class="rounded-3xl bg-white px-6 py-12 text-center text-gray-500 shadow-sm ring-1 ring-gray-200">
                        Belum ada pengajuan.
                    </div>

                @endforelse

            </div>


            <div class="mt-6">
                {{ $items->links() }}
            </div>

        </div>

    </div>

</x-app-layout>
