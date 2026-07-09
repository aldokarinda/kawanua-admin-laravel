{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
<x-admin-layout>
    <div class="space-y-6">
        <x-breadcrumb :items="[['label' => 'Master Data', 'url' => '#'], ['label' => 'Regions', 'url' => route('admin.regions.index')]]" />

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Regions</h1>
            @can('region.create')
                <a href="{{ route('admin.regions.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-medium text-white hover:bg-primary-700">
                    <i class="bi bi-plus-lg mr-2"></i> Add Region
                </a>
            @endcan
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="table-responsive-desktop overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Parent</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($regions as $region)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">{{ $region->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400 capitalize">{{ $region->type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">{{ $region->parent ? $region->parent->name : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @can('region.edit')
                                        <a href="{{ route('admin.regions.edit', $region->id) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 mr-3">Edit</a>
                                    @endcan
                                    @can('region.delete')
                                        <form action="{{ route('admin.regions.destroy', $region->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-slate-500 dark:text-slate-400">No regions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="cards-responsive-mobile p-4">
                @forelse($regions as $region)
                    <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-4 mb-3 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-slate-900 dark:text-white truncate">{{ $region->name }}</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 capitalize">{{ $region->type }}</p>
                            </div>
                            <span class="ml-2 flex-shrink-0 px-2 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-full">
                                {{ $region->parent ? $region->parent->name : 'Root' }}
                            </span>
                        </div>
                        <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-700">
                            @can('region.edit')
                                <a href="{{ route('admin.regions.edit', $region->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 rounded-md hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors">
                                    <i class="bi bi-pencil text-xs"></i> Edit
                                </a>
                            @endcan
                            @can('region.delete')
                                <form action="{{ route('admin.regions.destroy', $region->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-md hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                                        <i class="bi bi-trash text-xs"></i> Delete
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-500 dark:text-slate-400">
                        <i class="bi bi-geo-alt text-4xl block mb-3 text-slate-300 dark:text-slate-600"></i>
                        <p class="text-sm">No regions found.</p>
                    </div>
                @endforelse
            </div>

            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $regions->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
