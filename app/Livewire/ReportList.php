<?php

namespace App\Livewire;

use App\Models\Report;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;
use Livewire\WithPagination;

class ReportList extends Component
{
    use WithPagination;

    public string $filterPeriod = '';  // Dönem filtresi (ör: 2026-04)
    public string $search = '';

    // Rapor oluşturma modal
    public bool $showGenerateModal = false;
    public string $generateMonth = '';

    // Filtre değişince sayfalamayı sıfırla
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterPeriod(): void { $this->resetPage(); }

    // Rapor oluşturma modalını aç
    public function openGenerateModal(): void
    {
        $this->generateMonth = now()->subMonth()->format('Y-m');
        $this->showGenerateModal = true;
    }

    // Rapor oluştur
    public function generateReport(): void
    {
        $this->validate([
            'generateMonth' => 'required|date_format:Y-m',
        ], [
            'generateMonth.required'    => 'Dönem seçimi zorunludur.',
            'generateMonth.date_format' => 'Geçerli bir dönem giriniz (YYYY-MM).',
        ]);

        // Artisan komutunu çalıştır
        Artisan::call('sites:generate-report', [
            '--month' => $this->generateMonth,
        ]);

        $this->showGenerateModal = false;
        session()->flash('message', "{$this->generateMonth} dönemi için raporlar oluşturuldu.");
    }

    public function render()
    {
        $reports = Report::with('site')
            ->when($this->search, function ($q) {
                $q->whereHas('site', fn ($s) => $s->where('name', 'like', "%{$this->search}%"));
            })
            ->when($this->filterPeriod, fn ($q) => $q->where('period', $this->filterPeriod))
            ->orderByDesc('generated_at')
            ->paginate(20);

        // Mevcut dönemler (dropdown için)
        $periods = Report::select('period')
            ->distinct()
            ->orderByDesc('period')
            ->pluck('period');

        return view('livewire.report-list', compact('reports', 'periods'))
            ->layout('components.layouts.app', [
                'title'  => 'Raporlar - SiteWatch',
                'header' => 'Raporlar',
            ]);
    }
}
