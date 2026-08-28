<x-guest-layout>

    <div class="mb-6 text-center">

        <h1 class="text-2xl font-bold text-gray-900">
            Buat Password Baru
        </h1>

        <p class="mt-2 text-sm leading-6 text-gray-500">
            Anda masih menggunakan password awal.
            Silakan buat password pribadi sebelum melanjutkan.
        </p>

        @if (auth()->user()?->employee)
            <div class="mt-4 rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-600">
                <div class="font-semibold text-gray-900">
                    {{ auth()->user()->employee->nama }}
                </div>

                <div class="mt-1">
                    ID Karyawan:
                    {{ auth()->user()->username }}
                </div>
            </div>
        @endif

    </div>

    <form method="POST"
          action="{{ route('password.first.update') }}">

        @csrf

        <div>
            <x-input-label
                for="password"
                value="Password Baru"
            />

            <x-text-input
                id="password"
                class="mt-1 block w-full"
                type="password"
                name="password"
                required
                autofocus
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>

        <div class="mt-4">

            <x-input-label
                for="password_confirmation"
                value="Konfirmasi Password Baru"
            />

            <x-text-input
                id="password_confirmation"
                class="mt-1 block w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />

        </div>

        <div class="mt-6">

            <x-primary-button
                class="w-full justify-center"
            >
                Simpan Password
            </x-primary-button>

        </div>

    </form>

    <form method="POST"
          action="{{ route('logout') }}"
          class="mt-4 text-center">

        @csrf

        <button type="submit"
                class="text-sm text-gray-500 hover:text-gray-700">
            Keluar
        </button>

    </form>

</x-guest-layout>
