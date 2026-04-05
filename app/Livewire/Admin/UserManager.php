<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    // Modal
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingUserId = null;

    // Form
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'viewer';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRole(): void
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

    public function create(): void
    {
        $this->resetForm();
        $this->editingUserId = null;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $user->id;
        $this->name  = $user->name;
        $this->email = $user->email;
        $this->role  = $user->role;
        $this->password = '';
        $this->password_confirmation = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'name'  => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email,' . $this->editingUserId,
            'role'  => 'required|in:super_admin,admin,viewer',
        ];

        if ($this->editingUserId) {
            $rules['password'] = 'nullable|string|min:6|confirmed';
        } else {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        $messages = [
            'name.required'      => 'İsim zorunludur.',
            'email.required'     => 'E-posta zorunludur.',
            'email.unique'       => 'Bu e-posta zaten kayıtlı.',
            'password.required'  => 'Şifre zorunludur.',
            'password.min'       => 'Şifre en az 6 karakter olmalıdır.',
            'password.confirmed' => 'Şifreler eşleşmiyor.',
        ];

        $this->validate($rules, $messages);

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $data = [
                'name'  => $this->name,
                'email' => $this->email,
                'role'  => $this->role,
            ];
            if ($this->password !== '') {
                $data['password'] = $this->password;
            }
            $user->update($data);
            session()->flash('message', 'Kullanıcı güncellendi.');
        } else {
            User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => $this->password,
                'role'     => $this->role,
            ]);
            session()->flash('message', 'Kullanıcı oluşturuldu.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        // Kendini silmeye çalışmasın
        if ($id === auth()->id()) {
            session()->flash('error', 'Kendi hesabınızı silemezsiniz.');
            return;
        }
        $this->editingUserId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        User::findOrFail($this->editingUserId)->delete();
        $this->showDeleteModal = false;
        $this->editingUserId = null;
        session()->flash('message', 'Kullanıcı silindi.');
    }

    // Kilidi kaldır
    public function unlock(int $id): void
    {
        $user = User::findOrFail($id);
        $user->resetFailedAttempts();
        session()->flash('message', "{$user->name} kullanıcısının kilidi kaldırıldı.");
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = 'viewer';
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterRole, fn ($q) => $q->where('role', $this->filterRole))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.admin.user-manager', compact('users'))
            ->layout('components.layouts.app', [
                'title'  => 'Kullanıcılar - SiteWatch',
                'header' => 'Kullanıcı Yönetimi',
            ]);
    }
}
