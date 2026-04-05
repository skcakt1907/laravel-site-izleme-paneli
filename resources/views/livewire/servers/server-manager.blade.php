<div>
    {{-- Basari Mesaji --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg flex items-center justify-between"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
            <span>{{ session('message') }}</span>
            <button @click="show = false" class="text-green-600 hover:text-green-800">&times;</button>
        </div>
    @endif

    {{-- Hata Mesaji --}}
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg flex items-center justify-between"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
            <span>{{ session('error') }}</span>
            <button @click="show = false" class="text-red-600 hover:text-red-800">&times;</button>
        </div>
    @endif

    {{-- Ust Bar: Arama + Ekle Butonu --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            {{-- Arama --}}
            <div class="relative w-full md:w-80">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Sunucu adi veya host ara..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            {{-- Yeni Sunucu Ekle Butonu --}}
            <button wire:click="create"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Yeni Sunucu
            </button>
        </div>
    </div>

    {{-- Sunucu Tablosu --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 cursor-pointer hover:bg-gray-100" wire:click="sortBy('name')">
                            <div class="flex items-center gap-1">
                                Sunucu Adi
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
                        <th class="px-6 py-3">WHM Host</th>
                        <th class="px-6 py-3">WHM Kullanici</th>
                        <th class="px-6 py-3">Port</th>
                        <th class="px-6 py-3 cursor-pointer hover:bg-gray-100" wire:click="sortBy('sites_count')">
                            <div class="flex items-center gap-1">
                                Site Sayisi
                            </div>
                        </th>
                        <th class="px-6 py-3 text-right">Islemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($servers as $server)
                        <tr class="hover:bg-gray-50 transition" wire:key="server-{{ $server->id }}">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $server->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                {{ $server->whm_host }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                {{ $server->whm_user }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                {{ $server->whm_port }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $server->sites_count }} site
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Senkronize Et --}}
                                    <button wire:click="syncServer({{ $server->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="syncServer({{ $server->id }})"
                                            class="text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50 transition"
                                            title="Hesaplari Senkronize Et">
                                        <svg class="w-4 h-4" wire:loading.class="animate-spin" wire:target="syncServer({{ $server->id }})" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>

                                    {{-- Duzenle --}}
                                    <button wire:click="edit({{ $server->id }})"
                                            class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition"
                                            title="Duzenle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    {{-- Sil --}}
                                    <button wire:click="confirmDelete({{ $server->id }})"
                                            class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition"
                                            title="Sil">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                                </svg>
                                <p class="text-lg font-medium">Henuz sunucu eklenmemis</p>
                                <p class="text-sm mt-1">Yukaridaki "Yeni Sunucu" butonuyla baslayin.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($servers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $servers->links() }}
            </div>
        @endif

        <div class="px-6 py-3 bg-gray-50 text-xs text-gray-500 border-t">
            Toplam {{ $servers->total() }} sunucu kayitli
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- EKLEME / DUZENLEME MODALI --}}
    {{-- ============================================ --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-data x-transition>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto"
                 @click.outside="$wire.set('showModal', false)">

                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        {{ $editingServerId ? 'Sunucuyu Duzenle' : 'Yeni Sunucu Ekle' }}
                    </h2>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-4">
                    {{-- Sunucu Adi --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sunucu Adi *</label>
                        <input type="text" wire:model="name" placeholder="Ornek: Ana Sunucu"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      @error('name') border-red-500 @enderror">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- WHM Host & Port --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">WHM Host *</label>
                            <input type="text" wire:model="whm_host" placeholder="server1.example.com"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                          @error('whm_host') border-red-500 @enderror">
                            @error('whm_host') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Port *</label>
                            <input type="number" wire:model="whm_port" placeholder="2087"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                          @error('whm_port') border-red-500 @enderror">
                            @error('whm_port') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- WHM Kullanici --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WHM Kullanici *</label>
                        <input type="text" wire:model="whm_user" placeholder="root"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      @error('whm_user') border-red-500 @enderror">
                        @error('whm_user') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- WHM API Token --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            WHM API Token {{ $editingServerId ? '' : '*' }}
                        </label>
                        <textarea wire:model="whm_token" rows="3"
                                  placeholder="{{ $editingServerId ? 'Degistirmek istemiyorsaniz bos birakin' : 'WHM API token buraya yapistirin' }}"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-xs
                                         @error('whm_token') border-red-500 @enderror"></textarea>
                        @error('whm_token') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    @if(!$editingServerId)
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-xs text-blue-700">
                                <strong>Not:</strong> Sunucu eklendikten sonra WHM API uzerinden tum cPanel hesaplari otomatik olarak cekilecek ve Siteler sayfasina kaydedilecektir.
                            </p>
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Iptal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                            <svg wire:loading wire:target="save" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ $editingServerId ? 'Guncelle' : 'Kaydet ve Senkronize Et' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- SILME ONAY MODALI --}}
    {{-- ============================================ --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-data x-transition>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
                <div class="p-6 text-center">
                    <div class="mx-auto w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Sunucuyu Sil</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Bu sunucuyu silmek istediginize emin misiniz? Sunucuya bagli sitelerin server_id degeri bos olarak kalacaktir.
                    </p>
                    <div class="flex justify-center gap-3">
                        <button wire:click="$set('showDeleteModal', false)"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Vazgec
                        </button>
                        <button wire:click="delete"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                            Evet, Sil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
