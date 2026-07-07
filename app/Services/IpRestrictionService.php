<?php

namespace App\Services;

use App\Models\IpRestriction;
use App\Models\SecuritySetting;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class IpRestrictionService
{
    public function getPaginatedRestrictions(string $type = 'blacklist', int $perPage = 15): LengthAwarePaginator
    {
        return IpRestriction::where('type', $type)
            ->with('creator')
            ->latest()
            ->paginate($perPage);
    }

    public function createRestriction(array $data): IpRestriction
    {
        return IpRestriction::create([
            'ip_address' => $data['ip_address'],
            'type' => $data['type'] ?? 'blacklist',
            'reason' => $data['reason'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'created_by' => auth()->id(),
            'expires_at' => $data['expires_at'] ?? null,
        ]);
    }

    public function updateRestriction(IpRestriction $restriction, array $data): IpRestriction
    {
        $restriction->update([
            'ip_address' => $data['ip_address'],
            'reason' => $data['reason'] ?? $restriction->reason,
            'is_active' => $data['is_active'] ?? $restriction->is_active,
            'expires_at' => $data['expires_at'] ?? $restriction->expires_at,
        ]);

        return $restriction;
    }

    public function deleteRestriction(IpRestriction $restriction): bool
    {
        return $restriction->delete();
    }

    public function toggleStatus(IpRestriction $restriction): IpRestriction
    {
        $restriction->update(['is_active' => !$restriction->is_active]);
        return $restriction;
    }

    /**
     * Check if the current request IP is allowed.
     */
    public function isAllowed(?string $ip = null): bool
    {
        $ip = $ip ?? Request::ip();
        $whitelistEnabled = SecuritySetting::get('ip_whitelist_enabled', false);

        // Check blacklist first
        if (IpRestriction::isIpInList($ip, 'blacklist')) {
            return false;
        }

        // If whitelist is enabled, check whitelist
        if ($whitelistEnabled) {
            return IpRestriction::isIpInList($ip, 'whitelist');
        }

        return true;
    }

    public function getStats(): array
    {
        return [
            'whitelist_count' => IpRestriction::whitelist()->active()->count(),
            'blacklist_count' => IpRestriction::blacklist()->active()->count(),
            'total_restrictions' => IpRestriction::active()->count(),
        ];
    }
}
