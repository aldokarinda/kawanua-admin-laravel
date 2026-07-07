<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SecuritySetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('security_settings');
        });

        static::deleted(function () {
            Cache::forget('security_settings');
        });
    }

    /**
     * Get a security setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::rememberForever('security_settings', function () {
            return static::all()->keyBy('key');
        });

        $setting = $settings->get($key);

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'integer' => (int) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set a security setting value.
     */
    public static function set(string $key, mixed $value, ?string $type = null): void
    {
        $setting = static::where('key', $key)->first();

        if (is_bool($value)) {
            $type = $type ?? 'boolean';
            $value = $value ? 'true' : 'false';
        } elseif (is_int($value)) {
            $type = $type ?? 'integer';
            $value = (string) $value;
        } elseif (is_array($value)) {
            $type = $type ?? 'json';
            $value = json_encode($value);
        }

        if ($setting) {
            $setting->update(['value' => $value, 'type' => $type]);
        } else {
            static::create(['key' => $key, 'value' => $value, 'type' => $type ?? 'string']);
        }
    }

    public function getValueAttribute(): mixed
    {
        return match ($this->type) {
            'integer' => (int) $this->attributes['value'],
            'boolean' => filter_var($this->attributes['value'], FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->attributes['value'], true),
            default => $this->attributes['value'],
        };
    }
}
