<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SystemSetting extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'category',
        'is_public',
        'requires_restart',
        'updated_by'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'requires_restart' => 'boolean',
        'value' => 'string'
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("system_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->getCastValue() : $default;
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string', string $category = 'general')
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'category' => $category,
                'updated_by' => auth()->id()
            ]
        );

        // Clear cache
        Cache::forget("system_setting_{$key}");
        
        return $setting;
    }

    /**
     * Get all settings grouped by category
     */
    public static function getGrouped()
    {
        return static::all()->groupBy('category')->map(function ($settings) {
            return $settings->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->getCastValue()];
            });
        });
    }

    /**
     * Get cast value based on type
     */
    public function getCastValue()
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'array' => json_decode($this->value, true),
            'json' => json_decode($this->value),
            default => $this->value
        };
    }

    /**
     * Scope for public settings
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for settings by category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
