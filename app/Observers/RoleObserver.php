<?php

namespace App\Observers;

use Spatie\Permission\Models\Role;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class RoleObserver
{
    private function log($action, Role $role)
    {
        if (Auth::check()) {
            try {
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => $action,
                    'module' => 'Role Management',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'details' => $role->getChanges()
                ]);
            } catch (\Exception $e) {}
        }
    }

    public function created(Role $role): void { $this->log('Created', $role); }
    public function updated(Role $role): void { $this->log('Updated', $role); }
    public function deleted(Role $role): void { $this->log('Deleted', $role); }
}
