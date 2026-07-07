<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'User Management']
        ]" />

        <!-- Page Header -->
        <div class="sm:flex sm:items-center sm:justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200 tracking-tight">User Management</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">A list of all the users in your account including their name, department, email and role.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <button type="button" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-slate-600 text-sm font-medium rounded-md text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-slate-900 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export
                </button>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-slate-900 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add New User
                </a>
            </div>
        </div>

        <!-- Data Table Container -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden"
            x-data="{
                selected: [],
                selectAll: false,
                toggleSelectAll() {
                    this.selected = this.selectAll
                        ? Array.from(this.\$el.querySelectorAll('tbody input[type=checkbox]')).map(cb => cb.value)
                        : [];
                },
                bulkDelete() {
                    window.dispatchEvent(new CustomEvent('confirm', {
                        detail: {
                            title: 'Bulk Delete Users',
                            message: 'Are you sure you want to delete ' + this.selected.length + ' selected user(s)? This action cannot be undone.',
                            confirmText: 'Delete',
                            onConfirm: () => {
                                const form = document.getElementById('bulk-delete-form');
                                document.getElementById('bulk-ids').value = this.selected.join(',');
                                form.submit();
                            }
                        }
                    }));
                }
            }">
            <!-- Filter Bar -->
            <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users by name, email, or department..." class="w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-slate-700 dark:text-slate-200">
                    </div>
                    <div class="sm:w-48">
                        <select name="status" class="w-full py-2 px-3 border border-gray-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-slate-700 dark:text-slate-200" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-4 py-2 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-600 focus:ring-2 focus:ring-primary-500">Filter</button>
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-slate-300 border border-transparent rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-slate-500">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Bulk Action Bar -->
            <div x-show="selected.length > 0" x-transition class="px-4 py-2 bg-primary-50 dark:bg-primary-900/20 border-b border-primary-100 dark:border-primary-800 flex items-center gap-3">
                <span class="text-sm font-medium text-primary-700 dark:text-primary-300" x-text="selected.length + ' user(s) selected'"></span>
                <button @click="bulkDelete()" class="px-3 py-1.5 text-xs font-medium text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">Delete Selected</button>
                <form id="bulk-delete-form" action="{{ route('admin.users.bulk-destroy') }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids" id="bulk-ids">
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto max-h-[calc(100vh-20rem)] overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50/75 dark:bg-slate-800/75">
                        <tr>
                            <th scope="col" class="sticky top-0 z-10 bg-gray-50/75 dark:bg-slate-800/75 px-6 py-3.5 w-10">
                                <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="w-4 h-4 text-primary-600 rounded border-gray-300 dark:border-slate-600 focus:ring-primary-500 dark:bg-slate-700">
                            </th>
                            <th scope="col" class="sticky top-0 z-10 bg-gray-50/75 dark:bg-slate-800/75 px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">User</th>
                            <th scope="col" class="sticky top-0 z-10 bg-gray-50/75 dark:bg-slate-800/75 px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Department</th>
                            <th scope="col" class="sticky top-0 z-10 bg-gray-50/75 dark:bg-slate-800/75 px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Roles</th>
                            <th scope="col" class="sticky top-0 z-10 bg-gray-50/75 dark:bg-slate-800/75 px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="sticky top-0 z-10 bg-gray-50/75 dark:bg-slate-800/75 px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Last Login</th>
                            <th scope="col" class="sticky top-0 z-10 bg-gray-50/75 dark:bg-slate-800/75 relative px-6 py-3.5"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach($users as $user)
                        <tr class="hover:bg-primary-50/30 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" value="{{ $user->id }}" x-model="selected" class="w-4 h-4 text-primary-600 rounded border-gray-300 dark:border-slate-600 focus:ring-primary-500 dark:bg-slate-700">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        @if($user->avatar)
                                            <img class="h-10 w-10 rounded-full object-cover ring-2 ring-white dark:ring-slate-800 shadow-sm" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center text-primary-700 dark:text-primary-400 font-bold uppercase ring-2 ring-white dark:ring-slate-800 shadow-sm">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-slate-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-slate-300">{{ $user->department ?? '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-slate-400">{{ $user->phone_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 dark:text-slate-500">No Role</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-slate-600 text-gray-800 dark:text-slate-300">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3" x-data>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <button type="button"
                                        @click="$dispatch('confirm', {
                                            title: 'Delete User',
                                            message: 'Are you sure you want to delete {{ addslashes($user->name) }}? This action cannot be undone.',
                                            confirmText: 'Delete',
                                            onConfirm() { document.getElementById('delete-form-{{ $user->id }}').submit(); }
                                        })"
                                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition-colors" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Confirm Dialog -->
    <x-confirm-dialog />
</x-admin-layout>
