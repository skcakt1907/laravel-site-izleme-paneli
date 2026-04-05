<?php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationList extends Component
{
    use WithPagination;

    // Filtreler
    public string $search = '';
    public string $filterType = '';
    public string $filterChannel = '';
    public string $sortField = 'sent_at';
    public string $sortDirection = 'desc';

    // Arama/filtre değişince sayfalamayı sıfırla
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterType(): void { $this->resetPage(); }
    public function updatingFilterChannel(): void { $this->resetPage(); }

    // Sıralama
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function render()
    {
        $notifications = Notification::with('site')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('subject', 'like', "%{$this->search}%")
                        ->orWhere('message', 'like', "%{$this->search}%")
                        ->orWhereHas('site', fn ($s) => $s->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->filterType, fn ($q) => $q->where('type', $this->filterType))
            ->when($this->filterChannel, fn ($q) => $q->where('channel', $this->filterChannel))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);

        // Tip bazlı sayılar
        $countByType = [
            'site_down'        => Notification::where('type', 'site_down')->count(),
            'ssl_expiry'       => Notification::where('type', 'ssl_expiry')->count(),
            'disk_warning'     => Notification::where('type', 'disk_warning')->count(),
            'update_available' => Notification::where('type', 'update_available')->count(),
        ];

        return view('livewire.notification-list', compact('notifications', 'countByType'))
            ->layout('components.layouts.app', [
                'title'  => 'Bildirimler - SiteWatch',
                'header' => 'Bildirimler',
            ]);
    }
}
