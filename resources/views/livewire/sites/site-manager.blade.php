<div>
    {{-- Başarı Mesajı --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg flex items-center justify-between"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            <span>{{ session('message') }}</span>
            <button @click="show = false" class="text-green-600 hover:text-green-800">&times;</button>
        </div>
    @endif

    {{-- Üst Bar: Arama + Filtreler + Ekle Butonu --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            {{-- Arama --}}
            <div class="relative w-full md:w-80">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Site adı, URL veya e-posta ara..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            {{-- Filtreler --}}
            <div class="flex flex-wrap gap-2 items-center">
                {{-- Site Tipi --}}
                <select wire:model.live="filterType" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Tüm Tipler</option>
                    <option value="wordpress">WordPress</option>
                    <option value="laravel">Laravel</option>
                    <option value="other">Diğer</option>
                </select>

                {{-- Durum --}}
                <select wire:model.live="filterStatus" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Tüm Durumlar</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Pasif</option>
                </select>

                {{-- Sunucu --}}
                <select wire:model.live="filterServer" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Tüm Sunucular</option>
                    <option value="none">Sunucu Atanmamış</option>
                    @foreach($servers as $server)
                        <option value="{{ $server->id }}">{{ $server->name }}</option>
                    @endforeach
                </select>

                {{-- Sağlık --}}
                <select wire:model.live="filterHealth" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Tüm Sağlık</option>
                    <option value="down">Çöken Siteler</option>
                    <option value="ssl_expiring">SSL Süresi Dolan</option>
                    <option value="ssl_expired">SSL Süresi Dolmuş</option>
                    <option value="domain_expiring">Domain Süresi Dolan</option>
                    <option value="domain_expired">Domain Süresi Dolmuş</option>
                </select>

                {{-- Yeni Site Ekle --}}
                <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Yeni Site
                </button>
            </div>
        </div>
    </div>

    {{-- Toplu İşlem Toolbar --}}
    @if(count($selectedSites) > 0)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4 flex flex-col md:flex-row items-center justify-between gap-3">
            <span class="text-sm text-blue-800 font-medium">
                {{ count($selectedSites) }} site seçildi
            </span>
            <div class="flex items-center gap-2">
                <button wire:click="bulkCheck"
                        class="px-3 py-1.5 text-xs font-medium bg-white border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-100 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Kontrol Et
                </button>
                <button wire:click="bulkActivate"
                        class="px-3 py-1.5 text-xs font-medium bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Aktif Yap
                </button>
                <button wire:click="bulkDeactivate"
                        class="px-3 py-1.5 text-xs font-medium bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                    Pasif Yap
                </button>
                <button wire:click="confirmBulkDelete"
                        class="px-3 py-1.5 text-xs font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Sil
                </button>
            </div>
        </div>
    @endif

    {{-- Site Tablosu --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        {{-- Toplu Seç Checkbox --}}
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" wire:model.live="selectAll"
                                   class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        </th>
                        {{-- Sıralanabilir kolon başlıkları --}}
                        <th class="px-6 py-3 cursor-pointer hover:bg-gray-100" wire:click="sortBy('name')">
                            <div class="flex items-center gap-1">
                                Site Adı
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
                        <th class="px-6 py-3">URL</th>
                        <th class="px-6 py-3 cursor-pointer hover:bg-gray-100" wire:click="sortBy('type')">
                            <div class="flex items-center gap-1">
                                Tip
                                @if($sortField === 'type')
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
                        <th class="px-6 py-3">Müşteri E-posta</th>
                        <th class="px-6 py-3">Hosting</th>
                        <th class="px-6 py-3">Durum</th>
                        <th class="px-6 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($sites as $site)
                        <tr class="hover:bg-gray-50 transition {{ in_array($site->id, $selectedSites) ? 'bg-blue-50' : '' }}" wire:key="site-{{ $site->id }}">
                            {{-- Checkbox --}}
                            <td class="px-4 py-4">
                                <input type="checkbox" wire:model.live="selectedSites" value="{{ $site->id }}"
                                       class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                            </td>
                            {{-- Site Adı --}}
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $site->name }}
                            </td>

                            {{-- URL --}}
                            <td class="px-6 py-4">
                                <a href="{{ $site->url }}" target="_blank" class="text-blue-600 hover:underline text-xs">
                                    {{ Str::limit($site->url, 35) }}
                                </a>
                            </td>

                            {{-- Site Tipi Rozeti --}}
                            <td class="px-6 py-4">
                                @switch($site->type)
                                    @case('wordpress')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">WordPress</span>
                                        @break
                                    @case('laravel')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Laravel</span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Diğer</span>
                                @endswitch
                            </td>

                            {{-- Müşteri E-posta --}}
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                {{ $site->customer_email ?? '-' }}
                            </td>

                            {{-- Hosting --}}
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                {{ $site->hosting_provider ?? '-' }}
                            </td>

                            {{-- Aktif/Pasif Durumu --}}
                            <td class="px-6 py-4">
                                <button wire:click="toggleActive({{ $site->id }})" class="focus:outline-none">
                                    @if($site->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Pasif
                                        </span>
                                    @endif
                                </button>
                            </td>

                            {{-- İşlem Butonları --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Detay --}}
                                    <a href="{{ route('admin.sites.show', $site) }}"
                                       class="text-gray-600 hover:text-gray-800 p-1 rounded hover:bg-gray-50 transition"
                                       title="Detay">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{-- Düzenle --}}
                                    <button wire:click="edit({{ $site->id }})"
                                            class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition"
                                            title="Düzenle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    {{-- Sil --}}
                                    <button wire:click="confirmDelete({{ $site->id }})"
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
                        {{-- Kayıt bulunamadı --}}
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                                </svg>
                                <p class="text-lg font-medium">Henüz site eklenmemiş</p>
                                <p class="text-sm mt-1">Yukarıdaki "Yeni Site" butonuyla başlayın.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Sayfalama --}}
        @if($sites->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $sites->links() }}
            </div>
        @endif

        {{-- Toplam Kayıt Bilgisi --}}
        <div class="px-6 py-3 bg-gray-50 text-xs text-gray-500 border-t">
            Toplam {{ $sites->total() }} site kayıtlı
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- EKLEME / DÜZENLEME MODALI --}}
    {{-- ============================================ --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-data x-transition>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto"
                 @click.outside="$wire.set('showModal', false)">

                {{-- Modal Başlık --}}
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        {{ $editingSiteId ? 'Siteyi Düzenle' : 'Yeni Site Ekle' }}
                    </h2>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Form --}}
                <form wire:submit="save" class="p-6 space-y-4">
                    {{-- Site Adı & URL --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site Adı *</label>
                            <input type="text" wire:model="name" placeholder="Müşteri veya site adı"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                          @error('name') border-red-500 @enderror">
                            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL *</label>
                            <input type="url" wire:model="url" placeholder="https://example.com"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                          @error('url') border-red-500 @enderror">
                            @error('url') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Site Tipi & E-posta --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site Tipi *</label>
                            <select wire:model="type"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                <option value="wordpress">WordPress</option>
                                <option value="laravel">Laravel</option>
                                <option value="other">Diğer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri E-posta</label>
                            <input type="email" wire:model="customer_email" placeholder="musteri@example.com"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                          @error('customer_email') border-red-500 @enderror">
                            @error('customer_email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Hosting & Sunucu IP --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hosting Sağlayıcı</label>
                            <input type="text" wire:model="hosting_provider" placeholder="Turhost, DigitalOcean..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sunucu IP</label>
                            <input type="text" wire:model="server_ip" placeholder="192.168.1.1"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    {{-- PHP Versiyon & Aktif/Pasif --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PHP Versiyonu</label>
                            <input type="text" wire:model="php_version" placeholder="8.3"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_active"
                                       class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <span class="text-sm text-gray-700">Site izleme aktif</span>
                            </label>
                        </div>
                    </div>

                    {{-- Notlar --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
                        <textarea wire:model="notes" rows="3" placeholder="Ek notlar..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    {{-- Butonlar --}}
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            İptal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            {{ $editingSiteId ? 'Güncelle' : 'Kaydet' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- SİLME ONAY MODALI --}}
    {{-- ============================================ --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-data x-transition>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
                <div class="p-6 text-center">
                    {{-- Uyarı İkonu --}}
                    <div class="mx-auto w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Siteyi Sil</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Bu siteyi ve tüm ilişkili verileri (kontrol kayıtları, SSL, bildirimler) kalıcı olarak silmek istediğinize emin misiniz?
                    </p>
                    <div class="flex justify-center gap-3">
                        <button wire:click="$set('showDeleteModal', false)"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Vazgeç
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

    {{-- ============================================ --}}
    {{-- TOPLU SİLME ONAY MODALI --}}
    {{-- ============================================ --}}
    @if($showBulkDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-data x-transition>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
                <div class="p-6 text-center">
                    <div class="mx-auto w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Toplu Silme</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        <strong>{{ count($selectedSites) }}</strong> siteyi ve tüm ilişkili verileri kalıcı olarak silmek istediğinize emin misiniz?
                    </p>
                    <div class="flex justify-center gap-3">
                        <button wire:click="$set('showBulkDeleteModal', false)"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Vazgeç
                        </button>
                        <button wire:click="bulkDelete"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                            Evet, {{ count($selectedSites) }} Siteyi Sil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
