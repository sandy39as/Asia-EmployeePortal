<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
            Masuk ke Akun
        </h1>
        <p class="mt-1.5 text-xs sm:text-sm text-slate-500 font-medium">
            Gunakan ID Karyawan Anda untuk melanjutkan
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-5">
        @csrf

        {{-- ID KARYAWAN --}}
        <div>
            <label for="username" class="block text-xs sm:text-sm font-extrabold text-slate-700 mb-1.5">
                ID Karyawan
            </label>
            <input
                id="username"
                type="text"
                name="username"
                value="{{ old('username') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="Contoh: A0403"
                class="w-full uppercase rounded-xl border border-[#d1d5db] bg-white px-4 py-3 text-sm sm:text-base font-semibold text-slate-900 placeholder-slate-400 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 transition shadow-xs"
            />
            <x-input-error :messages="$errors->get('username')" class="mt-1.5 text-xs sm:text-sm font-bold text-rose-600" />
        </div>

        {{-- PASSWORD --}}
        <div>
            <label for="password" class="block text-xs sm:text-sm font-extrabold text-slate-700 mb-1.5">
                Password
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="w-full rounded-xl border border-[#d1d5db] bg-white px-4 py-3 text-sm sm:text-base font-semibold text-slate-900 placeholder-slate-400 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 transition shadow-xs"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs sm:text-sm font-bold text-rose-600" />
        </div>

        {{-- REMEMBER ME --}}
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="rounded border-[#d1d5db] text-slate-900 focus:ring-0 h-4.5 w-4.5"
                >
                <span class="ms-2.5 text-xs sm:text-sm text-slate-600 font-bold">
                    Ingat saya
                </span>
            </label>
        </div>

        {{-- SUBMIT BUTTON --}}
        <div class="pt-2">
            <button
                type="submit"
                class="w-full rounded-xl bg-slate-900 hover:bg-black py-3 px-5 text-sm sm:text-base font-extrabold text-white shadow-md focus:outline-none transition active:scale-[0.99]"
            >
                Masuk
            </button>
        </div>
    </form>
</x-guest-layout>
