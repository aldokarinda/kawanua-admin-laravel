{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
<x-admin-layout>
    <div class="space-y-6"
         x-data="{
                selected: [],
                selectAll: false,
                showCreateModal: false,
                errors: {},
                formData: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    department: '',
                    phone_number: '',
                    is_active: true,
                    roles: []
                },
                toggleSelectAll() {
                    this.selected = this.selectAll
                        ? Array.from(this.$el.querySelectorAll('tbody input[type=checkbox]')).map(cb => cb.value)
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
                },
                submitCreateForm() {
                    this.errors = {};
                    fetch('{{ route("admin.users.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            name: this.formData.name,
                            email: this.formData.email,
                            password: this.formData.password,
                            password_confirmation: this.formData.password_confirmation,
                            department: this.formData.department,
                            phone_number: this.formData.phone_number,
                            is_active: this.formData.is_active ? 1 : 0,
                            roles: this.formData.roles
                        })
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (response.ok && data.success) {
                            this.showCreateModal = false;
                            this.formData = {
                                name: '',
                                email: '',
                                password: '',
                                password_confirmation: '',
                                department: '',
                                phone_number: '',
                                is_active: true,
                                roles: []
                            };
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message || 'User created successfully.',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            this.reloadTable();
                        } else {
                            this.errors = data.errors || { general: data.message || 'An error occurred.' };
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        this.errors = { general: 'Failed to save user.' };
                    });
                },
                reloadTable() {
                    fetch(window.location.href)
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newTable = doc.querySelector('#users-table-container');
                            if (newTable) {
                                document.querySelector('#users-table-container').innerHTML = newTable.innerHTML;
                                this.selected = [];
                                this.selectAll = false;
                            }
                        });
                }
            }">
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[
            ['label' => 'Administration', 'url' => '#'],
            ['label' => 'Users']
        ]" />

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200 tracking-tight">Users</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">A list of all the users in your account including their name, department, email and role.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users.export') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-slate-600 text-sm font-medium rounded-md text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-slate-900 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export
                </a>
                <button type="button" @click="showCreateModal = true" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-slate-900 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add New User
                </button>
            </div>
        </div>

        <!-- Data Table Container -->
        <div id="users-table-container" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <!-- Filter Bar -->
            <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
                <!-- Mobile: Collapsible filters -->
                <div class="sm:hidden">
                    <button @click="showFilters = !showFilters" type="button" class="w-full flex items-center justify-between px-3 py-2 bg-gray-100 dark:bg-slate-700 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <span><i class="bi bi-funnel mr-2"></i> Filters</span>
                        <i class="bi" :class="showFilters ? 'bi-chevron-up' : 'bi-chevron-down'" class="ml-2"></i>
                    </button>
                    <div x-show="showFilters" x-transition x-cloak class="mt-3 space-y-3">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-3">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="bi bi-search text-gray-400"></i>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..." class="w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 dark:text-slate-200">
                            </div>
                            <select name="status" class="w-full py-2 px-3 border border-gray-200 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 dark:text-slate-200">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700">Filter</button>
                                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-slate-500">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Desktop: Inline filters -->
                <form method="GET" action="{{ route('admin.users.index') }}" class="hidden sm:flex gap-3 sm:gap-4">
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
            <div class="table-responsive-desktop overflow-x-auto max-h-[calc(100vh-20rem)] overflow-y-auto">
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
                                    <a href="{{ route('admin.users.edit', $user) }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-colors" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    @if($user->id !== 1 && !$user->hasRole('super-admin'))
                                        <button type="button" 
                                            onclick="confirmDelete('delete-form-{{ $user->id }}', 'the user {{ addslashes($user->name) }}')"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif

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

            <!-- Mobile Cards -->
            <div class="cards-responsive-mobile p-4">
                @foreach($users as $user)
                    <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-4 mb-3 shadow-sm">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center flex-1 min-w-0">
                                <div class="w-11 h-11 rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-400 flex items-center justify-center font-bold text-sm uppercase flex-shrink-0 ring-2 ring-white dark:ring-slate-800">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-11 h-11 rounded-full object-cover">
                                    @else
                                        {{ substr($user->name, 0, 1) }}
                                    @endif
                                </div>
                                <div class="ml-3 flex-1 min-w-0">
                                    <h3 class="font-semibold text-slate-900 dark:text-slate-200 truncate">{{ $user->name }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="ml-2 flex-shrink-0">
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-slate-600 text-gray-800 dark:text-slate-300">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></span>
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-1.5 text-sm">
                            <div class="flex items-center text-gray-600 dark:text-slate-400">
                                <i class="bi bi-building text-xs text-gray-400 w-4 mr-2"></i>
                                <span class="text-xs">{{ $user->department ?? 'No department' }}</span>
                            </div>
                            <div class="flex items-center text-gray-500 dark:text-slate-400">
                                <i class="bi bi-clock text-xs text-gray-400 w-4 mr-2"></i>
                                <span class="text-xs">Last login: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-1 mt-2.5">
                            @forelse($user->roles as $role)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 dark:text-slate-500">No Role</span>
                            @endforelse
                        </div>

                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 rounded-md hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors">
                                <i class="bi bi-pencil text-xs"></i> Edit
                            </a>
                            @if($user->id !== 1 && !$user->hasRole('super-admin'))
                                <button type="button" onclick="confirmDelete('delete-form-mobile-{{ $user->id }}', 'the user {{ addslashes($user->name) }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-md hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                                    <i class="bi bi-trash text-xs"></i> Delete
                                </button>
                                <form id="delete-form-mobile-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
                    {{ $users->links() }}
                </div>
            @endif
            <!-- Create User Modal -->
            <div x-show="showCreateModal" 
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" @click="showCreateModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all w-full sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100 dark:border-slate-700"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        
                        <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-slate-200">Add New User</h3>
                            <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-slate-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form @submit.prevent="submitCreateForm()">
                            <div class="p-4 sm:p-6 space-y-6 max-h-[calc(100vh-16rem)] overflow-y-auto">
                                <template x-if="errors.general">
                                    <div class="p-3 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg text-sm" x-text="errors.general"></div>
                                </template>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="formData.name" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-shadow" placeholder="John Doe" required>
                                        <span class="text-red-500 text-xs mt-1 block" x-show="errors.name" x-text="errors.name ? errors.name[0] : ''"></span>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                                        <input type="email" x-model="formData.email" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-shadow" placeholder="john@example.com" required>
                                        <span class="text-red-500 text-xs mt-1 block" x-show="errors.email" x-text="errors.email ? errors.email[0] : ''"></span>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Phone Number</label>
                                        <input type="text" x-model="formData.phone_number" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-shadow" placeholder="+1 234 567 8900">
                                        <span class="text-red-500 text-xs mt-1 block" x-show="errors.phone_number" x-text="errors.phone_number ? errors.phone_number[0] : ''"></span>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Department</label>
                                        <input type="text" x-model="formData.department" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-shadow" placeholder="e.g. Human Resources">
                                        <span class="text-red-500 text-xs mt-1 block" x-show="errors.department" x-text="errors.department ? errors.department[0] : ''"></span>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Password <span class="text-red-500">*</span></label>
                                        <input type="password" x-model="formData.password" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-shadow" required>
                                        <span class="text-red-500 text-xs mt-1 block" x-show="errors.password" x-text="errors.password ? errors.password[0] : ''"></span>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
                                        <input type="password" x-model="formData.password_confirmation" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-shadow" required>
                                        <span class="text-red-500 text-xs mt-1 block" x-show="errors.password_confirmation" x-text="errors.password_confirmation ? errors.password_confirmation[0] : ''"></span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-950 dark:text-slate-200 mb-3">Role Assignment</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach($roles as $role)
                                        <label class="inline-flex items-center p-3 border border-gray-200 dark:border-slate-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700/50 hover:border-blue-300 transition-colors">
                                            <input type="checkbox" x-model="formData.roles" value="{{ $role->name }}" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300">{{ $role->name }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                    <span class="text-red-500 text-xs mt-1 block" x-show="errors.roles" x-text="errors.roles ? errors.roles[0] : ''"></span>
                                </div>

                                <div class="p-4 bg-gray-50 dark:bg-slate-700/50 border border-gray-100 dark:border-slate-600 rounded-xl">
                                    <label class="flex items-center cursor-pointer">
                                        <div class="relative">
                                            <input type="checkbox" x-model="formData.is_active" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        </div>
                                        <div class="ml-4">
                                            <span class="block text-sm font-bold text-gray-900 dark:text-slate-200">Active Account</span>
                                            <span class="block text-xs text-gray-500 dark:text-slate-400 mt-0.5">If disabled, the user will not be able to log in.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="px-4 sm:px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 bg-gray-50 dark:bg-slate-800/50">
                                <button type="button" @click="showCreateModal = false" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-700 hover:bg-gray-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">Cancel</button>
                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-colors">
                                    <i class="bi bi-check-lg mr-2"></i> Save User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Dialog -->
    <x-confirm-dialog />
</x-admin-layout>
