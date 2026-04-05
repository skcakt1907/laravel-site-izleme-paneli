<div>
    {{-- Mesajlar --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg flex items-center justify-between"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            <span>{{ session('message') }}</span>
            <button @click="show = false" class="text-green-600 hover:text-green-800">&times;</button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg flex items-center justify-between"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
            <span>{{ session('error') }}</span>
            <button @click="show = false" class="text-red-600 hover:text-red-800">&times;</button>
        </div>
    @endif

    {{-- Üst Bar --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="relative w-full md:w-80">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="İsim veya e-posta ara..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <div class="flex gap-3 items-center">
                <select wire:model.live="filterRole" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Tüm Roller</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="admin">Admin</option>
                    <option value="viewer">Viewer</option>
                </select>
                <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Yeni Kullanıcı
                </button>
            </div>
        </div>
    </div>

    {{-- Tablo --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 cursor-pointer hover:bg-gray-100" wire:click="sortBy('name')">
                            <div class="flex items-center gap-1">
                                İsim
                                @if($sortField === 'name')
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        @if($sortDirection === 'asc')
                                            <path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/>
                                        @else
                                            <path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/>
                                        @endif
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3">E-posta</th>
                        <th class="px-6 py-3">Rol</th>
                        <th class="px-6 py-3">Durum</th>
                        <th class="px-6 py-3 cursor-pointer hover:bg-gray-100" wire:click="sortBy('created_at')">
                            <div class="flex items-center gap-1">
                                Kayıt Tarihi
                                @if($sortField === 'created_at')
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        @if($sortDirection === 'asc')
                                            <path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/>
                                        @else
                                            <path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/>
                                        @endif
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 transition" wire:key="user-{{ $user->id }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @switch($user->role)
                                    @case('super_admin')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Super Admin</span>
                                        @break
                                    @case('admin')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Admin</span>
                                        @break
                                    @default
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Viewer</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4">
                                @if($user->isLocked())
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Kilitli</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs">{{ $user->created_at->format('d.m.Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($user->isLocked())
                                        <button wire:click="unlock({{ $user->id }})"
                                                class="text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50 transition"
                                                title="Kilidi Kaldır">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    @endif
                                    <button wire:click="edit({{ $user->id }})"
                                            class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition"
                                            title="Düzenle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button wire:click="confirmDelete({{ $user->id }})"
                                                class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition"
                                                title="Sil">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <p class="text-lg font-medium">Kullanıcı bulunamadı</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif

        <div class="px-6 py-3 bg-gray-50 text-xs text-gray-500 border-t">
            Toplam {{ $users->total() }} kullanıcı
        </div>
    </div>

    {{-- EKLEME / DÜZENLEME MODALI --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-data x-transition>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto"
                 @click.outside="$wire.set('showModal', false)">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        {{ $editingUserId ? 'Kullanıcıyı Düzenle' : 'Yeni Kullanıcı' }}
                    </h2>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">İsim *</label>
                        <input type="text" wire:model="name" placeholder="Ad Soyad"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-posta *</label>
                        <input type="email" wire:model="email" placeholder="admin@example.com"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rol *</label>
                        <select wire:model="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="super_admin">Super Admin — her şeye erişir</option>
                            <option value="admin">Admin — siteleri yönetir</option>
                            <option value="viewer">Viewer — sadece dashboard görür</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Şifre {{ $editingUserId ? '(değiştirmek için)' : '*' }}
                            </label>
                            <input type="password" wire:model="password" placeholder="••••••••"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Şifre Tekrar</label>
                            <input type="password" wire:model="password_confirmation" placeholder="••••••••"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">İptal</button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            {{ $editingUserId ? 'Güncelle' : 'Oluştur' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- SİLME ONAY MODALI --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-data x-transition>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
                <div class="p-6 text-center">
                    <div class="mx-auto w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Kullanıcıyı Sil</h3>
                    <p class="text-sm text-gray-500 mb-6">Bu kullanıcıyı kalıcı olarak silmek istediğinize emin misiniz?</p>
                    <div class="flex justify-center gap-3">
                        <button wire:click="$set('showDeleteModal', false)"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Vazgeç</button>
                        <button wire:click="delete"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">Evet, Sil</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
