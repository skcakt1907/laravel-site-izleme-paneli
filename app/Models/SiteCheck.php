<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteCheck extends Model
{
    // Bu tabloda Laravel'in created_at/updated_at kolonları yok
    public $timestamps = false;

    // Toplu atama yapılabilecek alanlar
    protected $fillable = [
        'site_id',
        'status_code',
        'response_time_ms',
        'is_up',
        'consecutive_failures',
        'error_message',
        'checked_at',
    ];

    // Veri tipi dönüşümleri
    protected $casts = [
        'is_up'        => 'boolean',
        'checked_at'   => 'datetime',
        'status_code'  => 'integer',
    ];

    // ---------- İLİŞKİLER ----------

    // Bu kontrol kaydı hangi siteye ait
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
