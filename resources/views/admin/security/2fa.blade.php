<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Security Center', 'url' => route('admin.security.index')], ['label' => 'Two-Factor Authentication']]" />
        
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200">Two-Factor Authentication (2FA)</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Configure and manage 2FA settings for users. It adds an extra layer of protection by requiring a security code at login.</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 max-w-4xl">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Enabled Users</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ number_format($stats['enabled_count']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                        <i class="bi bi-shield-lock-fill text-emerald-600 dark:text-emerald-400 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Adoption Rate</p>
                        @php
                            $rate = $stats['total_users'] > 0 ? ($stats['enabled_count'] / $stats['total_users']) * 100 : 0;
                        @endphp
                        <p class="text-3xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ number_format($rate, 1) }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <i class="bi bi-pie-chart-fill text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">2FA Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Setup Date</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700 bg-white dark:bg-slate-800">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-400 flex items-center justify-center font-semibold text-sm uppercase">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-slate-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->twoFactorAuth && $user->twoFactorAuth->enabled)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Enabled
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Disabled
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                                    {{ $user->twoFactorAuth && $user->twoFactorAuth->enabled_at ? $user->twoFactorAuth->enabled_at->format('M d, Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($user->twoFactorAuth && $user->twoFactorAuth->enabled)
                                        <form action="{{ route('admin.security.2fa-disable', $user->id) }}" method="POST" id="disable-2fa-form-{{ $user->id }}" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('disable-2fa-form-{{ $user->id }}', 'disable Two-Factor Authentication')" class="text-red-600 hover:text-red-900 dark:hover:text-red-400">
                                                Disable
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.security.2fa-setup', $user->id) }}" class="text-primary-600 hover:text-primary-900 dark:hover:text-primary-400 font-semibold">
                                            Setup 2FA
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
