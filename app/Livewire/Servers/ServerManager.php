<?php

namespace App\Livewire\Servers;

use App\Models\Server;
use Livewire\Component;
use Livewire\WithPagination;

class ServerManager extends Component
{
    use WithPagination;

    // ---------- FİLTRE & ARAMA ----------
    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    // ---------- MODAL DURUMU ----------
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingServerId = null;

    // ---------- FORM ALANLARI ----------
    public string $name = '';
    public string $whm_host = '';
    public string $whm_user = '';
    public string $whm_token = '';
    public int $whm_port = 2087;

    // ---------- SYNC DURUMU ----------
    public bool $syncing = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // ---------- CRUD İŞLEMLERİ ----------

    public function create(): void
    {
        $this->resetForm();
        $this->editingServerId = null;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $server = Server::findOrFail($id);

        $this->editingServerId = $server->id;
        $this->name      = $server->name;
        $this->whm_host  = $server->whm_host;
        $this->whm_user  = $server->whm_user;
        $this->whm_token = ''; // Token güvenlik nedeniyle gösterilmez
        $this->whm_port  = $server->whm_port;

        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'name'      => 'required|string|max:191',
            'whm_host'  => 'required|string|max:191',
            'whm_user'  => 'required|string|max:191',
            'whm_token' => $this->editingServerId ? 'nullable|string' : 'required|string',
            'whm_port'  => 'required|integer|min:1|max:65535',
        ];

        $messages = [
            'name.required'      => 'Sunucu adı zorunludur.',
            'whm_host.required'  => 'WHM host adresi zorunludur.',
            'whm_user.required'  => 'WHM kullanıcı adı zorunludur.',
            'whm_token.required' => 'WHM API token zorunludur.',
            'whm_port.required'  => 'WHM port zorunludur.',
        ];

        $this->validate($rules, $messages);

        if ($this->editingServerId) {
            $server = Server::findOrFail($this->editingServerId);
            $data = [
                'name'     => $this->name,
                'whm_host' => $this->whm_host,
                'whm_user' => $this->whm_user,
                'whm_port' => $this->whm_port,
            ];
            // Token sadece girilmişse güncelle
            if ($this->whm_token !== '') {
                $data['whm_token'] = $this->whm_token;
            }
            $server->update($data);
            session()->flash('message', 'Sunucu başarıyla güncellendi.');
        } else {
            $server = Server::create([
                'name'      => $this->name,
                'whm_host'  => $this->whm_host,
                'whm_user'  => $this->whm_user,
                'whm_token' => $this->whm_token,
                'whm_port'  => $this->whm_port,
            ]);

            // Yeni sunucu eklendiğinde otomatik olarak hesapları çek
            try {
                $result = $server->syncAccounts();
                session()->flash('message', "Sunucu eklendi. WHM'den {$result['synced']} hesap senkronize edildi.");
            } catch (\Exception $e) {
                session()->flash('message', "Sunucu eklendi ancak WHM senkronizasyonu başarısız: {$e->getMessage()}");
            }
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function syncServer(int $id): void
    {
        $server = Server::findOrFail($id);

        try {
            $result = $server->syncAccounts();
            session()->flash('message', "{$server->name}: {$result['synced']} hesap senkronize edildi.");
        } catch (\Exception $e) {
            session()->flash('error', "Senkronizasyon hatası: {$e->getMessage()}");
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->editingServerId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        Server::findOrFail($this->editingServerId)->delete();
        $this->showDeleteModal = false;
        $this->editingServerId = null;
        session()->flash('message', 'Sunucu başarıyla silindi.');
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->whm_host = '';
        $this->whm_user = '';
        $this->whm_token = '';
        $this->whm_port = 2087;
        $this->resetValidation();
    }

    public function render()
    {
        $servers = Server::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('whm_host', 'like', "%{$this->search}%");
                });
            })
            ->withCount('sites')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.servers.server-manager', compact('servers'))
            ->layout('components.layouts.app', [
                'title'  => 'Sunucular - SiteWatch',
                'header' => 'Sunucu Yönetimi',
            ]);
    }
}
