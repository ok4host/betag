<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }

    /**
     * Get all settings for a group
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }
}
