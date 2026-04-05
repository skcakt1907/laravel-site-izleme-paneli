<?php

use App\Livewire\Admin\Settings;
use App\Livewire\Admin\UserManager;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\NotificationList;
use App\Livewire\ReportList;
use App\Livewire\Servers\ServerManager;
use App\Livewire\Sites\SiteDetail;
use App\Livewire\Sites\SiteManager;
use App\Livewire\StatusPage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ==========================================
// ÖN PANEL (Public - login gerektirmez)
// ==========================================
Route::get('/', StatusPage::class)->name('status');

// ==========================================
// AUTH ROTALARI
// ==========================================
Route::get('/admin/login', Login::class)->name('admin.login');

Route::post('/admin/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('admin.login');
})->name('admin.logout');

// ==========================================
// ADMIN PANELİ (Giriş gerekli)
// ==========================================
Route::prefix('admin')->middleware('admin')->group(function () {

    // Dashboard - tüm roller görebilir
    Route::get('/dashboard', Dashboard::class)->name('admin.dashboard');

    // Sadece super_admin
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/users', UserManager::class)->name('admin.users.index');
        Route::get('/settings', Settings::class)->name('admin.settings');
    });

    // super_admin ve admin rolü gerekli
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('/servers', ServerManager::class)->name('admin.servers.index');
        Route::get('/sites', SiteManager::class)->name('admin.sites.index');
        Route::get('/sites/{site}', SiteDetail::class)->name('admin.sites.show');
        Route::get('/notifications', NotificationList::class)->name('admin.notifications.index');
        Route::get('/reports', ReportList::class)->name('admin.reports.index');
        Route::get('/reports/{report}/download', function (App\Models\Report $report) {
            return response()->download(storage_path("app/{$report->file_path}"));
        })->name('admin.reports.download');
    });
});
