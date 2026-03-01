<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    // ── Read / Write ────────────────────────────────────────────────────────

    /**
     * Get a typed setting value with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return self::cast($setting->value, $setting->type);
    }

    /**
     * Update a setting value by key (must already exist in DB).
     */
    public static function set(string $key, mixed $value): bool
    {
        return (bool) static::where('key', $key)->update(['value' => (string) $value]);
    }

    /**
     * Return all settings grouped by their group key.
     * Structure: ['general' => Collection, 'security' => Collection, ...]
     */
    public static function grouped(): array
    {
        return static::orderBy('group')->orderBy('id')->get()
            ->groupBy('group')
            ->toArray();
    }

    /**
     * Cast a raw string value to the declared PHP type.
     */
    private static function cast(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => (bool)(int) $value,
            'integer' => (int) $value,
            default   => (string) $value,
        };
    }

    // ── Accessor for typed value reading on instances ────────────────────────

    public function getTypedValueAttribute(): mixed
    {
        return self::cast($this->value, $this->type);
    }
}
