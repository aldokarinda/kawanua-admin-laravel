<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class TwoFactorAuth extends Model
{
    protected $table = 'two_factor_auth';

    protected $fillable = [
        'user_id',
        'secret',
        'recovery_codes',
        'enabled',
        'enabled_at',
        'last_verified_at',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'recovery_codes' => 'encrypted:json',
        'enabled_at' => 'datetime',
        'last_verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSecretDecryptedAttribute(): string
    {
        return Crypt::decryptString($this->secret);
    }

    public function isEnabled(): bool
    {
        return $this->enabled && $this->enabled_at !== null;
    }

    public function verifyRecoveryCode(string $code): bool
    {
        $codes = $this->recovery_codes ?? [];
        $index = array_search(hash('sha256', $code), $codes);

        if ($index !== false) {
            unset($codes[$index]);
            $this->recovery_codes = array_values($codes);
            $this->save();
            return true;
        }

        return false;
    }
}
