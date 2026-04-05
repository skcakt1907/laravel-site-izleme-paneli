<?php

namespace App\Livewire;

use App\Models\SiteCheck;
use Livewire\Component;

class StatusPage extends Component
{
    public int $refreshInterval = 60;

    public function render()
    {
        // Son 24 saatlik uptime oranı
        $totalChecks = SiteCheck::where('checked_at', '>=', now()->subDay())->count();
        $upChecks    = SiteCheck::where('checked_at', '>=', now()->subDay())->where('is_up', true)->count();

        $uptimePercent = $totalChecks > 0
            ? round(($upChecks / $totalChecks) * 100, 1)
            : 100.0;

        // Tüm sistemler çevrimiçi mi?
        $allUp = $uptimePercent >= 99.0;

        return view('livewire.status-page', compact('uptimePercent', 'allUp'))
            ->layout('components.layouts.status', [
                'title' => 'Sistem Durumu - DnKreatif',
            ]);
    }
}
