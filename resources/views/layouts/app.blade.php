<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Asia Employee Portal') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full bg-[#f1f3f5] text-slate-900 antialiased selection:bg-slate-300 selection:text-slate-900 text-sm">
    <div class="min-h-full flex flex-col md:flex-row">
        
        {{-- TOP NAVBAR (MOBILE & DESKTOP TERPADU) --}}
        <header class="md:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-[#e2e8f0] sticky top-0 z-40 shadow-xs">
            <div class="flex items-center gap-2.5">
                <div class="h-9 w-9 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center p-1.5 shadow-xs">
                    <img src="{{ asset('images/logo.png') }}" 
                         alt="Logo" 
                         class="h-full w-full object-contain"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                    <span style="display:none" class="text-[11px] font-black text-white">AEP</span>
                </div>
                <span class="font-extrabold text-base text-slate-900 tracking-tight">Asia Employee Portal</span>
            </div>
            
            {{-- KANAN ATAS MOBILE: DROPDOWN PROFIL & TOMBOL SIDEBAR --}}
            <div class="flex items-center gap-2">
                {{-- DROPDOWN PROFIL MOBILE --}}
                <div class="relative" id="mobileUserMenuDropdown">
                    <button id="mobileUserMenuBtn" type="button" class="h-9 w-9 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-slate-800 flex items-center justify-center shadow-xs active:bg-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </button>

                    <div id="mobileUserMenuContent" class="hidden absolute right-0 mt-2 w-52 rounded-2xl bg-white border border-[#e2e8f0] shadow-xl py-2 z-50 text-sm font-bold text-slate-700">
                        <div class="px-4 py-2 border-b border-[#e2e8f0] bg-slate-50">
                            <p class="font-extrabold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500 font-semibold capitalize">{{ auth()->user()->role ?? 'Karyawan' }}</p>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 transition text-left font-bold">
                                <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>

                {{-- TOMBOL SIDEBAR MENU MOBILE --}}
                <button id="mobileMenuBtn" type="button" class="p-2 rounded-xl bg-slate-100 text-slate-700 hover:text-slate-900 focus:outline-none">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </header>

        {{-- SIDEBAR CONTAINER --}}
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-68 bg-[#f8fafc] border-r border-[#e2e8f0] transform -translate-x-full md:translate-x-0 md:static md:inset-auto md:flex md:flex-col transition-transform duration-200 ease-in-out shadow-sm md:shadow-none">
            <div class="p-5 flex items-center justify-between border-b border-[#e2e8f0]">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center p-2 shadow-xs">
                        <img src="{{ asset('images/logo.png') }}" 
                             alt="Logo" 
                             class="h-full w-full object-contain"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                        <span style="display:none" class="text-xs font-black text-white">AEP</span>
                    </div>

                    <div>
                        <div class="font-extrabold text-base text-slate-900 leading-tight">Asia Employee Portal</div>
                        <div class="text-xs text-slate-500 font-bold mt-0.5">ASIA PLASTIK</div>
                    </div>
                </div>
                <button id="closeSidebarBtn" class="md:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700">✕</button>
            </div>

            {{-- NAVIGASI SIDEBAR --}}
            <nav class="flex-1 px-3.5 py-5 space-y-1.5 overflow-y-auto">
                @php
                    $user = auth()->user();
                    $isHrd = in_array($user->role ?? '', ['hrd', 'admin', 'superadmin'], true);
                @endphp

                @if(!$isHrd)
                    <a href="{{ route('leave-requests.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition {{ request()->routeIs('leave-requests.*') ? 'bg-white text-slate-900 shadow-xs border border-[#e2e8f0]' : 'text-slate-600 hover:bg-slate-200/60 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2.25 4.5H6.75A2.25 2.25 0 014.5 18.25V5.75A2.25 2.25 0 016.75 3.5h7.5L19.5 8.75v9.5a2.25 2.25 0 01-2.25 2.25z" />
                        </svg>
                        Pengajuan Saya
                    </a>
                @else
                    <a href="{{ route('hrd.leave-requests.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition {{ request()->routeIs('hrd.leave-requests.*') ? 'bg-white text-slate-900 shadow-xs border border-[#e2e8f0]' : 'text-slate-600 hover:bg-slate-200/60 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Pengajuan Karyawan
                    </a>

                    <a href="{{ route('hrd.employees.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition {{ request()->routeIs('hrd.employees.*') ? 'bg-white text-slate-900 shadow-xs border border-[#e2e8f0]' : 'text-slate-600 hover:bg-slate-200/60 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Data Karyawan
                    </a>
                @endif
            </nav>

            <div class="p-4 border-t border-[#e2e8f0] text-xs text-slate-500 text-center font-semibold">
                © {{ date('Y') }} ASIA PLASTIK
            </div>
        </aside>

        <div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden md:hidden"></div>

        {{-- MAIN CONTENT AREA --}}
        <div class="flex-1 flex flex-col min-w-0 bg-[#f1f3f5]">
            
            {{-- DESKTOP ONLY TOPBAR (HANYA MUNCUL DI DESKTOP) --}}
            <header class="hidden md:flex h-16 bg-white border-b border-[#e2e8f0] px-6 lg:px-8 items-center justify-between sticky top-0 z-30 shadow-xs">
                <div>
                    {{ $headerTitle ?? '' }}
                </div>

                {{-- DESKTOP DROPDOWN --}}
                <div class="relative" id="desktopUserMenuDropdown">
                    <button id="desktopUserMenuBtn" type="button" class="flex items-center gap-3 p-1.5 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] hover:bg-slate-100 transition focus:outline-none">
                        <div class="h-8.5 w-8.5 rounded-lg bg-slate-950 text-white flex items-center justify-center shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="text-sm font-extrabold text-slate-900 leading-none">{{ auth()->user()->name ?? 'User' }}</div>
                            <div class="text-xs text-slate-500 font-bold leading-none mt-1 capitalize">{{ auth()->user()->role ?? 'Karyawan' }}</div>
                        </div>
                        <svg class="h-4 w-4 text-slate-400 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="desktopUserMenuContent" class="hidden absolute right-0 mt-2 w-52 rounded-2xl bg-white border border-[#e2e8f0] shadow-xl py-2 z-50 text-sm font-bold text-slate-700">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 transition text-left font-bold">
                                <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- KONTEN UTAMA --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        // Dropdown Mobile
        const mobileUserMenuBtn = document.getElementById('mobileUserMenuBtn');
        const mobileUserMenuContent = document.getElementById('mobileUserMenuContent');
        mobileUserMenuBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            mobileUserMenuContent?.classList.toggle('hidden');
        });

        // Dropdown Desktop
        const desktopUserMenuBtn = document.getElementById('desktopUserMenuBtn');
        const desktopUserMenuContent = document.getElementById('desktopUserMenuContent');
        desktopUserMenuBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            desktopUserMenuContent?.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!mobileUserMenuContent?.contains(e.target) && !mobileUserMenuBtn?.contains(e.target)) {
                mobileUserMenuContent?.classList.add('hidden');
            }
            if (!desktopUserMenuContent?.contains(e.target) && !desktopUserMenuBtn?.contains(e.target)) {
                desktopUserMenuContent?.classList.add('hidden');
            }
        });

        // Sidebar Mobile Drawer
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const closeSidebarBtn = document.getElementById('closeSidebarBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function toggleMobileSidebar(show) {
            if (show) {
                sidebar?.classList.remove('-translate-x-full');
                sidebarBackdrop?.classList.remove('hidden');
            } else {
                sidebar?.classList.add('-translate-x-full');
                sidebarBackdrop?.classList.add('hidden');
            }
        }

        mobileMenuBtn?.addEventListener('click', () => toggleMobileSidebar(true));
        closeSidebarBtn?.addEventListener('click', () => toggleMobileSidebar(false));
        sidebarBackdrop?.addEventListener('click', () => toggleMobileSidebar(false));
    </script>
</body>
</html>
