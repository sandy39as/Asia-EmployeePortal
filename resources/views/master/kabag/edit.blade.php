<x-app-layout>

    <div class="max-w-2xl mx-auto">

        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">

                <a href="{{ route('master.kabag.index') }}"
                   class="text-xs font-extrabold text-slate-500 hover:text-slate-900">
                    ← Kembali
                </a>

                <h1 class="mt-3 text-xl font-extrabold text-slate-900">
                    Edit Kabag
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $kabag->name }}
                </p>

            </div>


            <form method="POST"
                  action="{{ route('master.kabag.update', $kabag) }}"
                  class="space-y-5 p-5 sm:p-6">

                @csrf
                @method('PUT')


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
                           value="{{ old('name', $kabag->name) }}"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                           required>
                </div>


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Email
                    </label>

                    <input type="email"
                           name="email"
                           value="{{ old('email', $kabag->email) }}"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                           required>
                </div>


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Username
                    </label>

                    <input type="text"
                           name="username"
                           value="{{ old('username', $kabag->username) }}"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                </div>


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Password Baru
                    </label>

                    <input type="password"
                           name="password"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">

                    <p class="mt-1 text-xs text-slate-400">
                        Kosongkan jika password tidak ingin diubah.
                    </p>
                </div>


                <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 cursor-pointer">

                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           @checked(old('is_active', $kabag->is_active))
                           class="h-5 w-5 rounded border-slate-300 text-slate-900">

                    <div>
                        <div class="text-sm font-extrabold text-slate-800">
                            Akun Aktif
                        </div>

                        <div class="text-xs text-slate-500">
                            Kabag dapat login dan memproses pengajuan.
                        </div>
                    </div>

                </label>


                <div class="flex justify-end gap-3 pt-2">

                    <a href="{{ route('master.kabag.index') }}"
                       class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
                        Batal
                    </a>

                    <button type="submit"
                            class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-extrabold text-white hover:bg-black">
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>
