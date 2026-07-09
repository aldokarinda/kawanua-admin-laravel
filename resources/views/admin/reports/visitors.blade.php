<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="[['label' => 'Reports', 'url' => '#'], ['label' => 'Visitor Statistics', 'url' => route('admin.reports.visitors')]]" />

        <div class="mt-4 mb-8">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Visitor Statistics</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">View real-time analysis of visitor counts, page views, and traffic demographics.</p>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Traffic Chart (Line/Area) - Span 2 -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Daily Traffic Overview</h3>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">Page views and unique visitors for the last 30 days</p>
                    </div>
                </div>
                <div id="traffic-chart" class="w-full min-h-[350px]"></div>
            </div>

            <!-- Device Distribution (Donut) & Traffic Sources -->
            <div class="space-y-6">
                <!-- Device Stats -->
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Device Split</h3>
                    <div id="device-chart" class="w-full flex justify-center"></div>
                </div>

                <!-- Traffic Sources -->
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Acquisition Channels</h3>
                    <div id="sources-chart" class="w-full flex justify-center"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Load ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.classList.contains('dark');
            const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--color-primary-600') || '#4f46e5';

            // 1. Traffic Chart (Area)
            const trafficOptions = {
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    foreColor: isDark ? '#94a3b8' : '#64748b'
                },
                series: [{
                    name: 'Page Views',
                    data: @json($pageViews)
                }, {
                    name: 'Unique Visitors',
                    data: @json($uniqueVisitors)
                }],
                xaxis: {
                    categories: @json($dates),
                    labels: { rotate: -45, style: { fontSize: '10px' } }
                },
                colors: [primaryColor, '#10b981'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2.5 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.35,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                grid: {
                    borderColor: isDark ? '#334155' : '#f1f5f9',
                    strokeDashArray: 4
                },
                tooltip: { theme: isDark ? 'dark' : 'light' }
            };
            new ApexCharts(document.querySelector("#traffic-chart"), trafficOptions).render();

            // 2. Device Chart
            const deviceOptions = {
                chart: { type: 'donut', height: 200, foreColor: isDark ? '#94a3b8' : '#64748b' },
                series: @json($deviceStats['series']),
                labels: @json($deviceStats['labels']),
                colors: [primaryColor, '#10b981', '#f59e0b'],
                legend: { position: 'bottom', fontSize: '12px' },
                dataLabels: { enabled: false },
                tooltip: { theme: isDark ? 'dark' : 'light' }
            };
            new ApexCharts(document.querySelector("#device-chart"), deviceOptions).render();

            // 3. Acquisition Channels
            const sourcesOptions = {
                chart: { type: 'pie', height: 200, foreColor: isDark ? '#94a3b8' : '#64748b' },
                series: @json($trafficSources['series']),
                labels: @json($trafficSources['labels']),
                colors: [primaryColor, '#3b82f6', '#10b981', '#a855f7'],
                legend: { position: 'bottom', fontSize: '11px' },
                dataLabels: { enabled: false },
                tooltip: { theme: isDark ? 'dark' : 'light' }
            };
            new ApexCharts(document.querySelector("#sources-chart"), sourcesOptions).render();
        });
    </script>
</x-admin-layout>
