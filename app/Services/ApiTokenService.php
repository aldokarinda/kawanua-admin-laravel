<?php

namespace App\Services;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenService
{
    // Access token lives 15 minutes
    const ACCESS_TTL_MINUTES  = 15;

    // Refresh token lives 7 days
    const REFRESH_TTL_MINUTES = 60 * 24 * 7;

    /**
     * Issue a fresh access + refresh token pair for a user.
     * Returns the plaintext tokens (only time they are ever visible).
     * Revokes only existing access tokens — old refresh tokens are expired (not deleted)
     * so we can detect reuse (breach detection).
     */
    public function issueTokenPair(User $user): array
    {
        // Expire all existing tokens for this user (don't delete — needed for breach detection)
        $user->tokens()->update(['expires_at' => now()->subSecond()]);

        $accessToken  = $user->createToken('access',  ['access'],  now()->addMinutes(self::ACCESS_TTL_MINUTES));
        $refreshToken = $user->createToken('refresh', ['refresh'], now()->addMinutes(self::REFRESH_TTL_MINUTES));

        return [
            'access_token'       => $accessToken->plainTextToken,
            'refresh_token'      => $refreshToken->plainTextToken,
            'token_type'         => 'Bearer',
            'access_expires_in'  => self::ACCESS_TTL_MINUTES * 60,
            'refresh_expires_in' => self::REFRESH_TTL_MINUTES * 60,
            'access_expires_at'  => now()->addMinutes(self::ACCESS_TTL_MINUTES)->toIso8601String(),
            'refresh_expires_at' => now()->addMinutes(self::REFRESH_TTL_MINUTES)->toIso8601String(),
        ];
    }

    /**
     * Rotate the refresh token — invalidate the provided one and issue a new pair.
     *
     * SECURITY: If a refresh token is presented that has already expired (i.e. it was
     * previously used and rotated), we immediately revoke ALL active tokens for that user
     * to contain the breach.
     */
    public function refreshTokenPair(string $plainTextRefreshToken): array
    {
        [$id, $token] = $this->parseToken($plainTextRefreshToken);

        /** @var PersonalAccessToken|null $record */
        $record = PersonalAccessToken::find($id);

        // Token record not found at all — generic error (don't leak info)
        if (!$record) {
            throw new \RuntimeException('Invalid refresh token.', 401);
        }

        // Must be a refresh-type token
        if (!$record->can('refresh')) {
            throw new \RuntimeException('Invalid token type.', 401);
        }

        // Verify the hash matches
        if (!hash_equals($record->token, hash('sha256', $token))) {
            $record->tokenable->tokens()->delete();
            throw new \RuntimeException('Token integrity check failed.', 401);
        }

        /** @var User $user */
        $user = $record->tokenable;

        // Token is expired — means it was already used in a previous rotation
        // This is a REUSE ATTACK. Revoke everything and alert.
        if ($record->expires_at && now()->isAfter($record->expires_at)) {
            // Breach containment: delete ALL tokens for this user
            $user->tokens()->delete();
            throw new \RuntimeException('Refresh token expired. Please log in again.', 401);
        }

        // All good — issue a new pair (which will expire the current refresh token)
        return $this->issueTokenPair($user);
    }

    /**
     * Revoke all tokens for a user (logout).
     */
    public function revokeAll(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Parse Sanctum plaintext token into [id, token] parts.
     */
    private function parseToken(string $plainText): array
    {
        $parts = explode('|', $plainText, 2);

        if (count($parts) !== 2) {
            throw new \RuntimeException('Malformed token.', 401);
        }

        return [$parts[0], $parts[1]];
    }
}
