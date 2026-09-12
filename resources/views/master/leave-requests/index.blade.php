<x-app-layout>

    <div class="max-w-6xl mx-auto">

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-slate-400">
                Kabag
            </p>

            <h1 class="mt-1 text-2xl font-extrabold text-slate-900">
                Persetujuan Pengajuan
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Selamat datang, {{ $kabag->name }}.
            </p>

            <div class="mt-5 rounded-2xl bg-blue-50 border border-blue-200 p-4">
                <div class="text-sm font-bold text-blue-900">
                    {{ $employeeIds->count() }} karyawan berada di bawah Kabag ini.
                </div>
            </div>

        </div>

    </div>

</x-app-layout>
