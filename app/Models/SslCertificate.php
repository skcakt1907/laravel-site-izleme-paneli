<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SslCertificate extends Model
{
    // Toplu atama yapılabilecek alanlar
    protected $fillable = [
        'site_id',
        'issuer',
        'expires_at',
        'days_remaining',
        'is_valid',
        'last_checked_at',
    ];

    // Veri tipi dönüşümleri
    protected $casts = [
        'expires_at'      => 'date',
        'is_valid'        => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    // ---------- İLİŞKİLER ----------

    // Bu sertifika hangi siteye ait
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    // ---------- YARDIMCI METODLAR ----------

    // Sertifika süresi kritik mi? (30 gün veya altında)
    public function isExpiringSoon(): bool
    {
        return $this->days_remaining <= 30;
    }
}
