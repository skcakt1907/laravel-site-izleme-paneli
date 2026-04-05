<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Site extends Model
{
    // Toplu atama yapılabilecek alanlar
    protected $fillable = [
        'server_id',
        'name',
        'url',
        'type',
        'customer_email',
        'hosting_provider',
        'server_ip',
        'php_version',
        'notes',
        'is_active',
    ];

    // Veri tipi dönüşümleri
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ---------- İLİŞKİLER ----------

    // Sitenin bağlı olduğu sunucu
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    // Bir sitenin birçok HTTP kontrol kaydı olabilir
    public function checks(): HasMany
    {
        return $this->hasMany(SiteCheck::class);
    }

    // Bir sitenin bir SSL sertifikası olabilir
    public function sslCertificate(): HasOne
    {
        return $this->hasOne(SslCertificate::class);
    }

    // Bir sitenin bir cPanel hesabı olabilir
    public function cpanelAccount(): HasOne
    {
        return $this->hasOne(CpanelAccount::class);
    }

    // Bir sitenin bir WordPress bilgisi olabilir (sadece type=wordpress)
    public function wordpressSite(): HasOne
    {
        return $this->hasOne(WordpressSite::class);
    }

    // Bir sitenin birçok bildirimi olabilir
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    // Bir sitenin birçok raporu olabilir
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    // ---------- YARDIMCI METODLAR ----------

    // Son HTTP kontrol kaydını getir
    public function latestCheck(): HasOne
    {
        return $this->hasOne(SiteCheck::class)->latestOfMany('checked_at');
    }

    // Site WordPress mi?
    public function isWordpress(): bool
    {
        return $this->type === 'wordpress';
    }

    // Site Laravel mi?
    public function isLaravel(): bool
    {
        return $this->type === 'laravel';
    }
}
