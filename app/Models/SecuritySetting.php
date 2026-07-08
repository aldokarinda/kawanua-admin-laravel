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
     *
     * Stores a plain PHP array in cache (not an Eloquent Collection) to prevent
     * unserialize() failures when the cache is read early in the middleware
     * pipeline before Eloquent classes are autoloaded.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        // Cache stores a plain associative array: ['key' => ['value' => ..., 'type' => ...]]
        $settings = Cache::rememberForever('security_settings', function () {
            return static::all()
                ->keyBy('key')
                ->map(fn ($s) => ['value' => $s->getRawOriginal('value'), 'type' => $s->type])
                ->toArray();
        });

        if (!isset($settings[$key])) {
            return $default;
        }

        $value = $settings[$key]['value'];
        $type  = $settings[$key]['type'];

        return match ($type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json'    => json_decode($value, true),
            default   => $value,
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
