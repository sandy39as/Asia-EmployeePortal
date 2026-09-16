<x-guest-layout>

    <div class="mb-6 text-center">

        <h1 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">
            Buat ID Login Baru
        </h1>

        <p class="mt-1.5 text-xs font-medium leading-5 text-slate-500 sm:text-sm">
            ID awal hanya digunakan untuk aktivasi. Buat ID Login pribadi yang mudah Anda ingat.
        </p>

    </div>


    @if (session('success'))

        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-800">
            {{ session('success') }}
        </div>

    @endif


    <div class="mb-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">

        <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
            ID Login Saat Ini
        </div>

        <div class="mt-1 font-mono text-base font-black text-slate-900">
            {{ auth()->user()->username }}
        </div>

    </div>


    <form
        method="POST"
        action="{{ route('login-id.first.update') }}"
        class="space-y-5"
    >
        @csrf


        <div>

            <label
                for="username"
                class="mb-1.5 block text-xs font-extrabold text-slate-700 sm:text-sm"
            >
                ID Login / Email Baru
            </label>


            <input
                id="username"
                type="text"
                name="username"
                value="{{ old('username') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="Contoh: sandiaditya atau sandi@gmail.com"
                class="w-full rounded-xl border border-[#d1d5db] bg-white px-4 py-3 text-sm font-semibold text-slate-900 placeholder-slate-400 shadow-xs transition focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 sm:text-base"
            >


            <x-input-error
                :messages="$errors->get('username')"
                class="mt-1.5 text-xs font-bold text-rose-600 sm:text-sm"
            />


            <p class="mt-2 text-xs leading-5 text-slate-500">
                Bisa menggunakan username atau alamat email. Jangan gunakan spasi.
            </p>

        </div>


        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-xs leading-5 text-blue-800">
            Setelah ID Login disimpan, Anda akan diminta membuat password baru.
        </div>


        <button
            type="submit"
            class="w-full rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-md transition hover:bg-black active:scale-[0.99] sm:text-base"
        >
            Simpan & Lanjut
        </button>

    </form>

</x-guest-layout>
