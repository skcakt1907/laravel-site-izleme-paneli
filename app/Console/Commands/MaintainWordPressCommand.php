<?php

namespace App\Console\Commands;

use App\Mail\MaintenanceReportMail;
use App\Models\Setting;
use App\Models\WordpressSite;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MaintainWordPressCommand extends Command
{
    protected $signature = 'sites:maintain {--site= : Belirli bir site ID} {--dry-run : Güncelleme yapmadan sadece kontrol et}';
    protected $description = 'WordPress sitelerinde plugin ve tema güncellemelerini uygula';

    public function handle(): int
    {
        $query = WordpressSite::with('site')->whereHas('site', function ($q) {
            $q->where('is_active', true)->where('type', 'wordpress');
        })->whereNotNull('api_key');

        if ($siteId = $this->option('site')) {
            $query->where('site_id', $siteId);
        }

        $wpSites = $query->get();

        if ($wpSites->isEmpty()) {
            $this->warn('API key tanımlı aktif WordPress sitesi bulunamadı.');
            return self::SUCCESS;
        }

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->info('[DRY RUN] Güncelleme uygulanmayacak, sadece kontrol edilecek.');
        }

        $this->info("Bakım yapılacak site sayısı: {$wpSites->count()}");

        foreach ($wpSites as $wpSite) {
            $this->maintainSite($wpSite, $dryRun);
        }

        $this->newLine();
        $this->info('Bakım tamamlandı.');
        return self::SUCCESS;
    }

    private function maintainSite(WordpressSite $wpSite, bool $dryRun): void
    {
        $siteName = $wpSite->site->name;
        $siteUrl  = rtrim($wpSite->site->url, '/');
        $results  = ['plugins' => [], 'themes' => [], 'errors' => []];

        $this->newLine();
        $this->info("=== {$siteName} ({$siteUrl}) ===");

        // Plugin güncellemeleri
        $this->updatePlugins($wpSite, $siteUrl, $dryRun, $results);

        // Tema güncellemeleri
        $this->updateThemes($wpSite, $siteUrl, $dryRun, $results);

        // Sonuçları logla
        $this->logResults($wpSite, $results, $dryRun);

        // Müşteriye mail gönder
        if (!$dryRun && (count($results['plugins']) > 0 || count($results['themes']) > 0)) {
            $this->sendReport($wpSite, $results);
        }
    }

    private function updatePlugins(WordpressSite $wpSite, string $siteUrl, bool $dryRun, array &$results): void
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Basic {$wpSite->api_key}",
            ])->timeout(15)->get("{$siteUrl}/wp-json/wp/v2/plugins");

            if (!$response->successful()) {
                $results['errors'][] = "Plugin listesi alınamadı (HTTP {$response->status()})";
                $this->error("  Plugin listesi alınamadı");
                return;
            }

            $plugins = $response->json();
            if (!is_array($plugins)) return;

            foreach ($plugins as $plugin) {
                $hasUpdate = isset($plugin['update']) && $plugin['update'] !== 'none' && !empty($plugin['update']);
                if (!$hasUpdate) continue;

                $pluginSlug = $plugin['plugin'] ?? 'unknown';
                $pluginName = $plugin['name'] ?? $pluginSlug;
                $currentVer = $plugin['version'] ?? '?';
                $newVer     = $plugin['update']['version'] ?? '?';

                if ($dryRun) {
                    $this->line("  [DRY] Plugin güncelleme mevcut: {$pluginName} ({$currentVer} → {$newVer})");
                    $results['plugins'][] = ['name' => $pluginName, 'from' => $currentVer, 'to' => $newVer, 'status' => 'skipped'];
                    continue;
                }

                // Güncellemeyi uygula
                $this->line("  Güncelleniyor: {$pluginName} ({$currentVer} → {$newVer})...");

                $updateResponse = Http::withHeaders([
                    'Authorization' => "Basic {$wpSite->api_key}",
                ])->timeout(120)->put("{$siteUrl}/wp-json/wp/v2/plugins/{$pluginSlug}", [
                    'status' => $plugin['status'] ?? 'active',
                ]);

                if ($updateResponse->successful()) {
                    $this->info("    Güncellendi: {$pluginName} → {$newVer}");
                    $results['plugins'][] = ['name' => $pluginName, 'from' => $currentVer, 'to' => $newVer, 'status' => 'success'];
                } else {
                    $this->error("    Hata: {$pluginName} güncellenemedi (HTTP {$updateResponse->status()})");
                    $results['plugins'][] = ['name' => $pluginName, 'from' => $currentVer, 'to' => $newVer, 'status' => 'failed'];
                    $results['errors'][] = "{$pluginName} güncellenemedi";
                }
            }
        } catch (\Exception $e) {
            $results['errors'][] = "Plugin güncelleme hatası: {$e->getMessage()}";
            $this->error("  Plugin hatası: {$e->getMessage()}");
        }
    }

    private function updateThemes(WordpressSite $wpSite, string $siteUrl, bool $dryRun, array &$results): void
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Basic {$wpSite->api_key}",
            ])->timeout(15)->get("{$siteUrl}/wp-json/wp/v2/themes");

            if (!$response->successful()) {
                $results['errors'][] = "Tema listesi alınamadı (HTTP {$response->status()})";
                return;
            }

            $themes = $response->json();
            if (!is_array($themes)) return;

            foreach ($themes as $theme) {
                $hasUpdate = isset($theme['update']) && !empty($theme['update']);
                if (!$hasUpdate) continue;

                $themeSlug  = $theme['stylesheet'] ?? 'unknown';
                $themeName  = $theme['name']['rendered'] ?? $themeSlug;
                $currentVer = $theme['version'] ?? '?';
                $newVer     = $theme['update']['version'] ?? '?';

                if ($dryRun) {
                    $this->line("  [DRY] Tema güncelleme mevcut: {$themeName} ({$currentVer} → {$newVer})");
                    $results['themes'][] = ['name' => $themeName, 'from' => $currentVer, 'to' => $newVer, 'status' => 'skipped'];
                    continue;
                }

                $this->line("  Tema güncelleniyor: {$themeName} ({$currentVer} → {$newVer})...");

                // WP REST API tema güncelleme - tema endpoint'i PUT desteklemez, alternatif kullanılır
                $results['themes'][] = ['name' => $themeName, 'from' => $currentVer, 'to' => $newVer, 'status' => 'pending'];
                $this->warn("    Tema güncellemesi manuel gerekebilir: {$themeName}");
            }
        } catch (\Exception $e) {
            $results['errors'][] = "Tema güncelleme hatası: {$e->getMessage()}";
            $this->error("  Tema hatası: {$e->getMessage()}");
        }
    }

    private function logResults(WordpressSite $wpSite, array $results, bool $dryRun): void
    {
        $siteName = $wpSite->site->name;
        $prefix = $dryRun ? '[DRY RUN] ' : '';

        $pluginSuccess = collect($results['plugins'])->where('status', 'success')->count();
        $pluginFailed  = collect($results['plugins'])->where('status', 'failed')->count();
        $themeCount    = count($results['themes']);

        Log::channel('single')->info("{$prefix}WordPress Bakım: {$siteName}", [
            'site_id'         => $wpSite->site_id,
            'url'             => $wpSite->site->url,
            'plugins_updated' => $pluginSuccess,
            'plugins_failed'  => $pluginFailed,
            'themes_pending'  => $themeCount,
            'errors'          => $results['errors'],
        ]);
    }

    private function sendReport(WordpressSite $wpSite, array $results): void
    {
        $customerEmail = $wpSite->site->customer_email;
        $adminEmail    = Setting::get('admin_email') ?? config('services.sitewatch.admin_email');

        // Admin'e her zaman gönder
        if ($adminEmail) {
            Mail::to($adminEmail)->queue(new MaintenanceReportMail($wpSite->site, $results));
            $this->info("  → Admin mail gönderildi: {$adminEmail}");
        }

        // Müşteriye sadece e-postası varsa gönder
        if ($customerEmail && $customerEmail !== $adminEmail) {
            Mail::to($customerEmail)->queue(new MaintenanceReportMail($wpSite->site, $results));
            $this->info("  → Müşteri mail gönderildi: {$customerEmail}");
        }
    }
}
