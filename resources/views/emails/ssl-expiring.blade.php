<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; padding: 20px; }
        .card { background: #fff; border-radius: 12px; padding: 30px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .alert-bar { background: #f59e0b; color: #fff; padding: 12px 20px; border-radius: 8px; font-size: 18px; font-weight: bold; margin-bottom: 20px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .info-table td:first-child { color: #6b7280; width: 40%; }
        .info-table td:last-child { font-weight: 600; }
        .footer { margin-top: 20px; font-size: 12px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        {{-- Uyarı Başlığı --}}
        <div class="alert-bar">
            🔒 SSL Sertifika Uyarısı
        </div>

        <p style="color: #374151; margin-bottom: 20px;">
            <strong>{{ $site->name }}</strong> sitesinin SSL sertifikası
            <strong>{{ $daysRemaining }} gün</strong> içinde sona erecek.
            Lütfen yenileme işlemini planlayın.
        </p>

        {{-- Detay Bilgileri --}}
        <table class="info-table">
            <tr>
                <td>Site URL</td>
                <td><a href="{{ $site->url }}" style="color: #2563eb;">{{ $site->url }}</a></td>
            </tr>
            <tr>
                <td>Sertifika Sağlayıcı</td>
                <td>{{ $issuer }}</td>
            </tr>
            <tr>
                <td>Son Kullanma Tarihi</td>
                <td style="color: #dc2626;">{{ $expiresAt->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td>Kalan Gün</td>
                <td style="color: #f59e0b; font-size: 18px;">{{ $daysRemaining }} gün</td>
            </tr>
        </table>

        <p class="footer">Bu mail SiteWatch Panel tarafından otomatik olarak gönderilmiştir.</p>
    </div>
</body>
</html>
