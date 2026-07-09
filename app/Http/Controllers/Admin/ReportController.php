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
        return view('admin.reports.visitors');
    }

    public function transactions()
    {
        return view('admin.reports.transactions');
    }
}
