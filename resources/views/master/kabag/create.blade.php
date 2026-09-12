<x-app-layout>

    <div class="max-w-2xl mx-auto">

        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">

                <a href="{{ route('master.kabag.index') }}"
                   class="text-xs font-extrabold text-slate-500 hover:text-slate-900">
                    ← Kembali
                </a>

                <h1 class="mt-3 text-xl font-extrabold text-slate-900">
                    Tambah Kabag
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Buat akun Kabag untuk proses persetujuan pengajuan karyawan.
                </p>

            </div>


            <form method="POST"
                  action="{{ route('master.kabag.store') }}"
                  class="space-y-5 p-5 sm:p-6">

                @csrf


                @if ($errors->any())
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                        <div class="font-extrabold text-rose-800">
                            Ada data yang perlu diperbaiki:
                        </div>

                        <ul class="mt-2 list-disc pl-5 text-sm font-medium text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Nama Kabag
                    </label>

                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="Contoh: Budi Santoso"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                           required>
                </div>


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Email
                    </label>

                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="kabag@asia.com"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                           required>
                </div>


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Username
                    </label>

                    <input type="text"
                           name="username"
                           value="{{ old('username') }}"
                           placeholder="Contoh: kabagproduksia"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">

                    <p class="mt-1 text-xs text-slate-400">
                        Bisa digunakan untuk login Portal.
                    </p>
                </div>


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Password Awal
                    </label>

                    <input type="password"
                           name="password"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                           required>

                    <p class="mt-1 text-xs text-slate-400">
                        Kabag akan diminta mengganti password setelah login pertama.
                    </p>
                </div>


                <div class="flex justify-end gap-3 pt-2">

                    <a href="{{ route('master.kabag.index') }}"
                       class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
                        Batal
                    </a>

                    <button type="submit"
                            class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-extrabold text-white hover:bg-black">
                        Simpan Kabag
                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>
