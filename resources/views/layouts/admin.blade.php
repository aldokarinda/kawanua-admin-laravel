{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-gray-50 dark:bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Kawanua Admin') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">

    {{-- DNS prefetch for external resources --}}
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

    {{-- Google Font: non-blocking load --}}
    <link rel="preload" href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" as="style"
          onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    </noscript>

    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])

    @php
        $theme = \App\Models\AppSetting::get('app_theme', 'indigo');
        $presets = [
            'indigo' => [
                '50' => '#f5f3ff', '100' => '#ede9fe', '200' => '#ddd6fe', '300' => '#c4b5fd',
                '400' => '#a78bfa', '500' => '#818cf8', '600' => '#4f46e5', '700' => '#4338ca',
                '800' => '#3730a3', '900' => '#01007f', '950' => '#01005a'
            ],
            'blue' => [
                '50' => '#eff6ff', '100' => '#dbeafe', '200' => '#bfdbfe', '300' => '#93c5fd',
                '400' => '#60a5fa', '500' => '#3b82f6', '600' => '#2563eb', '700' => '#1d4ed8',
                '800' => '#1e40af', '900' => '#1e3a8a', '950' => '#172554'
            ],
            'emerald' => [
                '50' => '#ecfdf5', '100' => '#d1fae5', '200' => '#a7f3d0', '300' => '#6ee7b7',
                '400' => '#34d399', '500' => '#10b981', '600' => '#059669', '700' => '#047857',
                '800' => '#065f46', '900' => '#064e3b', '950' => '#022c22'
            ],
            'purple' => [
                '50' => '#faf5ff', '100' => '#f3e8ff', '200' => '#e9d5ff', '300' => '#d8b4fe',
                '400' => '#c084fc', '500' => '#a855f7', '600' => '#9333ea', '700' => '#7e22ce',
                '800' => '#6b21a8', '900' => '#581c87', '950' => '#3b0764'
            ],
            'rose' => [
                '50' => '#fff1f2', '100' => '#ffe4e6', '200' => '#fecdd3', '300' => '#fda4af',
                '400' => '#fb7185', '500' => '#f43f5e', '600' => '#e11d48', '700' => '#be123c',
                '800' => '#9f1239', '900' => '#881337', '950' => '#4c0519'
            ]
        ];
        $colors = $presets[$theme] ?? $presets['indigo'];
    @endphp

    <style>
        :root {
            @foreach($colors as $shade => $hex)
                --color-primary-{{ $shade }}: {{ $hex }};
            @endforeach
        }
        @foreach($colors as $shade => $hex)
            .bg-primary-{{ $shade }} { background-color: {{ $hex }} !important; }
            .text-primary-{{ $shade }} { color: {{ $hex }} !important; }
            .border-primary-{{ $shade }} { border-color: {{ $hex }} !important; }
            .ring-primary-{{ $shade }} { --tw-ring-color: {{ $hex }} !important; }
            .focus\:border-primary-{{ $shade }}:focus { border-color: {{ $hex }} !important; }
            .focus\:ring-primary-{{ $shade }}:focus { --tw-ring-color: {{ $hex }} !important; }
            .hover\:bg-primary-{{ $shade }}:hover { background-color: {{ $hex }} !important; }
            .hover\:text-primary-{{ $shade }}:hover { color: {{ $hex }} !important; }
            .peer-checked\:bg-primary-{{ $shade }}:checked ~ div { background-color: {{ $hex }} !important; }
            .peer-checked\:bg-primary-600:checked ~ div { background-color: {{ $colors['600'] }} !important; }
        @endforeach
    </style>

    {{-- Bootstrap Icons — loaded async --}}
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
          as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    </noscript>

    {{-- NProgress — slim page loading bar --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nprogress@0.2.0/nprogress.css">
    <script src="https://cdn.jsdelivr.net/npm/nprogress@0.2.0/nprogress.min.js"></script>
    <script>
        // Configure NProgress
        NProgress.configure({
            minimum: 0.1,
            speed: 220,
            trickleSpeed: 80,
            showSpinner: false,
            easing: 'ease',
        });
        // Start loading bar immediately on link clicks
        document.addEventListener('click', function (e) {
            const a = e.target.closest('a[href]');
            if (!a) return;
            const href = a.getAttribute('href');
            // Only intercept same-origin navigation links (not anchors, js:, mailto:, external)
            if (!href || href.startsWith('#') || href.startsWith('javascript') ||
                href.startsWith('mailto') || a.target === '_blank') return;
            try {
                const url = new URL(href, location.origin);
                if (url.origin === location.origin) NProgress.start();
            } catch(e) {}
        });
        document.addEventListener('submit', function (e) {
            NProgress.start();
        });
        window.addEventListener('pageshow', function () {
            NProgress.done();
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }
        /* Override NProgress bar color to match brand */
        #nprogress .bar {
            background: #818cf8 !important;
            height: 3px !important;
        }
        #nprogress .peg {
            box-shadow: 0 0 10px #818cf8, 0 0 5px #818cf8 !important;
        }
        /* Collapsed sidebar active state */
        [class*="sidebar-item active"][class*="justify-center"],
        [class*="sidebar-subitem active"][class*="justify-center"] {
            transform: translateX(0);
        }
        [class*="sidebar-item active"][class*="justify-center"]:hover,
        [class*="sidebar-subitem active"][class*="justify-center"]:hover {
            transform: scale(1.05);
        }
        .sidebar-subitem:not(.active):hover {
            background: rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-800 dark:text-slate-300 bg-gray-50 dark:bg-slate-900"
      x-data="{ sidebarOpen: false, sidebarCollapsed: false, darkMode: localStorage.getItem('darkMode') === 'true', selected: [], selectAll: false }"
      :class="{ 'dark': darkMode }"
      x-init="
          if (darkMode) { document.documentElement.classList.add('dark'); }
          $watch('darkMode', val => {
              localStorage.setItem('darkMode', val);
              document.documentElement.classList.toggle('dark', val);
          });
          window.addEventListener('toast', e => {
              Swal.fire({
                  toast: true,
                  position: 'top-end',
                  icon: e.detail.type || 'info',
                  title: e.detail.message,
                  showConfirmButton: false,
                  timer: 3000,
                  timerProgressBar: true,
                  background: darkMode ? '#1e293b' : '#ffffff',
                  color: darkMode ? '#f8fafc' : '#0f172a'
              });
          });
      ">

    <div class="flex h-screen w-full overflow-hidden"
         x-data="{ isDesktop: window.innerWidth >= 1024 }"
         x-init="window.addEventListener('resize', () => {
             const wasDesktop = isDesktop;
             isDesktop = window.innerWidth >= 1024;
             // Auto-expand sidebar when switching to mobile view
             if (wasDesktop && !isDesktop) {
                 sidebarCollapsed = false;
                 sidebarOpen = false;
             }
         })">

        <!-- Sidebar Backdrop (Mobile) -->
        <template x-teleport="body">
            <div x-show="sidebarOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-40 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm lg:hidden"
                 @click="sidebarOpen = false"></div>
        </template>

        <!-- Sidebar -->
        <aside
            :class="{
                'translate-x-0': sidebarOpen,
                '-translate-x-full': !sidebarOpen,
                'sidebar-collapsed': sidebarCollapsed && isDesktop
            }"
            :style="sidebarCollapsed && isDesktop ? 'width: 5rem;' : 'width: 16rem;'"
            class="sidebar-premium fixed inset-y-0 left-0 z-50 lg:translate-x-0 lg:relative lg:inset-0 flex flex-col flex-shrink-0 shadow-2xl"
            style="transition: width 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), transform 0.3s ease-in-out;">

            <!-- Logo Area -->
            <div class="sidebar-logo-area flex items-center h-16 transition-all duration-300"
                 :class="sidebarCollapsed ? 'justify-center px-0' : 'justify-between px-4'">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden min-w-0" x-show="!sidebarCollapsed"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0 translate-x-2"
                   x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: linear-gradient(135deg, var(--color-primary-400, #818cf8), var(--color-primary-600, #4f46e5)); box-shadow: 0 0 14px rgba(129,140,248,0.5);">
                        <img src="{{ \App\Models\AppSetting::get('app_logo') ? asset('storage/' . \App\Models\AppSetting::get('app_logo')) : asset('images/logo.webp') }}" alt="Logo" class="w-5 h-5 object-cover rounded">
                    </div>
                    <span class="sidebar-app-name text-base whitespace-nowrap truncate">{{ config('app.name') }}</span>
                </a>

                <!-- Collapsed: just logo icon -->
                <a href="{{ route('dashboard') }}" x-show="sidebarCollapsed" x-cloak
                   class="w-9 h-9 rounded-lg flex items-center justify-center"
                   style="background: linear-gradient(135deg, var(--color-primary-400, #818cf8), var(--color-primary-600, #4f46e5)); box-shadow: 0 0 14px rgba(129,140,248,0.4);">
                    <img src="{{ \App\Models\AppSetting::get('app_logo') ? asset('storage/' . \App\Models\AppSetting::get('app_logo')) : asset('images/logo.webp') }}" alt="Logo" class="w-5 h-5 object-cover rounded">
                </a>


            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-0.5">
                @php $hasRenderedGroupDivider = false; @endphp
                @foreach($sidebarMenus as $menu)
                    {{-- Section divider before first parent group --}}
                    @if($menu->children->isNotEmpty() && !$hasRenderedGroupDivider)
                        @php $hasRenderedGroupDivider = true; @endphp
                        <div x-show="sidebarCollapsed" x-cloak class="sidebar-section-divider">
                            <span></span>
                        </div>
                    @endif

                    @if($menu->children->isEmpty() && ($menu->route_name || $menu->url))
                        @php
                            $url = '#';
                            if($menu->route_name && Route::has($menu->route_name)) {
                                $url = route($menu->route_name);
                            } elseif ($menu->url) {
                                $url = $menu->url;
                            }
                            $isActive = request()->url() == $url || ($menu->route_name && request()->routeIs($menu->route_name));
                        @endphp
                        <a href="{{ $url }}"
                           @click="sidebarOpen = false"
                           class="sidebar-item {{ $isActive ? 'active' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center' : ''"
                           @if($menu->name) data-sidebar-tooltip="{{ $menu->name }}" @endif
                           x-bind:data-sidebar-tooltip="sidebarCollapsed ? '{{ addslashes($menu->name) }}' : null">
                            <span class="sidebar-menu-icon {{ $isActive ? 'active' : '' }}">
                                <i class="{{ $menu->icon }}"></i>
                            </span>
                            <span class="sidebar-menu-label" x-show="!sidebarCollapsed"
                                  x-transition:enter="transition ease-out duration-150 delay-75"
                                  x-transition:enter-start="opacity-0"
                                  x-transition:enter-end="opacity-100">{{ $menu->name }}</span>
                        </a>

                    @elseif($menu->children->isNotEmpty())
                        @php
                            $isActiveGroup = false;
                            foreach($menu->children as $child) {
                                $childUrl = '#';
                                if($child->route_name && Route::has($child->route_name)) {
                                    $childUrl = route($child->route_name);
                                } elseif ($child->url) {
                                    $childUrl = $child->url;
                                }
                                if(request()->url() == $childUrl || ($child->route_name && request()->routeIs($child->route_name))) {
                                    $isActiveGroup = true;
                                    break;
                                }
                            }
                        @endphp
                        <div x-data="{ expanded: {{ $isActiveGroup ? 'true' : 'false' }} }" class="mt-0.5">
                            <!-- Parent group button -->
                            <button @click="if(!sidebarCollapsed) { expanded = !expanded } else { sidebarCollapsed = false; $nextTick(() => { expanded = true }); }"
                                    class="sidebar-item {{ $isActiveGroup ? 'active-group' : '' }}"
                                    :class="sidebarCollapsed ? 'justify-center' : ''"
                                    x-bind:data-sidebar-tooltip="sidebarCollapsed ? '{{ addslashes($menu->name) }}' : null">
                                <span class="sidebar-menu-icon">
                                    <i class="{{ $menu->icon }}"></i>
                                </span>
                                <span class="sidebar-menu-label" x-show="!sidebarCollapsed"
                                      x-transition:enter="transition ease-out duration-150 delay-75"
                                      x-transition:enter-start="opacity-0"
                                      x-transition:enter-end="opacity-100">{{ $menu->name }}</span>
                                <svg x-show="!sidebarCollapsed"
                                     :class="{ 'rotated': expanded }"
                                     class="sidebar-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Sub Menu Items -->
                            <div x-show="expanded && !sidebarCollapsed" x-collapse x-cloak class="sidebar-submenu mt-1">
                                @foreach($menu->children as $child)
                                    @php
                                        $childUrl = '#';
                                        if($child->route_name && Route::has($child->route_name)) {
                                            $childUrl = route($child->route_name);
                                        } elseif ($child->url) {
                                            $childUrl = $child->url;
                                        }
                                        $isChildActive = request()->url() == $childUrl || ($child->route_name && request()->routeIs($child->route_name));
                                    @endphp
                                    <a href="{{ $childUrl }}"
                                       @click="sidebarOpen = false"
                                       class="sidebar-subitem {{ $isChildActive ? 'active' : '' }}">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>

            <div x-show="!sidebarCollapsed" class="px-4 py-2 text-center text-[10px] text-gray-400 dark:text-slate-500">
                Copyright &copy; {{ date('Y') }} Aldo Karinda,<br>UNKLAB Business School
            </div>

            <!-- Bottom Profile Card -->
            <div class="sidebar-profile" :class="sidebarCollapsed ? 'p-3' : 'p-4'">
                <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'gap-3'">
                    <!-- Avatar -->
                    <div class="sidebar-avatar" x-bind:data-sidebar-tooltip="sidebarCollapsed ? '{{ addslashes(auth()->user()->name) }}' : null">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <!-- User Info -->
                    <div x-show="!sidebarCollapsed"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-x-2"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="flex-1 min-w-0">
                        <p class="sidebar-user-name">{{ auth()->user()->name }}</p>
                        <p class="sidebar-user-role">{{ auth()->user()->roles->pluck('name')->join(', ') ?: 'No Role' }}</p>
                    </div>

                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">

            <!-- Top Navbar -->
            <header class="bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-700 h-16 flex items-center justify-between px-4 sm:px-6 flex-shrink-0 z-10 relative">
                <div class="flex items-center gap-3">
                    <!-- Sidebar Toggle Button -->
                    <button @click="isDesktop ? sidebarCollapsed = !sidebarCollapsed : sidebarOpen = !sidebarOpen" class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-all focus:outline-none relative w-9 h-9 flex items-center justify-center overflow-hidden">
                        <!-- Hamburger (Shows when collapsed/closed) -->
                        <svg class="w-5 h-5 absolute transition-all duration-300 ease-in-out transform"
                             :class="((isDesktop && sidebarCollapsed) || (!isDesktop && !sidebarOpen)) ? 'scale-100 opacity-100 rotate-0' : 'scale-50 opacity-0 -rotate-180'"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <!-- Align Left (Shows when expanded/open) -->
                        <svg class="w-5 h-5 absolute transition-all duration-300 ease-in-out transform"
                             :class="((isDesktop && !sidebarCollapsed) || (!isDesktop && sidebarOpen)) ? 'scale-100 opacity-100 rotate-0' : 'scale-50 opacity-0 rotate-180'"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16" />
                        </svg>
                    </button>
                    <!-- Search - Hidden on very small screens -->
                    <div class="hidden sm:flex items-center bg-gray-100 dark:bg-slate-800 rounded-lg px-3 py-1.5 w-48 lg:w-64 border border-transparent focus-within:border-primary-500 focus-within:ring-1 focus-within:ring-primary-500 transition-all">
                        <i class="bi bi-search text-gray-400 dark:text-slate-500 text-sm"></i>
                        <input type="text" placeholder="Search..." class="bg-transparent border-none outline-none text-sm ml-2 w-full text-gray-700 dark:text-slate-300 placeholder-gray-400 dark:placeholder-slate-500 focus:ring-0 p-0">
                    </div>
                </div>
                
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Theme Toggle - Always visible -->
                    <button @click="darkMode = !darkMode" class="p-2 text-gray-400 dark:text-slate-400 hover:text-gray-600 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg transition-colors focus:outline-none">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>
                    
                    <!-- Notifications - Always visible -->
                    <button class="relative p-2 text-gray-400 dark:text-slate-400 hover:text-gray-600 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg transition-colors focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1.5 right-1.5 block w-2 h-2 bg-red-500 rounded-full ring-2 ring-white dark:ring-slate-900"></span>
                    </button>
                    
                    <!-- Profile - Text hidden on mobile -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none p-1 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                            <img class="w-8 h-8 rounded-full border border-gray-200 dark:border-slate-600 object-cover" src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('images/logo.webp') }}" alt="User">
                            <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-slate-300">{{ auth()->user()->name }}</span>
                            <svg class="hidden md:block w-4 h-4 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div x-show="open" x-transition x-cloak class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-100 dark:border-slate-700 py-1 z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">Profile Settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50/50 dark:bg-slate-900/50 min-w-0 p-4 sm:p-6 lg:p-8" id="main-content">
                <!-- Loading Skeleton -->
                <div id="main-loading" class="hidden animate-pulse p-6">
                    <div class="h-8 bg-gray-200 dark:bg-slate-700 rounded w-1/3 mb-4"></div>
                    <div class="h-64 bg-gray-200 dark:bg-slate-700 rounded mb-4"></div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="h-32 bg-gray-200 dark:bg-slate-700 rounded"></div>
                        <div class="h-32 bg-gray-200 dark:bg-slate-700 rounded"></div>
                        <div class="h-32 bg-gray-200 dark:bg-slate-700 rounded"></div>
                    </div>
                </div>

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- SweetAlert2 Session Toasts -->
    @if(session('success') || session('error'))
        <script>
            (function() {
                const fireToast = () => {
                    const isDark = document.documentElement.classList.contains('dark');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: "{{ session('success') ? 'success' : 'error' }}",
                        title: "{!! addslashes(session('success') ?? session('error')) !!}",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        background: isDark ? '#1e293b' : '#ffffff',
                        color: isDark ? '#f8fafc' : '#0f172a'
                    });
                };
                if (typeof Swal !== 'undefined') {
                    fireToast();
                } else {
                    document.addEventListener('DOMContentLoaded', fireToast, { once: true });
                }
            })();
        </script>
    @endif

    <!-- Include SweetAlert2 Global Delete Confirmation -->
    <script>
        function confirmDelete(formId, itemName = 'this item') {
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete " + itemName + ". This cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                background: isDark ? '#1e293b' : '#ffffff',
                color: isDark ? '#f8fafc' : '#0f172a'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            })
        }
    </script>

    <!-- Performance Monitoring Script -->
    <script>
        // Track page load metrics
        window.performance = window.performance || {};
        window.performance.timing = window.performance.timing || {};

        // Log navigation timing for debugging
        window.addEventListener('load', () => {
            if (window.performance && window.performance.timing) {
                const timing = window.performance.timing;
                const loadTime = timing.loadEventEnd - timing.navigationStart;
                if (loadTime > 0) {
                    console.log(`Page load time: ${loadTime}ms`);
                }
            }
        });
    </script>
    
    {{ $scripts ?? '' }}
</body>
</html>
