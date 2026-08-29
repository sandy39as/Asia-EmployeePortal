@php
    $user = auth()->user();
    $userRole = strtolower(trim((string) ($user?->role ?? '')));

    $isHrd = in_array(
        $userRole,
        [
            'hrd',
            'admin',
            'superadmin',
        ],
        true
    );

    $navClass = function ($routes) {
        $routes = (array) $routes;

        foreach ($routes as $route) {
            if (request()->routeIs($route)) {
                return 'bg-white text-slate-900 shadow-xs border border-[#e2e8f0] font-bold';
            }
        }

        return 'text-slate-600 hover:bg-slate-200/60 hover:text-slate-900 font-semibold';
    };
@endphp

{{-- ========================================================= --}}
{{-- MOBILE BACKDROP --}}
{{-- ========================================================= --}}
<div id="portalSidebarBackdrop"
     class="fixed inset-0 z-40 hidden bg-slate-900/40 opacity-0 backdrop-blur-sm transition-opacity duration-300 lg:hidden">
</div>

{{-- ========================================================= --}}
{{-- MOBILE OPEN BUTTON --}}
{{-- ========================================================= --}}
<button type="button"
        id="portalSidebarOpen"
        class="fixed left-4 top-3.5 z-40 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white border border-[#d1d5db] text-slate-700 shadow-xs transition hover:bg-slate-100 hover:text-slate-900 lg:hidden"
        title="Buka Menu">
    <svg xmlns="http://www.w3.org/2000/svg"
         class="h-5 w-5"
         fill="none"
         viewBox="0 0 24 24"
         stroke="currentColor"
         stroke-width="2">
        <path stroke-linecap="round"
              stroke-linejoin="round"
              d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

{{-- ========================================================= --}}
{{-- SIDEBAR COMPONENT --}}
{{-- ========================================================= --}}
<aside id="portalSidebar"
       class="fixed inset-y-0 left-0 z-50 w-64 -translate-x-full overflow-y-auto bg-[#f8fafc] border-r border-[#e2e8f0] shadow-xl transition-transform duration-300 ease-out lg:translate-x-0 lg:shadow-none">

    <div class="flex min-h-full flex-col p-5">

        {{-- ================================================= --}}
        {{-- BRAND --}}
        {{-- ================================================= --}}
        <div class="flex items-center justify-between border-b border-[#e2e8f0] pb-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-white shadow-xs">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.8">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <div class="truncate text-sm font-bold text-slate-900">
                        Employee Portal
                    </div>
                    <div class="truncate text-[11px] font-semibold text-slate-400">
                        ASIA PLASTIK
                    </div>
                </div>
            </div>

            <button type="button"
                    id="portalSidebarClose"
                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-[#e2e8f0] bg-white text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 lg:hidden">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- ================================================= --}}
        {{-- NAVIGATION --}}
        {{-- ================================================= --}}
        <nav class="mt-5 flex-1 space-y-1">
            <div class="mb-2 px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                Menu Utama
            </div>

            @if ($isHrd)
                {{-- HRD MENUS --}}
                <a href="{{ route('hrd.leave-requests.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs transition {{ $navClass('hrd.leave-requests.*') }}">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-4 w-4 shrink-0 text-slate-500"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.8">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M8.25 6.75V4.5m7.5 2.25V4.5M4.5 9.75h15m-13.5-3h12A1.5 1.5 0 0119.5 8.25v10.5a1.5 1.5 0 01-1.5 1.5H6a1.5 1.5 0 01-1.5-1.5V8.25A1.5 1.5 0 016 6.75z" />
                    </svg>
                    <span>Pengajuan Karyawan</span>
                </a>

                <a href="{{ route('hrd.employees.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs transition {{ $navClass('hrd.employees.*') }}">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-4 w-4 shrink-0 text-slate-500"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.8">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />
                    </svg>
                    <span>Data Karyawan</span>
                </a>
            @else
                {{-- EMPLOYEE MENUS --}}
                <a href="{{ route('leave-requests.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs transition {{ $navClass('leave-requests.*') }}">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-4 w-4 shrink-0 text-slate-500"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.8">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 12h6m-6 4h6m2.25 4.5H6.75A2.25 2.25 0 014.5 18.25V5.75A2.25 2.25 0 016.75 3.5h7.5L19.5 8.75v9.5a2.25 2.25 0 01-2.25 2.25z" />
                    </svg>
                    <span>Pengajuan Saya</span>
                </a>
            @endif
        </nav>

        {{-- ================================================= --}}
        {{-- FOOTER --}}
        {{-- ================================================= --}}
        <div class="mt-auto pt-4 border-t border-[#e2e8f0] text-[11px] text-slate-400">
            <div class="font-bold text-slate-700">
                © {{ date('Y') }} ASIA PLASTIK
            </div>
            <div class="text-[10px] text-slate-400 font-medium">
                Employee Management System
            </div>
        </div>

    </div>
</aside>

{{-- ========================================================= --}}
{{-- SIDEBAR SCRIPT --}}
{{-- ========================================================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('portalSidebar');
        const backdrop = document.getElementById('portalSidebarBackdrop');
        const openButton = document.getElementById('portalSidebarOpen');
        const closeButton = document.getElementById('portalSidebarClose');

        function openSidebar() {
            if (!sidebar) return;
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');

            if (backdrop) {
                backdrop.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                }, 10);
            }

            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            if (!sidebar) return;
            if (window.innerWidth >= 1024) return;

            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');

            if (backdrop) {
                backdrop.classList.add('opacity-0');
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
            }

            document.body.classList.remove('overflow-hidden');
        }

        openButton?.addEventListener('click', openSidebar);
        closeButton?.addEventListener('click', closeSidebar);
        backdrop?.addEventListener('click', closeSidebar);

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) {
                sidebar?.classList.remove('-translate-x-full');
                sidebar?.classList.add('translate-x-0');
                backdrop?.classList.add('hidden', 'opacity-0');
                document.body.classList.remove('overflow-hidden');
            } else {
                sidebar?.classList.remove('translate-x-0');
                sidebar?.classList.add('-translate-x-full');
            }
        });
    });
</script>
