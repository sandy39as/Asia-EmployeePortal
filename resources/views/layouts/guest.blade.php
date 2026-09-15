<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f1f3f5]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Asia Employee Portal') }}</title>

        {{-- FAVICON --}}
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
        </style>
    </head>
    <body class="h-full font-sans text-slate-900 antialiased bg-[#f1f3f5] selection:bg-slate-300 selection:text-slate-900">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 py-8 sm:px-6 lg:px-8">
            
            {{-- BRAND HEADER DENGAN CONTAINER LOGO HITAM --}}
            <div class="flex flex-col items-center gap-3">
                <div class="h-12 w-12 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center p-2.5 shadow-md">
                    <img src="{{ asset('images/logo.png') }}" 
                         alt="Logo" 
                         class="h-full w-full object-contain"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                    <span style="display:none" class="text-xs font-black text-white">AEP</span>
                </div>
                <div class="text-center">
                    <div class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Asia Employee Portal</div>
                    <div class="text-xs sm:text-sm text-slate-500 font-bold mt-0.5">ASIA PLASTIK</div>
                </div>
            </div>

            {{-- AUTH CARD --}}
            <div class="w-full sm:max-w-md mt-6 px-6 py-7 sm:px-8 sm:py-9 bg-white border border-[#e2e8f0] shadow-xl rounded-3xl">
                {{ $slot }}
            </div>

            <div class="mt-6 text-center text-xs sm:text-sm text-slate-500 font-bold">
                &copy; {{ date('Y') }} ASIA PLASTIK. All rights reserved.
            </div>
        </div>
    </body>
</html>
