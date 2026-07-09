{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Security Center', 'url' => route('admin.security.index')], ['label' => 'Activity Log']]" />
        
        <div class="sm:flex sm:items-center sm:justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200">Security Activity Log</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Review all administrative activities and system changes captured by the audit system.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <form method="GET" class="flex gap-2">
                    <select name="module" class="text-sm border border-gray-300 rounded-md dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600">
                        <option value="">All Modules</option>
                        @foreach($modules as $m)
                            <option value="{{ $m }}" {{ request('module') === $m ? 'selected' : '' }}>{{ ucwords($m) }}</option>
                        @endforeach
                    </select>
                    <select name="action" class="text-sm border border-gray-300 rounded-md dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600">
                        <option value="">All Actions</option>
                        @foreach($actions as $a)
                            <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucwords($a) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-slate-600 text-sm rounded-md bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('admin.security.activity-log') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-slate-600 text-sm rounded-md bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                        Clear
                    </a>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Module</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700 bg-white dark:bg-slate-800">
                        @forelse($logs as $log)
                            <tr x-data="{ open: false }">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-400 flex items-center justify-center font-semibold text-sm uppercase">
                                            {{ $log->user ? substr($log->user->name, 0, 1) : 'S' }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $log->user ? $log->user->name : 'System' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-slate-400">{{ $log->user ? $log->user->email : 'System Action' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                        {{ ucwords($log->module) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700 dark:text-slate-300">
                                    {{ ucwords($log->action) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400 font-mono">
                                    {{ $log->ip_address }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                                    {{ $log->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="open = !open" class="text-primary-600 hover:text-primary-900 dark:hover:text-primary-400">
                                        <span x-text="open ? 'Hide' : 'Show'"></span>
                                    </button>
                                    
                                    {{-- Slide-down Details Box --}}
                                    <div x-show="open" x-collapse x-cloak class="text-left bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg p-4 mt-2 max-w-lg overflow-x-auto whitespace-pre-wrap">
                                        <code class="text-xs text-gray-800 dark:text-slate-200 font-mono">
                                            @if($log->details)
                                                {{ json_encode($log->details, JSON_PRETTY_PRINT) }}
                                            @else
                                                No extra details available.
                                            @endif
                                        </code>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-slate-400">
                                    No activity logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
