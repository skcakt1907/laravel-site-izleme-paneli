<?php

namespace App\Livewire\Sites;

use App\Models\CpanelAccount;
use App\Models\Site;
use App\Models\SiteCheck;
use App\Models\WordpressSite;
use Illuminate\Support\Carbon;
use Livewire\Component;

class SiteDetail extends Component
{
    public Site $site;

    // Aktif sekme
    public string $activeTab = 'overview';

    // ---------- cPanel FORM ALANLARI ----------
    public string $cpanel_domain = '';
    public string $cpanel_username = '';
    public string $cpanel_api_token = '';

    // ---------- WordPress FORM ALANLARI ----------
    public string $wp_admin_url = '';
    public string $wp_api_key = '';

    // Modal durumları
    public bool $showCpanelModal = false;
    public bool $showWordpressModal = false;

    // Bileşen yüklendiğinde site bilgilerini al
    public function mount(Site $site): void
    {
        $this->site = $site->load([
            'checks' => fn ($q) => $q->latest('checked_at')->limit(20),
            'sslCertificate',
            'cpanelAccount',
            'wordpressSite',
            'latestPageSpeed',
            'pageSpeedScores' => fn ($q) => $q->latest('checked_at')->limit(10),
            'notifications' => fn ($q) => $q->latest('sent_at')->limit(10),
        ]);

        // Mevcut cPanel bilgilerini forma doldur
        if ($this->site->cpanelAccount) {
            $this->cpanel_domain   = $this->site->cpanelAccount->domain;
            $this->cpanel_username = $this->site->cpanelAccount->username;
        }

        // Mevcut WordPress bilgilerini forma doldur
        if ($this->site->wordpressSite) {
            $this->wp_admin_url = $this->site->wordpressSite->admin_url ?? '';
        }
    }

    // Sekme değiştir
    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ---------- cPanel İŞLEMLERİ ----------

    public function openCpanelModal(): void
    {
        $this->showCpanelModal = true;
    }

    // cPanel bilgilerini kaydet
    public function saveCpanel(): void
    {
        $this->validate([
            'cpanel_domain'    => 'required|string|max:191',
            'cpanel_username'  => 'required|string|max:191',
            'cpanel_api_token' => $this->site->cpanelAccount ? 'nullable|string' : 'required|string',
        ], [
            'cpanel_domain.required'    => 'cPanel domain zorunludur.',
            'cpanel_username.required'  => 'cPanel kullanıcı adı zorunludur.',
            'cpanel_api_token.required' => 'API token zorunludur.',
        ]);

        $data = [
            'domain'   => $this->cpanel_domain,
            'username' => $this->cpanel_username,
        ];

        // Token sadece girilmişse güncelle (mevcut token korunsun)
        if ($this->cpanel_api_token) {
            $data['api_token'] = $this->cpanel_api_token;
        }

        CpanelAccount::updateOrCreate(
            ['site_id' => $this->site->id],
            $data
        );

        $this->site->load('cpanelAccount');
        $this->showCpanelModal = false;
        $this->cpanel_api_token = ''; // Token alanını temizle
        session()->flash('message', 'cPanel bilgileri kaydedildi.');
    }

    // cPanel bilgilerini sil
    public function deleteCpanel(): void
    {
        $this->site->cpanelAccount?->delete();
        $this->site->load('cpanelAccount');
        $this->cpanel_domain = '';
        $this->cpanel_username = '';
        session()->flash('message', 'cPanel bilgileri silindi.');
    }

    // ---------- WordPress İŞLEMLERİ ----------

    public function openWordpressModal(): void
    {
        $this->showWordpressModal = true;
    }

    // WordPress bilgilerini kaydet
    public function saveWordpress(): void
    {
        $this->validate([
            'wp_admin_url' => 'nullable|url|max:191',
            'wp_api_key'   => $this->site->wordpressSite ? 'nullable|string' : 'nullable|string',
        ]);

        $data = [
            'admin_url' => $this->wp_admin_url ?: null,
        ];

        // API key sadece girilmişse güncelle
        if ($this->wp_api_key) {
            $data['api_key'] = $this->wp_api_key;
        }

        WordpressSite::updateOrCreate(
            ['site_id' => $this->site->id],
            $data
        );

        $this->site->load('wordpressSite');
        $this->showWordpressModal = false;
        $this->wp_api_key = ''; // API key alanını temizle
        session()->flash('message', 'WordPress bilgileri kaydedildi.');
    }

    // WordPress bilgilerini sil
    public function deleteWordpress(): void
    {
        $this->site->wordpressSite?->delete();
        $this->site->load('wordpressSite');
        $this->wp_admin_url = '';
        session()->flash('message', 'WordPress bilgileri silindi.');
    }

    /**
     * Son 30 günlük günlük uptime yüzdesini hesapla.
     */
    public function getUptimeChartData(): array
    {
        $labels = [];
        $data   = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d.m');

            $total = SiteCheck::where('site_id', $this->site->id)
                ->whereDate('checked_at', $date)
                ->count();

            $up = SiteCheck::where('site_id', $this->site->id)
                ->whereDate('checked_at', $date)
                ->where('is_up', true)
                ->count();

            $data[] = $total > 0 ? round(($up / $total) * 100, 1) : null;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function render()
    {
        $uptimeChart = $this->getUptimeChartData();

        return view('livewire.sites.site-detail', compact('uptimeChart'))
            ->layout('components.layouts.app', [
                'title'  => "{$this->site->name} - SiteWatch",
                'header' => $this->site->name,
            ]);
    }
}
