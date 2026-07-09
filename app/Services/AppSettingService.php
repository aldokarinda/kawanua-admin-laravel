<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Collection;

class AppSettingService
{
    public function getAllSettings(): Collection
    {
        return AppSetting::orderBy('key')->get();
    }

    /**
     * Get a single setting value by key (cached).
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = cache()->remember('app_settings_all', 300, function () {
            return AppSetting::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    public function updateSetting(string $key, mixed $value): AppSetting
    {
        $setting = AppSetting::where('key', $key)->firstOrFail();

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
        $this->clearSettingsCache();
        return $setting;
    }

    public function bulkUpdate(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $setting = AppSetting::where('key', $key)->first();
            if ($setting) {
                $this->updateSetting($key, $value);
            }
        }
        $this->clearSettingsCache();
    }

    /**
     * Clear settings cache after modifications.
     */
    protected function clearSettingsCache(): void
    {
        cache()->forget('app_settings_all');
    }
}
