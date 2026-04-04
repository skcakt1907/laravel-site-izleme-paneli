<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    // Bu tabloda Laravel'in created_at/updated_at kolonları yok
    public $timestamps = false;

    // Toplu atama yapılabilecek alanlar
    protected $fillable = [
        'site_id',
        'type',
        'channel',
        'subject',
        'message',
        'sent_at',
    ];

    // Veri tipi dönüşümleri
    protected $casts = [
        'sent_at' => 'datetime',
    ];

    // ---------- İLİŞKİLER ----------

    // Bu bildirim hangi siteye ait
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
