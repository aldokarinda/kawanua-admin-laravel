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
            <div class="overflow-x-auto">
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
        </div>
    </div>
</x-admin-layout>
