<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #333; padding: 20px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #2563eb; padding-bottom: 12px; }
        .header h1 { font-size: 20px; color: #1e3a5f; margin-bottom: 4px; }
        .header .period { font-size: 13px; color: #6b7280; }
        .summary { margin-bottom: 20px; text-align: center; }
        .summary .big { font-size: 14px; color: #374151; }
        table.data { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.data th { background: #1e3a5f; color: #fff; padding: 6px 8px; text-align: left; }
        table.data td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
        table.data tr:nth-child(even) { background: #f9fafb; }
        .green { color: #16a34a; }
        .red { color: #dc2626; }
        .yellow { color: #ca8a04; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-red { background: #fee2e2; color: #dc2626; }
        .badge-yellow { background: #fef9c3; color: #ca8a04; }
        .badge-blue { background: #dbeafe; color: #2563eb; }
        .footer { text-align: center; margin-top: 25px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    {{-- Başlık --}}
    <div class="header">
        <h1>SiteWatch - Genel Aylık Özet Raporu</h1>
        <div class="period">{{ $start->format('d.m.Y') }} - {{ $end->format('d.m.Y') }}</div>
    </div>

    {{-- Özet --}}
    <div class="summary">
        <p class="big">
            Toplam <strong>{{ $sites->count() }}</strong> site izlendi &middot;
            WordPress: {{ $sites->where('type', 'wordpress')->count() }} &middot;
            Laravel: {{ $sites->where('type', 'laravel')->count() }} &middot;
            Diğer: {{ $sites->where('type', 'other')->count() }}
        </p>
    </div>

    {{-- Tüm Siteler Tablosu --}}
    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">Site Adı</th>
                <th style="width: 20%;">URL</th>
                <th style="width: 8%;">Tip</th>
                <th style="width: 10%;">Uptime</th>
                <th style="width: 10%;">Kontrol</th>
                <th style="width: 10%;">Çökme</th>
                <th style="width: 10%;">Ort. Yanıt</th>
                <th style="width: 7%;">Durum</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siteStats as $index => $stat)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $stat['site']->name }}</strong></td>
                    <td style="font-size: 9px;">{{ Str::limit($stat['site']->url, 30) }}</td>
                    <td>
                        @switch($stat['site']->type)
                            @case('wordpress') <span class="badge badge-blue">WP</span> @break
                            @case('laravel') <span class="badge badge-red">Laravel</span> @break
                            @default <span class="badge">Diğer</span>
                        @endswitch
                    </td>
                    <td>
                        <strong class="{{ $stat['uptime_percent'] >= 99 ? 'green' : ($stat['uptime_percent'] >= 95 ? 'yellow' : 'red') }}">
                            %{{ $stat['uptime_percent'] }}
                        </strong>
                    </td>
                    <td>{{ $stat['total_checks'] }}</td>
                    <td>
                        @if($stat['down_count'] > 0)
                            <strong class="red">{{ $stat['down_count'] }}</strong>
                        @else
                            <span class="green">0</span>
                        @endif
                    </td>
                    <td>{{ $stat['avg_response'] ? round($stat['avg_response']) . 'ms' : '-' }}</td>
                    <td>
                        @if($stat['uptime_percent'] >= 99)
                            <span class="badge badge-green">İyi</span>
                        @elseif($stat['uptime_percent'] >= 95)
                            <span class="badge badge-yellow">Orta</span>
                        @else
                            <span class="badge badge-red">Kötü</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        Bu rapor SiteWatch Panel tarafından {{ now()->format('d.m.Y H:i') }} tarihinde otomatik olarak oluşturulmuştur.
    </div>
</body>
</html>
