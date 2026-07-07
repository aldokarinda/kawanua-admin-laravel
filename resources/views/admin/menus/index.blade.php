<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Menu Builder']
        ]" />

        <div class="sm:flex sm:items-center sm:justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200 tracking-tight">Menu Builder</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Manage sidebar navigation, hierarchical structures, and RBAC visibility.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.menus.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create New Menu
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
            <div class="flow-root">
                <ul role="list" class="-my-5 divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($menus as $menu)
                        <li class="py-4 border-b border-gray-100 dark:border-slate-700 last:border-0 hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition-colors rounded-lg px-2 -mx-2">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                                        @if($menu->icon)
                                            <i class="{{ $menu->icon }} text-2xl"></i>
                                        @else
                                            <i class="bi bi-grid text-2xl"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-base font-bold text-gray-900 truncate flex items-center">
                                        {{ $menu->name }}
                                        @if(!$menu->is_active)
                                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">Hidden</span>
                                        @endif
                                        @if($menu->permission_name)
                                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                                <i class="bi bi-shield-lock mr-1.5"></i> {{ $menu->permission_name }}
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-500 truncate mt-1">
                                        {{ $menu->route_name ? "Route: {$menu->route_name}" : ($menu->url ? "URL: {$menu->url}" : "Dropdown Group") }} • Order: {{ $menu->order }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('admin.menus.edit', $menu) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form id="delete-form-{{ $menu->id }}" action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-form-{{ $menu->id }}', 'the menu {{ addslashes($menu->name) }}')" class="w-8 h-8 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            @if($menu->children->isNotEmpty())
                                <div class="mt-4 relative before:absolute before:inset-y-0 before:left-6 before:w-px before:bg-gray-200 ml-1">
                                    <ul class="space-y-3 relative">
                                        @foreach($menu->children as $child)
                                            <li class="relative flex items-center justify-between p-3 bg-white hover:bg-gray-50 rounded-xl border border-gray-200 ml-12 transition-all shadow-sm">
                                                <!-- Tree branch line -->
                                                <div class="absolute -left-6 top-1/2 w-6 h-px bg-gray-200"></div>
                                                
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 border border-gray-100">
                                                        @if($child->icon)
                                                            <i class="{{ $child->icon }} text-lg"></i>
                                                        @else
                                                            <i class="bi bi-dash text-lg"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="text-sm font-bold text-gray-800">{{ $child->name }}</span>
                                                        <span class="text-xs text-gray-500 font-mono ml-2 bg-gray-100 px-2 py-0.5 rounded">{{ $child->route_name ?? $child->url }}</span>
                                                        @if($child->permission_name)
                                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                                <i class="bi bi-key mr-1"></i> {{ $child->permission_name }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-end space-x-2">
                                                    <a href="{{ route('admin.menus.edit', $child) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <form id="delete-form-{{ $child->id }}" action="{{ route('admin.menus.destroy', $child) }}" method="POST" class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="button" onclick="confirmDelete('delete-form-{{ $child->id }}', 'the sub-menu {{ addslashes($child->name) }}')" class="w-8 h-8 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-admin-layout>
