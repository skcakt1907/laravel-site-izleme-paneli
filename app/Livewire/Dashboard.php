<?php

namespace App\Livewire;

use App\Models\Notification;
use App\Models\Site;
use App\Models\SiteCheck;
use App\Models\SslCertificate;
use Livewire\Component;

class Dashboard extends Component
{
    // Otomatik yenileme aralığı (saniye)
    public int $refreshInterval = 60;

    public function render()
    {
        // ---------- GENEL İSTATİSTİKLER ----------
        $totalSites   = Site::count();
        $activeSites  = Site::where('is_active', true)->count();
        $wpSites      = Site::where('type', 'wordpress')->count();
        $laravelSites = Site::where('type', 'laravel')->count();
        $otherSites   = Site::where('type', 'other')->count();

        // ---------- ÇÖKEN SİTELER ----------
        // Her aktif sitenin son kontrolünü al, is_up=false olanları listele
        $downSites = Site::where('is_active', true)
            ->whereHas('checks', function ($q) {
                $q->where('is_up', false)
                  ->whereIn('id', function ($sub) {
                      // Her site için en son kontrol kaydının ID'sini al
                      $sub->selectRaw('MAX(id)')
                          ->from('site_checks')
                          ->groupBy('site_id');
                  });
            })
            ->with(['latestCheck'])
            ->get();

        // ---------- SSL UYARILARI ----------
        // 30 gün veya altında kalan sertifikalar
        $sslWarnings = SslCertificate::with('site')
            ->where('days_remaining', '<=', 30)
            ->where('days_remaining', '>', 0)
            ->orderBy('days_remaining')
            ->get();

        $sslExpired = SslCertificate::with('site')
            ->where('days_remaining', '<=', 0)
            ->get();

        // ---------- SON KONTROLLER ----------
        // En son yapılan 10 HTTP kontrolü
        $recentChecks = SiteCheck::with('site')
            ->latest('checked_at')
            ->limit(10)
            ->get();

        // ---------- SON BİLDİRİMLER ----------
        $recentNotifications = Notification::with('site')
            ->latest('sent_at')
            ->limit(8)
            ->get();

        // ---------- ORTALAMA YANIT SÜRESİ ----------
        $avgResponseTime = SiteCheck::where('is_up', true)
            ->whereNotNull('response_time_ms')
            ->where('checked_at', '>=', now()->subDay())
            ->avg('response_time_ms');

        // ---------- UPTIME ORANI (Son 24 saat) ----------
        $totalChecks24h = SiteCheck::where('checked_at', '>=', now()->subDay())->count();
        $upChecks24h = SiteCheck::where('checked_at', '>=', now()->subDay())->where('is_up', true)->count();
        $uptimePercent = $totalChecks24h > 0
            ? round(($upChecks24h / $totalChecks24h) * 100, 1)
            : 0;

        return view('livewire.dashboard', compact(
            'totalSites', 'activeSites', 'wpSites', 'laravelSites', 'otherSites',
            'downSites', 'sslWarnings', 'sslExpired',
            'recentChecks', 'recentNotifications',
            'avgResponseTime', 'uptimePercent',
        ))
        ->layout('components.layouts.app', [
            'title'  => 'Dashboard - SiteWatch',
            'header' => 'Dashboard',
        ]);
    }
}
