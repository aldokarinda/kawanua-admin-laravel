<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function index()
    {
        // Cache dashboard data for 5 minutes
        $chartData = cache()->remember('dashboard_chart_data', 300, function () {
            return $this->dashboardService->getChartData();
        });

        $stats = cache()->remember('dashboard_stats', 300, function () {
            return $this->dashboardService->getStats();
        });

        return view('dashboard', compact('chartData', 'stats'));
    }
}
