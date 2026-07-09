{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Security Center', 'url' => route('admin.security.index')], ['label' => 'Login History']]" />
        <div class="sm:flex sm:items-center sm:justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200">Login History</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Track all login attempts across the system.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <!-- Mobile: Collapsible filter -->
                <div x-data="{ showFilters: false }" class="sm:hidden">
                    <button @click="showFilters = !showFilters" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-slate-600 text-sm rounded-md bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 w-full justify-between focus:outline-none">
                        <span><i class="bi bi-funnel mr-2"></i> Filters</span>
                        <i class="bi" :class="showFilters ? 'bi-chevron-up' : 'bi-chevron-down'" class="ml-2"></i>
                    </button>
                    
                    <div x-show="showFilters" x-transition class="mt-4 p-4 bg-gray-50 dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700">
                        <form method="GET" class="space-y-3">
                            <select name="status" class="w-full text-sm border border-gray-300 rounded-md dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">All Status</option>
                                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="locked" {{ request('status') === 'locked' ? 'selected' : '' }}>Locked</option>
                            </select>
                            <select name="user_id" class="w-full text-sm border border-gray-300 rounded-md dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">All Users</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">From</label>
                                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full text-sm border border-gray-300 rounded-md dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">To</label>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full text-sm border border-gray-300 rounded-md dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                            </div>
                            <div class="flex gap-2 pt-2">
                                <button type="submit" class="flex-1 px-3 py-2 bg-primary-600 text-white text-sm rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">Apply Filter</button>
                                <a href="{{ route('admin.security.login-history') }}" class="px-3 py-2 border border-gray-300 dark:border-slate-600 text-sm rounded-md bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 text-center">Clear</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Desktop: Inline filters -->
                <form method="GET" class="hidden sm:flex gap-2 items-center">
                    <select name="status" class="text-sm border border-gray-300 rounded-md dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Status</option>
                        <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="locked" {{ request('status') === 'locked' ? 'selected' : '' }}>Locked</option>
                    </select>
                    <select name="user_id" class="text-sm border border-gray-300 rounded-md dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="text-sm border border-gray-300 rounded-md dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-primary-500 focus:border-primary-500">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="text-sm border border-gray-300 rounded-md dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-primary-500 focus:border-primary-500">
                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-slate-600 text-sm rounded-md bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500"><i class="bi bi-funnel mr-1"></i> Filter</button>
                    <a href="{{ route('admin.security.login-history') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-slate-600 text-sm rounded-md bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500">Clear</a>
                </form>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="table-responsive-desktop overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50/75 dark:bg-slate-800/75">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">User Agent</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-slate-200">{{ $log->user->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->status === 'success')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300">Success</span>
                                    @elseif($log->status === 'locked')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300">Locked</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Failed</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $log->ip_address }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $log->user_agent }}">{{ \Illuminate\Support\Str::limit($log->user_agent, 60) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ str_replace('_', ' ', $log->reason ?? '-') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->login_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500"><i class="bi bi-inbox text-4xl block mb-2"></i>No login history found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Cards -->
            <div class="cards-responsive-mobile p-4">
                @forelse($logs as $log)
                    <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-4 mb-3 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-slate-900 dark:text-slate-200">{{ $log->user->name ?? 'Unknown' }}</h3>
                                <p class="text-sm text-gray-500 font-mono mt-1">{{ $log->ip_address }}</p>
                            </div>
                            <div>
                                @if($log->status === 'success')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300">Success</span>
                                @elseif($log->status === 'locked')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300">Locked</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Failed</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center text-xs text-gray-500 dark:text-slate-400 mt-2">
                            <span>{{ str_replace('_', ' ', $log->reason ?? '-') }}</span>
                            <span>{{ $log->login_at->diffForHumans() }}</span>
                        </div>
                        <div class="mt-2 text-xs text-gray-400 dark:text-slate-500 truncate" title="{{ $log->user_agent }}">
                            <i class="bi bi-display mr-1"></i> {{ \Illuminate\Support\Str::limit($log->user_agent, 40) }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500"><i class="bi bi-inbox text-4xl block mb-2"></i>No login history found.</div>
                @endforelse
            </div>
            @if($logs->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">{{ $logs->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
