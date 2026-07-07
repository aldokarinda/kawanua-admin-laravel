<?php

namespace App\Observers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    private function log($action, User $user)
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id() ?? $user->id,
                'action' => $action,
                'module' => 'User Management',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'details' => $user->getChanges()
            ]);
        } catch (\Exception $e) {}
    }

    public function created(User $user): void { $this->log('Created', $user); }
    public function updated(User $user): void { $this->log('Updated', $user); }
    public function deleted(User $user): void { $this->log('Deleted', $user); }
}
