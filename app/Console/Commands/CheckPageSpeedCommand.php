<?php

namespace App\Console\Commands;

use App\Models\PageSpeedScore;
use App\Models\Setting;
use App\Models\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckPageSpeedCommand extends Command
{
    protected $signature = 'sites:check-pagespeed {--site= : Belirli bir site ID}';
    protected $description = 'Google PageSpeed Insights API ile sayfa hız skorlarını kontrol et';

    private const API_URL = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

    public function handle(): int
    {
        $apiKey = Setting::get('pagespeed_api_key') ?? config('services.pagespeed.api_key');

        if (!$apiKey) {
            $this->error('PAGESPEED_API_KEY tanımlı değil. Ayarlar sayfasından veya .env dosyasından girin.');
            return self::FAILURE;
        }

        $query = Site::where('is_active', true);

        if ($siteId = $this->option('site')) {
            $query->where('id', $siteId);
        }

        $sites = $query->get();
        $this->info("Kontrol edilecek site sayısı: {$sites->count()}");

        foreach ($sites as $site) {
            $this->line("  Kontrol ediliyor: {$site->url}");

            try {
                $mobile  = $this->fetchScore($site->url, 'mobile', $apiKey);
                $desktop = $this->fetchScore($site->url, 'desktop', $apiKey);

                PageSpeedScore::create([
                    'site_id'       => $site->id,
                    'mobile_score'  => $mobile['score'],
                    'desktop_score' => $desktop['score'],
                    'mobile_fcp'    => $mobile['fcp'],
                    'desktop_fcp'   => $desktop['fcp'],
                    'mobile_lcp'    => $mobile['lcp'],
                    'desktop_lcp'   => $desktop['lcp'],
                    'checked_at'    => now(),
                ]);

                $this->info("    Mobil: {$mobile['score']} | Masaüstü: {$desktop['score']}");

            } catch (\Exception $e) {
                $this->error("    Hata: {$e->getMessage()}");
            }
        }

        $this->info('PageSpeed kontrolleri tamamlandı.');
        return self::SUCCESS;
    }

    private function fetchScore(string $url, string $strategy, string $apiKey): array
    {
        $response = Http::timeout(120)->get(self::API_URL, [
            'url'      => $url,
            'key'      => $apiKey,
            'strategy' => $strategy,
            'category' => 'performance',
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException("API hatası ({$strategy}): HTTP {$response->status()}");
        }

        $data = $response->json();
        $lighthouse = $data['lighthouseResult']['categories']['performance'] ?? null;
        $audits     = $data['lighthouseResult']['audits'] ?? [];

        $score = $lighthouse ? round($lighthouse['score'] * 100) : null;

        // FCP ve LCP milisaniye cinsinden
        $fcp = isset($audits['first-contentful-paint']['numericValue'])
            ? round($audits['first-contentful-paint']['numericValue'])
            : null;

        $lcp = isset($audits['largest-contentful-paint']['numericValue'])
            ? round($audits['largest-contentful-paint']['numericValue'])
            : null;

        return ['score' => $score, 'fcp' => $fcp, 'lcp' => $lcp];
    }
}
