<x-app-layout>

    <x-slot name="headerTitle">
        <div>
            <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                Master Cuti Khusus
            </h1>

            <p class="text-xs sm:text-sm text-slate-500 font-medium">
                Pengaturan jenis cuti khusus di luar jatah cuti tahunan.
            </p>
        </div>
    </x-slot>


    <div class="max-w-7xl mx-auto space-y-4">

        {{-- FLASH --}}
        @if(session('success'))
            <div
                class="rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-sm font-bold text-emerald-800"
            >
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div
                class="rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-sm font-bold text-rose-800"
            >
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div
                class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"
            >
                <div class="font-extrabold">
                    Data belum bisa disimpan.
                </div>

                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- HEADER ACTION --}}
        <div
            class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
        >

            <div class="flex flex-wrap gap-2">

                <span
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700"
                >
                    Total:
                    <strong class="text-slate-900">
                        {{ number_format($summary['total'] ?? 0) }}
                    </strong>
                </span>

                <span
                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-800"
                >
                    Aktif:
                    {{ number_format($summary['active'] ?? 0) }}
                </span>

                <span
                    class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-600"
                >
                    Nonaktif:
                    {{ number_format($summary['inactive'] ?? 0) }}
                </span>

            </div>


            <button
                type="button"
                id="openCreateSpecialLeaveModal"
                class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-extrabold text-white transition hover:bg-black"
            >
                + Tambah Cuti Khusus
            </button>

        </div>


        {{-- FILTER --}}
        <form
            method="GET"
            action="{{ route('master.special-leave-types.index') }}"
            class="flex flex-col gap-2 rounded-2xl border border-slate-200 bg-white p-4 sm:flex-row"
        >

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Cari nama / kode..."
                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm sm:max-w-sm"
            >

            <select
                name="status"
                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm sm:w-44"
            >
                <option value="">
                    Semua Status
                </option>

                <option
                    value="active"
                    @selected($status === 'active')
                >
                    Aktif
                </option>

                <option
                    value="inactive"
                    @selected($status === 'inactive')
                >
                    Nonaktif
                </option>
            </select>

            <button
                type="submit"
                class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-extrabold text-white"
            >
                Cari
            </button>

            <a
                href="{{ route('master.special-leave-types.index') }}"
                class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-center text-sm font-bold text-slate-700"
            >
                Reset
            </a>

        </form>


        {{-- DESKTOP TABLE --}}
        <div
            class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:block"
        >

            <table class="min-w-full divide-y divide-slate-200 text-sm">

                <thead
                    class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-600"
                >
                    <tr>
                        <th class="px-5 py-3.5 text-left">
                            Cuti Khusus
                        </th>

                        <th class="px-5 py-3.5 text-left">
                            Kode
                        </th>

                        <th class="px-5 py-3.5 text-center">
                            Hari
                        </th>

                        <th class="px-5 py-3.5 text-left">
                            Keterangan
                        </th>

                        <th class="px-5 py-3.5 text-center">
                            Status
                        </th>

                        <th class="px-5 py-3.5 text-right">
                            Aksi
                        </th>
                    </tr>
                </thead>


                <tbody class="divide-y divide-slate-200">

                    @forelse($items as $item)

                        <tr class="transition hover:bg-slate-50">

                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900">
                                    {{ $item->name }}
                                </div>
                            </td>


                            <td class="px-5 py-4">
                                <span
                                    class="rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-xs font-bold text-slate-700"
                                >
                                    {{ $item->code }}
                                </span>
                            </td>


                            <td class="px-5 py-4 text-center">

                                <span
                                    class="inline-flex rounded-lg border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-extrabold text-sky-700"
                                >
                                    {{ $item->default_days }} hari
                                </span>

                            </td>


                            <td class="max-w-md px-5 py-4 text-slate-600">
                                {{ $item->description ?: '-' }}
                            </td>


                            <td class="px-5 py-4 text-center">

                                @if($item->is_active)

                                    <span
                                        class="rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-700"
                                    >
                                        Aktif
                                    </span>

                                @else

                                    <span
                                        class="rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600"
                                    >
                                        Nonaktif
                                    </span>

                                @endif

                            </td>


                            <td class="px-5 py-4 text-right">

                                <div
                                    class="inline-flex items-center gap-2"
                                >

                                    <button
                                        type="button"
                                        class="openEditSpecialLeave rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-extrabold text-blue-700 transition hover:bg-blue-100"
                                        data-modal="editSpecialLeave-{{ $item->id }}"
                                        data-box="editSpecialLeaveBox-{{ $item->id }}"
                                    >
                                        Edit
                                    </button>


                                    <button
                                        type="button"
                                        class="openDeleteSpecialLeave rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-extrabold text-rose-700 transition hover:bg-rose-100"
                                        data-url="{{ route(
                                            'master.special-leave-types.destroy',
                                            $item
                                        ) }}"
                                        data-name="{{ $item->name }}"
                                    >
                                        Hapus
                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="6"
                                class="px-5 py-10 text-center text-sm font-medium text-slate-500"
                            >
                                Belum ada jenis cuti khusus.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- MOBILE --}}
        <div class="space-y-3 lg:hidden">

            @forelse($items as $item)

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
                >

                    <div class="flex items-start justify-between gap-3">

                        <div>

                            <div class="font-extrabold text-slate-900">
                                {{ $item->name }}
                            </div>

                            <div class="mt-1 font-mono text-xs font-bold text-slate-500">
                                {{ $item->code }}
                            </div>

                        </div>


                        @if($item->is_active)

                            <span
                                class="rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700"
                            >
                                Aktif
                            </span>

                        @else

                            <span
                                class="rounded-lg border border-slate-200 bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600"
                            >
                                Nonaktif
                            </span>

                        @endif

                    </div>


                    <div class="mt-3 text-sm text-slate-600">
                        {{ $item->description ?: '-' }}
                    </div>


                    <div
                        class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3"
                    >

                        <span class="text-xs font-extrabold text-sky-700">
                            {{ $item->default_days }} hari
                        </span>


                        <div class="flex gap-2">

                            <button
                                type="button"
                                class="openEditSpecialLeave rounded-xl bg-blue-50 px-3 py-2 text-xs font-extrabold text-blue-700"
                                data-modal="editSpecialLeave-{{ $item->id }}"
                                data-box="editSpecialLeaveBox-{{ $item->id }}"
                            >
                                Edit
                            </button>

                            <button
                                type="button"
                                class="openDeleteSpecialLeave rounded-xl bg-rose-50 px-3 py-2 text-xs font-extrabold text-rose-700"
                                data-url="{{ route(
                                    'master.special-leave-types.destroy',
                                    $item
                                ) }}"
                                data-name="{{ $item->name }}"
                            >
                                Hapus
                            </button>

                        </div>

                    </div>

                </div>

            @empty

                <div
                    class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm font-bold text-slate-500"
                >
                    Belum ada jenis cuti khusus.
                </div>

            @endforelse

        </div>


        @if($items->hasPages())

            <div>
                {{ $items->links() }}
            </div>

        @endif

    </div>


    {{-- ============================================================= --}}
    {{-- CREATE MODAL --}}
    {{-- ============================================================= --}}

    <div
        id="createSpecialLeaveModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 px-4 backdrop-blur-sm"
    >

        <div
            id="createSpecialLeaveModalBox"
            class="w-full max-w-lg scale-95 rounded-2xl bg-white p-5 opacity-0 shadow-2xl transition-all duration-200"
        >

            <div
                class="flex items-center justify-between border-b border-slate-200 pb-4"
            >

                <div>

                    <h3 class="font-extrabold text-slate-900">
                        Tambah Cuti Khusus
                    </h3>

                    <p class="mt-1 text-xs font-medium text-slate-500">
                        Tambahkan jenis cuti di luar cuti tahunan.
                    </p>

                </div>

                <button
                    type="button"
                    class="closeSpecialLeaveModal text-slate-400"
                    data-modal="createSpecialLeaveModal"
                    data-box="createSpecialLeaveModalBox"
                >
                    ✕
                </button>

            </div>


            <form
                method="POST"
                action="{{ route(
                    'master.special-leave-types.store'
                ) }}"
                class="mt-5 space-y-4"
            >

                @csrf


                <div>

                    <label
                        class="mb-1.5 block text-xs font-bold uppercase text-slate-500"
                    >
                        Nama Cuti
                    </label>

                    <input
                        type="text"
                        name="name"
                        required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm"
                    >

                </div>


                <div class="grid grid-cols-2 gap-3">

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold uppercase text-slate-500"
                        >
                            Kode
                        </label>

                        <input
                            type="text"
                            name="code"
                            required
                            placeholder="MENIKAH"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm uppercase"
                        >

                    </div>


                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold uppercase text-slate-500"
                        >
                            Hari Default
                        </label>

                        <input
                            type="number"
                            name="default_days"
                            min="1"
                            value="1"
                            required
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm"
                        >

                    </div>

                </div>


                <div>

                    <label
                        class="mb-1.5 block text-xs font-bold uppercase text-slate-500"
                    >
                        Keterangan
                    </label>

                    <textarea
                        name="description"
                        rows="3"
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm"
                    ></textarea>

                </div>


                <label
                    class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3"
                >

                    <input
                        type="hidden"
                        name="is_active"
                        value="0"
                    >

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        checked
                        class="h-5 w-5 rounded border-slate-300"
                    >

                    <span class="text-sm font-bold text-slate-700">
                        Aktif
                    </span>

                </label>


                <div
                    class="flex justify-end gap-2 border-t border-slate-200 pt-4"
                >

                    <button
                        type="button"
                        class="closeSpecialLeaveModal rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700"
                        data-modal="createSpecialLeaveModal"
                        data-box="createSpecialLeaveModalBox"
                    >
                        Batal
                    </button>


                    <button
                        type="submit"
                        class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-extrabold text-white"
                    >
                        Simpan
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- ============================================================= --}}
    {{-- EDIT MODALS --}}
    {{-- ============================================================= --}}

    @foreach($items as $item)

        <div
            id="editSpecialLeave-{{ $item->id }}"
            class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 px-4 backdrop-blur-sm"
        >

            <div
                id="editSpecialLeaveBox-{{ $item->id }}"
                class="w-full max-w-lg scale-95 rounded-2xl bg-white p-5 opacity-0 shadow-2xl transition-all duration-200"
            >

                <div
                    class="flex items-center justify-between border-b border-slate-200 pb-4"
                >

                    <div>

                        <h3 class="font-extrabold text-slate-900">
                            Edit Cuti Khusus
                        </h3>

                        <p class="mt-1 text-xs font-medium text-slate-500">
                            {{ $item->name }}
                        </p>

                    </div>

                    <button
                        type="button"
                        class="closeSpecialLeaveModal text-slate-400"
                        data-modal="editSpecialLeave-{{ $item->id }}"
                        data-box="editSpecialLeaveBox-{{ $item->id }}"
                    >
                        ✕
                    </button>

                </div>


                <form
                    method="POST"
                    action="{{ route(
                        'master.special-leave-types.update',
                        $item
                    ) }}"
                    class="mt-5 space-y-4"
                >

                    @csrf
                    @method('PUT')


                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold uppercase text-slate-500"
                        >
                            Nama Cuti
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ $item->name }}"
                            required
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm"
                        >

                    </div>


                    <div class="grid grid-cols-2 gap-3">

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase text-slate-500"
                            >
                                Kode
                            </label>

                            <input
                                type="text"
                                name="code"
                                value="{{ $item->code }}"
                                required
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm uppercase"
                            >

                        </div>


                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase text-slate-500"
                            >
                                Hari Default
                            </label>

                            <input
                                type="number"
                                name="default_days"
                                value="{{ $item->default_days }}"
                                min="1"
                                required
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm"
                            >

                        </div>

                    </div>


                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold uppercase text-slate-500"
                        >
                            Keterangan
                        </label>

                        <textarea
                            name="description"
                            rows="3"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm"
                        >{{ $item->description }}</textarea>

                    </div>


                    <label
                        class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3"
                    >

                        <input
                            type="hidden"
                            name="is_active"
                            value="0"
                        >

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked($item->is_active)
                            class="h-5 w-5 rounded border-slate-300"
                        >

                        <span class="text-sm font-bold text-slate-700">
                            Aktif
                        </span>

                    </label>


                    <div
                        class="flex justify-end gap-2 border-t border-slate-200 pt-4"
                    >

                        <button
                            type="button"
                            class="closeSpecialLeaveModal rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700"
                            data-modal="editSpecialLeave-{{ $item->id }}"
                            data-box="editSpecialLeaveBox-{{ $item->id }}"
                        >
                            Batal
                        </button>


                        <button
                            type="submit"
                            class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-extrabold text-white"
                        >
                            Update
                        </button>

                    </div>

                </form>

            </div>

        </div>

    @endforeach


    {{-- ============================================================= --}}
    {{-- DELETE MODAL --}}
    {{-- ============================================================= --}}

    <div
        id="deleteSpecialLeaveModal"
        class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-900/50 px-4 backdrop-blur-sm"
    >

        <div
            id="deleteSpecialLeaveModalBox"
            class="w-full max-w-sm scale-95 rounded-2xl bg-white p-5 opacity-0 shadow-2xl transition-all duration-200"
        >

            <h3 class="font-extrabold text-slate-900">
                Hapus Cuti Khusus?
            </h3>

            <p class="mt-2 text-sm text-slate-600">
                Data
                <strong
                    id="deleteSpecialLeaveName"
                    class="text-slate-900"
                >
                    -
                </strong>
                akan dihapus.
            </p>


            <form
                id="deleteSpecialLeaveForm"
                method="POST"
                class="mt-5"
            >

                @csrf
                @method('DELETE')


                <div class="flex justify-end gap-2">

                    <button
                        type="button"
                        id="cancelDeleteSpecialLeave"
                        class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700"
                    >
                        Batal
                    </button>


                    <button
                        type="submit"
                        class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-extrabold text-white"
                    >
                        Ya, Hapus
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

                    if (!modal || !box) {
                        return;
                    }

                    modal.classList.remove(
                        'hidden'
                    );

                    modal.classList.add(
                        'flex'
                    );

                    document.body.classList.add(
                        'overflow-hidden'
                    );

                    setTimeout(
                        function () {
                            box.classList.remove(
                                'scale-95',
                                'opacity-0'
                            );
                        },
                        10
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

                    if (!modal || !box) {
                        return;
                    }

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
                        'openCreateSpecialLeaveModal'
                    )
                    ?.addEventListener(
                        'click',
                        function () {
                            openModal(
                                'createSpecialLeaveModal',
                                'createSpecialLeaveModalBox'
                            );
                        }
                    );


                document.addEventListener(
                    'click',
                    function (event) {

                        const editButton =
                            event.target.closest(
                                '.openEditSpecialLeave'
                            );

                        if (editButton) {
                            openModal(
                                editButton.dataset.modal,
                                editButton.dataset.box
                            );

                            return;
                        }


                        const closeButton =
                            event.target.closest(
                                '.closeSpecialLeaveModal'
                            );

                        if (closeButton) {
                            closeModal(
                                closeButton.dataset.modal,
                                closeButton.dataset.box
                            );

                            return;
                        }


                        const deleteButton =
                            event.target.closest(
                                '.openDeleteSpecialLeave'
                            );

                        if (deleteButton) {

                            const form =
                                document.getElementById(
                                    'deleteSpecialLeaveForm'
                                );

                            const name =
                                document.getElementById(
                                    'deleteSpecialLeaveName'
                                );

                            if (form) {
                                form.action =
                                    deleteButton.dataset.url;
                            }

                            if (name) {
                                name.textContent =
                                    deleteButton.dataset.name;
                            }

                            openModal(
                                'deleteSpecialLeaveModal',
                                'deleteSpecialLeaveModalBox'
                            );

                            return;
                        }

                    }
                );


                document
                    .getElementById(
                        'cancelDeleteSpecialLeave'
                    )
                    ?.addEventListener(
                        'click',
                        function () {
                            closeModal(
                                'deleteSpecialLeaveModal',
                                'deleteSpecialLeaveModalBox'
                            );
                        }
                    );


                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key !== 'Escape'
                        ) {
                            return;
                        }

                        const opened =
                            document.querySelector(
                                '.fixed.inset-0.flex'
                            );

                        if (!opened) {
                            return;
                        }

                        const box =
                            opened.querySelector(
                                '[id$="Box"]'
                            );

                        if (!box) {
                            return;
                        }

                        closeModal(
                            opened.id,
                            box.id
                        );
                    }
                );

            }
        );
    </script>

</x-app-layout>
