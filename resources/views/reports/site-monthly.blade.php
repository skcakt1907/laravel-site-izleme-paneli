<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #333; padding: 30px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #2563eb; padding-bottom: 15px; }
        .header h1 { font-size: 22px; color: #1e3a5f; margin-bottom: 5px; }
        .header .period { font-size: 14px; color: #6b7280; }
        .site-info { background: #f3f4f6; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .site-info h2 { font-size: 16px; color: #1e3a5f; margin-bottom: 8px; }
        .site-info table { width: 100%; }
        .site-info td { padding: 4px 8px; vertical-align: top; }
        .site-info td:first-child { color: #6b7280; width: 35%; }
        .stats-grid { display: table; width: 100%; margin-bottom: 20px; }
        .stat-box { display: table-cell; width: 25%; text-align: center; padding: 12px; background: #f9fafb; border: 1px solid #e5e7eb; }
        .stat-box .value { font-size: 24px; font-weight: bold; }
        .stat-box .label { font-size: 10px; color: #6b7280; text-transform: uppercase; margin-top: 4px; }
        .green { color: #16a34a; }
        .red { color: #dc2626; }
        .yellow { color: #ca8a04; }
        .blue { color: #2563eb; }
        .section { margin-bottom: 20px; }
        .section h3 { font-size: 14px; color: #1e3a5f; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 10px; }
        table.data { width: 100%; border-collapse: collapse; font-size: 11px; }
        table.data th { background: #f3f4f6; padding: 6px 8px; text-align: left; border-bottom: 2px solid #d1d5db; }
        table.data td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #9ca3af; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-red { background: #fee2e2; color: #dc2626; }
        .badge-yellow { background: #fef9c3; color: #ca8a04; }
    </style>
</head>
<body>
    {{-- Başlık --}}
    <div class="header">
        <h1>SiteWatch - Aylık Bakım Raporu</h1>
        <div class="period">{{ $start->format('d.m.Y') }} - {{ $end->format('d.m.Y') }}</div>
    </div>

    {{-- Site Bilgileri --}}
    <div class="site-info">
        <h2>{{ $site->name }}</h2>
        <table>
            <tr><td>URL:</td><td>{{ $site->url }}</td></tr>
            <tr><td>Tip:</td><td>{{ ucfirst($site->type) }}</td></tr>
            <tr><td>Müşteri E-posta:</td><td>{{ $site->customer_email ?? '-' }}</td></tr>
            <tr><td>Hosting:</td><td>{{ $site->hosting_provider ?? '-' }}</td></tr>
        </table>
    </div>

    {{-- İstatistik Kartları --}}
    <div class="stats-grid">
        <div class="stat-box">
            <div class="value {{ $uptimePercent >= 99 ? 'green' : ($uptimePercent >= 95 ? 'yellow' : 'red') }}">
                %{{ $uptimePercent }}
            </div>
            <div class="label">Uptime</div>
        </div>
        <div class="stat-box">
            <div class="value blue">{{ $totalChecks }}</div>
            <div class="label">Toplam Kontrol</div>
        </div>
        <div class="stat-box">
            <div class="value {{ $downCount > 0 ? 'red' : 'green' }}">{{ $downCount }}</div>
            <div class="label">Çökme Sayısı</div>
        </div>
        <div class="stat-box">
            <div class="value">{{ $avgResponse ? round($avgResponse) . 'ms' : '-' }}</div>
            <div class="label">Ort. Yanıt</div>
        </div>
    </div>

    {{-- SSL Sertifika --}}
    @if($ssl)
        <div class="section">
            <h3>SSL Sertifika Durumu</h3>
            <table class="data">
                <tr><td style="width:35%; color:#6b7280;">Sağlayıcı</td><td>{{ $ssl->issuer }}</td></tr>
                <tr><td style="color:#6b7280;">Son Kullanma</td><td>{{ $ssl->expires_at->format('d.m.Y') }}</td></tr>
                <tr><td style="color:#6b7280;">Kalan Gün</td><td>{{ $ssl->days_remaining }} gün</td></tr>
                <tr>
                    <td style="color:#6b7280;">Durum</td>
                    <td>
                        @if($ssl->days_remaining > 30)
                            <span class="badge badge-green">Geçerli</span>
                        @elseif($ssl->days_remaining > 0)
                            <span class="badge badge-yellow">Süresi Yaklaşıyor</span>
                        @else
                            <span class="badge badge-red">Süresi Dolmuş</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    @endif

    {{-- cPanel Disk Kullanımı --}}
    @if($cpanel && $cpanel->disk_used_mb)
        <div class="section">
            <h3>Disk Kullanımı (cPanel)</h3>
            <table class="data">
                <tr><td style="width:35%; color:#6b7280;">Kullanılan</td><td>{{ $cpanel->disk_used_mb }} MB</td></tr>
                <tr><td style="color:#6b7280;">Limit</td><td>{{ $cpanel->disk_limit_mb }} MB</td></tr>
                <tr><td style="color:#6b7280;">Doluluk</td><td>%{{ $cpanel->disk_usage_percent }}</td></tr>
            </table>
        </div>
    @endif

    {{-- WordPress Bilgisi --}}
    @if($wordpress)
        <div class="section">
            <h3>WordPress Durumu</h3>
            <table class="data">
                <tr><td style="width:35%; color:#6b7280;">WP Versiyon</td><td>{{ $wordpress->wp_version ?? '-' }}</td></tr>
                <tr><td style="color:#6b7280;">Plugin (Güncelleme Bekleyen)</td><td>{{ $wordpress->plugins_update_available }} / {{ $wordpress->plugins_total }}</td></tr>
                <tr><td style="color:#6b7280;">Tema Güncelleme</td><td>{{ $wordpress->themes_update_available }}</td></tr>
            </table>
        </div>
    @endif

    {{-- Bildirimler --}}
    @if($notifications->count() > 0)
        <div class="section">
            <h3>Dönem İçi Bildirimler ({{ $notifications->count() }})</h3>
            <table class="data">
                <thead>
                    <tr><th>Tarih</th><th>Tip</th><th>Konu</th></tr>
                </thead>
                <tbody>
                    @foreach($notifications as $notif)
                        <tr>
                            <td>{{ $notif->sent_at->format('d.m.Y H:i') }}</td>
                            <td>
                                @switch($notif->type)
                                    @case('site_down') <span class="badge badge-red">Çöktü</span> @break
                                    @case('ssl_expiry') <span class="badge badge-yellow">SSL</span> @break
                                    @case('disk_warning') <span class="badge badge-yellow">Disk</span> @break
                                    @case('update_available') <span class="badge badge-green">Güncelleme</span> @break
                                @endswitch
                            </td>
                            <td>{{ $notif->subject }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Bu rapor SiteWatch Panel tarafından {{ now()->format('d.m.Y H:i') }} tarihinde otomatik olarak oluşturulmuştur.
    </div>
</body>
</html>
