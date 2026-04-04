<?php

namespace App\Console\Commands;

use App\Mail\SslExpiringMail;
use App\Models\Notification;
use App\Models\Site;
use App\Models\SslCertificate;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class CheckSslCertificatesCommand extends Command
{
    // Komut adı
    protected $signature = 'sites:check-ssl';

    // Komut açıklaması
    protected $description = 'Aktif sitelerin SSL sertifika durumunu kontrol eder';

    public function handle(): int
    {
        // Sadece HTTPS kullanan aktif siteleri al
        $sites = Site::where('is_active', true)
            ->where('url', 'like', 'https://%')
            ->get();

        if ($sites->isEmpty()) {
            $this->warn('HTTPS kullanan aktif site bulunamadı.');
            return self::SUCCESS;
        }

        $this->info("Toplam {$sites->count()} sitenin SSL sertifikası kontrol ediliyor...");

        foreach ($sites as $site) {
            $this->checkSsl($site);
        }

        $this->newLine();
        $this->info('SSL kontrolü tamamlandı.');

        return self::SUCCESS;
    }

    // Tek bir sitenin SSL sertifikasını kontrol et
    private function checkSsl(Site $site): void
    {
        $host = parse_url($site->url, PHP_URL_HOST);

        if (!$host) {
            $this->error("  ✗ {$site->name} — URL parse edilemedi");
            return;
        }

        try {
            // SSL bağlantısı kur ve sertifika bilgisini al
            $context = stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true,
                    'verify_peer'       => false, // Sertifika doğrulama hatası olsa bile bilgiyi al
                    'verify_peer_name'  => false,
                ],
            ]);

            $stream = @stream_socket_client(
                "ssl://{$host}:443",
                $errno,
                $errstr,
                10, // 10 saniye timeout
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (!$stream) {
                throw new \Exception("Bağlantı kurulamadı: {$errstr}");
            }

            // Sertifika bilgilerini çek
            $params = stream_context_get_params($stream);
            $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
            fclose($stream);

            if (!$cert) {
                throw new \Exception('Sertifika parse edilemedi');
            }

            // Son kullanma tarihini hesapla
            $expiresAt = Carbon::createFromTimestamp($cert['validTo_time_t']);
            $daysRemaining = (int) now()->diffInDays($expiresAt, false);
            $issuer = $cert['issuer']['O'] ?? $cert['issuer']['CN'] ?? 'Bilinmiyor';
            $isValid = $daysRemaining > 0;

            // Veritabanına kaydet veya güncelle
            SslCertificate::updateOrCreate(
                ['site_id' => $site->id],
                [
                    'issuer'          => $issuer,
                    'expires_at'      => $expiresAt->toDateString(),
                    'days_remaining'  => max(0, $daysRemaining),
                    'is_valid'        => $isValid,
                    'last_checked_at' => now(),
                ]
            );

            // Konsol çıktısı
            if ($daysRemaining <= 0) {
                $this->line("  <fg=red>✗</> {$site->name} — SERTİFİKA SÜRESI DOLMUŞ!");
            } elseif ($daysRemaining <= 30) {
                $this->line("  <fg=yellow>!</> {$site->name} — {$daysRemaining} gün kaldı ({$issuer})");
                $this->sendSslWarning($site, $daysRemaining, $expiresAt, $issuer);
            } else {
                $this->line("  <fg=green>✓</> {$site->name} — {$daysRemaining} gün kaldı ({$issuer})");
            }

        } catch (\Exception $e) {
            $this->error("  ✗ {$site->name} — {$e->getMessage()}");

            // Hata durumunda da kaydı güncelle
            SslCertificate::updateOrCreate(
                ['site_id' => $site->id],
                [
                    'issuer'          => 'Bilinmiyor',
                    'expires_at'      => now()->toDateString(),
                    'days_remaining'  => 0,
                    'is_valid'        => false,
                    'last_checked_at' => now(),
                ]
            );
        }
    }

    // SSL süresi dolmak üzereyse uyarı gönder
    private function sendSslWarning(Site $site, int $daysRemaining, Carbon $expiresAt, string $issuer): void
    {
        // Bugün zaten bildirim gönderilmiş mi kontrol et (günde 1 kez yeterli)
        $alreadySent = Notification::where('site_id', $site->id)
            ->where('type', 'ssl_expiry')
            ->whereDate('sent_at', today())
            ->exists();

        if ($alreadySent) {
            return;
        }

        $adminEmail = config('sitewatch.admin_email', 'admin@example.com');

        try {
            Mail::to($adminEmail)->send(new SslExpiringMail($site, $daysRemaining, $expiresAt, $issuer));

            if ($site->customer_email) {
                Mail::to($site->customer_email)->send(new SslExpiringMail($site, $daysRemaining, $expiresAt, $issuer));
            }
        } catch (\Exception $e) {
            $this->error("  Mail gönderilemedi: {$e->getMessage()}");
        }

        // Bildirim kaydı oluştur
        Notification::create([
            'site_id' => $site->id,
            'type'    => 'ssl_expiry',
            'channel' => 'mail',
            'subject' => "{$site->name} — SSL sertifikası {$daysRemaining} gün içinde sona eriyor",
            'message' => "Sertifika ({$issuer}) {$expiresAt->format('d.m.Y')} tarihinde sona erecek.",
            'sent_at' => now(),
        ]);

        $this->warn("  → SSL uyarı bildirimi gönderildi");
    }
}
