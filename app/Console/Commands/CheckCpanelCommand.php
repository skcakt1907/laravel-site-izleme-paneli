<?php

namespace App\Console\Commands;

use App\Models\CpanelAccount;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckCpanelCommand extends Command
{
    protected $signature = 'sites:check-cpanel';
    protected $description = 'cPanel hesaplarının disk kullanımını kontrol eder';

    public function handle(): int
    {
        // cPanel hesabı tanımlı aktif siteleri al
        $accounts = CpanelAccount::with('site')->whereHas('site', function ($q) {
            $q->where('is_active', true);
        })->get();

        if ($accounts->isEmpty()) {
            $this->warn('cPanel hesabı tanımlı aktif site bulunamadı.');
            return self::SUCCESS;
        }

        $this->info("Toplam {$accounts->count()} cPanel hesabı kontrol ediliyor...");

        foreach ($accounts as $account) {
            $this->checkDiskUsage($account);
        }

        $this->newLine();
        $this->info('cPanel kontrolü tamamlandı.');

        return self::SUCCESS;
    }

    // Tek bir cPanel hesabının disk kullanımını kontrol et
    private function checkDiskUsage(CpanelAccount $account): void
    {
        try {
            // cPanel UAPI ile disk kullanım bilgisini çek
            // Endpoint: https://domain:2083/execute/Quota/get_local_quota_info
            $response = Http::withHeaders([
                'Authorization' => "cpanel {$account->username}:{$account->api_token}",
            ])
            ->withOptions([
                'verify' => false, // Self-signed sertifika sorunu için
            ])
            ->timeout(15)
            ->get("https://{$account->domain}:2083/execute/Quota/get_local_quota_info");

            if (!$response->successful()) {
                throw new \Exception("HTTP {$response->status()} yanıtı alındı");
            }

            $data = $response->json();

            // cPanel API yanıt kontrolü
            if (!isset($data['result']['data'])) {
                throw new \Exception('API yanıtı beklenmeyen formatta');
            }

            $quota = $data['result']['data'];

            // Disk bilgilerini hesapla (cPanel megabyte cinsinden döner)
            $diskUsedMb = isset($quota['bytes_used'])
                ? (int) ($quota['bytes_used'] / 1024 / 1024)
                : (int) ($quota['megabytes_used'] ?? 0);

            $diskLimitMb = isset($quota['byte_limit']) && $quota['byte_limit'] > 0
                ? (int) ($quota['byte_limit'] / 1024 / 1024)
                : (int) ($quota['megabyte_limit'] ?? 0);

            // Doluluk yüzdesi
            $usagePercent = $diskLimitMb > 0
                ? round(($diskUsedMb / $diskLimitMb) * 100, 2)
                : 0;

            // Veritabanını güncelle
            $account->update([
                'disk_used_mb'       => $diskUsedMb,
                'disk_limit_mb'      => $diskLimitMb,
                'disk_usage_percent' => $usagePercent,
                'last_checked_at'    => now(),
            ]);

            // Konsol çıktısı
            $warningThreshold = config('sitewatch.disk_warning_percent', 90);

            if ($usagePercent >= $warningThreshold) {
                $this->line("  <fg=red>!</> {$account->site->name} — %{$usagePercent} ({$diskUsedMb}MB / {$diskLimitMb}MB)");
                $this->sendDiskWarning($account, $usagePercent, $diskUsedMb, $diskLimitMb);
            } else {
                $this->line("  <fg=green>✓</> {$account->site->name} — %{$usagePercent} ({$diskUsedMb}MB / {$diskLimitMb}MB)");
            }

        } catch (\Exception $e) {
            $this->error("  ✗ {$account->site->name} — {$e->getMessage()}");
        }
    }

    // Disk doluluk uyarısı gönder
    private function sendDiskWarning(CpanelAccount $account, float $percent, int $used, int $limit): void
    {
        // Bugün zaten bildirim gönderilmiş mi?
        $alreadySent = Notification::where('site_id', $account->site_id)
            ->where('type', 'disk_warning')
            ->whereDate('sent_at', today())
            ->exists();

        if ($alreadySent) {
            return;
        }

        Notification::create([
            'site_id' => $account->site_id,
            'type'    => 'disk_warning',
            'channel' => 'mail',
            'subject' => "{$account->site->name} — Disk doluluk uyarısı (%{$percent})",
            'message' => "Disk kullanımı: {$used}MB / {$limit}MB (%{$percent}). Acil müdahale gerekebilir.",
            'sent_at' => now(),
        ]);

        $this->warn("  → Disk uyarı bildirimi oluşturuldu");
    }
}
