<?php

namespace App\Console\Commands;

use App\Mail\SiteDownMail;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Site;
use App\Models\SiteCheck;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CheckSitesCommand extends Command
{
    // Komut adı ve parametreleri
    protected $signature = 'sites:check {--site= : Belirli bir site ID kontrol et}';

    // Komut açıklaması
    protected $description = 'Aktif sitelerin HTTP durumunu kontrol eder (30 dakikada bir çalışır)';

    public function handle(): int
    {
        // Belirli bir site mi yoksa tüm aktif siteler mi kontrol edilecek?
        $query = Site::where('is_active', true);

        if ($siteId = $this->option('site')) {
            $query->where('id', $siteId);
        }

        $sites = $query->get();

        if ($sites->isEmpty()) {
            $this->warn('Kontrol edilecek aktif site bulunamadı.');
            return self::SUCCESS;
        }

        $this->info("Toplam {$sites->count()} site kontrol ediliyor...");

        $downCount = 0;

        foreach ($sites as $site) {
            $this->checkSite($site, $downCount);
        }

        $this->newLine();
        $this->info("Kontrol tamamlandı. Çöken site say��sı: {$downCount}");

        return self::SUCCESS;
    }

    // Tek bir siteyi kontrol et
    private function checkSite(Site $site, int &$downCount): void
    {
        $startTime = microtime(true);
        $isUp = false;
        $statusCode = null;
        $errorMessage = null;

        try {
            // HTTP GET isteği gönder (10 saniye timeout)
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->get($site->url);

            $statusCode = $response->status();
            // 200-399 arası başarılı sayılır
            $isUp = $statusCode >= 200 && $statusCode < 400;

        } catch (\Exception $e) {
            // Bağlantı hatası, timeout vb.
            $errorMessage = $e->getMessage();
            $isUp = false;
        }

        // Yanıt süresini hesapla (milisaniye)
        $responseTime = (int) ((microtime(true) - $startTime) * 1000);

        // Son kontrol kaydını al (ard arda hata sayısı için)
        $lastCheck = $site->checks()->latest('checked_at')->first();
        $consecutiveFailures = $lastCheck ? $lastCheck->consecutive_failures : 0;

        if (!$isUp) {
            $consecutiveFailures++;
            $downCount++;
        } else {
            $consecutiveFailures = 0; // Başarılı olursa sıfırla
        }

        // Kontrol kaydını veritabanına yaz
        SiteCheck::create([
            'site_id'              => $site->id,
            'status_code'          => $statusCode,
            'response_time_ms'     => $responseTime,
            'is_up'                => $isUp,
            'consecutive_failures' => $consecutiveFailures,
            'error_message'        => $errorMessage,
            'checked_at'           => now(),
        ]);

        // Konsol çıktısı
        if ($isUp) {
            $this->line("  <fg=green>✓</> {$site->name} — {$statusCode} ({$responseTime}ms)");
        } else {
            $this->line("  <fg=red>✗</> {$site->name} — " . ($errorMessage ?? "HTTP {$statusCode}"));
        }

        // Site çöktüyse ve ard arda 2+ başarısız ise mail gönder
        if (!$isUp && $consecutiveFailures >= 2) {
            $this->sendDownNotification($site, $statusCode, $errorMessage, $consecutiveFailures);
        }
    }

    // Site çöktüğünde bildirim gönder
    private function sendDownNotification(Site $site, ?int $statusCode, ?string $errorMessage, int $failures): void
    {
        // Admin e-posta adresini config'den al
        $adminEmail = Setting::get('admin_email') ?? config('services.sitewatch.admin_email', 'admin@example.com');

        // Mail gönder
        try {
            Mail::to($adminEmail)->send(new SiteDownMail($site, $statusCode, $errorMessage, $failures));

            // Müşteriye de mail gönder (varsa)
            if ($site->customer_email) {
                Mail::to($site->customer_email)->send(new SiteDownMail($site, $statusCode, $errorMessage, $failures));
            }
        } catch (\Exception $e) {
            $this->error("  Mail gönderilemedi: {$e->getMessage()}");
        }

        // Bildirim kaydı oluştur
        Notification::create([
            'site_id' => $site->id,
            'type'    => 'site_down',
            'channel' => 'mail',
            'subject' => "{$site->name} sitesi çöktü!",
            'message' => "Site {$failures} kez ard arda yanıt vermedi. Durum kodu: " . ($statusCode ?? 'N/A') . ". Hata: " . ($errorMessage ?? 'Yok'),
            'sent_at' => now(),
        ]);

        $this->warn("  → Bildirim gönderildi ({$failures}. ard arda hata)");
    }
}
