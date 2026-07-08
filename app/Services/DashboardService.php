<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    /**
     * Return chart data, cached for 5 minutes.
     * Cache key includes current month so it naturally invalidates at month rollover.
     */
    public function getChartData(): array
    {
        $cacheKey = 'dashboard_chart_data_' . now()->format('Y_m');

        return Cache::remember($cacheKey, 300, function () {
            $months = collect([]);
            $userRegistrations = collect([]);

            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $months->push($date->format('M Y'));

                $count = User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $userRegistrations->push($count);
            }

            $activeUsersCount   = User::where('is_active', true)->count();
            $inactiveUsersCount = User::where('is_active', false)->count();

            return [
                'registration_labels' => $months->toArray(),
                'registration_data'   => $userRegistrations->toArray(),
                'user_status_data'    => [$activeUsersCount, $inactiveUsersCount],
            ];
        });
    }

    /**
     * Return basic stat counts, cached for 1 minute.
     */
    public function getStats(): array
    {
        return Cache::remember('dashboard_stats', 60, function () {
            return [
                'total_users'  => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'total_roles'  => \Spatie\Permission\Models\Role::count(),
                'total_menus'  => \App\Models\Menu::count(),
            ];
        });
    }
}
