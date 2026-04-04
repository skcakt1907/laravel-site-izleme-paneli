<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\WordpressSite;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckWordPressCommand extends Command
{
    protected $signature = 'sites:check-wordpress';
    protected $description = 'WordPress sitelerinin plugin/tema güncelleme durumunu kontrol eder';

    public function handle(): int
    {
        // WordPress tipi ve WP bilgisi tanımlı aktif siteleri al
        $wpSites = WordpressSite::with('site')->whereHas('site', function ($q) {
            $q->where('is_active', true)->where('type', 'wordpress');
        })->get();

        if ($wpSites->isEmpty()) {
            $this->warn('WordPress bilgisi tanımlı aktif site bulunamadı.');
            return self::SUCCESS;
        }

        $this->info("Toplam {$wpSites->count()} WordPress sitesi kontrol ediliyor...");

        foreach ($wpSites as $wpSite) {
            $this->checkWordPressSite($wpSite);
        }

        $this->newLine();
        $this->info('WordPress kontrolü tamamlandı.');

        return self::SUCCESS;
    }

    // Tek bir WordPress sitesini kontrol et
    private function checkWordPressSite(WordpressSite $wpSite): void
    {
        $siteUrl = rtrim($wpSite->site->url, '/');
        $siteName = $wpSite->site->name;

        try {
            // WordPress REST API ile temel bilgileri çek
            $this->fetchWpVersion($wpSite, $siteUrl);

            // Plugin güncellemelerini kontrol et (Application Password veya API key gerektirir)
            if ($wpSite->api_key) {
                $this->fetchPluginUpdates($wpSite, $siteUrl);
                $this->fetchThemeUpdates($wpSite, $siteUrl);
            }

            $wpSite->update(['last_checked_at' => now()]);

            // Konsol çıktısı
            $pluginUpdates = $wpSite->plugins_update_available;
            $themeUpdates = $wpSite->themes_update_available;
            $totalUpdates = $pluginUpdates + $themeUpdates;

            if ($totalUpdates > 0) {
                $this->line("  <fg=yellow>!</> {$siteName} — WP {$wpSite->wp_version} | {$pluginUpdates} plugin, {$themeUpdates} tema güncelleme bekliyor");
                $this->sendUpdateNotification($wpSite, $pluginUpdates, $themeUpdates);
            } else {
                $this->line("  <fg=green>✓</> {$siteName} — WP {$wpSite->wp_version} | Güncel");
            }

        } catch (\Exception $e) {
            $this->error("  ✗ {$siteName} — {$e->getMessage()}");
        }
    }

    // WordPress versiyonunu çek (herkese açık endpoint)
    private function fetchWpVersion(WordpressSite $wpSite, string $siteUrl): void
    {
        $response = Http::timeout(10)->get("{$siteUrl}/wp-json/");

        if ($response->successful()) {
            $data = $response->json();

            // WordPress versiyon bilgisini al
            if (isset($data['description']) || isset($data['name'])) {
                // wp-json kök endpoint'i WP versiyonunu doğrudan vermez
                // Ancak namespaces içinden kontrol edebiliriz
            }
        }

        // Alternatif: wp-json/wp/v2/ endpoint'inden versiyon çekme denemesi
        $response = Http::timeout(10)->get("{$siteUrl}/wp-json/wp/v2/");

        if ($response->successful()) {
            // Yanıt başlığından WP versiyonunu tespit et
            $wpVersion = $this->extractWpVersion($siteUrl);
            if ($wpVersion) {
                $wpSite->update(['wp_version' => $wpVersion]);
            }
        }
    }

    // RSS veya meta tag'den WP versiyonunu tespit et
    private function extractWpVersion(string $siteUrl): ?string
    {
        try {
            $response = Http::timeout(10)->get($siteUrl);

            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();

            // <meta name="generator" content="WordPress 6.7.1" /> kalıbını ara
            if (preg_match('/<meta[^>]+name=["\']generator["\'][^>]+content=["\']WordPress\s+([\d.]+)["\']/', $html, $matches)) {
                return $matches[1];
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }

    // Plugin güncelleme durumunu kontrol et
    // WordPress Application Passwords veya özel API key ile çalışır
    private function fetchPluginUpdates(WordpressSite $wpSite, string $siteUrl): void
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Basic {$wpSite->api_key}",
            ])
            ->timeout(15)
            ->get("{$siteUrl}/wp-json/wp/v2/plugins");

            if (!$response->successful()) {
                return; // Yetki yoksa sessizce geç
            }

            $plugins = $response->json();

            if (!is_array($plugins)) {
                return;
            }

            $total = count($plugins);
            $updatesAvailable = 0;

            foreach ($plugins as $plugin) {
                // Plugin güncelleme mevcut mu?
                if (isset($plugin['update']) && $plugin['update'] !== 'none' && !empty($plugin['update'])) {
                    $updatesAvailable++;
                }
            }

            $wpSite->update([
                'plugins_total'            => $total,
                'plugins_update_available' => $updatesAvailable,
            ]);

        } catch (\Exception $e) {
            // Plugin API hatası sessizce geçilir
        }
    }

    // Tema güncelleme durumunu kontrol et
    private function fetchThemeUpdates(WordpressSite $wpSite, string $siteUrl): void
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Basic {$wpSite->api_key}",
            ])
            ->timeout(15)
            ->get("{$siteUrl}/wp-json/wp/v2/themes");

            if (!$response->successful()) {
                return;
            }

            $themes = $response->json();

            if (!is_array($themes)) {
                return;
            }

            $updatesAvailable = 0;

            foreach ($themes as $theme) {
                if (isset($theme['update']) && !empty($theme['update'])) {
                    $updatesAvailable++;
                }
            }

            $wpSite->update([
                'themes_update_available' => $updatesAvailable,
            ]);

        } catch (\Exception $e) {
            // Tema API hatası sessizce geçilir
        }
    }

    // Güncelleme bildirimi gönder
    private function sendUpdateNotification(WordpressSite $wpSite, int $plugins, int $themes): void
    {
        // Bugün zaten bildirim gönderilmiş mi?
        $alreadySent = Notification::where('site_id', $wpSite->site_id)
            ->where('type', 'update_available')
            ->whereDate('sent_at', today())
            ->exists();

        if ($alreadySent) {
            return;
        }

        $parts = [];
        if ($plugins > 0) $parts[] = "{$plugins} plugin";
        if ($themes > 0) $parts[] = "{$themes} tema";
        $summary = implode(' ve ', $parts);

        Notification::create([
            'site_id' => $wpSite->site_id,
            'type'    => 'update_available',
            'channel' => 'mail',
            'subject' => "{$wpSite->site->name} — {$summary} güncelleme mevcut",
            'message' => "WordPress {$wpSite->wp_version} üzerinde {$summary} güncellemesi bekliyor.",
            'sent_at' => now(),
        ]);

        $this->warn("  → Güncelleme bildirimi oluşturuldu");
    }
}
