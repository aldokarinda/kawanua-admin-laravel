{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Create Role</h2>
                <p class="text-sm text-gray-500 mt-1">Define a new role and its permissions.</p>
            </div>
            <a href="{{ route('admin.roles.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">&larr; Back to roles</a>
        </div>

        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            
            <div class="space-y-10 divide-y divide-gray-100 dark:divide-slate-700">
                
                <!-- Role Details Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-slate-200">Role Details</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">Define the basic information for this role. Role names should be clear and descriptive.</p>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Role Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-shadow" placeholder="e.g. Administrator" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                                    <input type="text" name="description" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-shadow" placeholder="Role description">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-10">
                    <div class="lg:col-span-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-slate-200">Permission Matrix</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">Assign granular permissions to this role. Grouped by module for easier management.</p>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                            @if($permissions->count() > 0)
                                <div class="space-y-6">
                                    @foreach($groupedPermissions as $module => $perms)
                                        <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-5 border border-gray-100 dark:border-slate-600">
                                            <h4 class="text-sm font-bold text-gray-800 dark:text-slate-200 uppercase tracking-wider mb-4">{{ $module }}</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                                @foreach($perms as $perm)
                                                    <label class="inline-flex items-center bg-white dark:bg-slate-800 p-2.5 rounded-lg border border-gray-200 dark:border-slate-600 cursor-pointer hover:border-blue-300 dark:hover:border-blue-500 transition-colors shadow-sm w-full">
                                                        <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 shrink-0">
                                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300 truncate">{{ explode('.', $perm->name)[1] ?? $perm->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic">No permissions available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-8 flex flex-col sm:flex-row items-center justify-end gap-3 w-full">
                    <a href="{{ route('admin.roles.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-700 hover:bg-gray-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">Cancel</a>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-lg text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-colors">
                        <i class="bi bi-check-lg mr-2"></i> Save Role
                    </button>
                </div>

            </div>
        </form>
    </div>
</x-admin-layout>
