<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditLogService
{
    public function getPaginatedLogs($module = null, $action = null, $perPage = 20)
    {
        $query = AuditLog::with('user')->latest();

        if ($module) {
            $query->where('module', $module);
        }

        if ($action) {
            $query->where('action', $action);
        }

        return $query->simplePaginate($perPage);
    }
}
