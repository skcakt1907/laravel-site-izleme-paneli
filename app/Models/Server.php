<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Http;

class Server extends Model
{
    protected $fillable = [
        'name',
        'whm_host',
        'whm_user',
        'whm_token',
        'whm_port',
    ];

    protected $casts = [
        'whm_token' => 'encrypted',
        'whm_port'  => 'integer',
    ];

    // ---------- İLİŞKİLER ----------

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    // ---------- WHM API ----------

    /**
     * WHM API'den tüm cPanel hesaplarını çek ve sites tablosuna kaydet.
     */
    public function syncAccounts(): array
    {
        $url = "https://{$this->whm_host}:{$this->whm_port}/json-api/listaccts";

        $response = Http::withOptions(['verify' => false])
            ->withHeaders([
                'Authorization' => "whm {$this->whm_user}:{$this->whm_token}",
            ])
            ->get($url);

        if (!$response->successful()) {
            throw new \RuntimeException("WHM API hatası: HTTP {$response->status()}");
        }

        $data = $response->json();
        $accounts = $data['acct'] ?? $data['data']['acct'] ?? [];
        $synced = 0;
        $skipped = 0;

        foreach ($accounts as $acct) {
            $domain = $acct['domain'] ?? null;
            if (!$domain) {
                $skipped++;
                continue;
            }

            $url = 'https://' . $domain;

            // Aynı sunucuda aynı domain varsa güncelle, yoksa oluştur
            $site = Site::updateOrCreate(
                ['server_id' => $this->id, 'url' => $url],
                [
                    'name'             => $acct['user'] ?? $domain,
                    'type'             => 'other',
                    'customer_email'   => $acct['email'] ?? null,
                    'hosting_provider' => $this->name,
                    'server_ip'        => $acct['ip'] ?? $this->whm_host,
                    'is_active'        => ($acct['suspended'] ?? false) ? false : true,
                ]
            );

            // cPanel hesap bilgilerini de kaydet
            $site->cpanelAccount()->updateOrCreate(
                ['site_id' => $site->id],
                [
                    'domain'             => $domain,
                    'username'           => $acct['user'] ?? '',
                    'disk_used_mb'       => $this->parseDisk($acct['diskused'] ?? '0'),
                    'disk_limit_mb'      => $this->parseDisk($acct['disklimit'] ?? '0'),
                    'disk_usage_percent' => $this->calcDiskPercent($acct['diskused'] ?? '0', $acct['disklimit'] ?? '0'),
                    'last_checked_at'    => now(),
                ]
            );

            $synced++;
        }

        return ['synced' => $synced, 'skipped' => $skipped];
    }

    /**
     * WHM'den gelen disk değerini MB'ye çevir (örn: "250M", "1G", "unlimited").
     */
    private function parseDisk(string $value): float
    {
        $value = strtolower(trim($value));
        if ($value === 'unlimited' || $value === '0') {
            return 0;
        }

        if (str_ends_with($value, 'g')) {
            return (float) $value * 1024;
        }

        // Varsayılan MB
        return (float) $value;
    }

    private function calcDiskPercent(string $used, string $limit): float
    {
        $usedMb  = $this->parseDisk($used);
        $limitMb = $this->parseDisk($limit);

        if ($limitMb <= 0) {
            return 0;
        }

        return round(($usedMb / $limitMb) * 100, 2);
    }
}
