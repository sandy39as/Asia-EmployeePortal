<x-app-layout>

    <div class="max-w-6xl mx-auto space-y-5">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-slate-400">
                    Master Data
                </p>

                <h1 class="mt-1 text-xl sm:text-2xl font-extrabold text-slate-900">
                    Data Kabag
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola akun Kabag dan mapping karyawan dalam satu tempat.
                </p>
            </div>


            <button
                type="button"
                id="openCreateKabagModal"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-black"
            >
                + Tambah Kabag
            </button>

        </div>


        {{-- FLASH --}}
        @if (session('success'))

            <div
                id="successFlash"
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800"
            >
                {{ session('success') }}
            </div>

        @endif


        @if ($errors->any())

            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">

                <div class="font-extrabold">
                    Ada data yang perlu diperbaiki.
                </div>

                <ul class="mt-2 list-disc pl-5 text-xs font-medium">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif


        {{-- MAIN CARD --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

            {{-- SEARCH --}}
            <div class="border-b border-slate-200 p-4 sm:p-5">

                <form
                    method="GET"
                    action="{{ route('master.kabag.index') }}"
                    class="flex flex-col gap-3 sm:flex-row"
                >

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari nama, email, username..."
                        class="flex-1 rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:ring-4 focus:ring-slate-100"
                    >


                    <button
                        type="submit"
                        class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white hover:bg-black"
                    >
                        Cari
                    </button>


                    @if ($search !== '')

                        <a
                            href="{{ route('master.kabag.index') }}"
                            class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-bold text-slate-700 hover:bg-slate-50"
                        >
                            Reset
                        </a>

                    @endif

                </form>

            </div>


            {{-- TABLE --}}
            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-slate-50">

                        <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                            <th class="px-5 py-4 font-extrabold">Kabag</th>
                            <th class="px-5 py-4 font-extrabold">Username</th>
                            <th class="px-5 py-4 font-extrabold">Karyawan</th>
                            <th class="px-5 py-4 font-extrabold">Status</th>
                            <th class="px-5 py-4 text-right font-extrabold">Aksi</th>
                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($kabags as $kabag)

                            <tr class="hover:bg-slate-50/70">

                                <td class="px-5 py-4">

                                    <div class="font-extrabold text-slate-900">
                                        {{ $kabag->name }}
                                    </div>

                                    <div class="mt-0.5 text-xs font-medium text-slate-500">
                                        {{ $kabag->email ?: '-' }}
                                    </div>

                                </td>


                                <td class="px-5 py-4 text-sm font-bold text-slate-700">
                                    {{ $kabag->username ?: '-' }}
                                </td>


                                <td class="px-5 py-4">

                                    <span class="inline-flex rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-extrabold text-blue-700">
                                        {{ $kabag->managed_employees_count }} karyawan
                                    </span>

                                </td>


                                <td class="px-5 py-4">

                                    @if ($kabag->is_active)

                                        <span class="inline-flex rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-700">
                                            Aktif
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1 text-xs font-extrabold text-slate-600">
                                            Nonaktif
                                        </span>

                                    @endif

                                </td>


                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-end gap-2">

                                        <a
                                            href="{{ route(
                                                'master.kabag-mapping.index',
                                                [
                                                    'kabag_id' => $kabag->id,
                                                ]
                                            ) }}"
                                            class="rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-extrabold text-blue-700 hover:bg-blue-100"
                                        >
                                            Mapping
                                        </a>


                                        <button
                                            type="button"
                                            data-edit-kabag
                                            data-id="{{ $kabag->id }}"
                                            data-name="{{ $kabag->name }}"
                                            data-email="{{ $kabag->email }}"
                                            data-username="{{ $kabag->username }}"
                                            data-active="{{ $kabag->is_active ? '1' : '0' }}"
                                            data-action="{{ route('master.kabag.update', $kabag) }}"
                                            class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-50"
                                        >
                                            Edit
                                        </button>


                                        <form
                                            method="POST"
                                            action="{{ route('master.kabag.destroy', $kabag) }}"
                                            onsubmit="return confirm('Hapus akun Kabag {{ $kabag->name }}?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-extrabold text-rose-700 hover:bg-rose-100"
                                            >
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="5"
                                    class="px-5 py-12 text-center text-sm font-bold text-slate-500"
                                >
                                    Belum ada akun Kabag.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            @if ($kabags->hasPages())

                <div class="border-t border-slate-200 p-4">
                    {{ $kabags->links() }}
                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- CREATE MODAL --}}
    {{-- ========================================================= --}}
    <div
        id="createKabagModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
    >

        <div
            id="createKabagModalBox"
            class="max-h-[92vh] w-full max-w-lg scale-95 overflow-y-auto rounded-3xl bg-white opacity-0 shadow-2xl transition-all duration-200"
        >

            <div class="flex items-start justify-between border-b border-slate-200 px-5 py-4">

                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">
                        Tambah Kabag
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Buat akun Kabag baru.
                    </p>
                </div>


                <button
                    type="button"
                    data-close-modal="createKabagModal"
                    data-close-box="createKabagModalBox"
                    class="text-slate-400 hover:text-slate-700"
                >
                    ✕
                </button>

            </div>


            <form
                method="POST"
                action="{{ route('master.kabag.store') }}"
                class="space-y-4 p-5"
            >
                @csrf


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Nama Kabag
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                    >
                </div>


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                    >
                </div>


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                    >
                </div>


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Password Awal
                    </label>

                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                    >
                </div>


                <div class="flex justify-end gap-3 pt-2">

                    <button
                        type="button"
                        data-close-modal="createKabagModal"
                        data-close-box="createKabagModalBox"
                        class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700"
                    >
                        Batal
                    </button>


                    <button
                        type="submit"
                        class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-extrabold text-white hover:bg-black"
                    >
                        Simpan Kabag
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- EDIT MODAL --}}
    {{-- ========================================================= --}}
    <div
        id="editKabagModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
    >

        <div
            id="editKabagModalBox"
            class="max-h-[92vh] w-full max-w-lg scale-95 overflow-y-auto rounded-3xl bg-white opacity-0 shadow-2xl transition-all duration-200"
        >

            <div class="flex items-start justify-between border-b border-slate-200 px-5 py-4">

                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">
                        Edit Kabag
                    </h3>

                    <p
                        id="editKabagSubtitle"
                        class="mt-1 text-xs font-bold text-slate-500"
                    ></p>
                </div>


                <button
                    type="button"
                    data-close-modal="editKabagModal"
                    data-close-box="editKabagModalBox"
                    class="text-slate-400 hover:text-slate-700"
                >
                    ✕
                </button>

            </div>


            <form
                method="POST"
                id="editKabagForm"
                class="space-y-4 p-5"
            >
                @csrf
                @method('PUT')


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Nama Kabag
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="editKabagName"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                    >
                </div>


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="editKabagEmail"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                    >
                </div>


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        id="editKabagUsername"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                    >
                </div>


                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Password Baru
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                    >

                    <p class="mt-1 text-xs text-slate-400">
                        Kosongkan jika tidak diubah.
                    </p>
                </div>


                <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        id="editKabagActive"
                        class="h-5 w-5 rounded border-slate-300 text-slate-900"
                    >


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

                    <button
                        type="button"
                        data-close-modal="editKabagModal"
                        data-close-box="editKabagModalBox"
                        class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700"
                    >
                        Batal
                    </button>


                    <button
                        type="submit"
                        class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-extrabold text-white hover:bg-black"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>


    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {

                function openModal(
                    modalId,
                    boxId
                ) {

                    const modal =
                        document.getElementById(
                            modalId
                        );

                    const box =
                        document.getElementById(
                            boxId
                        );


                    if (
                        ! modal
                        ||
                        ! box
                    ) {
                        return;
                    }


                    modal.classList.remove(
                        'hidden'
                    );

                    modal.classList.add(
                        'flex'
                    );


                    setTimeout(
                        function () {

                            box.classList.remove(
                                'scale-95',
                                'opacity-0'
                            );

                            box.classList.add(
                                'scale-100',
                                'opacity-100'
                            );
                        },
                        10
                    );


                    document.body.classList.add(
                        'overflow-hidden'
                    );
                }


                function closeModal(
                    modalId,
                    boxId
                ) {

                    const modal =
                        document.getElementById(
                            modalId
                        );

                    const box =
                        document.getElementById(
                            boxId
                        );


                    if (
                        ! modal
                        ||
                        ! box
                    ) {
                        return;
                    }


                    box.classList.remove(
                        'scale-100',
                        'opacity-100'
                    );

                    box.classList.add(
                        'scale-95',
                        'opacity-0'
                    );


                    setTimeout(
                        function () {

                            modal.classList.remove(
                                'flex'
                            );

                            modal.classList.add(
                                'hidden'
                            );

                            document.body.classList.remove(
                                'overflow-hidden'
                            );
                        },
                        180
                    );
                }


                document
                    .getElementById(
                        'openCreateKabagModal'
                    )
                    ?.addEventListener(
                        'click',
                        function () {

                            openModal(
                                'createKabagModal',
                                'createKabagModalBox'
                            );
                        }
                    );


                document
                    .querySelectorAll(
                        '[data-edit-kabag]'
                    )
                    .forEach(
                        function (button) {

                            button.addEventListener(
                                'click',
                                function () {

                                    const form =
                                        document.getElementById(
                                            'editKabagForm'
                                        );

                                    const nameInput =
                                        document.getElementById(
                                            'editKabagName'
                                        );

                                    const emailInput =
                                        document.getElementById(
                                            'editKabagEmail'
                                        );

                                    const usernameInput =
                                        document.getElementById(
                                            'editKabagUsername'
                                        );

                                    const activeInput =
                                        document.getElementById(
                                            'editKabagActive'
                                        );

                                    const subtitle =
                                        document.getElementById(
                                            'editKabagSubtitle'
                                        );


                                    form.action =
                                        button.dataset.action;

                                    nameInput.value =
                                        button.dataset.name
                                        ?? '';

                                    emailInput.value =
                                        button.dataset.email
                                        ?? '';

                                    usernameInput.value =
                                        button.dataset.username
                                        ?? '';

                                    activeInput.checked =
                                        button.dataset.active
                                        === '1';

                                    subtitle.textContent =
                                        button.dataset.name
                                        ?? '';


                                    openModal(
                                        'editKabagModal',
                                        'editKabagModalBox'
                                    );
                                }
                            );
                        }
                    );


                document
                    .querySelectorAll(
                        '[data-close-modal]'
                    )
                    .forEach(
                        function (button) {

                            button.addEventListener(
                                'click',
                                function () {

                                    closeModal(
                                        button.dataset.closeModal,
                                        button.dataset.closeBox
                                    );
                                }
                            );
                        }
                    );


                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key
                            !==
                            'Escape'
                        ) {
                            return;
                        }


                        const createModal =
                            document.getElementById(
                                'createKabagModal'
                            );

                        const editModal =
                            document.getElementById(
                                'editKabagModal'
                            );


                        if (
                            createModal
                            ?.classList
                            .contains(
                                'flex'
                            )
                        ) {
                            closeModal(
                                'createKabagModal',
                                'createKabagModalBox'
                            );
                        }


                        if (
                            editModal
                            ?.classList
                            .contains(
                                'flex'
                            )
                        ) {
                            closeModal(
                                'editKabagModal',
                                'editKabagModalBox'
                            );
                        }
                    }
                );


                const successFlash =
                    document.getElementById(
                        'successFlash'
                    );


                if (successFlash) {

                    setTimeout(
                        function () {

                            successFlash.style.transition =
                                'opacity .4s ease';

                            successFlash.style.opacity =
                                '0';


                            setTimeout(
                                function () {

                                    successFlash.remove();
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
