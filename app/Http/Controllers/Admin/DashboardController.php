<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Data for User Registrations Trend (Last 6 Months)
        $months = collect([]);
        $userRegistrations = collect([]);
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date->format('M Y'));
            
            $count = \App\Models\User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
                
            $userRegistrations->push($count);
        }

        // Data for Active vs Inactive Users
        $activeUsersCount = \App\Models\User::where('is_active', true)->count();
        $inactiveUsersCount = \App\Models\User::where('is_active', false)->count();

        $chartData = [
            'registration_labels' => $months->toArray(),
            'registration_data' => $userRegistrations->toArray(),
            'user_status_data' => [$activeUsersCount, $inactiveUsersCount]
        ];

        return view('dashboard', compact('chartData'));
    }
}
