<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group', 'type'];

    public static function getValue(string $key, string $default = ''): string
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function clearCache(string $key): void
    {
        Cache::forget("setting_{$key}");
    }

    public static function scopeWelcome($query)
    {
        return $query->where('group', 'welcome');
    }
}
