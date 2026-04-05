<?php

namespace App\Console\Commands;

use App\Mail\DomainExpiringMail;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CheckDomainExpiryCommand extends Command
{
    protected $signature = 'sites:check-domain {--site= : Belirli bir site ID}';
    protected $description = 'WHOIS API ile domain bitiş tarihlerini kontrol et';

    public function handle(): int
    {
        $apiKey = Setting::get('whois_api_key') ?? config('services.whois.api_key');

        if (!$apiKey) {
            $this->error('WHOIS_API_KEY tanımlı değil. Ayarlar sayfasından veya .env dosyasından girin.');
            return self::FAILURE;
        }

        $query = Site::where('is_active', true);

        if ($siteId = $this->option('site')) {
            $query->where('id', $siteId);
        }

        $sites = $query->get();
        $warningDays = (int) (Setting::get('domain_warning_days') ?? config('services.whois.warning_days', 30));
        $adminEmail = Setting::get('admin_email') ?? config('services.sitewatch.admin_email');

        $this->info("Kontrol edilecek site sayısı: {$sites->count()}");

        foreach ($sites as $site) {
            $domain = $site->domain;

            if (!$domain) {
                $this->warn("  [{$site->name}] Domain çıkarılamadı, atlanıyor.");
                continue;
            }

            $this->line("  Kontrol ediliyor: {$domain}");

            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                ])->get("https://whoisjson.com/api/v1/whois", [
                    'domain' => $domain,
                ]);

                if (!$response->successful()) {
                    $this->warn("    API hatası: HTTP {$response->status()}");
                    continue;
                }

                $data = $response->json();

                // expires alanını bul - API farklı anahtarlar döndürebilir
                $expiresRaw = $data['expires']
                    ?? $data['expiration_date']
                    ?? $data['registry_expiry_date']
                    ?? $data['registrar_registration_expiration_date']
                    ?? null;

                if (!$expiresRaw) {
                    $this->warn("    Bitiş tarihi bulunamadı.");
                    $site->update(['domain_checked_at' => now()]);
                    continue;
                }

                $expiresAt = Carbon::parse($expiresRaw);
                $daysRemaining = (int) now()->diffInDays($expiresAt, false);

                $site->update([
                    'domain_expires_at' => $expiresAt,
                    'domain_checked_at' => now(),
                ]);

                $this->info("    Bitiş: {$expiresAt->format('d.m.Y')} ({$daysRemaining} gün kaldı)");

                // Uyarı gönder
                if ($daysRemaining <= $warningDays && $daysRemaining > 0) {
                    $this->sendWarning($site, $daysRemaining, $expiresAt, $adminEmail);
                } elseif ($daysRemaining <= 0) {
                    $this->sendExpiredWarning($site, $expiresAt, $adminEmail);
                }

            } catch (\Exception $e) {
                $this->error("    Hata: {$e->getMessage()}");
            }
        }

        $this->info('Domain kontrolleri tamamlandı.');
        return self::SUCCESS;
    }

    private function sendWarning(Site $site, int $daysRemaining, Carbon $expiresAt, ?string $adminEmail): void
    {
        // Bugün zaten bildirim gönderilmiş mi?
        $alreadySent = Notification::where('site_id', $site->id)
            ->where('type', 'domain_expiry')
            ->whereDate('sent_at', today())
            ->exists();

        if ($alreadySent) {
            return;
        }

        // Bildirim kaydı
        Notification::create([
            'site_id' => $site->id,
            'type'    => 'domain_expiry',
            'channel' => 'mail',
            'subject' => "{$site->name} — domain {$daysRemaining} gün içinde sona eriyor",
            'message' => "{$site->domain} domaini {$expiresAt->format('d.m.Y')} tarihinde sona erecek.",
            'sent_at' => now(),
        ]);

        // Mail gönder
        if ($adminEmail) {
            Mail::to($adminEmail)->queue(new DomainExpiringMail($site, $daysRemaining, $expiresAt));
        }

        $this->warn("    Uyarı gönderildi: {$daysRemaining} gün kaldı");
    }

    private function sendExpiredWarning(Site $site, Carbon $expiresAt, ?string $adminEmail): void
    {
        $alreadySent = Notification::where('site_id', $site->id)
            ->where('type', 'domain_expiry')
            ->whereDate('sent_at', today())
            ->exists();

        if ($alreadySent) {
            return;
        }

        Notification::create([
            'site_id' => $site->id,
            'type'    => 'domain_expiry',
            'channel' => 'mail',
            'subject' => "{$site->name} — domain süresi dolmuş!",
            'message' => "{$site->domain} domaini {$expiresAt->format('d.m.Y')} tarihinde sona erdi.",
            'sent_at' => now(),
        ]);

        if ($adminEmail) {
            Mail::to($adminEmail)->queue(new DomainExpiringMail($site, 0, $expiresAt));
        }

        $this->error("    Domain süresi dolmuş!");
    }
}
