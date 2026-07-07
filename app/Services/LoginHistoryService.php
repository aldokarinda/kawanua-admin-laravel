<?php

namespace App\Services;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class LoginHistoryService
{
    public function logAttempt(User $user, string $status, ?string $reason = null): LoginHistory
    {
        return LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'status' => $status,
            'reason' => $reason,
            'location' => $this->resolveLocation(Request::ip()),
            'login_at' => now(),
        ]);
    }

    public function logSuccess(User $user): LoginHistory
    {
        $user->update(['last_login_at' => now()]);
        return $this->logAttempt($user, 'success');
    }

    public function logFailure(User $user, string $reason): LoginHistory
    {
        return $this->logAttempt($user, 'failed', $reason);
    }

    public function logLockout(User $user): LoginHistory
    {
        return $this->logAttempt($user, 'locked', 'rate_limit');
    }

    public function getPaginatedLogs(
        ?int $userId = null,
        ?string $status = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        int $perPage = 20
    ): LengthAwarePaginator {
        $query = LoginHistory::with('user')->latest('login_at');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('login_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('login_at', '<=', $dateTo);
        }

        return $query->paginate($perPage);
    }

    public function getStats(): array
    {
        return [
            'total_logins' => LoginHistory::count(),
            'successful_logins' => LoginHistory::successful()->count(),
            'failed_logins' => LoginHistory::failed()->count(),
            'locked_accounts' => LoginHistory::locked()->count(),
            'recent_failures' => LoginHistory::failed()->recent()->count(),
            'unique_ips_24h' => LoginHistory::where('login_at', '>=', now()->subDay())
                ->distinct('ip_address')
                ->count('ip_address'),
            'failed_last_hour' => LoginHistory::failed()
                ->where('login_at', '>=', now()->subHour())
                ->count(),
        ];
    }

    public function getRecentFailures(int $limit = 20): array
    {
        return LoginHistory::failed()
            ->with('user')
            ->recent()
            ->latest('login_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getUserLoginHistory(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return LoginHistory::where('user_id', $user->id)
            ->latest('login_at')
            ->paginate($perPage);
    }

    public function purgeOldLogs(int $days = 90): int
    {
        return LoginHistory::where('login_at', '<', now()->subDays($days))->delete();
    }

    protected function resolveLocation(string $ip): ?string
    {
        // In production, integrate with geoip package.
        // For now return null — can be extended with maxmind/geoip later.
        return null;
    }
}
