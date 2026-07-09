<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="[['label' => 'Reports', 'url' => '#'], ['label' => 'Transaction Reports', 'url' => route('admin.reports.transactions')]]" />

        <div class="mt-4 mb-8">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Transaction Reports</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Track and monitor monthly revenue trends, checkout success metrics, and sales counts.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Revenue & Count Charts - Span 2 -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Sales Revenue Card -->
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">Monthly Sales Revenue</h3>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mb-4">Total revenue trends generated over the last 12 months</p>
                    <div id="revenue-chart" class="w-full min-h-[300px]"></div>
                </div>

                <!-- Transaction Volume Card -->
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">Transaction Volume</h3>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mb-4">Number of completed purchase operations processed per month</p>
                    <div id="volume-chart" class="w-full min-h-[250px]"></div>
                </div>
            </div>

            <!-- Transaction Status & Stats Panel -->
            <div class="space-y-6">
                <!-- Status Chart -->
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Payment Success Rate</h3>
                    <div id="status-chart" class="w-full flex justify-center"></div>
                </div>

                <!-- Financial Highlights -->
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Performance Summary</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900/50 rounded-lg">
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Revenue</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">${{ number_format(array_sum($revenue)) }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900/50 rounded-lg">
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Avg. Monthly Sales</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">${{ number_format(array_sum($revenue) / count($revenue), 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900/50 rounded-lg">
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Transactions</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ number_format(array_sum($transactionCounts)) }}</span>
                        </div>
                    </div>
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

            // 1. Revenue Area Chart
            const revenueOptions = {
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: { show: false },
                    foreColor: isDark ? '#94a3b8' : '#64748b'
                },
                series: [{
                    name: 'Revenue ($)',
                    data: @json($revenue)
                }],
                xaxis: { categories: @json($months) },
                colors: [primaryColor],
                dataLabels: { enabled: false },
                stroke: { curve: 'straight', width: 2 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.3,
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
            new ApexCharts(document.querySelector("#revenue-chart"), revenueOptions).render();

            // 2. Volume Column/Bar Chart
            const volumeOptions = {
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: { show: false },
                    foreColor: isDark ? '#94a3b8' : '#64748b'
                },
                series: [{
                    name: 'Orders',
                    data: @json($transactionCounts)
                }],
                xaxis: { categories: @json($months) },
                colors: ['#3b82f6'],
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '50%'
                    }
                },
                grid: {
                    borderColor: isDark ? '#334155' : '#f1f5f9',
                    strokeDashArray: 4
                },
                tooltip: { theme: isDark ? 'dark' : 'light' }
            };
            new ApexCharts(document.querySelector("#volume-chart"), volumeOptions).render();

            // 3. Payment Status Pie Chart
            const statusOptions = {
                chart: { type: 'donut', height: 200, foreColor: isDark ? '#94a3b8' : '#64748b' },
                series: @json($statusStats['series']),
                labels: @json($statusStats['labels']),
                colors: ['#10b981', '#f59e0b', '#ef4444'],
                legend: { position: 'bottom', fontSize: '12px' },
                dataLabels: { enabled: false },
                tooltip: { theme: isDark ? 'dark' : 'light' }
            };
            new ApexCharts(document.querySelector("#status-chart"), statusOptions).render();
        });
    </script>
</x-admin-layout>
