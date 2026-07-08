<?php

namespace App\Services;

use ParagonIE\ConstantTime\Base32;

class SimpleTOTP
{
    protected string $secret;
    protected string $label = '';
    protected string $issuer = '';

    public function __construct(string $secret)
    {
        // Remove spaces and make uppercase
        $this->secret = strtoupper(str_replace(' ', '', $secret));
    }

    public static function create(string $secret): self
    {
        return new self($secret);
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function setIssuer(string $issuer): void
    {
        $this->issuer = $issuer;
    }

    public function getProvisioningUri(): string
    {
        $label = rawurlencode($this->label);
        $issuer = rawurlencode($this->issuer);
        return "otpauth://totp/{$issuer}:{$label}?secret={$this->secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
    }

    public function verify(string $code, ?int $timestamp = null, int $window = 2): bool
    {
        $timestamp = $timestamp ?? time();
        $currentTimeStep = floor($timestamp / 30);

        for ($i = -$window; $i <= $window; $i++) {
            if ($this->calculateCode($currentTimeStep + $i) === $code) {
                return true;
            }
        }

        return false;
    }

    protected function calculateCode(int $timeStep): string
    {
        // Decode the secret from Base32
        $secretKey = Base32::decodeUpper($this->secret);

        // Pack the time step as a 64-bit big-endian integer binary string
        $timeBin = pack('N*', 0, $timeStep);

        // Calculate HMAC-SHA1
        $hashBin = hash_hmac('sha1', $timeBin, $secretKey, true);

        // Determine offset from the last byte of the hash
        $offset = ord($hashBin[19]) & 0xf;

        // Extract 4 bytes starting from offset
        $truncatedHash = substr($hashBin, $offset, 4);

        // Unpack as 32-bit unsigned integer
        $num = unpack('N', $truncatedHash)[1];

        // Mask the most significant bit
        $num = $num & 0x7fffffff;

        // Generate 6-digit code
        $code = $num % 1000000;

        return str_pad((string)$code, 6, '0', STR_PAD_LEFT);
    }
}
