{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
          x-init="$watch('darkMode', val => { localStorage.setItem('darkMode', val); document.documentElement.classList.toggle('dark', val); }); if (darkMode) document.documentElement.classList.add('dark');"
          :class="{ 'dark': darkMode }"
          class="font-sans text-gray-900 dark:text-slate-200 antialiased bg-slate-100 dark:bg-slate-900 min-h-screen h-full relative flex items-center justify-center p-4">
        <div class="absolute inset-x-0 top-0 h-56 bg-primary-900 dark:bg-slate-950"></div>
        <div class="w-full max-w-md relative z-10 mt-16">
            <div class="bg-white dark:bg-slate-800 shadow-2xl rounded-2xl relative pt-20 pb-10 px-8 sm:px-10 border border-primary-100 dark:border-slate-700">

                <!-- Floating Avatar/Logo -->
                <div class="absolute -top-16 left-1/2 transform -translate-x-1/2">
                    <a href="/">
                        <img src="{{ asset('images/logo.webp') }}"
                             class="w-32 h-32 object-cover rounded-full border-[6px] border-white dark:border-slate-700 shadow-xl ring-4 ring-primary-100 dark:ring-slate-600 transition-transform duration-500 hover:scale-105"
                             alt="Login Logo" />
                    </a>
                </div>

                <!-- Welcome Header -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-extrabold text-primary-900 dark:text-slate-100 tracking-tight">Kawanua</h2>
                    <p class="text-sm font-medium text-gray-500 dark:text-slate-400 mt-2">Silakan masuk ke akun Anda</p>
                </div>

                <!-- Form Content (Slot) -->
                <div class="w-full">
                    {{ $slot }}
                </div>

            </div>

            <!-- Footer Text -->
            <p class="text-center text-slate-500 dark:text-slate-500 text-xs mt-8 font-medium">
                &copy; {{ date('Y') }} Kawanua Admin. All rights reserved.
            </p>

            <!-- Dark Mode Toggle -->
            <div class="flex justify-center mt-4">
                <button @click="darkMode = !darkMode"
                        class="text-xs text-slate-400 dark:text-slate-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                    <span x-show="!darkMode">🌙 Dark Mode</span>
                    <span x-show="darkMode">☀️ Light Mode</span>
                </button>
            </div>
        </div>
    </body>
</html>
