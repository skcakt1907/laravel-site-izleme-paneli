<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = 'key';
    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /**
     * Ayar değerini getir (cache ile).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = static::find($key);
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Ayar değerini kaydet ve cache'i güncelle.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
    }

    /**
     * Tüm ayar cache'lerini temizle.
     */
    public static function flushCache(): void
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("setting.{$key}");
        }
    }
}
