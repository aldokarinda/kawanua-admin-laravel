<?php

namespace App\Services;

use App\Models\User;

class DashboardService
{
    public function getChartData()
    {
        // Data for User Registrations Trend (Last 6 Months)
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

        // Data for Active vs Inactive Users
        $activeUsersCount = User::where('is_active', true)->count();
        $inactiveUsersCount = User::where('is_active', false)->count();

        return [
            'registration_labels' => $months->toArray(),
            'registration_data' => $userRegistrations->toArray(),
            'user_status_data' => [$activeUsersCount, $inactiveUsersCount]
        ];
    }
}
