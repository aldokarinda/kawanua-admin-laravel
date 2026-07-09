{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Security Center', 'url' => route('admin.security.index')], ['label' => 'Security Settings']]" />
        
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200">Security Settings</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Configure global security configurations, password policies, session timeouts, and notifications.</p>
        </div>

        <form action="{{ route('admin.security.settings.update') }}" method="POST">
            @csrf
            <div class="space-y-6">
                @foreach($groupedSettings as $groupKey => $group)
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                        <div class="flex items-start gap-4 mb-6 border-b border-gray-100 dark:border-slate-700 pb-4">
                            <div class="w-10 h-10 rounded-lg bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0 text-primary-600 dark:text-primary-400 text-lg">
                                <i class="bi {{ $group['icon'] }}"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-slate-200">{{ $group['title'] }}</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">{{ $group['description'] }}</p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            @foreach($group['settings'] as $setting)
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div class="max-w-xl">
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-200">
                                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                        </label>
                                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">{{ $setting->description }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if($setting->type === 'boolean')
                                            <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="settings[{{ $setting->key }}]" value="1" {{ $setting->value === 'true' || $setting->value === '1' ? 'checked' : '' }} class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-primary-600"></div>
                                            </label>
                                        @elseif($setting->type === 'integer')
                                            <input type="number" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="block w-24 rounded-lg border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 bg-white dark:bg-slate-700 text-sm dark:text-slate-200">
                                        @else
                                            <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="block w-64 rounded-lg border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 bg-white dark:bg-slate-700 text-sm dark:text-slate-200">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
