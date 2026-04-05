<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class Settings extends Component
{
    // ---------- MAIL SMTP ----------
    public string $mail_host = '';
    public string $mail_port = '';
    public string $mail_username = '';
    public string $mail_password = '';
    public string $mail_encryption = 'tls';
    public string $mail_from_address = '';
    public string $mail_from_name = '';

    // ---------- UYARI EŞİKLERİ ----------
    public string $disk_warning_percent = '90';
    public string $ssl_warning_days = '30';
    public string $domain_warning_days = '30';
    public string $failure_threshold = '2';
    public string $check_interval = '30';

    // ---------- API ANAHTARLARI ----------
    public string $whois_api_key = '';
    public string $pagespeed_api_key = '';

    // ---------- BİLDİRİM ----------
    public string $admin_email = '';

    public function mount(): void
    {
        // Mail
        $this->mail_host         = (string) (Setting::get('mail_host') ?? config('mail.mailers.smtp.host') ?? '');
        $this->mail_port         = (string) (Setting::get('mail_port') ?? config('mail.mailers.smtp.port') ?? '587');
        $this->mail_username     = (string) (Setting::get('mail_username') ?? config('mail.mailers.smtp.username') ?? '');
        $this->mail_password     = '';
        $this->mail_encryption   = (string) (Setting::get('mail_encryption') ?? config('mail.mailers.smtp.encryption') ?? 'tls');
        $this->mail_from_address = (string) (Setting::get('mail_from_address') ?? config('mail.from.address') ?? '');
        $this->mail_from_name    = (string) (Setting::get('mail_from_name') ?? config('mail.from.name') ?? '');

        // Eşikler
        $this->disk_warning_percent = (string) (Setting::get('disk_warning_percent') ?? config('services.sitewatch.disk_warning') ?? '90');
        $this->ssl_warning_days     = (string) (Setting::get('ssl_warning_days') ?? config('services.sitewatch.ssl_warning') ?? '30');
        $this->domain_warning_days  = (string) (Setting::get('domain_warning_days') ?? config('services.sitewatch.domain_warning') ?? '30');
        $this->failure_threshold    = (string) (Setting::get('failure_threshold') ?? config('services.sitewatch.failure_threshold') ?? '2');
        $this->check_interval       = (string) (Setting::get('check_interval') ?? config('services.sitewatch.check_interval') ?? '30');

        // API
        $this->whois_api_key     = (string) (Setting::get('whois_api_key') ?? config('services.whois.api_key') ?? '');
        $this->pagespeed_api_key = (string) (Setting::get('pagespeed_api_key') ?? config('services.pagespeed.api_key') ?? '');

        // Bildirim
        $this->admin_email = (string) (Setting::get('admin_email') ?? config('services.sitewatch.admin_email') ?? '');
    }

    public function saveMail(): void
    {
        $this->validate([
            'mail_host'         => 'required|string',
            'mail_port'         => 'required|numeric',
            'mail_username'     => 'nullable|string',
            'mail_encryption'   => 'required|in:tls,ssl,null',
            'mail_from_address' => 'required|email',
            'mail_from_name'    => 'required|string',
        ], [
            'mail_host.required'         => 'SMTP host zorunludur.',
            'mail_port.required'         => 'SMTP port zorunludur.',
            'mail_from_address.required' => 'Gönderici e-posta zorunludur.',
            'mail_from_name.required'    => 'Gönderici adı zorunludur.',
        ]);

        Setting::set('mail_host', $this->mail_host);
        Setting::set('mail_port', $this->mail_port);
        Setting::set('mail_username', $this->mail_username);
        if ($this->mail_password !== '') {
            Setting::set('mail_password', $this->mail_password);
        }
        Setting::set('mail_encryption', $this->mail_encryption);
        Setting::set('mail_from_address', $this->mail_from_address);
        Setting::set('mail_from_name', $this->mail_from_name);

        session()->flash('message_mail', 'Mail ayarları kaydedildi.');
    }

    public function saveThresholds(): void
    {
        $this->validate([
            'disk_warning_percent' => 'required|numeric|min:1|max:100',
            'ssl_warning_days'     => 'required|numeric|min:1|max:365',
            'domain_warning_days'  => 'required|numeric|min:1|max:365',
            'failure_threshold'    => 'required|numeric|min:1|max:50',
            'check_interval'       => 'required|numeric|min:1|max:1440',
        ]);

        Setting::set('disk_warning_percent', $this->disk_warning_percent);
        Setting::set('ssl_warning_days', $this->ssl_warning_days);
        Setting::set('domain_warning_days', $this->domain_warning_days);
        Setting::set('failure_threshold', $this->failure_threshold);
        Setting::set('check_interval', $this->check_interval);

        session()->flash('message_thresholds', 'Eşik ayarları kaydedildi.');
    }

    public function saveApi(): void
    {
        Setting::set('whois_api_key', $this->whois_api_key);
        Setting::set('pagespeed_api_key', $this->pagespeed_api_key);
        Setting::set('admin_email', $this->admin_email);

        session()->flash('message_api', 'API ve bildirim ayarları kaydedildi.');
    }

    public function render()
    {
        return view('livewire.admin.settings')
            ->layout('components.layouts.app', [
                'title'  => 'Ayarlar - SiteWatch',
                'header' => 'Ayarlar',
            ]);
    }
}
