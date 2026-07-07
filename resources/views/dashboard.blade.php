<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[['label' => 'Dashboard']]" />

        <!-- Welcome Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200 tracking-tight">
                Welcome back, {{ auth()->user()->name }}!
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                Here's what's happening with your business today.
            </p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 transition-all hover:shadow-md hover:border-primary-200 dark:hover:border-primary-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Total Users</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-slate-200">{{ \App\Models\User::count() }}</p>
                        <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">
                            <span class="inline-flex items-center gap-0.5">
                                @php $activeCount = \App\Models\User::where('is_active', true)->count(); @endphp
                                {{ $activeCount }} active
                            </span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 bg-primary-50 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Total Roles -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 transition-all hover:shadow-md hover:border-primary-200 dark:hover:border-primary-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Total Roles</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-slate-200">{{ Spatie\Permission\Models\Role::count() }}</p>
                        <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                            Role-based access control
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Total Menus -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 transition-all hover:shadow-md hover:border-primary-200 dark:hover:border-primary-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Total Menus</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-slate-200">{{ \App\Models\Menu::count() }}</p>
                        <p class="mt-1 text-xs text-cyan-600 dark:text-cyan-400">
                            Sidebar navigation items
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 bg-cyan-50 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 transition-all hover:shadow-md hover:border-primary-200 dark:hover:border-primary-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Last Login</p>
                        <p class="mt-2 text-lg font-bold text-gray-900 dark:text-slate-200">
                            {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'N/A' }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-slate-400 truncate max-w-[180px]" title="{{ auth()->user()->email }}">
                            {{ auth()->user()->email }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:border-primary-200 dark:hover:border-primary-700 transition-all group">
                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="text-xs font-medium text-gray-700 dark:text-slate-300">Manage Users</span>
                </a>
                <a href="{{ route('admin.roles.index') }}" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:border-amber-200 dark:hover:border-amber-700 transition-all group">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span class="text-xs font-medium text-gray-700 dark:text-slate-300">Manage Roles</span>
                </a>
                <a href="{{ route('admin.menus.index') }}" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 hover:border-cyan-200 dark:hover:border-cyan-700 transition-all group">
                    <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                    <span class="text-xs font-medium text-gray-700 dark:text-slate-300">Menu Builder</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-all group">
                    <svg class="w-6 h-6 text-gray-500 dark:text-slate-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="text-xs font-medium text-gray-700 dark:text-slate-300">Your Profile</span>
                </a>
            </div>
        </div>

        <!-- Charts Placeholder -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700">
                <h3 class="font-semibold text-gray-800 dark:text-slate-200 mb-4">Tren Penjualan</h3>
                <div class="h-48 flex items-center justify-center text-gray-400 dark:text-slate-500 bg-gray-50 dark:bg-slate-700/50 rounded-lg border border-dashed border-gray-200 dark:border-slate-600">
                    [Charts will be integrated with ApexCharts or Chart.js]
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700">
                <h3 class="font-semibold text-gray-800 dark:text-slate-200 mb-4">Arus Kas</h3>
                <div class="h-48 flex items-center justify-center text-gray-400 dark:text-slate-500 bg-gray-50 dark:bg-slate-700/50 rounded-lg border border-dashed border-gray-200 dark:border-slate-600">
                    [Charts will be integrated with ApexCharts or Chart.js]
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
