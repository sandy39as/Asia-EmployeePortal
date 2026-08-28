<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">


    <title>
        {{ config('app.name', 'Asia Employee Portal') }}
    </title>


    <link rel="preconnect"
          href="https://fonts.bunny.net">

    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap"
          rel="stylesheet" />


    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body class="font-sans antialiased">

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">


        {{-- ================================================= --}}
        {{-- SIDEBAR --}}
        {{-- ================================================= --}}

        @include('layouts.navigation')


        {{-- ================================================= --}}
        {{-- MAIN --}}
        {{-- ================================================= --}}

        <div class="min-h-screen transition-all duration-300 lg:pl-72">


            {{-- ================================================= --}}
            {{-- TOP HEADER --}}
            {{-- ================================================= --}}

            @isset($header)

                <header class="border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-900/95">

                    <div class="mx-auto max-w-[1600px] px-4 py-4 pl-20 sm:px-6 sm:pl-20 lg:px-8">

                        {{ $header }}

                    </div>

                </header>

            @endisset


            {{-- ================================================= --}}
            {{-- PAGE --}}
            {{-- ================================================= --}}

            <main>

                {{ $slot }}

            </main>

        </div>

    </div>

</body>

</html>
