<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center sm:justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Audit Logs</h2>
                <p class="mt-1 text-sm text-gray-500">Track and view all user activities, permission changes, and system events.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 overflow-hidden">
            <!-- Filter Bar -->
            <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                <form method="GET" action="{{ route('admin.audit_logs.index') }}" class="flex flex-col sm:flex-row gap-4">
                    <div class="sm:w-48">
                        <select name="module" class="w-full py-2 px-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white" onchange="this.form.submit()">
                            <option value="">All Modules</option>
                            <option value="User Management" {{ request('module') == 'User Management' ? 'selected' : '' }}>User Management</option>
                            <option value="Role Management" {{ request('module') == 'Role Management' ? 'selected' : '' }}>Role Management</option>
                        </select>
                    </div>
                    <div class="sm:w-48">
                        <select name="action" class="w-full py-2 px-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white" onchange="this.form.submit()">
                            <option value="">All Actions</option>
                            <option value="Created" {{ request('action') == 'Created' ? 'selected' : '' }}>Created</option>
                            <option value="Updated" {{ request('action') == 'Updated' ? 'selected' : '' }}>Updated</option>
                            <option value="Deleted" {{ request('action') == 'Deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500">Filter</button>
                        <a href="{{ route('admin.audit_logs.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 border border-transparent rounded-lg text-sm font-medium hover:bg-gray-200">Reset</a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/75">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Timestamp</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Module</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Details</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($logs as $log)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $log->action == 'Created' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $log->action == 'Updated' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $log->action == 'Deleted' ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->module }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->ip_address }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <button class="text-blue-600 hover:underline text-xs" onclick="showDetails({{ json_encode($log->details) }})">View Details</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">No audit logs found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>

<script>
    function showDetails(details) {
        if (!details) {
            Swal.fire({
                icon: 'info',
                title: 'No Details',
                text: 'There are no additional details for this action.'
            });
            return;
        }
        
        let htmlContent = '<div class="text-left bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm overflow-auto max-h-[60vh]">';
        htmlContent += '<pre class="whitespace-pre-wrap text-gray-700 font-mono text-xs">' + JSON.stringify(details, null, 2) + '</pre>';
        htmlContent += '</div>';

        Swal.fire({
            title: 'Audit Log Details',
            html: htmlContent,
            width: '600px',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                title: 'text-lg font-bold text-gray-900',
                popup: 'rounded-xl',
                htmlContainer: 'mt-4'
            }
        });
    }
</script>
