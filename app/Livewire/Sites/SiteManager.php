<?php

namespace App\Livewire\Sites;

use App\Models\Server;
use App\Models\Site;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;
use Livewire\WithPagination;

class SiteManager extends Component
{
    use WithPagination;

    // ---------- FİLTRE & ARAMA ----------
    public string $search = '';           // Arama metni
    public string $filterType = '';       // Site tipi filtresi (wordpress/laravel/other)
    public string $filterStatus = '';     // Durum filtresi (active/inactive)
    public string $filterServer = '';     // Sunucu filtresi
    public string $filterHealth = '';     // Sağlık filtresi (down/ssl_expiring/domain_expiring)
    public string $sortField = 'name';    // Sıralama alanı
    public string $sortDirection = 'asc'; // Sıralama yönü

    // ---------- TOPLU İŞLEM ----------
    public array $selectedSites = [];  // Seçili site ID'leri
    public bool $selectAll = false;    // Tümünü seç

    // ---------- MODAL DURUMU ----------
    public bool $showModal = false;    // Ekleme/düzenleme modalı açık mı?
    public bool $showDeleteModal = false; // Silme onay modalı açık mı?
    public bool $showBulkDeleteModal = false; // Toplu silme onay modalı
    public ?int $editingSiteId = null; // Düzenlenen site ID (null = yeni kayıt)

    // ---------- FORM ALANLARI ----------
    public string $name = '';
    public string $url = '';
    public string $type = 'other';
    public string $customer_email = '';
    public string $hosting_provider = '';
    public string $server_ip = '';
    public string $php_version = '';
    public string $notes = '';
    public bool $is_active = true;

    // Arama değiştiğinde sayfalamayı sıfırla
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // Filtre değiştiğinde sayfalamayı sıfırla
    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterServer(): void
    {
        $this->resetPage();
    }

    public function updatingFilterHealth(): void
    {
        $this->resetPage();
    }

    // Sıralama değiştir
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            // Aynı alan tıklanırsa yönü değiştir
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // ---------- CRUD İŞLEMLERİ ----------

    // Yeni site ekleme modalını aç
    public function create(): void
    {
        $this->resetForm();
        $this->editingSiteId = null;
        $this->showModal = true;
    }

    // Mevcut siteyi düzenleme modalını aç
    public function edit(int $id): void
    {
        $site = Site::findOrFail($id);

        $this->editingSiteId    = $site->id;
        $this->name             = $site->name;
        $this->url              = $site->url;
        $this->type             = $site->type;
        $this->customer_email   = $site->customer_email ?? '';
        $this->hosting_provider = $site->hosting_provider ?? '';
        $this->server_ip        = $site->server_ip ?? '';
        $this->php_version      = $site->php_version ?? '';
        $this->notes            = $site->notes ?? '';
        $this->is_active        = $site->is_active;

        $this->showModal = true;
    }

    // Formu kaydet (ekleme veya güncelleme)
    public function save(): void
    {
        // Form doğrulama kuralları
        $rules = [
            'name' => 'required|string|max:191',
            'url'  => 'required|url|max:191',
            'type' => 'required|in:wordpress,laravel,other',
            'customer_email'   => 'nullable|email|max:191',
            'hosting_provider' => 'nullable|string|max:191',
            'server_ip'        => 'nullable|string|max:45',
            'php_version'      => 'nullable|string|max:20',
            'notes'            => 'nullable|string|max:5000',
            'is_active'        => 'boolean',
        ];

        // Türkçe hata mesajları
        $messages = [
            'name.required' => 'Site adı zorunludur.',
            'url.required'  => 'URL zorunludur.',
            'url.url'       => 'Geçerli bir URL giriniz.',
            'customer_email.email' => 'Geçerli bir e-posta adresi giriniz.',
        ];

        $validated = $this->validate($rules, $messages);

        if ($this->editingSiteId) {
            // Güncelleme
            Site::findOrFail($this->editingSiteId)->update($validated);
            session()->flash('message', 'Site başarıyla güncellendi.');
        } else {
            // Yeni kayıt
            Site::create($validated);
            session()->flash('message', 'Site başarıyla eklendi.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    // Silme onay modalını aç
    public function confirmDelete(int $id): void
    {
        $this->editingSiteId = $id;
        $this->showDeleteModal = true;
    }

    // Siteyi sil
    public function delete(): void
    {
        Site::findOrFail($this->editingSiteId)->delete();
        $this->showDeleteModal = false;
        $this->editingSiteId = null;
        session()->flash('message', 'Site başarıyla silindi.');
    }

    // Aktif/Pasif durumunu hızlıca değiştir
    public function toggleActive(int $id): void
    {
        $site = Site::findOrFail($id);
        $site->update(['is_active' => !$site->is_active]);
    }

    // ---------- TOPLU İŞLEMLER ----------

    // Tümünü seç/bırak - mevcut filtrelere uyan tüm siteleri seçer
    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selectedSites = $this->buildFilteredQuery()
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedSites = [];
        }
    }

    // Filtre sorgusunu merkezi bir metotta tut (tekrar kullanmak için)
    private function buildFilteredQuery()
    {
        return Site::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('url', 'like', "%{$this->search}%")
                      ->orWhere('customer_email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterType, fn ($query) => $query->where('type', $this->filterType))
            ->when($this->filterStatus !== '', function ($query) {
                if ($this->filterStatus === 'active') {
                    $query->where('is_active', true);
                } elseif ($this->filterStatus === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->when($this->filterServer !== '', function ($query) {
                if ($this->filterServer === 'none') {
                    $query->whereNull('server_id');
                } else {
                    $query->where('server_id', $this->filterServer);
                }
            })
            ->when($this->filterHealth, function ($query) {
                match ($this->filterHealth) {
                    'down' => $query->whereHas('checks', function ($q) {
                        $q->where('is_up', false)
                          ->whereIn('id', function ($sub) {
                              $sub->selectRaw('MAX(id)')->from('site_checks')->groupBy('site_id');
                          });
                    }),
                    'ssl_expiring' => $query->whereHas('sslCertificate', function ($q) {
                        $q->where('days_remaining', '<=', 30)->where('days_remaining', '>', 0);
                    }),
                    'ssl_expired' => $query->whereHas('sslCertificate', function ($q) {
                        $q->where('days_remaining', '<=', 0);
                    }),
                    'domain_expiring' => $query->whereNotNull('domain_expires_at')
                        ->where('domain_expires_at', '<=', now()->addDays(30))
                        ->where('domain_expires_at', '>', now()),
                    'domain_expired' => $query->whereNotNull('domain_expires_at')
                        ->where('domain_expires_at', '<=', now()),
                    default => null,
                };
            });
    }

    // Toplu aktif yap
    public function bulkActivate(): void
    {
        Site::whereIn('id', $this->selectedSites)->update(['is_active' => true]);
        $count = count($this->selectedSites);
        $this->selectedSites = [];
        $this->selectAll = false;
        session()->flash('message', "{$count} site aktif yapıldı.");
    }

    // Toplu pasif yap
    public function bulkDeactivate(): void
    {
        Site::whereIn('id', $this->selectedSites)->update(['is_active' => false]);
        $count = count($this->selectedSites);
        $this->selectedSites = [];
        $this->selectAll = false;
        session()->flash('message', "{$count} site pasif yapıldı.");
    }

    // Toplu sil onay
    public function confirmBulkDelete(): void
    {
        $this->showBulkDeleteModal = true;
    }

    // Toplu sil
    public function bulkDelete(): void
    {
        $count = count($this->selectedSites);
        Site::whereIn('id', $this->selectedSites)->delete();
        $this->selectedSites = [];
        $this->selectAll = false;
        $this->showBulkDeleteModal = false;
        session()->flash('message', "{$count} site silindi.");
    }

    // Seçili siteleri kontrol et
    public function bulkCheck(): void
    {
        $count = 0;
        foreach ($this->selectedSites as $siteId) {
            Artisan::call('sites:check', ['--site' => $siteId]);
            $count++;
        }
        $this->selectedSites = [];
        $this->selectAll = false;
        session()->flash('message', "{$count} site kontrol edildi.");
    }

    // Form alanlarını sıfırla
    private function resetForm(): void
    {
        $this->name = '';
        $this->url = '';
        $this->type = 'other';
        $this->customer_email = '';
        $this->hosting_provider = '';
        $this->server_ip = '';
        $this->php_version = '';
        $this->notes = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    // Bileşeni render et
    public function render()
    {
        // Sorguyu filtrele ve sırala
        $sites = $this->buildFilteredQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        $servers = Server::orderBy('name')->get();

        return view('livewire.sites.site-manager', compact('sites', 'servers'))
            ->layout('components.layouts.app', [
                'title'  => 'Siteler - SiteWatch',
                'header' => 'Site Yönetimi',
            ]);
    }
}
