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
                            <p class="mt-1 truncate font-mono text-[11px] font-bold text-slate-400">
                                {{ auth()->user()->username ?? '-' }}
                            </p>
                        </div>

                        <a
                            href="{{ route('profile.edit') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-slate-700 hover:bg-slate-50 transition font-bold {{ request()->routeIs('profile.*') ? 'bg-slate-100 text-slate-900' : '' }}"
                        >
                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15.75A3.75 3.75 0 1012 8.25a3.75 3.75 0 000 7.5zM19.5 12a7.5 7.5 0 01-.123 1.35l2.036 1.586-1.875 3.248-2.51-1.012a7.58 7.58 0 01-2.338 1.35L14.31 21h-4.62l-.38-2.478a7.58 7.58 0 01-2.338-1.35l-2.51 1.012-1.875-3.248 2.036-1.586A7.5 7.5 0 014.5 12c0-.46.041-.91.123-1.35L2.587 9.064l1.875-3.248 2.51 1.012A7.58 7.58 0 019.31 5.478L9.69 3h4.62l.38 2.478a7.58 7.58 0 012.338 1.35l2.51-1.012 1.875 3.248-2.036 1.586c.082.44.123.89.123 1.35z" />
                            </svg>
                            Pengaturan Profil
                        </a>

                        <div class="my-1 border-t border-[#e2e8f0]"></div>

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
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-[#f8fafc] border-r border-[#e2e8f0] transform -translate-x-full md:translate-x-0 md:static md:inset-auto md:flex md:flex-col transition-transform duration-200 ease-in-out shadow-lg md:shadow-none">
    
            {{-- SIDEBAR HEADER --}}
            <div class="h-16 px-5 flex items-center justify-between border-b border-[#e2e8f0] bg-white md:bg-transparent">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center p-1.5 shadow-xs shrink-0">
                        <img src="{{ asset('images/logo.png') }}" 
                             alt="Logo" 
                             class="h-full w-full object-contain"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                        <span style="display:none" class="text-xs font-black text-white">AEP</span>
                    </div>
                    <div class="min-w-0">
                        <div class="font-extrabold text-sm text-slate-900 leading-tight truncate">Asia Employee Portal</div>
                        <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">Asia Plastik</div>
                    </div>
                </div>
                <button id="closeSidebarBtn" type="button" class="md:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- NAVIGASI SIDEBAR --}}
            <nav class="flex-1 px-3.5 py-4 space-y-1 overflow-y-auto">
                @php
                    $user = auth()->user();
                    $userRole = strtolower(trim((string) ($user?->role ?? '')));
                    $userEmail = strtolower(trim((string) ($user?->email ?? '')));

                    $isHrd = in_array($userRole, ['hrd', 'admin', 'superadmin'], true);
                    $isKabag = $userRole === 'kabag';
                    $isPortalMasterAdmin = $userEmail === 'sandyramdani65@gmail.com';

                    // Base class seragam untuk semua menu item
                    $baseLinkClass = "flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150";
                    $activeLinkClass = "bg-white text-slate-900 shadow-xs border border-[#e2e8f0] font-bold";
                    $inactiveLinkClass = "text-slate-600 hover:bg-slate-200/60 hover:text-slate-900";
                @endphp

                {{-- ========================================================= --}}
                {{-- MENU KARYAWAN --}}
                {{-- ========================================================= --}}
                @if (!$isHrd && !$isKabag)
                    <div class="px-3 pt-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Menu Utama
                    </div>

                    <a href="{{ route('leave-requests.index') }}" 
                       class="{{ $baseLinkClass }} {{ request()->routeIs('leave-requests.*') ? $activeLinkClass : $inactiveLinkClass }}">
                        <svg class="h-5 w-5 {{ request()->routeIs('leave-requests.*') ? 'text-slate-900' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2.25 4.5H6.75A2.25 2.25 0 014.5 18.25V5.75A2.25 2.25 0 016.75 3.5h7.5L19.5 8.75v9.5a2.25 2.25 0 01-2.25 2.25z" />
                        </svg>
                        <span>Pengajuan Saya</span>
                    </a>

                {{-- ========================================================= --}}
                {{-- MENU HRD / ADMIN --}}
                {{-- ========================================================= --}}
                @elseif ($isHrd)
                    <div class="px-3 pt-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Menu HRD
                    </div>

                    <a href="{{ route('hrd.leave-requests.index') }}" 
                       class="{{ $baseLinkClass }} {{ request()->routeIs('hrd.leave-requests.*') ? $activeLinkClass : $inactiveLinkClass }}">
                        <svg class="h-5 w-5 {{ request()->routeIs('hrd.leave-requests.*') ? 'text-slate-900' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Pengajuan Karyawan</span>
                    </a>

                    <a href="{{ route('hrd.employees.index') }}" 
                       class="{{ $baseLinkClass }} {{ request()->routeIs('hrd.employees.*') ? $activeLinkClass : $inactiveLinkClass }}">
                        <svg class="h-5 w-5 {{ request()->routeIs('hrd.employees.*') ? 'text-slate-900' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Data Karyawan</span>
                    </a>

                    {{-- MASTER DATA KHUSUS ADMIN UTAMA --}}
                    @if ($isPortalMasterAdmin)
                        <div class="pt-5 pb-1.5 px-3">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                Master Data
                            </div>
                        </div>

                        <a href="{{ route('master.kabag.index') }}"
                           class="{{ $baseLinkClass }} {{ request()->routeIs('master.kabag.*') ? $activeLinkClass : $inactiveLinkClass }}">
                            <svg class="h-5 w-5 {{ request()->routeIs('master.kabag.*') ? 'text-slate-900' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />
                            </svg>
                            <span>Data Kabag</span>
                        </a>

                        <a href="{{ route('master.kabag-mapping.index') }}"
                           class="{{ $baseLinkClass }} {{ request()->routeIs('master.kabag-mapping.*') ? $activeLinkClass : $inactiveLinkClass }}">
                            <svg class="h-5 w-5 {{ request()->routeIs('master.kabag-mapping.*') ? 'text-slate-900' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                            <span>Mapping Kabag</span>
                        </a>

                        <a href="{{ route('master.special-leave-types.index') }}"
                           class="{{ $baseLinkClass }} {{ request()->routeIs('master.special-leave-types.*') ? $activeLinkClass : $inactiveLinkClass }}">
                            <svg class="h-5 w-5 {{ request()->routeIs('master.special-leave-types.*') ? 'text-slate-900' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 6.75V4.5m7.5 2.25V4.5M3.75 9.75h16.5m-15 10.5h13.5A1.5 1.5 0 0020.25 18.75V7.5A1.5 1.5 0 0018.75 6H5.25A1.5 1.5 0 003.75 7.5v11.25a1.5 1.5 0 001.5 1.5z" />
                            </svg>
                            <span>Cuti Khusus</span>
                        </a>

                        <a href="{{ route('master.permission-types.index') }}"
                           class="{{ $baseLinkClass }} {{ request()->routeIs('master.permission-types.*') ? $activeLinkClass : $inactiveLinkClass }}">
                            <svg class="h-5 w-5 {{ request()->routeIs('master.permission-types.*') ? 'text-slate-900' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 8h10M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z" />
                            </svg>
                            <span>Jenis Izin</span>
                        </a>

                        <a
                            href="{{ route('master.employee-credentials.index') }}"
                            class="{{ $baseLinkClass }} {{ request()->routeIs('master.employee-credentials.*') ? $activeLinkClass : $inactiveLinkClass }}"
                        >
                            <svg
                                class="h-5 w-5 {{ request()->routeIs('master.employee-credentials.*') ? 'text-slate-900' : 'text-slate-500' }}"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 7a4 4 0 11-7.874 1H3m12-1h6m-3-3v6M5 21a7 7 0 0114 0"
                                />
                            </svg>

                            <span>Kredensial Karyawan</span>
                        </a>


                    @endif

                {{-- ========================================================= --}}
                {{-- MENU KABAG --}}
                {{-- ========================================================= --}}
                @elseif ($isKabag)
                    <div class="px-3 pt-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Persetujuan
                    </div>

                    <a href="{{ route('kabag.leave-requests.index') }}"
                       class="{{ $baseLinkClass }} {{ request()->routeIs('kabag.leave-requests.*') ? $activeLinkClass : $inactiveLinkClass }}">
                        <svg class="h-5 w-5 {{ request()->routeIs('kabag.leave-requests.*') ? 'text-slate-900' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Persetujuan Pengajuan</span>
                    </a>
                @endif
            </nav>

            {{-- SIDEBAR FOOTER --}}
            <div class="p-4 border-t border-[#e2e8f0] text-xs text-slate-400 text-center font-medium">
                &copy; {{ date('Y') }} ASIA PLASTIK
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

                    <div id="desktopUserMenuContent" class="hidden absolute right-0 mt-2 w-64 rounded-2xl bg-white border border-[#e2e8f0] shadow-xl py-2 z-50 text-sm font-bold text-slate-700">

                        <div class="px-4 py-2.5 border-b border-[#e2e8f0] bg-slate-50">
                            <p class="font-extrabold text-slate-900 truncate">
                                {{ auth()->user()->name ?? 'User' }}
                            </p>
                            <p class="mt-0.5 text-xs text-slate-500 font-semibold capitalize">
                                {{ auth()->user()->role ?? 'Karyawan' }}
                            </p>
                            <p class="mt-1 truncate font-mono text-[11px] font-bold text-slate-400">
                                {{ auth()->user()->username ?? '-' }}
                            </p>
                        </div>

                        <a
                            href="{{ route('profile.edit') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-slate-700 hover:bg-slate-50 transition font-bold {{ request()->routeIs('profile.*') ? 'bg-slate-100 text-slate-900' : '' }}"
                        >
                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15.75A3.75 3.75 0 1012 8.25a3.75 3.75 0 000 7.5zM19.5 12a7.5 7.5 0 01-.123 1.35l2.036 1.586-1.875 3.248-2.51-1.012a7.58 7.58 0 01-2.338 1.35L14.31 21h-4.62l-.38-2.478a7.58 7.58 0 01-2.338-1.35l-2.51 1.012-1.875-3.248 2.036-1.586A7.5 7.5 0 014.5 12c0-.46.041-.91.123-1.35L2.587 9.064l1.875-3.248 2.51 1.012A7.58 7.58 0 019.31 5.478L9.69 3h4.62l.38 2.478a7.58 7.58 0 012.338 1.35l2.51-1.012 1.875 3.248-2.036 1.586c.082.44.123.89.123 1.35z" />
                            </svg>
                            Pengaturan Profil
                        </a>

                        <div class="my-1 border-t border-[#e2e8f0]"></div>

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
