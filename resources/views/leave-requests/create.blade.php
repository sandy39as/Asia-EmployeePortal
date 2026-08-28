<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">
            Buat Pengajuan
        </h2>
    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">


            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-200">


                {{-- EMPLOYEE --}}
                <div class="mb-6 rounded-2xl bg-blue-50 p-4">

                    <div class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                        Karyawan
                    </div>

                    <div class="mt-1 font-bold text-gray-900">
                        {{ $employee->nama }}
                    </div>

                    <div class="mt-1 text-sm text-gray-600">
                        {{ $employee->employee_code }}
                        @if ($employee->jabatan)
                            • {{ $employee->jabatan }}
                        @endif
                    </div>

                </div>


                @if ($errors->any())

                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">

                        <div class="font-semibold text-red-700">
                            Periksa kembali data pengajuan.
                        </div>

                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>

                @endif


                <form method="POST"
                      action="{{ route('leave-requests.store') }}"
                      enctype="multipart/form-data"
                      class="space-y-5">

                    @csrf


                    {{-- JENIS --}}
                    <div>

                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Jenis Pengajuan
                        </label>

                        <select name="jenis"
                                class="w-full rounded-2xl border-gray-300"
                                required>

                            <option value="">
                                -- Pilih jenis --
                            </option>

                            <option value="izin"
                                @selected(old('jenis', request('jenis')) === 'izin')>
                                Izin
                            </option>

                            <option value="cuti"
                                @selected(old('jenis', request('jenis')) === 'cuti')>
                                Cuti
                            </option>

                            <option value="sakit"
                                @selected(old('jenis', request('jenis')) === 'sakit')>
                                Sakit
                            </option>

                        </select>

                    </div>


                    {{-- DURASI --}}
                    <div>

                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Durasi
                        </label>

                        <select name="durasi_type"
                                id="durasiType"
                                class="w-full rounded-2xl border-gray-300"
                                required>

                            <option value="full_day"
                                @selected(old('durasi_type') === 'full_day')>
                                Sehari Penuh / Beberapa Hari
                            </option>

                            <option value="hourly"
                                @selected(old('durasi_type') === 'hourly')>
                                Beberapa Jam
                            </option>

                        </select>

                    </div>


                    {{-- DATE --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>

                            <label class="mb-2 block text-sm font-semibold text-gray-700">
                                Tanggal Mulai
                            </label>

                            <input type="date"
                                   name="tanggal_mulai"
                                   id="tanggalMulai"
                                   value="{{ old('tanggal_mulai') }}"
                                   class="w-full rounded-2xl border-gray-300"
                                   required>

                        </div>


                        <div>

                            <label class="mb-2 block text-sm font-semibold text-gray-700">
                                Tanggal Selesai
                            </label>

                            <input type="date"
                                   name="tanggal_selesai"
                                   id="tanggalSelesai"
                                   value="{{ old('tanggal_selesai') }}"
                                   class="w-full rounded-2xl border-gray-300"
                                   required>

                        </div>

                    </div>


                    {{-- HOURLY --}}
                    <div id="hourlyFields"
                         class="hidden grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>

                            <label class="mb-2 block text-sm font-semibold text-gray-700">
                                Jam Mulai
                            </label>

                            <input type="time"
                                   name="jam_mulai"
                                   value="{{ old('jam_mulai') }}"
                                   class="w-full rounded-2xl border-gray-300">

                        </div>


                        <div>

                            <label class="mb-2 block text-sm font-semibold text-gray-700">
                                Jam Selesai
                            </label>

                            <input type="time"
                                   name="jam_selesai"
                                   value="{{ old('jam_selesai') }}"
                                   class="w-full rounded-2xl border-gray-300">

                        </div>

                    </div>


                    {{-- REASON --}}
                    <div>

                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Alasan
                        </label>

                        <textarea name="alasan"
                                  rows="5"
                                  maxlength="2000"
                                  class="w-full rounded-2xl border-gray-300"
                                  placeholder="Tuliskan alasan pengajuan..."
                                  required>{{ old('alasan') }}</textarea>

                    </div>


                    {{-- ATTACHMENT --}}
                    <div>

                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Lampiran
                        </label>

                        <input type="file"
                               name="lampiran"
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full rounded-2xl border border-gray-300 p-3">

                        <p class="mt-2 text-xs text-gray-500">
                            PDF/JPG/PNG maksimal 5 MB.
                        </p>

                    </div>


                    <div class="flex flex-col-reverse gap-3 pt-3 sm:flex-row sm:justify-end">

                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center rounded-2xl border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700">

                            Batal

                        </a>

                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">

                            Kirim Pengajuan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const durasiType =
                document.getElementById('durasiType');

            const hourlyFields =
                document.getElementById('hourlyFields');

            const tanggalMulai =
                document.getElementById('tanggalMulai');

            const tanggalSelesai =
                document.getElementById('tanggalSelesai');


            function syncDurasi()
            {
                const hourly =
                    durasiType.value === 'hourly';

                if (hourly) {
                    hourlyFields.classList.remove('hidden');
                    hourlyFields.classList.add('grid');

                    /*
                     * Kalau beberapa jam,
                     * tanggal selesai otomatis mengikuti tanggal mulai.
                     */
                    if (tanggalMulai.value) {
                        tanggalSelesai.value =
                            tanggalMulai.value;
                    }
                } else {
                    hourlyFields.classList.add('hidden');
                    hourlyFields.classList.remove('grid');
                }
            }


            durasiType.addEventListener(
                'change',
                syncDurasi
            );


            tanggalMulai.addEventListener(
                'change',
                function () {

                    if (
                        durasiType.value === 'hourly'
                    ) {
                        tanggalSelesai.value =
                            tanggalMulai.value;
                    }

                    tanggalSelesai.min =
                        tanggalMulai.value;
                }
            );


            syncDurasi();

        });
    </script>

</x-app-layout>
