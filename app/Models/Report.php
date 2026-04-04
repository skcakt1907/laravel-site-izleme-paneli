<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    // Bu tabloda Laravel'in created_at/updated_at kolonları yok
    public $timestamps = false;

    // Toplu atama yapılabilecek alanlar
    protected $fillable = [
        'site_id',
        'period',
        'file_path',
        'generated_at',
    ];

    // Veri tipi dönüşümleri
    protected $casts = [
        'generated_at' => 'datetime',
    ];

    // ---------- İLİŞKİLER ----------

    // Bu rapor hangi siteye ait (null olabilir = genel rapor)
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
