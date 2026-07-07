<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-gray-50 dark:bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Kawanua Admin') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased text-gray-800 dark:text-slate-300 bg-gray-50 dark:bg-slate-900"
      x-data="{ sidebarOpen: false, sidebarCollapsed: false, darkMode: localStorage.getItem('darkMode') === 'true' }"
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
                '-translate-x-full': !sidebarOpen
            }"
            :style="sidebarCollapsed ? 'width: 5rem;' : 'width: 16rem;'"
            class="fixed inset-y-0 left-0 z-30 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 transition-all duration-300 lg:translate-x-0 lg:static lg:inset-0 flex flex-col shadow-sm flex-shrink-0">

            <!-- Logo Area -->
            <div class="flex items-center h-16 border-b border-gray-100 dark:border-slate-700 transition-all duration-300" :class="sidebarCollapsed ? 'justify-center px-0' : 'justify-between px-4'">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden" x-show="!sidebarCollapsed" x-transition>
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 rounded-md shadow-sm object-cover flex-shrink-0">
                    <span class="text-lg font-bold text-primary-700 dark:text-primary-400 tracking-tight whitespace-nowrap truncate">{{ config('app.name') }}</span>
                </a>
                <button @click="sidebarCollapsed = !sidebarCollapsed"
                    class="hidden lg:flex p-1 rounded-md text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors items-center justify-center"
                    title="Toggle sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!sidebarCollapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                        <path x-show="sidebarCollapsed" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                @foreach($sidebarMenus as $menu)
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
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                  {{ $isActive
                                      ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400'
                                      : 'text-gray-600 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-700/50 hover:text-gray-900 dark:hover:text-slate-200' }}"
                           :title="sidebarCollapsed ? '{{ $menu->name }}' : ''"
                           :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3'">
                            <div class="w-5 h-5 opacity-75 flex-shrink-0 flex items-center justify-center">
                                <i class="{{ $menu->icon }} text-xl"></i>
                            </div>
                            <span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">{{ $menu->name }}</span>
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
                        <div x-data="{ expanded: {{ $isActiveGroup ? 'true' : 'false' }} }" class="pt-2 relative">
                            <button @click="if(!sidebarCollapsed) { expanded = !expanded } else { sidebarCollapsed = false; expanded = true; }"
                                    class="w-full flex items-center py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-700/50 hover:text-gray-900 dark:hover:text-slate-200 transition-colors"
                                    :title="sidebarCollapsed ? '{{ $menu->name }}' : ''"
                                    :class="sidebarCollapsed ? 'justify-center px-0' : 'justify-between px-3'">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 h-5 opacity-75 flex-shrink-0 flex items-center justify-center">
                                        <i class="{{ $menu->icon }} text-xl"></i>
                                    </div>
                                    <span x-show="!sidebarCollapsed" x-transition>{{ $menu->name }}</span>
                                </div>
                                <svg x-show="!sidebarCollapsed" :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <!-- Sub Menu Items -->
                            <div x-show="expanded && !sidebarCollapsed" x-collapse x-cloak class="mt-1 space-y-1 pl-11 pr-3">
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
                                       class="block px-3 py-2 rounded-md text-sm transition-colors
                                              {{ $isChildActive
                                                  ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 font-semibold'
                                                  : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700/50' }}">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>

            <!-- Bottom Profile Summary -->
            <div class="border-t border-gray-100 dark:border-slate-700" :class="sidebarCollapsed ? 'p-2' : 'p-4'">
                <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'gap-3'">
                    <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-400 flex items-center justify-center font-bold uppercase flex-shrink-0">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-slate-200 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ auth()->user()->roles->pluck('name')->join(', ') ?: 'No Role' }}</p>
                    </div>
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
                            <img class="w-8 h-8 rounded-full border border-gray-200 dark:border-slate-600 object-cover" src="{{ asset('images/logo.png') }}" alt="User">
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
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50/50 dark:bg-slate-900/50">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- SweetAlert2 Session Toasts -->
    @if(session('success') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
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
            });
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
</body>
</html>
