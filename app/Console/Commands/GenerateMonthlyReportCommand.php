<?php

namespace App\Console\Commands;

use App\Models\Report;
use App\Models\Site;
use App\Models\SiteCheck;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateMonthlyReportCommand extends Command
{
    protected $signature = 'sites:generate-report
                            {--month= : Rapor dönemi (YYYY-MM formatında, varsayılan: geçen ay)}
                            {--site= : Belirli bir site ID için rapor oluştur}';

    protected $description = 'Aylık PDF bakım raporu oluşturur (site bazında veya genel)';

    public function handle(): int
    {
        // Rapor dönemini belirle
        $month = $this->option('month')
            ? Carbon::createFromFormat('Y-m', $this->option('month'))
            : now()->subMonth();

        $period = $month->format('Y-m');
        $periodStart = $month->copy()->startOfMonth();
        $periodEnd = $month->copy()->endOfMonth();

        $this->info("Rapor dönemi: {$period} ({$periodStart->format('d.m.Y')} - {$periodEnd->format('d.m.Y')})");

        // Belirli bir site mi yoksa tüm siteler mi?
        if ($siteId = $this->option('site')) {
            $sites = Site::where('id', $siteId)->get();
        } else {
            $sites = Site::where('is_active', true)->get();
        }

        if ($sites->isEmpty()) {
            $this->warn('Rapor oluşturulacak site bulunamadı.');
            return self::SUCCESS;
        }

        // Reports dizinini oluştur
        $reportDir = storage_path("app/reports/{$period}");
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0755, true);
        }

        $this->info("Toplam {$sites->count()} site için rapor oluşturuluyor...");

        foreach ($sites as $site) {
            $this->generateSiteReport($site, $period, $periodStart, $periodEnd, $reportDir);
        }

        // Genel özet raporu oluştur (tüm siteler)
        if (!$this->option('site')) {
            $this->generateSummaryReport($sites, $period, $periodStart, $periodEnd, $reportDir);
        }

        $this->newLine();
        $this->info('Rapor oluşturma tamamlandı.');

        return self::SUCCESS;
    }

    // Tek bir site için aylık rapor oluştur
    private function generateSiteReport(Site $site, string $period, Carbon $start, Carbon $end, string $dir): void
    {
        // Bu dönemdeki HTTP kontrollerini al
        $checks = SiteCheck::where('site_id', $site->id)
            ->whereBetween('checked_at', [$start, $end])
            ->orderBy('checked_at')
            ->get();

        $totalChecks = $checks->count();
        $upChecks = $checks->where('is_up', true)->count();

        // Uptime yüzdesi
        $uptimePercent = $totalChecks > 0
            ? round(($upChecks / $totalChecks) * 100, 2)
            : 0;

        // Ortalama yanıt süresi
        $avgResponse = $checks->where('is_up', true)->avg('response_time_ms');

        // En kötü yanıt süresi
        $maxResponse = $checks->where('is_up', true)->max('response_time_ms');

        // Toplam çökme sayısı
        $downCount = $checks->where('is_up', false)->count();

        // SSL bilgisi
        $ssl = $site->sslCertificate;

        // cPanel bilgisi
        $cpanel = $site->cpanelAccount;

        // WordPress bilgisi
        $wordpress = $site->wordpressSite;

        // Bildirimler
        $notifications = $site->notifications()
            ->whereBetween('sent_at', [$start, $end])
            ->get();

        // PDF oluştur
        $pdf = Pdf::loadView('reports.site-monthly', compact(
            'site', 'period', 'start', 'end',
            'totalChecks', 'upChecks', 'uptimePercent',
            'avgResponse', 'maxResponse', 'downCount',
            'ssl', 'cpanel', 'wordpress', 'notifications',
        ));

        $pdf->setPaper('a4', 'portrait');

        // Dosya adını oluştur ve kaydet
        $fileName = "reports/{$period}/{$site->id}-" . \Str::slug($site->name) . ".pdf";
        $fullPath = storage_path("app/{$fileName}");
        $pdf->save($fullPath);

        // Veritabanına rapor kaydı
        Report::updateOrCreate(
            ['site_id' => $site->id, 'period' => $period],
            [
                'file_path'    => $fileName,
                'generated_at' => now(),
            ]
        );

        $this->line("  <fg=green>✓</> {$site->name} — {$fileName}");
    }

    // Genel özet raporu (tüm siteler)
    private function generateSummaryReport($sites, string $period, Carbon $start, Carbon $end, string $dir): void
    {
        $this->info('Genel özet raporu oluşturuluyor...');

        // Tüm sitelerin dönem istatistikleri
        $siteStats = [];
        foreach ($sites as $site) {
            $checks = SiteCheck::where('site_id', $site->id)
                ->whereBetween('checked_at', [$start, $end])
                ->get();

            $total = $checks->count();
            $up = $checks->where('is_up', true)->count();

            $siteStats[] = [
                'site'           => $site,
                'total_checks'   => $total,
                'uptime_percent' => $total > 0 ? round(($up / $total) * 100, 2) : 0,
                'avg_response'   => $checks->where('is_up', true)->avg('response_time_ms'),
                'down_count'     => $checks->where('is_up', false)->count(),
            ];
        }

        // Uptime'a göre sırala (en kötü üstte)
        usort($siteStats, fn ($a, $b) => $a['uptime_percent'] <=> $b['uptime_percent']);

        $pdf = Pdf::loadView('reports.summary-monthly', compact(
            'siteStats', 'period', 'start', 'end', 'sites',
        ));

        $pdf->setPaper('a4', 'landscape');

        $fileName = "reports/{$period}/genel-ozet.pdf";
        $fullPath = storage_path("app/{$fileName}");
        $pdf->save($fullPath);

        // Genel rapor kaydı (site_id = null)
        Report::updateOrCreate(
            ['site_id' => null, 'period' => $period],
            [
                'file_path'    => $fileName,
                'generated_at' => now(),
            ]
        );

        $this->line("  <fg=green>✓</> Genel Özet — {$fileName}");
    }
}
