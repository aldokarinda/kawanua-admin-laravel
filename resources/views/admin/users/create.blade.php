{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">

        <!-- Breadcrumb -->
        <x-breadcrumb :items="[
            ['label' => 'Administration', 'url' => '#'],
            ['label' => 'Users', 'url' => route('admin.users.index')],
            ['label' => 'Add User']
        ]" />

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200 tracking-tight">Create User</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Add a new user and assign roles and department.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300">&larr; Back to users</a>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            
            <div class="space-y-10 divide-y divide-gray-100 dark:divide-slate-700">
                
                <!-- Personal Information Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-slate-200">Personal Information</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">Add the user's basic profile information and department details. Make sure the email is active.</p>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-shadow" placeholder="John Doe" required>
                                    @error('name') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-shadow" placeholder="john@example.com" required>
                                    @error('email') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Phone Number</label>
                                    <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-shadow" placeholder="+1 234 567 8900">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Department</label>
                                    <input type="text" name="department" value="{{ old('department') }}" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-shadow" placeholder="e.g. Human Resources">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security & Access Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-10">
                    <div class="lg:col-span-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-slate-200">Security & Access</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">Configure login credentials, assign RBAC roles, and set the account status.</p>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 space-y-8">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-shadow" required>
                                    @error('password') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password_confirmation" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-shadow" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-slate-200 mb-3">Role Assignment</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach($roles as $role)
                                    <label class="inline-flex items-center p-3 border border-gray-200 dark:border-slate-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700/50 hover:border-blue-300 transition-colors">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300">{{ $role->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="p-4 bg-gray-50 dark:bg-slate-700/50 border border-gray-100 dark:border-slate-600 rounded-xl">
                                <label class="flex items-center cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" name="is_active" class="sr-only peer" checked>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    </div>
                                    <div class="ml-4">
                                        <span class="block text-sm font-bold text-gray-900 dark:text-slate-200">Active Account</span>
                                        <span class="block text-xs text-gray-500 dark:text-slate-400 mt-0.5">If disabled, the user will not be able to log in.</span>
                                    </div>
                                </label>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="pt-8 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-700 hover:bg-gray-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">Cancel</a>
                    <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-lg text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-colors">
                        <i class="bi bi-check-lg mr-2"></i> Create User
                    </button>
                </div>

            </div>
        </form>
    </div>
</x-admin-layout>
