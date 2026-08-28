@php

    $user = auth()->user();

    $userRole = strtolower(
        trim(
            (string) ($user?->role ?? '')
        )
    );

    $isHrd = in_array(
        $userRole,
        [
            'hrd',
            'admin',
            'superadmin',
        ],
        true
    );

    $dashboardRoute = $isHrd
        ? 'hrd.dashboard'
        : 'dashboard';

    $navClass = function ($routes) {

        $routes = (array) $routes;

        foreach ($routes as $route) {

            if (request()->routeIs($route)) {

                return
                    'bg-white/15 text-white shadow-sm ring-1 ring-white/20';

            }

        }

        return
            'text-blue-100/80 hover:bg-white/10 hover:text-white';

    };

@endphp


{{-- ========================================================= --}}
{{-- MOBILE BACKDROP --}}
{{-- ========================================================= --}}

<div id="portalSidebarBackdrop"
     class="fixed inset-0 z-40 hidden bg-slate-950/60 opacity-0 backdrop-blur-sm transition-opacity duration-300 lg:hidden">
</div>


{{-- ========================================================= --}}
{{-- MOBILE OPEN BUTTON --}}
{{-- ========================================================= --}}

<button type="button"
        id="portalSidebarOpen"
        class="fixed left-4 top-4 z-[60] inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 lg:hidden"
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
{{-- SIDEBAR --}}
{{-- ========================================================= --}}

<aside id="portalSidebar"
       class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full overflow-y-auto bg-gradient-to-b from-[#1D4ED8] via-[#1E40AF] to-[#172554] shadow-2xl shadow-blue-950/20 transition-transform duration-300 ease-out lg:translate-x-0">

    <div class="flex min-h-full flex-col px-5 py-6">


        {{-- ================================================= --}}
        {{-- BRAND --}}
        {{-- ================================================= --}}

        <div class="flex items-center gap-3 border-b border-white/10 pb-5">

            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/10 shadow-lg ring-1 ring-white/10 backdrop-blur">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-7 w-7 text-white"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.7">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />

                </svg>

            </div>


            <div class="min-w-0 flex-1">

                <div class="truncate text-lg font-black tracking-tight text-white">
                    Employee Portal
                </div>

                <div class="truncate text-xs text-blue-100/75">
                    CV ASIA
                </div>

            </div>


            <button type="button"
                    id="portalSidebarClose"
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/10 text-blue-100 transition hover:bg-white/20 hover:text-white lg:hidden">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5"
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
        {{-- USER --}}
        {{-- ================================================= --}}

        <div class="mt-5 rounded-2xl bg-white/10 p-4 ring-1 ring-white/10 backdrop-blur">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-black text-blue-700">

                    {{ strtoupper(
                        substr(
                            (string) ($user?->name ?? 'U'),
                            0,
                            1
                        )
                    ) }}

                </div>


                <div class="min-w-0">

                    <div class="truncate text-sm font-bold text-white">
                        {{ $user?->name ?? '-' }}
                    </div>

                    <div class="mt-0.5 truncate text-[11px] font-medium text-blue-100/70">

                        {{ $isHrd
                            ? strtoupper($userRole)
                            : ($user?->username ?? 'Karyawan') }}

                    </div>

                </div>

            </div>

        </div>


        {{-- ================================================= --}}
        {{-- NAVIGATION --}}
        {{-- ================================================= --}}

        <nav class="mt-7 flex-1 space-y-7">


            {{-- MAIN MENU --}}

            <div>

                <div class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100/50">
                    Main Menu
                </div>


                <div class="space-y-2">


                    {{-- DASHBOARD --}}

                    <a href="{{ route($dashboardRoute) }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition {{ $navClass([$dashboardRoute]) }}">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5 shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="1.8">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M3.75 13.5l8.25-8.25 8.25 8.25M5.25 12v7.5h4.5v-4.5h4.5v4.5h4.5V12" />

                        </svg>

                        <span>
                            Dashboard
                        </span>

                    </a>


                    {{-- HRD MENU --}}

                    @if ($isHrd)

                        <a href="{{ route('hrd.leave-requests.index') }}"
                           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition {{ $navClass('hrd.leave-requests.*') }}">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="1.8">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M8.25 6.75V4.5m7.5 2.25V4.5M4.5 9.75h15m-13.5-3h12A1.5 1.5 0 0119.5 8.25v10.5a1.5 1.5 0 01-1.5 1.5H6a1.5 1.5 0 01-1.5-1.5V8.25A1.5 1.5 0 016 6.75z" />

                            </svg>

                            <span>
                                Pengajuan
                            </span>

                        </a>


                        <a href="{{ route('hrd.employees.index') }}"
                           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition {{ $navClass('hrd.employees.*') }}">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="1.8">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />

                            </svg>

                            <span>
                                Data Karyawan
                            </span>

                        </a>


                    {{-- EMPLOYEE MENU --}}

                    @else

                        <a href="{{ route('leave-requests.index') }}"
                           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition {{ $navClass('leave-requests.*') }}">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="1.8">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M9 12h6m-6 4h6m2.25 4.5H6.75A2.25 2.25 0 014.5 18.25V5.75A2.25 2.25 0 016.75 3.5h7.5L19.5 8.75v9.5a2.25 2.25 0 01-2.25 2.25z" />

                            </svg>

                            <span>
                                Pengajuan Saya
                            </span>

                        </a>

                    @endif

                </div>

            </div>


            {{-- ACCOUNT --}}

            <div>

                <div class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100/50">
                    Akun
                </div>


                <div class="space-y-2">


                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition {{ $navClass('profile.*') }}">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5 shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="1.8">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />

                        </svg>

                        <span>
                            Profile
                        </span>

                    </a>


                    <form method="POST"
                          action="{{ route('logout') }}">

                        @csrf

                        <button type="submit"
                                class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-blue-100/80 transition hover:bg-red-500/20 hover:text-red-100">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="1.8">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M18 15l3-3m0 0l-3-3m3 3H9" />

                            </svg>

                            <span>
                                Logout
                            </span>

                        </button>

                    </form>

                </div>

            </div>

        </nav>


        {{-- ================================================= --}}
        {{-- FOOTER --}}
        {{-- ================================================= --}}

        <div class="mt-6 rounded-2xl border border-white/10 bg-white/10 p-4 text-xs text-blue-100/70 backdrop-blur">

            <div class="font-bold text-white">
                © {{ date('Y') }} CV ASIA
            </div>

            <div class="mt-1">
                Employee Portal
            </div>

        </div>

    </div>

</aside>


{{-- ========================================================= --}}
{{-- SIDEBAR SCRIPT --}}
{{-- ========================================================= --}}

<script>

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            const sidebar =
                document.getElementById(
                    'portalSidebar'
                );

            const backdrop =
                document.getElementById(
                    'portalSidebarBackdrop'
                );

            const openButton =
                document.getElementById(
                    'portalSidebarOpen'
                );

            const closeButton =
                document.getElementById(
                    'portalSidebarClose'
                );


            function openSidebar() {

                if (!sidebar) {
                    return;
                }

                sidebar.classList.remove(
                    '-translate-x-full'
                );

                sidebar.classList.add(
                    'translate-x-0'
                );

                if (backdrop) {

                    backdrop.classList.remove(
                        'hidden'
                    );

                    setTimeout(
                        () => {
                            backdrop.classList.remove(
                                'opacity-0'
                            );
                        },
                        10
                    );

                }

                document.body.classList.add(
                    'overflow-hidden'
                );

            }


            function closeSidebar() {

                if (!sidebar) {
                    return;
                }

                if (
                    window.innerWidth >= 1024
                ) {
                    return;
                }

                sidebar.classList.remove(
                    'translate-x-0'
                );

                sidebar.classList.add(
                    '-translate-x-full'
                );


                if (backdrop) {

                    backdrop.classList.add(
                        'opacity-0'
                    );

                    setTimeout(
                        () => {
                            backdrop.classList.add(
                                'hidden'
                            );
                        },
                        300
                    );

                }

                document.body.classList.remove(
                    'overflow-hidden'
                );

            }


            openButton?.addEventListener(
                'click',
                openSidebar
            );


            closeButton?.addEventListener(
                'click',
                closeSidebar
            );


            backdrop?.addEventListener(
                'click',
                closeSidebar
            );


            window.addEventListener(
                'resize',
                function () {

                    if (
                        window.innerWidth >= 1024
                    ) {

                        sidebar
                            ?.classList
                            .remove(
                                '-translate-x-full'
                            );

                        sidebar
                            ?.classList
                            .add(
                                'translate-x-0'
                            );

                        backdrop
                            ?.classList
                            .add(
                                'hidden'
                            );

                        document.body
                            .classList
                            .remove(
                                'overflow-hidden'
                            );

                    } else {

                        sidebar
                            ?.classList
                            .remove(
                                'translate-x-0'
                            );

                        sidebar
                            ?.classList
                            .add(
                                '-translate-x-full'
                            );

                    }

                }
            );

        }
    );

</script>
