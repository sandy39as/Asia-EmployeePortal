<x-guest-layout>

    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900">
            Asia Employee Portal
        </h1>

        <p class="mt-2 text-sm text-gray-500">
            Masuk menggunakan ID Karyawan Anda
        </p>
    </div>

    <form method="POST"
          action="{{ route('login') }}">

        @csrf

        {{-- ID KARYAWAN --}}
        <div>

            <x-input-label
                for="username"
                value="ID Karyawan"
            />

            <x-text-input
                id="username"
                class="mt-1 block w-full uppercase"
                type="text"
                name="username"
                :value="old('username')"
                required
                autofocus
                autocomplete="username"
                placeholder="Contoh: A0403"
            />

            <x-input-error
                :messages="$errors->get('username')"
                class="mt-2"
            />

        </div>

        {{-- PASSWORD --}}
        <div class="mt-4">

            <x-input-label
                for="password"
                value="Password"
            />

            <x-text-input
                id="password"
                class="mt-1 block w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />

        </div>

        {{-- REMEMBER --}}
        <div class="mt-4 block">

            <label
                for="remember_me"
                class="inline-flex items-center"
            >

                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    name="remember"
                >

                <span class="ms-2 text-sm text-gray-600">
                    Ingat saya
                </span>

            </label>

        </div>

        <div class="mt-6">

            <x-primary-button
                class="w-full justify-center"
            >
                Masuk
            </x-primary-button>

        </div>

    </form>

</x-guest-layout>
