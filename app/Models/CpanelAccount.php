<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpanelAccount extends Model
{
    // Toplu atama yapılabilecek alanlar
    protected $fillable = [
        'site_id',
        'domain',
        'username',
        'api_token',
        'disk_used_mb',
        'disk_limit_mb',
        'disk_usage_percent',
        'last_checked_at',
    ];

    // Veri tipi dönüşümleri
    protected $casts = [
        'api_token'          => 'encrypted', // API token şifreli saklanır
        'disk_usage_percent' => 'decimal:2',
        'last_checked_at'    => 'datetime',
    ];

    // ---------- İLİŞKİLER ----------

    // Bu cPanel hesabı hangi siteye ait
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    // ---------- YARDIMCI METODLAR ----------

    // Disk doluluk oranı tehlikeli mi? (%90 üstü)
    public function isDiskCritical(): bool
    {
        return $this->disk_usage_percent >= 90;
    }
}
