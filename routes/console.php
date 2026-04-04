<?php

use Illuminate\Support\Facades\Schedule;

// Her 30 dakikada bir tüm aktif sitelerin HTTP durumunu kontrol et
Schedule::command('sites:check')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Günde bir kez SSL sertifikalarını kontrol et (sabah 08:00)
Schedule::command('sites:check-ssl')
    ->dailyAt('08:00')
    ->withoutOverlapping();

// Her 6 saatte bir cPanel disk kullanımını kontrol et
Schedule::command('sites:check-cpanel')
    ->everySixHours()
    ->withoutOverlapping();

// Günde bir kez WordPress plugin/tema güncellemelerini kontrol et (sabah 09:00)
Schedule::command('sites:check-wordpress')
    ->dailyAt('09:00')
    ->withoutOverlapping();

// Her ayın 1'inde geçen ayın raporunu oluştur (sabah 07:00)
Schedule::command('sites:generate-report')
    ->monthlyOn(1, '07:00')
    ->withoutOverlapping();
