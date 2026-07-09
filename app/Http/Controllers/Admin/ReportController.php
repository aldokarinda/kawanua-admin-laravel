<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:report.view', only: ['visitors', 'transactions']),
        ];
    }

    public function visitors()
    {
        $dates = [];
        $pageViews = [];
        $uniqueVisitors = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dates[] = $date->format('M d');
            // Seed semi-random numbers
            $unique = rand(600, 1800);
            $uniqueVisitors[] = $unique;
            $pageViews[] = (int) ($unique * rand(18, 32) / 10);
        }

        $deviceStats = [
            'labels' => ['Desktop', 'Mobile', 'Tablet'],
            'series' => [62, 28, 10],
        ];

        $trafficSources = [
            'labels' => ['Organic Search', 'Direct Traffic', 'Referrals', 'Social Media'],
            'series' => [45, 30, 15, 10]
        ];

        return view('admin.reports.visitors', compact('dates', 'pageViews', 'uniqueVisitors', 'deviceStats', 'trafficSources'));
    }

    public function transactions()
    {
        $months = ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        
        // Mock revenue for last 12 months
        $revenue = [12450, 14200, 13800, 16500, 19200, 24800, 21000, 23500, 26400, 25800, 28900, 32400];
        $transactionCounts = [310, 340, 325, 390, 440, 520, 480, 510, 560, 540, 590, 680];

        $statusStats = [
            'labels' => ['Success', 'Pending', 'Failed'],
            'series' => [88, 8, 4]
        ];

        return view('admin.reports.transactions', compact('months', 'revenue', 'transactionCounts', 'statusStats'));
    }
}
