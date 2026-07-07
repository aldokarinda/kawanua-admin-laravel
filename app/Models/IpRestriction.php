<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpRestriction extends Model
{
    protected $fillable = [
        'ip_address',
        'type',
        'reason',
        'is_active',
        'created_by',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeWhitelist($query)
    {
        return $query->where('type', 'whitelist');
    }

    public function scopeBlacklist($query)
    {
        return $query->where('type', 'blacklist');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Check if an IP is in a specific list.
     */
    public static function isIpInList(string $ip, string $type): bool
    {
        return static::where('ip_address', $ip)
            ->where('type', $type)
            ->active()
            ->exists();
    }

    /**
     * Check if an IP matches a CIDR range.
     */
    public function matchesCidr(string $ip): bool
    {
        if (!str_contains($this->ip_address, '/')) {
            return $this->ip_address === $ip;
        }

        [$subnet, $mask] = explode('/', $this->ip_address);
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ipLong = ip2long($ip);
            $subnetLong = ip2long($subnet);
            $maskLong = -1 << (32 - (int)$mask);
            return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
        }

        return $this->ip_address === $ip;
    }
}
