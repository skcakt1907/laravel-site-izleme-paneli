<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WordpressSite extends Model
{
    // Toplu atama yapılabilecek alanlar
    protected $fillable = [
        'site_id',
        'wp_version',
        'admin_url',
        'api_key',
        'plugins_total',
        'plugins_update_available',
        'themes_update_available',
        'last_checked_at',
    ];

    // Veri tipi dönüşümleri
    protected $casts = [
        'api_key'         => 'encrypted', // API anahtarı şifreli saklanır
        'last_checked_at' => 'datetime',
    ];

    // ---------- İLİŞKİLER ----------

    // Bu WordPress bilgisi hangi siteye ait
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    // ---------- YARDIMCI METODLAR ----------

    // Güncelleme bekleyen plugin/tema var mı?
    public function hasUpdates(): bool
    {
        return $this->plugins_update_available > 0 || $this->themes_update_available > 0;
    }
}
