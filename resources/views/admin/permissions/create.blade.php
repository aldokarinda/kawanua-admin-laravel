{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Create Permission</h2>
                <p class="text-sm text-gray-500 mt-1">Define a new system permission.</p>
            </div>
            <a href="{{ route('admin.permissions.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">&larr; Back</a>
        </div>

        <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-8">
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Permission Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. user.view" required>
                    <p class="mt-1 text-xs text-gray-500">Use dot notation for grouping (e.g., `user.create`, `menu.edit`).</p>
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.permissions.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm">Save Permission</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
