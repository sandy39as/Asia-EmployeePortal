<x-app-layout>
    <x-slot name="headerTitle">
        <div>
            <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                Master Jenis Izin
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium">
                Kelola jenis izin yang dapat dipilih karyawan saat membuat pengajuan.
            </p>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4">

        {{-- ========================================================= --}}
        {{-- FLASH MESSAGE --}}
        {{-- ========================================================= --}}
        @if (session('success'))
            <div
                id="successFlash"
                class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800 shadow-sm"
            >
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div
                id="validationFlash"
                class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 shadow-sm"
            >
                <div class="font-extrabold">
                    Data belum dapat disimpan.
                </div>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-xs font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- HEADER ACTION --}}
        {{-- ========================================================= --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">

            {{-- SUMMARY --}}
            <div class="flex flex-wrap items-center gap-2">

                <span class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs sm:text-sm font-semibold text-slate-700 shadow-sm">
                    Total:
                    <strong class="font-extrabold text-slate-900">
                        {{ number_format($summary['total'] ?? 0) }}
                    </strong>
                </span>

                <a
                    href="{{ route('master.permission-types.index', ['status' => 'active']) }}"
                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs sm:text-sm font-bold text-emerald-800 transition hover:bg-emerald-100 {{ ($status ?? '') === 'active' ? 'ring-2 ring-emerald-500' : '' }}"
                >
                    Aktif:
                    {{ number_format($summary['active'] ?? 0) }}
                </a>

                <a
                    href="{{ route('master.permission-types.index', ['status' => 'inactive']) }}"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs sm:text-sm font-bold text-slate-600 transition hover:bg-slate-50 {{ ($status ?? '') === 'inactive' ? 'ring-2 ring-slate-400' : '' }}"
                >
                    Nonaktif:
                    {{ number_format($summary['inactive'] ?? 0) }}
                </a>

            </div>


            {{-- BUTTON ADD --}}
            <button
                type="button"
                id="openCreatePermissionModal"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-extrabold text-white shadow-sm transition hover:bg-black"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4v16m8-8H4"
                    />
                </svg>

                Tambah Jenis Izin
            </button>

        </div>


        {{-- ========================================================= --}}
        {{-- FILTER --}}
        {{-- ========================================================= --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

            <form
                method="GET"
                action="{{ route('master.permission-types.index') }}"
                class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-12"
            >

                <div class="lg:col-span-7">
                    <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                        Pencarian
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        placeholder="Cari nama, kode, atau keterangan..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 shadow-sm focus:border-slate-500 focus:outline-none"
                    >
                </div>


                <div class="lg:col-span-3">
                    <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                        Status
                    </label>

                    <select
                        name="status"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-700 shadow-sm focus:border-slate-500 focus:outline-none"
                    >
                        <option value="">
                            Semua Status
                        </option>

                        <option
                            value="active"
                            @selected(($status ?? '') === 'active')
                        >
                            Aktif
                        </option>

                        <option
                            value="inactive"
                            @selected(($status ?? '') === 'inactive')
                        >
                            Nonaktif
                        </option>
                    </select>
                </div>


                <div class="flex items-end gap-2 lg:col-span-2">
                    <button
                        type="submit"
                        class="flex-1 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-extrabold text-white transition hover:bg-black"
                    >
                        Cari
                    </button>

                    <a
                        href="{{ route('master.permission-types.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        Reset
                    </a>
                </div>

            </form>

        </div>


        {{-- ========================================================= --}}
        {{-- MOBILE --}}
        {{-- ========================================================= --}}
        <div class="space-y-3 lg:hidden">

            @forelse ($items as $item)

                @php
                    $editModalId = 'editPermissionModal-' . $item->id;
                    $editModalBoxId = 'editPermissionModalBox-' . $item->id;

                    $deleteModalId = 'deletePermissionModal-' . $item->id;
                    $deleteModalBoxId = 'deletePermissionModalBox-' . $item->id;
                @endphp

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                    <div class="flex items-start justify-between gap-3">

                        <div class="min-w-0">
                            <h3 class="break-words text-base font-extrabold text-slate-900">
                                {{ $item->name }}
                            </h3>

                            <div class="mt-1">
                                <span class="inline-flex rounded-lg border border-slate-200 bg-slate-50 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-600">
                                    {{ $item->code }}
                                </span>
                            </div>
                        </div>


                        @if ($item->is_active)

                            <span class="shrink-0 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-700">
                                Aktif
                            </span>

                        @else

                            <span class="shrink-0 rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1 text-xs font-extrabold text-slate-500">
                                Nonaktif
                            </span>

                        @endif

                    </div>


                    <div class="mt-3 rounded-xl bg-slate-50 p-3 text-xs leading-5 text-slate-600">
                        {{ $item->description ?: 'Tidak ada keterangan.' }}
                    </div>


                    <div class="mt-4 grid grid-cols-2 gap-2">

                        <button
                            type="button"
                            data-modal="{{ $editModalId }}"
                            data-box="{{ $editModalBoxId }}"
                            class="openPermissionModal rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-extrabold text-slate-700 transition hover:bg-slate-50"
                        >
                            Edit
                        </button>

                        <button
                            type="button"
                            data-modal="{{ $deleteModalId }}"
                            data-box="{{ $deleteModalBoxId }}"
                            class="openPermissionModal rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-extrabold text-rose-700 transition hover:bg-rose-100"
                        >
                            Hapus
                        </button>

                    </div>

                </div>

            @empty

                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
                    <p class="text-sm font-bold text-slate-500">
                        Belum ada jenis izin.
                    </p>
                </div>

            @endforelse

        </div>


        {{-- ========================================================= --}}
        {{-- DESKTOP --}}
        {{-- ========================================================= --}}
        <div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:block">

            <table class="min-w-full divide-y divide-slate-200 text-sm">

                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Nama Izin
                        </th>

                        <th class="px-5 py-3.5 text-left text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Kode
                        </th>

                        <th class="px-5 py-3.5 text-left text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Keterangan
                        </th>

                        <th class="px-5 py-3.5 text-left text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Status
                        </th>

                        <th class="px-5 py-3.5 text-right text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Aksi
                        </th>
                    </tr>
                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse ($items as $item)

                        @php
                            $editModalId = 'editPermissionModal-' . $item->id;
                            $editModalBoxId = 'editPermissionModalBox-' . $item->id;

                            $deleteModalId = 'deletePermissionModal-' . $item->id;
                            $deleteModalBoxId = 'deletePermissionModalBox-' . $item->id;
                        @endphp

                        <tr class="transition hover:bg-slate-50">

                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900">
                                    {{ $item->name }}
                                </div>
                            </td>


                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 font-mono text-xs font-bold text-slate-600">
                                    {{ $item->code }}
                                </span>
                            </td>


                            <td class="max-w-md px-5 py-4 text-slate-600">
                                <div class="line-clamp-2">
                                    {{ $item->description ?: '-' }}
                                </div>
                            </td>


                            <td class="px-5 py-4">

                                @if ($item->is_active)

                                    <span class="inline-flex rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-700">
                                        Aktif
                                    </span>

                                @else

                                    <span class="inline-flex rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1 text-xs font-extrabold text-slate-500">
                                        Nonaktif
                                    </span>

                                @endif

                            </td>


                            <td class="px-5 py-4 text-right">

                                <div class="inline-flex gap-2">

                                    <button
                                        type="button"
                                        data-modal="{{ $editModalId }}"
                                        data-box="{{ $editModalBoxId }}"
                                        class="openPermissionModal rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-extrabold text-slate-700 transition hover:bg-slate-50"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        data-modal="{{ $deleteModalId }}"
                                        data-box="{{ $deleteModalBoxId }}"
                                        class="openPermissionModal rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-extrabold text-rose-700 transition hover:bg-rose-100"
                                    >
                                        Hapus
                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="5"
                                class="px-5 py-10 text-center text-sm font-medium text-slate-500"
                            >
                                Belum ada jenis izin.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ========================================================= --}}
        {{-- PAGINATION --}}
        {{-- ========================================================= --}}
        @if ($items->hasPages())
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                {{ $items->withQueryString()->links() }}
            </div>
        @endif

    </div>


    {{-- ========================================================= --}}
    {{-- CREATE MODAL --}}
    {{-- ========================================================= --}}
    <div
        id="createPermissionModal"
        class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-900/55 px-3 py-5 backdrop-blur-sm"
    >
        <div
            id="createPermissionModalBox"
            class="max-h-[92vh] w-full max-w-lg scale-95 overflow-y-auto rounded-2xl border border-slate-200 bg-white opacity-0 shadow-2xl transition-all duration-200"
        >

            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white/95 px-5 py-4 backdrop-blur">

                <div>
                    <h3 class="text-base font-extrabold text-slate-900">
                        Tambah Jenis Izin
                    </h3>

                    <p class="mt-0.5 text-xs font-medium text-slate-500">
                        Jenis ini akan tampil pada form pengajuan karyawan.
                    </p>
                </div>


                <button
                    type="button"
                    data-modal="createPermissionModal"
                    data-box="createPermissionModalBox"
                    class="closePermissionModal rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                >
                    ✕
                </button>

            </div>


            <form
                method="POST"
                action="{{ route('master.permission-types.store') }}"
            >
                @csrf

                <div class="space-y-4 p-5">

                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Nama Izin
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Izin Terlambat"
                            required
                            maxlength="150"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-800 placeholder-slate-400 focus:border-slate-500 focus:outline-none"
                        >
                    </div>


                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Kode
                        </label>

                        <input
                            type="text"
                            name="code"
                            value="{{ old('code') }}"
                            placeholder="Contoh: TERLAMBAT"
                            required
                            maxlength="50"
                            class="permissionCodeInput w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 font-mono text-sm font-bold uppercase text-slate-800 placeholder-slate-400 focus:border-slate-500 focus:outline-none"
                        >

                        <p class="mt-1.5 text-[11px] font-medium text-slate-400">
                            Gunakan kode singkat tanpa spasi, contoh: TERLAMBAT, PULANG_AWAL, KELUAR_PABRIK.
                        </p>
                    </div>


                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Keterangan
                        </label>

                        <textarea
                            name="description"
                            rows="4"
                            maxlength="2000"
                            placeholder="Keterangan atau aturan jenis izin..."
                            class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-slate-500 focus:outline-none"
                        >{{ old('description') }}</textarea>
                    </div>


                    <label class="flex cursor-pointer items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-3.5">

                        <div>
                            <div class="text-sm font-extrabold text-slate-800">
                                Aktif
                            </div>

                            <div class="mt-0.5 text-xs font-medium text-slate-500">
                                Jika aktif, jenis izin dapat dipilih karyawan.
                            </div>
                        </div>


                        <input
                            type="hidden"
                            name="is_active"
                            value="0"
                        >

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked(old('is_active', true))
                            class="h-5 w-5 rounded border-slate-300 text-slate-900 focus:ring-slate-500"
                        >

                    </label>

                </div>


                <div class="sticky bottom-0 flex justify-end gap-2 border-t border-slate-200 bg-white/95 px-5 py-4 backdrop-blur">

                    <button
                        type="button"
                        data-modal="createPermissionModal"
                        data-box="createPermissionModalBox"
                        class="closePermissionModal rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-extrabold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-extrabold text-white transition hover:bg-black"
                    >
                        Simpan
                    </button>

                </div>

            </form>

        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- EDIT + DELETE MODALS --}}
    {{-- ========================================================= --}}
    @foreach ($items as $item)

        @php
            $editModalId = 'editPermissionModal-' . $item->id;
            $editModalBoxId = 'editPermissionModalBox-' . $item->id;

            $deleteModalId = 'deletePermissionModal-' . $item->id;
            $deleteModalBoxId = 'deletePermissionModalBox-' . $item->id;
        @endphp


        {{-- EDIT --}}
        <div
            id="{{ $editModalId }}"
            class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-900/55 px-3 py-5 backdrop-blur-sm"
        >
            <div
                id="{{ $editModalBoxId }}"
                class="max-h-[92vh] w-full max-w-lg scale-95 overflow-y-auto rounded-2xl border border-slate-200 bg-white opacity-0 shadow-2xl transition-all duration-200"
            >

                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white/95 px-5 py-4 backdrop-blur">

                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">
                            Edit Jenis Izin
                        </h3>

                        <p class="mt-0.5 text-xs font-medium text-slate-500">
                            {{ $item->name }}
                        </p>
                    </div>


                    <button
                        type="button"
                        data-modal="{{ $editModalId }}"
                        data-box="{{ $editModalBoxId }}"
                        class="closePermissionModal rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    >
                        ✕
                    </button>

                </div>


                <form
                    method="POST"
                    action="{{ route('master.permission-types.update', $item) }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="space-y-4 p-5">

                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Nama Izin
                            </label>

                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $item->name) }}"
                                required
                                maxlength="150"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:border-slate-500 focus:outline-none"
                            >
                        </div>


                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Kode
                            </label>

                            <input
                                type="text"
                                name="code"
                                value="{{ old('code', $item->code) }}"
                                required
                                maxlength="50"
                                class="permissionCodeInput w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 font-mono text-sm font-bold uppercase text-slate-800 focus:border-slate-500 focus:outline-none"
                            >
                        </div>


                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Keterangan
                            </label>

                            <textarea
                                name="description"
                                rows="4"
                                maxlength="2000"
                                class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:border-slate-500 focus:outline-none"
                            >{{ old('description', $item->description) }}</textarea>
                        </div>


                        <label class="flex cursor-pointer items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-3.5">

                            <div>
                                <div class="text-sm font-extrabold text-slate-800">
                                    Aktif
                                </div>

                                <div class="mt-0.5 text-xs font-medium text-slate-500">
                                    Jika nonaktif, jenis izin tidak dapat dipilih pada pengajuan baru.
                                </div>
                            </div>


                            <input
                                type="hidden"
                                name="is_active"
                                value="0"
                            >

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                @checked(old('is_active', $item->is_active))
                                class="h-5 w-5 rounded border-slate-300 text-slate-900 focus:ring-slate-500"
                            >

                        </label>

                    </div>


                    <div class="sticky bottom-0 flex justify-end gap-2 border-t border-slate-200 bg-white/95 px-5 py-4 backdrop-blur">

                        <button
                            type="button"
                            data-modal="{{ $editModalId }}"
                            data-box="{{ $editModalBoxId }}"
                            class="closePermissionModal rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-extrabold text-slate-700 transition hover:bg-slate-50"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-extrabold text-white transition hover:bg-black"
                        >
                            Simpan Perubahan
                        </button>

                    </div>

                </form>

            </div>
        </div>


        {{-- DELETE --}}
        <div
            id="{{ $deleteModalId }}"
            class="fixed inset-0 z-[120] hidden items-center justify-center bg-slate-900/55 px-3 backdrop-blur-sm"
        >
            <div
                id="{{ $deleteModalBoxId }}"
                class="w-full max-w-sm scale-95 rounded-2xl border border-slate-200 bg-white p-5 opacity-0 shadow-2xl transition-all duration-200"
            >

                <div class="flex items-start gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </div>


                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">
                            Hapus Jenis Izin?
                        </h3>

                        <p class="mt-1.5 text-xs leading-5 text-slate-500">
                            Data
                            <strong class="text-slate-800">
                                {{ $item->name }}
                            </strong>
                            akan dihapus.
                        </p>
                    </div>

                </div>


                <form
                    method="POST"
                    action="{{ route('master.permission-types.destroy', $item) }}"
                    class="mt-5 flex justify-end gap-2"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="button"
                        data-modal="{{ $deleteModalId }}"
                        data-box="{{ $deleteModalBoxId }}"
                        class="closePermissionModal rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-extrabold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-extrabold text-white transition hover:bg-rose-700"
                    >
                        Ya, Hapus
                    </button>

                </form>

            </div>
        </div>

    @endforeach


    {{-- ========================================================= --}}
    {{-- JAVASCRIPT --}}
    {{-- ========================================================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /*
            |--------------------------------------------------------------------------
            | FLASH
            |--------------------------------------------------------------------------
            */

            ['successFlash', 'validationFlash'].forEach(function (id) {

                const element =
                    document.getElementById(id);

                if (!element) return;


                setTimeout(function () {

                    element.style.transition =
                        'opacity .4s ease, transform .4s ease';

                    element.style.opacity =
                        '0';

                    element.style.transform =
                        'translateY(-6px)';


                    setTimeout(function () {
                        element.remove();
                    }, 400);

                }, 3500);

            });


            /*
            |--------------------------------------------------------------------------
            | MODAL
            |--------------------------------------------------------------------------
            */

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


                if (!modal || !box) return;


                modal.classList.remove(
                    'hidden'
                );

                modal.classList.add(
                    'flex'
                );

                document.body.classList.add(
                    'overflow-hidden'
                );


                setTimeout(function () {

                    box.classList.remove(
                        'scale-95',
                        'opacity-0'
                    );

                    box.classList.add(
                        'scale-100',
                        'opacity-100'
                    );

                }, 10);
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


                if (!modal || !box) return;


                box.classList.remove(
                    'scale-100',
                    'opacity-100'
                );

                box.classList.add(
                    'scale-95',
                    'opacity-0'
                );


                setTimeout(function () {

                    modal.classList.remove(
                        'flex'
                    );

                    modal.classList.add(
                        'hidden'
                    );


                    if (
                        !document.querySelector(
                            '.fixed.inset-0.flex'
                        )
                    ) {
                        document.body.classList.remove(
                            'overflow-hidden'
                        );
                    }

                }, 180);
            }


            /*
            |--------------------------------------------------------------------------
            | CREATE
            |--------------------------------------------------------------------------
            */

            const createButton =
                document.getElementById(
                    'openCreatePermissionModal'
                );


            createButton?.addEventListener(
                'click',
                function () {

                    openModal(
                        'createPermissionModal',
                        'createPermissionModalBox'
                    );
                }
            );


            /*
            |--------------------------------------------------------------------------
            | OPEN EDIT / DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function (event) {

                    const openButton =
                        event.target.closest(
                            '.openPermissionModal'
                        );


                    if (openButton) {

                        openModal(
                            openButton.dataset.modal,
                            openButton.dataset.box
                        );

                        return;
                    }


                    const closeButton =
                        event.target.closest(
                            '.closePermissionModal'
                        );


                    if (closeButton) {

                        closeModal(
                            closeButton.dataset.modal,
                            closeButton.dataset.box
                        );

                        return;
                    }


                    const overlay =
                        event.target.closest(
                            '.fixed.inset-0'
                        );


                    if (
                        overlay
                        &&
                        event.target === overlay
                    ) {

                        const box =
                            overlay.querySelector(
                                '[id$="Box"]'
                            );


                        if (box) {

                            closeModal(
                                overlay.id,
                                box.id
                            );
                        }
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function (event) {

                    if (
                        event.key
                        !== 'Escape'
                    ) {
                        return;
                    }


                    const opened =
                        document.querySelector(
                            '.fixed.inset-0.flex'
                        );


                    if (!opened) return;


                    const box =
                        opened.querySelector(
                            '[id$="Box"]'
                        );


                    if (box) {

                        closeModal(
                            opened.id,
                            box.id
                        );
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | FORMAT KODE
            |--------------------------------------------------------------------------
            */

            document.querySelectorAll(
                '.permissionCodeInput'
            ).forEach(function (input) {

                input.addEventListener(
                    'input',
                    function () {

                        this.value =
                            this.value
                                .toUpperCase()
                                .replace(
                                    /\s+/g,
                                    '_'
                                )
                                .replace(
                                    /[^A-Z0-9_]/g,
                                    ''
                                );
                    }
                );

            });


            /*
            |--------------------------------------------------------------------------
            | AUTO OPEN CREATE IF VALIDATION FAILED
            |--------------------------------------------------------------------------
            */

            @if ($errors->any())

                openModal(
                    'createPermissionModal',
                    'createPermissionModalBox'
                );

            @endif

        });
    </script>

</x-app-layout>
