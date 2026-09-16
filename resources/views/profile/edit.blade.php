<x-app-layout>

    <x-slot name="headerTitle">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">
                Pengaturan Profil
            </h1>

            <p class="text-sm font-medium text-slate-500">
                Kelola ID Login, email, dan keamanan akun Anda. Nama karyawan mengikuti data master.
            </p>
        </div>
    </x-slot>


    <div class="mx-auto max-w-5xl space-y-5 px-2 sm:px-4">


        {{-- FLASH --}}
        @if (session('status') === 'profile-updated')

            <div
                id="profileSuccessFlash"
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800"
            >
                Profil berhasil diperbarui.
            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- INFORMASI AKUN --}}
        {{-- ========================================================= --}}
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">

                <h2 class="text-base font-extrabold text-slate-900">
                    Informasi Akun
                </h2>

                <p class="mt-1 text-xs leading-5 text-slate-500">
                    ID Login dapat berupa username biasa atau alamat email.
                </p>

            </div>


            <form
                method="POST"
                action="{{ route('profile.update') }}"
                id="profileInformationForm"
                class="space-y-5 p-5 sm:p-6"
            >
                @csrf
                @method('PATCH')


                {{-- NAME - READ ONLY --}}
                <div>

                    <label
                        for="profileNameReadonly"
                        class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500"
                    >
                        Nama Karyawan
                    </label>

                    <div class="relative">

                        <input
                            id="profileNameReadonly"
                            type="text"
                            value="{{ $user->name }}"
                            readonly
                            tabindex="-1"
                            class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-100 px-4 py-3 pr-28 text-sm font-extrabold text-slate-600 outline-none"
                        >

                        <span class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                            Terkunci
                        </span>

                    </div>

                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Nama mengikuti data master karyawan/FaceLog dan tidak dapat diubah dari halaman profil.
                    </p>

                </div>


                {{-- USERNAME --}}
                <div>

                    <label
                        for="username"
                        class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500"
                    >
                        ID Login
                    </label>


                    <div class="flex flex-col gap-2 sm:flex-row">

                        <input
                            id="username"
                            name="username"
                            type="text"
                            value="{{ old('username', $user->username) }}"
                            required
                            autocomplete="username"
                            placeholder="Contoh: sandi.aditya atau nama@email.com"
                            class="min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 focus:border-slate-500 focus:outline-none"
                        >


                        <button
                            type="button"
                            id="checkUsernameBtn"
                            class="shrink-0 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-black"
                        >
                            Cek
                        </button>

                    </div>


                    <div
                        id="usernameCheckResult"
                        class="mt-2 hidden rounded-xl border px-3 py-2 text-xs font-bold"
                    ></div>


                    <x-input-error
                        :messages="$errors->get('username')"
                        class="mt-2 text-xs font-bold text-rose-600"
                    />


                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Boleh menggunakan huruf, angka, titik, underscore, @, +, atau tanda minus.
                    </p>

                </div>


                {{-- EMAIL --}}
                <div>

                    <label
                        for="email"
                        class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500"
                    >
                        Email
                    </label>

                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        autocomplete="email"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 focus:border-slate-500 focus:outline-none"
                    >

                    <x-input-error
                        :messages="$errors->get('email')"
                        class="mt-2 text-xs font-bold text-rose-600"
                    />

                </div>


                {{-- ROLE / INFO --}}
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">

                        <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                            Role
                        </div>

                        <div class="mt-1 text-sm font-black uppercase text-slate-800">
                            {{ $user->role ?? '-' }}
                        </div>

                    </div>


                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">

                        <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                            ID Login Aktif
                        </div>

                        <div class="mt-1 break-all font-mono text-sm font-black text-slate-800">
                            {{ $user->username ?? '-' }}
                        </div>

                    </div>

                </div>


                {{-- SAVE --}}
                <div class="flex justify-end border-t border-slate-100 pt-4">

                    <button
                        type="submit"
                        id="saveProfileBtn"
                        class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>


        {{-- ========================================================= --}}
        {{-- PASSWORD --}}
        {{-- ========================================================= --}}
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">

                <h2 class="text-base font-extrabold text-slate-900">
                    Ubah Password
                </h2>

                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Gunakan password yang berbeda dari password sebelumnya.
                </p>

            </div>


            <div class="p-5 sm:p-6">

                @include(
                    'profile.partials.update-password-form'
                )

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- DELETE USER --}}
        {{-- ========================================================= --}}
        <div class="rounded-3xl border border-rose-200 bg-white shadow-sm">

            <div class="border-b border-rose-100 px-5 py-4 sm:px-6">

                <h2 class="text-base font-extrabold text-rose-800">
                    Hapus Akun
                </h2>

                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Penghapusan akun bersifat permanen.
                </p>

            </div>


            <div class="p-5 sm:p-6">

                @include(
                    'profile.partials.delete-user-form'
                )

            </div>

        </div>

    </div>


    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const usernameInput =
                    document.getElementById(
                        'username'
                    );

                const checkButton =
                    document.getElementById(
                        'checkUsernameBtn'
                    );

                const resultBox =
                    document.getElementById(
                        'usernameCheckResult'
                    );

                const saveButton =
                    document.getElementById(
                        'saveProfileBtn'
                    );

                const profileForm =
                    document.getElementById(
                        'profileInformationForm'
                    );


                const originalUsername =
                    @json(
                        (string) $user->username
                    );


                let checkedUsername =
                    originalUsername;

                let usernameAvailable =
                    true;


                function setCheckResult(
                    type,
                    message
                ) {
                    resultBox.classList.remove(
                        'hidden',
                        'border-emerald-200',
                        'bg-emerald-50',
                        'text-emerald-700',
                        'border-rose-200',
                        'bg-rose-50',
                        'text-rose-700',
                        'border-slate-200',
                        'bg-slate-50',
                        'text-slate-600'
                    );


                    if (
                        type === 'success'
                    ) {
                        resultBox.classList.add(
                            'border-emerald-200',
                            'bg-emerald-50',
                            'text-emerald-700'
                        );
                    }
                    else if (
                        type === 'error'
                    ) {
                        resultBox.classList.add(
                            'border-rose-200',
                            'bg-rose-50',
                            'text-rose-700'
                        );
                    }
                    else {
                        resultBox.classList.add(
                            'border-slate-200',
                            'bg-slate-50',
                            'text-slate-600'
                        );
                    }


                    resultBox.textContent =
                        message;
                }


                function refreshSaveState() {

                    const currentUsername =
                        usernameInput.value.trim();


                    const usernameChanged =
                        currentUsername
                        !==
                        originalUsername;


                    if (
                        ! usernameChanged
                    ) {
                        usernameAvailable =
                            true;

                        checkedUsername =
                            originalUsername;

                        saveButton.disabled =
                            false;

                        saveButton.classList.remove(
                            'opacity-40',
                            'cursor-not-allowed'
                        );

                        return;
                    }


                    const valid =
                        usernameAvailable
                        &&
                        checkedUsername
                        ===
                        currentUsername;


                    saveButton.disabled =
                        ! valid;


                    saveButton.classList.toggle(
                        'opacity-40',
                        ! valid
                    );

                    saveButton.classList.toggle(
                        'cursor-not-allowed',
                        ! valid
                    );
                }


                usernameInput.addEventListener(
                    'input',
                    function () {

                        const currentUsername =
                            usernameInput.value.trim();


                        if (
                            currentUsername
                            ===
                            originalUsername
                        ) {
                            resultBox.classList.add(
                                'hidden'
                            );

                            usernameAvailable =
                                true;

                            checkedUsername =
                                originalUsername;
                        }
                        else {
                            usernameAvailable =
                                false;

                            checkedUsername =
                                '';

                            setCheckResult(
                                'neutral',
                                'Klik tombol Cek untuk memastikan ID Login tersedia.'
                            );
                        }


                        refreshSaveState();
                    }
                );


                checkButton.addEventListener(
                    'click',
                    async function () {

                        const username =
                            usernameInput.value.trim();


                        if (
                            username === ''
                        ) {
                            usernameAvailable =
                                false;

                            checkedUsername =
                                '';

                            setCheckResult(
                                'error',
                                'ID Login wajib diisi.'
                            );

                            refreshSaveState();

                            return;
                        }


                        const oldText =
                            checkButton.textContent;

                        checkButton.disabled =
                            true;

                        checkButton.textContent =
                            'Mengecek...';


                        try {

                            const response =
                                await fetch(
                                    @json(
                                        route(
                                            'profile.check-username'
                                        )
                                    ),
                                    {
                                        method:
                                            'POST',

                                        headers: {
                                            'Content-Type':
                                                'application/json',

                                            'Accept':
                                                'application/json',

                                            'X-CSRF-TOKEN':
                                                @json(
                                                    csrf_token()
                                                ),

                                            'X-Requested-With':
                                                'XMLHttpRequest',
                                        },

                                        body:
                                            JSON.stringify({
                                                username:
                                                    username,
                                            }),
                                    }
                                );


                            const data =
                                await response.json();


                            if (
                                ! response.ok
                            ) {
                                const message =
                                    data?.errors
                                        ?.username
                                        ?.[0]
                                    ??
                                    data?.message
                                    ??
                                    'Pengecekan gagal.';

                                usernameAvailable =
                                    false;

                                checkedUsername =
                                    '';

                                setCheckResult(
                                    'error',
                                    message
                                );

                                return;
                            }


                            usernameAvailable =
                                data.available
                                === true;

                            checkedUsername =
                                usernameAvailable
                                    ? username
                                    : '';


                            setCheckResult(
                                usernameAvailable
                                    ? 'success'
                                    : 'error',

                                data.message
                                ??
                                (
                                    usernameAvailable
                                        ? 'ID Login tersedia.'
                                        : 'ID Login tidak tersedia.'
                                )
                            );

                        }
                        catch (
                            error
                        ) {
                            usernameAvailable =
                                false;

                            checkedUsername =
                                '';

                            setCheckResult(
                                'error',
                                'Tidak dapat mengecek ID Login. Coba lagi.'
                            );
                        }
                        finally {

                            checkButton.disabled =
                                false;

                            checkButton.textContent =
                                oldText;

                            refreshSaveState();
                        }
                    }
                );


                profileForm.addEventListener(
                    'submit',
                    function (
                        event
                    ) {
                        refreshSaveState();


                        if (
                            saveButton.disabled
                        ) {
                            event.preventDefault();

                            setCheckResult(
                                'error',
                                'Cek ketersediaan ID Login terlebih dahulu.'
                            );
                        }
                    }
                );


                refreshSaveState();


                const flash =
                    document.getElementById(
                        'profileSuccessFlash'
                    );

                if (
                    flash
                ) {
                    setTimeout(
                        function () {

                            flash.style.transition =
                                'opacity .4s ease, transform .4s ease';

                            flash.style.opacity =
                                '0';

                            flash.style.transform =
                                'translateY(-6px)';

                            setTimeout(
                                function () {
                                    flash.remove();
                                },
                                400
                            );
                        },
                        3500
                    );
                }

            }
        );
    </script>

</x-app-layout>
