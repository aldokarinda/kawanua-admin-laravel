<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    use HasFactory;

    protected $table = 'app_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('app_settings');
        });

        static::deleted(function () {
            Cache::forget('app_settings');
        });
    }

    /**
     * Get an app setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::rememberForever('app_settings', function () {
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
