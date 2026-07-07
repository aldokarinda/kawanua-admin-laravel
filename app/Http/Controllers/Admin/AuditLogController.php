<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    protected $auditLogService;

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
