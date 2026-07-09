{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center sm:justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Permission Management</h2>
                <p class="mt-1 text-sm text-gray-500">Manage all system capabilities and access rights.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.permissions.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="bi bi-plus-lg mr-2"></i> Create Permission
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 overflow-hidden">
            <div class="table-responsive-desktop overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Module Group</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Permission Name</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Assigned To</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($permissions as $perm)
                            @php 
                                $module = explode('.', $perm->name)[0] ?? 'general';
                                $colors = [
                                    'user' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                    'role' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'permission' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'menu' => 'bg-fuchsia-100 text-fuchsia-800 border-fuchsia-200',
                                    'audit' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    'dashboard' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                ];
                                $colorClass = $colors[$module] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border uppercase tracking-wider {{ $colorClass }}">
                                        {{ $module }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $perm->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-xs text-gray-500">{{ $perm->roles()->count() }} Roles</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-3">
                                        <a href="{{ route('admin.permissions.edit', $perm) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form id="delete-form-{{ $perm->id }}" action="{{ route('admin.permissions.destroy', $perm) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-form-{{ $perm->id }}', 'the permission {{ addslashes($perm->name) }}')" class="w-8 h-8 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500 text-sm">
                                    <i class="bi bi-shield-slash text-4xl text-gray-300 mb-3 block"></i>
                                    No permissions found. Create one to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="cards-responsive-mobile p-4">
                @forelse($permissions as $perm)
                    @php
                        $module = explode('.', $perm->name)[0] ?? 'general';
                        $colors = [
                            'user' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                            'role' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                            'permission' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                            'menu' => 'bg-fuchsia-100 text-fuchsia-800 dark:bg-fuchsia-900/30 dark:text-fuchsia-400',
                            'audit' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400',
                            'dashboard' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400',
                        ];
                        $colorClass = $colors[$module] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
                    @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-4 mb-3 shadow-sm">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-slate-900 dark:text-white truncate text-sm">{{ $perm->name }}</h3>
                                <span class="text-xs text-gray-500 dark:text-slate-400">{{ $perm->roles()->count() }} Roles</span>
                            </div>
                            <span class="ml-2 flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $colorClass }}">
                                {{ $module }}
                            </span>
                        </div>
                        <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-700">
                            <a href="{{ route('admin.permissions.edit', $perm) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 rounded-md hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors">
                                <i class="bi bi-pencil text-xs"></i> Edit
                            </a>
                            <form id="delete-form-mobile-{{ $perm->id }}" action="{{ route('admin.permissions.destroy', $perm) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('delete-form-mobile-{{ $perm->id }}', 'the permission {{ addslashes($perm->name) }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-md hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                                    <i class="bi bi-trash text-xs"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500 dark:text-slate-400">
                        <i class="bi bi-shield-slash text-4xl text-gray-300 dark:text-slate-600 mb-3 block"></i>
                        <p class="text-sm">No permissions found. Create one to get started.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
