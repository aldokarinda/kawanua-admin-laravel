<?php

namespace App\Services;

use App\Models\LoginHistory;
use App\Models\User;
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
    ) {
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

        return $query->simplePaginate($perPage);
    }

    public function getStats(): array
    {
        $baseStats = LoginHistory::selectRaw("
            COUNT(*) as total_logins,
            SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful_logins,
            SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_logins,
            SUM(CASE WHEN status = 'locked' THEN 1 ELSE 0 END) as locked_accounts,
            SUM(CASE WHEN status = 'failed' AND login_at >= ? THEN 1 ELSE 0 END) as recent_failures
        ", [now()->subDays(7)])->first()->toArray();

        $uniqueIps = LoginHistory::where('login_at', '>=', now()->subDay())
            ->distinct()
            ->count('ip_address');

        $failedLastHour = LoginHistory::failed()
            ->where('login_at', '>=', now()->subHour())
            ->count();

        return [
            'total_logins'      => (int) ($baseStats['total_logins'] ?? 0),
            'successful_logins' => (int) ($baseStats['successful_logins'] ?? 0),
            'failed_logins'     => (int) ($baseStats['failed_logins'] ?? 0),
            'locked_accounts'   => (int) ($baseStats['locked_accounts'] ?? 0),
            'recent_failures'   => (int) ($baseStats['recent_failures'] ?? 0),
            'unique_ips_24h'    => $uniqueIps,
            'failed_last_hour'  => $failedLastHour,
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

    public function getUserLoginHistory(User $user, int $perPage = 15)
    {
        return LoginHistory::where('user_id', $user->id)
            ->latest('login_at')
            ->simplePaginate($perPage);
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
