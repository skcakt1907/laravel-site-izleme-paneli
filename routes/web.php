<?php

use App\Livewire\Dashboard;
use App\Livewire\Sites\SiteDetail;
use App\Livewire\Sites\SiteManager;
use Illuminate\Support\Facades\Route;

// Ana sayfa → Dashboard'a yönlendir
Route::get('/', fn () => redirect()->route('dashboard'));

// Dashboard sayfası (Livewire - otomatik yenilenme)
Route::get('/dashboard', Dashboard::class)->name('dashboard');

// Site yönetimi (Livewire CRUD)
Route::get('/sites', SiteManager::class)->name('sites.index');

// Site detay sayfası (sekmeli: genel bakış, HTTP kontrol, SSL, cPanel, WordPress, bildirimler)
Route::get('/sites/{site}', SiteDetail::class)->name('sites.show');

// Aylık PDF rapor indirme
Route::get('/reports/{report}/download', function (App\Models\Report $report) {
    return response()->download(storage_path("app/{$report->file_path}"));
})->name('reports.download');
