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

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar Backdrop (Mobile) -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-gray-900/50 dark:bg-black/70 lg:hidden" @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside
            :class="{
                'translate-x-0': sidebarOpen,
                '-translate-x-full': !sidebarOpen,
                'sidebar-collapsed': sidebarCollapsed
            }"
            :style="sidebarCollapsed ? 'width: 5rem;' : 'width: 16rem;'"
            class="sidebar-premium fixed inset-y-0 left-0 z-30 lg:translate-x-0 lg:static lg:inset-0 flex flex-col flex-shrink-0 shadow-2xl"
            style="transition: width 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), transform 0.3s ease-in-out;">

            <!-- Logo Area -->
            <div class="sidebar-logo-area flex items-center h-16 transition-all duration-300"
                 :class="sidebarCollapsed ? 'justify-center px-0' : 'justify-between px-4'">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden min-w-0" x-show="!sidebarCollapsed"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0 translate-x-2"
                   x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: linear-gradient(135deg, #818cf8, #4f46e5); box-shadow: 0 0 14px rgba(129,140,248,0.5);">
                        <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="w-5 h-5 object-cover rounded">
                    </div>
                    <span class="sidebar-app-name text-base whitespace-nowrap truncate">{{ config('app.name') }}</span>
                </a>

                <!-- Collapsed: just logo icon -->
                <a href="{{ route('dashboard') }}" x-show="sidebarCollapsed" x-cloak
                   class="w-9 h-9 rounded-lg flex items-center justify-center"
                   style="background: linear-gradient(135deg, #818cf8, #4f46e5); box-shadow: 0 0 14px rgba(129,140,248,0.4);">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="w-5 h-5 object-cover rounded">
                </a>

                <!-- Collapse toggle button (desktop) -->
                <button @click="sidebarCollapsed = !sidebarCollapsed"
                        x-show="!sidebarCollapsed"
                        class="sidebar-collapse-btn hidden lg:flex"
                        title="Toggle sidebar">
                    <svg class="w-3.5 h-3.5 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
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
                    <!-- Expand btn when collapsed -->
                    <button x-show="sidebarCollapsed" x-cloak @click="sidebarCollapsed = false"
                            class="sidebar-collapse-btn" title="Expand sidebar">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Navbar -->
            <header class="h-16 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-sm z-10">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-md p-1 mr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <!-- Search Bar -->
                    <div class="hidden sm:block relative w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-4 h-4 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" class="w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 bg-gray-50 dark:bg-slate-700 dark:text-slate-200 dark:placeholder-slate-400" placeholder="Search...">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" class="p-1.5 rounded-lg text-gray-400 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 hover:text-gray-600 dark:hover:text-slate-200 transition-colors" title="Toggle dark mode">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>

                    <!-- Notifications -->
                    <button class="relative p-1 text-gray-400 dark:text-slate-400 hover:text-gray-600 dark:hover:text-slate-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-0 right-0 block w-2.5 h-2.5 bg-red-500 rounded-full ring-2 ring-white dark:ring-slate-800"></span>
                    </button>

                    <!-- Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none">
                            <img class="w-8 h-8 rounded-full border border-gray-200 dark:border-slate-600 object-cover" src="{{ asset('images/logo.webp') }}" alt="User">
                            <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-slate-300">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="open" x-transition x-cloak class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-100 dark:border-slate-700 py-1 z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700">Profile Settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50/50 dark:bg-slate-900/50" id="main-content">
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
