<?php

return [
    // Bildirimlerin gönderileceği admin e-posta adresi
    'admin_email' => env('SITEWATCH_ADMIN_EMAIL', 'admin@example.com'),

    // HTTP kontrol aralığı (dakika)
    'check_interval' => env('SITEWATCH_CHECK_INTERVAL', 30),

    // SSL uyarı eşiği (gün)
    'ssl_warning_days' => env('SITEWATCH_SSL_WARNING_DAYS', 30),

    // Bildirim gönderilmesi için gereken ard arda hata sayısı
    'failure_threshold' => env('SITEWATCH_FAILURE_THRESHOLD', 2),

    // Disk doluluk uyarı eşiği (yüzde)
    'disk_warning_percent' => env('SITEWATCH_DISK_WARNING', 90),
];
