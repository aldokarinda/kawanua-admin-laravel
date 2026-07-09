{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="[['label' => 'Reports', 'url' => '#'], ['label' => 'Visitor Statistics', 'url' => route('admin.reports.visitors')]]" />

        <div class="mt-4">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Visitor Statistics</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">View your app's visitor traffic.</p>
        </div>

        <div class="mt-6 glass-panel rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-6 flex flex-col items-center justify-center min-h-[400px]">
            <i class="bi bi-graph-up text-6xl text-slate-300 dark:text-slate-600 mb-4"></i>
            <h3 class="text-lg font-medium text-slate-900 dark:text-white">Coming Soon</h3>
            <p class="text-slate-500 dark:text-slate-400 mt-2 text-center max-w-md">The visitor statistics dashboard is currently under development. Here you will be able to view daily, weekly, and monthly traffic insights.</p>
        </div>
    </div>
</x-admin-layout>
