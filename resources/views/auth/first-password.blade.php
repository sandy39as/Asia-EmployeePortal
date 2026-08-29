<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
            Buat Password Baru
        </h1>
        <p class="mt-1.5 text-xs sm:text-sm text-slate-500 font-medium leading-relaxed">
            Anda masih menggunakan password bawaan. Silakan buat password pribadi sebelum melanjutkan.
        </p>

        @if (auth()->user()?->employee)
            <div class="mt-4 rounded-2xl border border-[#e2e8f0] bg-[#f8fafc] p-4 text-left">
                <div class="text-sm sm:text-base font-extrabold text-slate-900">
                    {{ auth()->user()->employee->nama }}
                </div>
                <div class="mt-1 text-xs sm:text-sm text-slate-500 font-bold">
                    ID Karyawan: <span class="text-slate-900 font-extrabold">{{ auth()->user()->username }}</span>
                </div>
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('password.first.update') }}" class="space-y-4 sm:space-y-5">
        @csrf

        {{-- PASSWORD BARU --}}
        <div>
            <label for="password" class="block text-xs sm:text-sm font-extrabold text-slate-700 mb-1.5">
                Password Baru
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autofocus
                autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                class="w-full rounded-xl border border-[#d1d5db] bg-white px-4 py-3 text-sm sm:text-base font-semibold text-slate-900 placeholder-slate-400 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 transition shadow-xs"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs sm:text-sm font-bold text-rose-600" />
        </div>

        {{-- KONFIRMASI PASSWORD BARU --}}
        <div>
            <label for="password_confirmation" class="block text-xs sm:text-sm font-extrabold text-slate-700 mb-1.5">
                Konfirmasi Password Baru
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Ulangi password baru"
                class="w-full rounded-xl border border-[#d1d5db] bg-white px-4 py-3 text-sm sm:text-base font-semibold text-slate-900 placeholder-slate-400 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 transition shadow-xs"
            />
        </div>

        {{-- SUBMIT BUTTON --}}
        <div class="pt-2">
            <button
                type="submit"
                class="w-full rounded-xl bg-slate-900 hover:bg-black py-3 px-5 text-sm sm:text-base font-extrabold text-white shadow-md focus:outline-none transition active:scale-[0.99]"
            >
                Simpan Password
            </button>
        </div>
    </form>

    {{-- LOGOUT LINK --}}
    <form method="POST" action="{{ route('logout') }}" class="mt-6 text-center">
        @csrf
        <button
            type="submit"
            class="text-xs sm:text-sm font-bold text-slate-500 hover:text-slate-900 transition"
        >
            Keluar dari Sesi
        </button>
    </form>
</x-guest-layout>
