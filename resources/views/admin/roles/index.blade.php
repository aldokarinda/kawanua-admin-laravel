<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Role Management']
        ]" />

        <!-- Page Header -->
        <div class="sm:flex sm:items-center sm:justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200 tracking-tight">Role Management</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Manage user roles, descriptions, and view permission counts.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-slate-900 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create New Role
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto max-h-[calc(100vh-20rem)] overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50/75 dark:bg-slate-800/75">
                        <tr>
                            <th class="sticky top-0 z-10 bg-gray-50/75 dark:bg-slate-800/75 px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Role Name</th>
                            <th class="sticky top-0 z-10 bg-gray-50/75 dark:bg-slate-800/75 px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Description</th>
                            <th class="sticky top-0 z-10 bg-gray-50/75 dark:bg-slate-800/75 px-6 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Users</th>
                            <th class="sticky top-0 z-10 bg-gray-50/75 dark:bg-slate-800/75 px-6 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Permissions</th>
                            <th class="sticky top-0 z-10 bg-gray-50/75 dark:bg-slate-800/75 px-6 py-3.5 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach($roles as $role)
                        <tr class="hover:bg-primary-50/30 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">{{ $role->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-slate-400 max-w-xs truncate" title="{{ $role->description }}">{{ $role->description ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-slate-600 text-gray-800 dark:text-slate-300">
                                    {{ $role->users_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300">
                                    {{ $role->permissions_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form action="{{ route('admin.roles.clone', $role) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 p-1.5 rounded-md transition-colors" title="Clone Role">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Delete this role?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition-colors" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($roles->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
