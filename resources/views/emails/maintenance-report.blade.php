<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; padding: 20px; }
        .card { background: #fff; border-radius: 12px; padding: 30px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #2563eb; color: #fff; padding: 12px 20px; border-radius: 8px; font-size: 18px; font-weight: bold; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f9fafb; padding: 8px 12px; text-align: left; font-size: 12px; text-transform: uppercase; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
        td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-failed { background: #fee2e2; color: #991b1b; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .footer { margin-top: 20px; font-size: 12px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">Bakım Raporu</div>

        <p style="color: #374151; margin-bottom: 20px;">
            <strong>{{ $site->name }}</strong> sitesinde bakım işlemi tamamlandı.
        </p>

        <table style="margin-bottom: 10px;">
            <tr>
                <td style="color: #6b7280; width: 40%;">Site</td>
                <td><a href="{{ $site->url }}" style="color: #2563eb;">{{ $site->url }}</a></td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Tarih</td>
                <td>{{ now()->format('d.m.Y H:i') }}</td>
            </tr>
        </table>

        @if(count($results['plugins']) > 0)
            <h3 style="font-size: 14px; color: #374151; margin: 20px 0 10px;">Plugin Güncellemeleri</h3>
            <table>
                <thead>
                    <tr>
                        <th>Plugin</th>
                        <th>Versiyon</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results['plugins'] as $plugin)
                        <tr>
                            <td>{{ $plugin['name'] }}</td>
                            <td>{{ $plugin['from'] }} &rarr; {{ $plugin['to'] }}</td>
                            <td>
                                @if($plugin['status'] === 'success')
                                    <span class="badge badge-success">Güncellendi</span>
                                @elseif($plugin['status'] === 'failed')
                                    <span class="badge badge-failed">Başarısız</span>
                                @else
                                    <span class="badge badge-pending">Beklemede</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(count($results['themes']) > 0)
            <h3 style="font-size: 14px; color: #374151; margin: 20px 0 10px;">Tema Güncellemeleri</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tema</th>
                        <th>Versiyon</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results['themes'] as $theme)
                        <tr>
                            <td>{{ $theme['name'] }}</td>
                            <td>{{ $theme['from'] }} &rarr; {{ $theme['to'] }}</td>
                            <td>
                                @if($theme['status'] === 'success')
                                    <span class="badge badge-success">Güncellendi</span>
                                @elseif($theme['status'] === 'failed')
                                    <span class="badge badge-failed">Başarısız</span>
                                @else
                                    <span class="badge badge-pending">Manuel Gerekli</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(count($results['errors']) > 0)
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px; margin-top: 15px;">
                <strong style="color: #991b1b; font-size: 13px;">Hatalar:</strong>
                <ul style="margin: 5px 0 0 15px; padding: 0; color: #991b1b; font-size: 13px;">
                    @foreach($results['errors'] as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="footer">Bu mail DnPanel tarafından otomatik olarak gönderilmiştir.</p>
    </div>
</body>
</html>
