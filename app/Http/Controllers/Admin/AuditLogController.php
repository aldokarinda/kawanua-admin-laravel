<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AuditLogController extends Controller implements HasMiddleware
{
    protected $auditLogService;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:audit.view', only: ['index']),
        ];
    }

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    public function index(Request $request)
    {
        $logs = $this->auditLogService->getPaginatedLogs(
            $request->module,
            $request->action
        )->withQueryString();
        
        return view('admin.audit.index', compact('logs'));
    }
}
