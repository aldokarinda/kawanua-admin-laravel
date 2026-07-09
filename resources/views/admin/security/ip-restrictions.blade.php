<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8" x-data="{ openAddModal: false, editMode: false, currentIp: { id: '', ip_address: '', type: '{{ $type }}', reason: '', expires_at: '', is_active: true } }">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Security Center', 'url' => route('admin.security.index')], ['label' => 'IP Restrictions']]" />
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200">IP Access Control</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Restrict access to the application by Whitelisting or Blacklisting IP addresses.</p>
            </div>
            <div>
                <button @click="openAddModal = true; editMode = false; currentIp = { id: '', ip_address: '', type: '{{ $type }}', reason: '', expires_at: '', is_active: true }" class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition-colors">
                    <i class="bi bi-plus-lg mr-2"></i> Add IP Restriction
                </button>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 max-w-4xl">
            <a href="{{ route('admin.security.ip-restrictions', ['type' => 'whitelist']) }}" class="block bg-white dark:bg-slate-800 rounded-xl shadow-sm border {{ $type === 'whitelist' ? 'border-primary-500 dark:border-primary-400 ring-1 ring-primary-500' : 'border-gray-100 dark:border-slate-700' }} p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Whitelisted IPs</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ number_format($stats['whitelist_count'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                        <i class="bi bi-shield-check text-emerald-600 dark:text-emerald-400 text-xl"></i>
                    </div>
                </div>
            </a>
            <a href="{{ route('admin.security.ip-restrictions', ['type' => 'blacklist']) }}" class="block bg-white dark:bg-slate-800 rounded-xl shadow-sm border {{ $type === 'blacklist' ? 'border-primary-500 dark:border-primary-400 ring-1 ring-primary-500' : 'border-gray-100 dark:border-slate-700' }} p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Blacklisted IPs</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ number_format($stats['blacklist_count'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-50 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                        <i class="bi bi-shield-x text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                </div>
            </a>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="table-responsive-desktop overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Expires At</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700 bg-white dark:bg-slate-800">
                        @forelse($restrictions as $restrict)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-slate-200 font-mono font-semibold">
                                    {{ $restrict->ip_address }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-slate-400 max-w-xs truncate" title="{{ $restrict->reason }}">
                                    {{ $restrict->reason ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <form action="{{ route('admin.security.ip-restrictions.toggle', $restrict->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $restrict->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-400' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $restrict->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $restrict->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                                    {{ $restrict->expires_at ? $restrict->expires_at->format('Y-m-d H:i') : 'Never' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                                    {{ $restrict->created_at->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button @click="
                                        openAddModal = true;
                                        editMode = true;
                                        currentIp = {
                                            id: '{{ $restrict->id }}',
                                            ip_address: '{{ $restrict->ip_address }}',
                                            type: '{{ $restrict->type }}',
                                            reason: '{{ addslashes($restrict->reason) }}',
                                            expires_at: '{{ $restrict->expires_at ? $restrict->expires_at->format('Y-m-d\TH:i') : '' }}',
                                            is_active: {{ $restrict->is_active ? 'true' : 'false' }}
                                        }
                                    " class="text-primary-600 hover:text-primary-900 dark:hover:text-primary-400">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.security.ip-restrictions.destroy', $restrict->id) }}" method="POST" id="delete-ip-form-{{ $restrict->id }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-ip-form-{{ $restrict->id }}', 'this IP restriction')" class="text-red-600 hover:text-red-900 dark:hover:text-red-400">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-slate-400">
                                    No IP restrictions defined.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="cards-responsive-mobile p-4">
                @forelse($restrictions as $restrict)
                    <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-4 mb-3 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-slate-900 dark:text-slate-200 font-mono">{{ $restrict->ip_address }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $restrict->reason ?? '-' }}</p>
                            </div>
                            <form action="{{ route('admin.security.ip-restrictions.toggle', $restrict->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $restrict->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $restrict->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ $restrict->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-500 dark:text-slate-400 mt-2">
                            <span>Expires: {{ $restrict->expires_at ? $restrict->expires_at->format('M d, Y') : 'Never' }}</span>
                            <span>Created: {{ $restrict->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-end space-x-3 mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                            <button @click="
                                openAddModal = true;
                                editMode = true;
                                currentIp = {
                                    id: '{{ $restrict->id }}',
                                    ip_address: '{{ $restrict->ip_address }}',
                                    type: '{{ $restrict->type }}',
                                    reason: '{{ addslashes($restrict->reason) }}',
                                    expires_at: '{{ $restrict->expires_at ? $restrict->expires_at->format('Y-m-d\TH:i') : '' }}',
                                    is_active: {{ $restrict->is_active ? 'true' : 'false' }}
                                }
                            " class="text-primary-600 hover:text-primary-900 dark:hover:text-primary-400">
                                Edit
                            </button>
                            <form action="{{ route('admin.security.ip-restrictions.destroy', $restrict->id) }}" method="POST" id="delete-ip-form-mobile-{{ $restrict->id }}" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('delete-ip-form-mobile-{{ $restrict->id }}', 'this IP restriction')" class="text-red-600 hover:text-red-900 dark:hover:text-red-400">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500"><i class="bi bi-inbox text-4xl block mb-2"></i>No IP restrictions defined.</div>
                @endforelse
            </div>

            @if($restrictions->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-gray-100 dark:border-slate-700">
                    {{ $restrictions->links() }}
                </div>
            @endif
        </div>

        {{-- Add / Edit Modal --}}
        <div x-show="openAddModal" x-transition.opacity class="fixed inset-0 z-50 bg-gray-900/50 dark:bg-black/70 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white dark:bg-slate-800 rounded-xl max-w-md w-full p-6 shadow-xl border border-gray-100 dark:border-slate-700" @click.away="openAddModal = false">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-slate-700 pb-4 mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-slate-200" x-text="editMode ? 'Edit IP Restriction' : 'Add IP Restriction'"></h3>
                    <button @click="openAddModal = false" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form :action="editMode ? '/admin/security/ip-restrictions/' + currentIp.id : '/admin/security/ip-restrictions'" method="POST">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="space-y-4">
                        <div>
                            <label for="ip_address" class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">IP Address</label>
                            <input type="text" name="ip_address" id="ip_address" x-model="currentIp.ip_address" required placeholder="e.g. 192.168.1.1 or 2001:db8::1" class="block w-full rounded-lg border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 bg-white dark:bg-slate-700 text-sm dark:text-slate-200">
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">Access Type</label>
                            <select name="type" id="type" x-model="currentIp.type" :disabled="editMode" class="block w-full rounded-lg border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 bg-white dark:bg-slate-700 text-sm dark:text-slate-200">
                                <option value="whitelist">Whitelist (Always Allow)</option>
                                <option value="blacklist">Blacklist (Block)</option>
                            </select>
                        </div>

                        <div>
                            <label for="reason" class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">Reason / Description</label>
                            <textarea name="reason" id="reason" x-model="currentIp.reason" rows="3" placeholder="Reason for whitelisting or blocking..." class="block w-full rounded-lg border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 bg-white dark:bg-slate-700 text-sm dark:text-slate-200"></textarea>
                        </div>

                        <div>
                            <label for="expires_at" class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">Expiration Date (Optional)</label>
                            <input type="datetime-local" name="expires_at" id="expires_at" x-model="currentIp.expires_at" class="block w-full rounded-lg border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 bg-white dark:bg-slate-700 text-sm dark:text-slate-200">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 dark:border-slate-700 pt-4">
                        <button type="button" @click="openAddModal = false" class="px-4 py-2 border border-gray-200 dark:border-slate-600 text-sm font-semibold rounded-lg text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition-colors">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
