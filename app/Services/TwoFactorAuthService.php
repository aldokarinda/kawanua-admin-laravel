<?php

namespace App\Services;

use App\Models\TwoFactorAuth;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

class TwoFactorAuthService
{
    /**
     * Generate a new secret and return the provisioning URI.
     */
    public function generateSecret(User $user): array
    {
        $secret = trim(Base32::encodeUpper(random_bytes(20)), '='); // 32 char base32
        $totp = TOTP::create($secret);
        $totp->setLabel($user->email);
        $totp->setIssuer(config('app.name', 'Kawanua Admin'));

        $twoFactor = TwoFactorAuth::updateOrCreate(
            ['user_id' => $user->id],
            [
                'secret' => Crypt::encryptString($secret),
                'enabled' => false,
            ]
        );

        return [
            'secret' => $secret,
            'qr_url' => $totp->getProvisioningUri(),
            'two_factor' => $twoFactor,
        ];
    }

    /**
     * Enable 2FA for a user after they verify a valid code.
     */
    public function enable(User $user, string $code): bool
    {
        $twoFactor = TwoFactorAuth::where('user_id', $user->id)->first();

        if (!$twoFactor) {
            return false;
        }

        if (!$this->verifyCode($twoFactor->secret_decrypted, $code)) {
            return false;
        }

        $recoveryCodes = $this->generateRecoveryCodes();

        $twoFactor->update([
            'enabled' => true,
            'enabled_at' => now(),
            'last_verified_at' => now(),
            'recovery_codes' => $recoveryCodes,
        ]);

        return true;
    }

    /**
     * Disable 2FA for a user.
     */
    public function disable(User $user): void
    {
        TwoFactorAuth::where('user_id', $user->id)->delete();
    }

    /**
     * Verify a TOTP code.
     */
    public function verify(User $user, string $code): bool
    {
        $twoFactor = TwoFactorAuth::where('user_id', $user->id)
            ->where('enabled', true)
            ->first();

        if (!$twoFactor) {
            return false;
        }

        if ($this->verifyCode($twoFactor->secret_decrypted, $code)) {
            $twoFactor->update(['last_verified_at' => now()]);
            return true;
        }

        return false;
    }

    /**
     * Verify using a recovery code.
     */
    public function verifyRecoveryCode(User $user, string $code): bool
    {
        $twoFactor = TwoFactorAuth::where('user_id', $user->id)
            ->where('enabled', true)
            ->first();

        if (!$twoFactor) {
            return false;
        }

        return $twoFactor->verifyRecoveryCode($code);
    }

    /**
     * Check if user has 2FA enabled.
     */
    public function isEnabled(User $user): bool
    {
        return TwoFactorAuth::where('user_id', $user->id)
            ->where('enabled', true)
            ->exists();
    }

    /**
     * Get remaining recovery codes count.
     */
    public function getRemainingRecoveryCodes(User $user): int
    {
        $twoFactor = TwoFactorAuth::where('user_id', $user->id)->first();
        return $twoFactor ? count($twoFactor->recovery_codes ?? []) : 0;
    }

    /**
     * Regenerate recovery codes.
     */
    public function regenerateRecoveryCodes(User $user): array
    {
        $twoFactor = TwoFactorAuth::where('user_id', $user->id)->first();

        if (!$twoFactor) {
            return [];
        }

        $codes = $this->generateRecoveryCodes();
        $twoFactor->update(['recovery_codes' => $codes]);

        return $codes;
    }

    protected function verifyCode(string $secret, string $code): bool
    {
        $totp = TOTP::create($secret);
        return $totp->verify($code, null, 2); // ±2 windows = ~2 minutes tolerance
    }

    protected function generateRecoveryCodes(int $count = 10): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $code = strtoupper(bin2hex(random_bytes(5))); // 10-char hex
            $codes[] = hash('sha256', $code);
        }
        return $codes;
    }
}
