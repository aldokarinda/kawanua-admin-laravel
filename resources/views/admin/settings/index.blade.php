<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Administration', 'url' => '#'], ['label' => 'App Settings']]" />
        
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200">App Settings</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Configure global application identity settings, brand assets, and color theme preferences.</p>
        </div>

        <form action="{{ route('admin.app_settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-6">
                <!-- General Identity Card -->
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                    <div class="flex items-start gap-4 mb-6 border-b border-gray-100 dark:border-slate-700 pb-4">
                        <div class="w-10 h-10 rounded-lg bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0 text-primary-600 dark:text-primary-400 text-lg">
                            <i class="bi bi-window-sidebar"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-slate-200">General Identity</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Define how Kawanua Panel presents itself across the system.</p>
                        </div>
                    </div>

                    @php
                        $appName = $settings->firstWhere('key', 'app_name')->value ?? '';
                        $appDesc = $settings->firstWhere('key', 'app_description')->value ?? '';
                        $appLogo = $settings->firstWhere('key', 'app_logo')->value ?? '';
                        $appTheme = $settings->firstWhere('key', 'app_theme')->value ?? 'indigo';
                    @endphp

                    <div class="space-y-5">
                        <!-- App Name -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="max-w-xl">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-200">App Name</label>
                                <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">Application name displayed in sidebar and header.</p>
                            </div>
                            <div class="flex-shrink-0">
                                <input type="text" name="settings[app_name]" value="{{ $appName }}" class="block w-64 rounded-lg border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 bg-white dark:bg-slate-700 text-sm dark:text-slate-200">
                            </div>
                        </div>

                        <!-- App Description -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="max-w-xl">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-200">App Description</label>
                                <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">A brief description of the application.</p>
                            </div>
                            <div class="flex-shrink-0">
                                <input type="text" name="settings[app_description]" value="{{ $appDesc }}" class="block w-64 rounded-lg border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 bg-white dark:bg-slate-700 text-sm dark:text-slate-200">
                            </div>
                        </div>

                        <!-- App Logo -->
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="max-w-xl">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-200">Brand Logo</label>
                                <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">Upload a brand logo (automatic square cropping to 128x128 WebP format).</p>
                            </div>
                            <div class="flex items-center gap-4 w-64 flex-shrink-0">
                                <div class="w-14 h-14 rounded-xl border border-gray-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    @if($appLogo)
                                        <img src="{{ asset('storage/' . $appLogo) }}" class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ asset('images/logo.webp') }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <input type="file" name="app_logo" class="block w-full text-xs text-gray-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-400">
                            </div>
                        </div>

                        <!-- Sidebar Color Theme -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="max-w-xl">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-200">Primary Color Theme</label>
                                <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">Select a brand color configuration for active state elements and layouts.</p>
                            </div>
                            <div class="flex items-center gap-3 w-64 flex-shrink-0">
                                <input type="hidden" name="settings[app_theme]" id="app_theme_input" value="{{ $appTheme }}">
                                @foreach([
                                    'indigo' => 'bg-indigo-600 ring-indigo-500',
                                    'blue' => 'bg-blue-600 ring-blue-500',
                                    'emerald' => 'bg-emerald-600 ring-emerald-500',
                                    'purple' => 'bg-purple-600 ring-purple-500',
                                    'rose' => 'bg-rose-600 ring-rose-500'
                                ] as $themeKey => $classes)
                                    <button type="button" onclick="selectTheme('{{ $themeKey }}')" class="w-8 h-8 rounded-full {{ $classes }} transition-all focus:outline-none theme-btn-selector @if($appTheme === $themeKey) ring-4 ring-offset-2 dark:ring-offset-slate-900 @endif" data-theme="{{ $themeKey }}"></button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Save App Settings
                </button>
            </div>
        </form>

        <!-- System Backup Card -->
        <div class="mt-8 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
            <div class="flex items-start gap-4 mb-6 border-b border-gray-100 dark:border-slate-700 pb-4">
                <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0 text-amber-600 dark:text-amber-400 text-lg">
                    <i class="bi bi-database-down"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-slate-200">Database SQL Backup</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Download a complete SQL structure and data snapshot of the active application database.</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="max-w-xl">
                    <p class="text-xs text-gray-500 dark:text-slate-400">The generated backup contains structure and data tables, ready for restoration. Driver connection: <code class="bg-slate-100 dark:bg-slate-900 px-1.5 py-0.5 rounded font-mono text-amber-600">{{ config('database.default') }}</code></p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.backup.download') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-amber-300 dark:border-amber-700 text-sm font-semibold rounded-lg text-amber-700 dark:text-amber-400 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/20 dark:hover:bg-amber-900/20 shadow-sm transition-colors focus:outline-none">
                        <i class="bi bi-download"></i>
                        Download SQL Backup
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectTheme(theme) {
            document.getElementById('app_theme_input').value = theme;
            document.querySelectorAll('.theme-btn-selector').forEach(btn => {
                btn.classList.remove('ring-4', 'ring-offset-2', 'dark:ring-offset-slate-900');
                if (btn.getAttribute('data-theme') === theme) {
                    btn.classList.add('ring-4', 'ring-offset-2', 'dark:ring-offset-slate-900');
                }
            });
        }
    </script>
</x-admin-layout>
