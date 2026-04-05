<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSpeedScore extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'site_id',
        'mobile_score',
        'desktop_score',
        'mobile_fcp',
        'desktop_fcp',
        'mobile_lcp',
        'desktop_lcp',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
