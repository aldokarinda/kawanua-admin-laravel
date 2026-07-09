{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Security Center', 'url' => route('admin.security.index')], ['label' => 'Active Sessions']]" />
        
        <div class="sm:flex sm:items-center sm:justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200">Active Sessions</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Manage all active sessions across the application. You can terminate individual sessions or force logout all users.</p>
            </div>
            @if(count($sessions) > 0)
                <div class="mt-4 sm:mt-0">
                    <form action="{{ route('admin.security.sessions.flush') }}" method="POST" id="flush-form">
                        @csrf
                        <button type="button" onclick="confirmDelete('flush-form', 'ALL active sessions')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 shadow-sm transition-colors">
                            <i class="bi bi-trash-fill mr-2"></i> Terminate All Sessions
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="table-responsive-desktop overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">User Agent</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Last Activity</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700 bg-white dark:bg-slate-800">
                        @forelse($sessions as $session)
                            @php
                                $sessionUser = $session->user_id ? \App\Models\User::find($session->user_id) : null;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-400 flex items-center justify-center font-semibold text-sm uppercase">
                                            {{ $sessionUser ? substr($sessionUser->name, 0, 1) : 'G' }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $sessionUser ? $sessionUser->name : 'Guest' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-slate-400">{{ $sessionUser ? $sessionUser->email : 'Anonymous' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300 font-mono">
                                    {{ $session->ip_address }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-slate-400 max-w-xs truncate" title="{{ $session->user_agent }}">
                                    {{ $session->user_agent }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                                    {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('admin.security.sessions.destroy', $session->id) }}" method="POST" id="delete-form-{{ $session->id }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-form-{{ $session->id }}', 'this session')" class="text-red-600 hover:text-red-900 dark:hover:text-red-400">
                                            Terminate
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-slate-400">
                                    No active sessions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="cards-responsive-mobile p-4">
                @forelse($sessions as $session)
                    @php
                        $sessionUser = $session->user_id ? \App\Models\User::find($session->user_id) : null;
                    @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-4 mb-3 shadow-sm">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center flex-1 min-w-0">
                                <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-400 flex items-center justify-center font-semibold text-sm uppercase flex-shrink-0">
                                    {{ $sessionUser ? substr($sessionUser->name, 0, 1) : 'G' }}
                                </div>
                                <div class="ml-3 flex-1 min-w-0">
                                    <h3 class="font-semibold text-slate-900 dark:text-slate-200 truncate">{{ $sessionUser ? $sessionUser->name : 'Guest' }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ $sessionUser ? $sessionUser->email : 'Anonymous' }}</p>
                                </div>
                            </div>
                            <span class="ml-2 flex-shrink-0 px-2 py-0.5 text-xs font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block mr-1"></span>Active
                            </span>
                        </div>
                        <div class="mt-2 space-y-1.5 text-sm">
                            <div class="flex items-center gap-2 text-gray-600 dark:text-slate-300">
                                <i class="bi bi-globe text-xs text-gray-400 w-4"></i>
                                <span class="font-mono text-xs">{{ $session->ip_address }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-500 dark:text-slate-400">
                                <i class="bi bi-display text-xs text-gray-400 w-4"></i>
                                <span class="text-xs truncate" title="{{ $session->user_agent }}">{{ \Illuminate\Support\Str::limit($session->user_agent, 50) }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-500 dark:text-slate-400">
                                <i class="bi bi-clock text-xs text-gray-400 w-4"></i>
                                <span class="text-xs">{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="flex justify-end mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                            <form action="{{ route('admin.security.sessions.destroy', $session->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-md hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                                    <i class="bi bi-x-lg text-xs"></i> Terminate
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500 dark:text-slate-400">
                        <i class="bi bi-inbox text-4xl block mb-3 text-gray-300 dark:text-slate-600"></i>
                        <p class="text-sm">No active sessions found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
