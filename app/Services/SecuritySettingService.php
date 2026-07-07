<?php

namespace App\Services;

use App\Models\SecuritySetting;
use Illuminate\Support\Collection;

class SecuritySettingService
{
    public function getAllSettings(): Collection
    {
        return SecuritySetting::orderBy('key')->get();
    }

    public function getGroupedSettings(): array
    {
        return [
            'password_policy' => [
                'title' => 'Password Policy',
                'icon' => 'bi-shield-lock',
                'description' => 'Configure password strength requirements for all users.',
                'settings' => SecuritySetting::whereIn('key', [
                    'password_min_length',
                    'password_require_uppercase',
                    'password_require_numeric',
                    'password_require_special',
                ])->get(),
            ],
            'login_security' => [
                'title' => 'Login Security',
                'icon' => 'bi-shield-check',
                'description' => 'Control login attempts, lockout behavior, and brute-force protection.',
                'settings' => SecuritySetting::whereIn('key', [
                    'max_login_attempts',
                    'lockout_duration_minutes',
                    'session_lifetime_hours',
                ])->get(),
            ],
            'two_factor' => [
                'title' => 'Two-Factor Authentication',
                'icon' => 'bi-shield-shaded',
                'description' => 'Manage two-factor authentication policies.',
                'settings' => SecuritySetting::whereIn('key', [
                    'enforce_2fa',
                ])->get(),
            ],
            'ip_access' => [
                'title' => 'IP Access Control',
                'icon' => 'bi-globe',
                'description' => 'Configure IP whitelist/blacklist and access restrictions.',
                'settings' => SecuritySetting::whereIn('key', [
                    'ip_whitelist_enabled',
                ])->get(),
            ],
            'session_control' => [
                'title' => 'Session Control',
                'icon' => 'bi-clock-history',
                'description' => 'Manage session timeouts and auto-logout behavior.',
                'settings' => SecuritySetting::whereIn('key', [
                    'auto_logout_inactive_minutes',
                ])->get(),
            ],
            'notifications' => [
                'title' => 'Security Notifications',
                'icon' => 'bi-bell',
                'description' => 'Configure security-related notification preferences.',
                'settings' => SecuritySetting::whereIn('key', [
                    'notify_failed_login',
                    'notify_new_device',
                ])->get(),
            ],
        ];
    }

    public function updateSetting(string $key, mixed $value): SecuritySetting
    {
        $setting = SecuritySetting::where('key', $key)->firstOrFail();

        if (is_bool($value)) {
            $setting->type = 'boolean';
            $setting->value = $value ? 'true' : 'false';
        } elseif (is_int($value)) {
            $setting->type = 'integer';
            $setting->value = (string) $value;
        } elseif (is_array($value)) {
            $setting->type = 'json';
            $setting->value = json_encode($value);
        } else {
            $setting->value = (string) $value;
        }

        $setting->save();
        return $setting;
    }

    public function bulkUpdate(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $setting = SecuritySetting::where('key', $key)->first();
            if ($setting) {
                $this->updateSetting($key, $value);
            }
        }
    }

    /**
     * Validate a password against current policy.
     */
    public function validatePassword(string $password): array
    {
        $errors = [];
        $minLength = SecuritySetting::get('password_min_length', 8);

        if (strlen($password) < $minLength) {
            $errors[] = "Password must be at least {$minLength} characters.";
        }

        if (SecuritySetting::get('password_require_uppercase', true)) {
            if (!preg_match('/[A-Z]/', $password)) {
                $errors[] = 'Password must contain at least one uppercase letter.';
            }
        }

        if (SecuritySetting::get('password_require_numeric', true)) {
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = 'Password must contain at least one number.';
            }
        }

        if (SecuritySetting::get('password_require_special', true)) {
            if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                $errors[] = 'Password must contain at least one special character.';
            }
        }

        return $errors;
    }

    public function getPasswordPolicySummary(): string
    {
        $parts = [];
        $parts[] = 'Min ' . SecuritySetting::get('password_min_length', 8) . ' chars';

        if (SecuritySetting::get('password_require_uppercase', true)) $parts[] = 'uppercase';
        if (SecuritySetting::get('password_require_numeric', true)) $parts[] = 'number';
        if (SecuritySetting::get('password_require_special', true)) $parts[] = 'special char';

        return implode(', ', $parts);
    }
}
