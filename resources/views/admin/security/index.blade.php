<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Security Center']]" />
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200 tracking-tight">Security Center</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Monitor and manage security across the application. Track login activity, manage 2FA, configure policies, and control access.</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div><p class="text-sm font-medium text-gray-500 dark:text-slate-400">Total Logins</p><p class="text-3xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ number_format($loginStats['total_logins']) }}</p></div>
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center"><i class="bi bi-box-arrow-in-right text-blue-600 dark:text-blue-400 text-xl"></i></div>
                </div>
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-3">{{ number_format($loginStats['unique_ips_24h']) }} unique IPs in 24h</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div><p class="text-sm font-medium text-gray-500 dark:text-slate-400">Successful</p><p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($loginStats['successful_logins']) }}</p></div>
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center"><i class="bi bi-check-circle text-emerald-600 dark:text-emerald-400 text-xl"></i></div>
                </div>
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-3">{{ $loginStats['total_logins'] > 0 ? round(($loginStats['successful_logins'] / $loginStats['total_logins']) * 100) : 0 }}% success rate</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div><p class="text-sm font-medium text-gray-500 dark:text-slate-400">Failed</p><p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1">{{ number_format($loginStats['failed_logins']) }}</p></div>
                    <div class="w-12 h-12 bg-red-50 dark:bg-red-900/30 rounded-xl flex items-center justify-center"><i class="bi bi-x-circle text-red-600 dark:text-red-400 text-xl"></i></div>
                </div>
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-3">{{ $loginStats['failed_last_hour'] }} in last hour</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div><p class="text-sm font-medium text-gray-500 dark:text-slate-400">Locked</p><p class="text-3xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ number_format($loginStats['locked_accounts']) }}</p></div>
                    <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center"><i class="bi bi-lock text-amber-600 dark:text-amber-400 text-xl"></i></div>
                </div>
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-3">IP: {{ $ipStats['blacklist_count'] }} blocked, {{ $ipStats['whitelist_count'] }} allowed</p>
            </div>
        </div>

        {{-- Quick Navigation --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach([
                ['route' => 'admin.security.login-history', 'icon' => 'bi-list-ul', 'color' => 'blue', 'title' => 'Login History', 'desc' => 'View all login attempts, filter by status, date range, and user.'],
                ['route' => 'admin.security.2fa', 'icon' => 'bi-shield-shaded', 'color' => 'purple', 'title' => 'Two-Factor Auth', 'desc' => 'Manage TOTP-based two-factor authentication for users.'],
                ['route' => 'admin.security.settings', 'icon' => 'bi-sliders', 'color' => 'emerald', 'title' => 'Security Settings', 'desc' => 'Configure password policy, lockout rules, session timeouts, and notifications.'],
                ['route' => 'admin.security.ip-restrictions', 'icon' => 'bi-globe', 'color' => 'orange', 'title' => 'IP Restrictions', 'desc' => 'Manage IP whitelist/blacklist rules including CIDR support.'],
                ['route' => 'admin.security.activity-log', 'icon' => 'bi-journal-text', 'color' => 'cyan', 'title' => 'Activity Log', 'desc' => 'Enhanced audit trail of all administrative actions across modules.'],
                ['route' => 'admin.security.sessions', 'icon' => 'bi-people', 'color' => 'rose', 'title' => 'Active Sessions', 'desc' => 'View and terminate active user sessions. Force logout across devices.'],
            ] as $card)
                <a href="{{ route($card['route']) }}" class="group block bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 hover:border-primary-300 dark:hover:border-primary-700 transition-all hover:shadow-md">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-{{ $card['color'] }}-50 dark:bg-{{ $card['color'] }}-900/30 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-{{ $card['color'] }}-100 dark:group-hover:bg-{{ $card['color'] }}-900/50 transition-colors">
                            <i class="bi {{ $card['icon'] }} text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-slate-200 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $card['title'] }}</h3>
                            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">{{ $card['desc'] }}</p>
                            <span class="inline-flex items-center text-xs font-medium text-primary-600 dark:text-primary-400 mt-3">Open <i class="bi bi-arrow-right ml-1"></i></span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Recent Failures --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-x-auto">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 flex items-center gap-2"><i class="bi bi-exclamation-triangle text-red-500"></i>Recent Failed Login Attempts</h3>
                <a href="{{ route('admin.security.login-history', ['status' => 'failed']) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">View all</a>
            </div>
            @if(count($recentFailures) > 0)
                <div class="table-responsive-desktop overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50/75 dark:bg-slate-800/75">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">IP</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($recentFailures as $failure)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-slate-200">{{ $failure['user']['name'] ?? 'Unknown' }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $failure['ip_address'] }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">{{ str_replace('_', ' ', $failure['reason'] ?? 'Unknown') }}</span></td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($failure['login_at'])->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="cards-responsive-mobile p-4">
                    @foreach($recentFailures as $failure)
                        <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-4 mb-3">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="font-semibold text-slate-900 dark:text-slate-200">{{ $failure['user']['name'] ?? 'Unknown' }}</h3>
                                    <p class="text-sm text-gray-500 font-mono mt-1">{{ $failure['ip_address'] }}</p>
                                </div>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($failure['login_at'])->diffForHumans() }}</span>
                            </div>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">{{ str_replace('_', ' ', $failure['reason'] ?? 'Unknown') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

            @else
                <div class="p-8 sm:p-12 text-center">
                    <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-4"><i class="bi bi-shield-check text-emerald-500 text-2xl"></i></div>
                    <p class="text-gray-500 dark:text-slate-400">No recent failed login attempts. Everything looks secure!</p>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>