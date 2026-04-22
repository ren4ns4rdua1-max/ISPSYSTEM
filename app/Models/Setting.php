<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'content', 'group', 'type'];

    protected $casts = [
        'content' => 'array', // For potential JSON values later
    ];

    public static function getValue($key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
        return static::where('key', $key)->first()?->content ?? $default;
        });
    }

public static function scopeWelcome($query)
    {
        return $query->where('group', 'welcome');
    }

}

