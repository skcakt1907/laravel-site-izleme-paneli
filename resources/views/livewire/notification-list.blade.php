<div>
    {{-- Tip Bazlı Özet Kartları --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-red-600">{{ $countByType['site_down'] }}</div>
                <div class="text-xs text-gray-500">Site Çöktü</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m5.657-9.657a8 8 0 11-11.314 0"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-yellow-600">{{ $countByType['ssl_expiry'] }}</div>
                <div class="text-xs text-gray-500">SSL Uyarı</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-orange-600">{{ $countByType['disk_warning'] }}</div>
                <div class="text-xs text-gray-500">Disk Uyarı</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-blue-600">{{ $countByType['update_available'] }}</div>
                <div class="text-xs text-gray-500">Güncelleme</div>
            </div>
        </div>
    </div>

    {{-- Arama + Filtreler --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center">
            {{-- Arama --}}
            <div class="relative w-full md:w-80">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Site adı veya bildirim konusu ara..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            {{-- Tip Filtresi --}}
            <select wire:model.live="filterType" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Tüm Tipler</option>
                <option value="site_down">Site Çöktü</option>
                <option value="ssl_expiry">SSL Uyarı</option>
                <option value="disk_warning">Disk Uyarı</option>
                <option value="update_available">Güncelleme</option>
            </select>

            {{-- Kanal Filtresi --}}
            <select wire:model.live="filterChannel" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Tüm Kanallar</option>
                <option value="mail">Mail</option>
                <option value="slack">Slack</option>
            </select>
        </div>
    </div>

    {{-- Bildirim Tablosu --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 cursor-pointer hover:bg-gray-100" wire:click="sortBy('sent_at')">
                            <div class="flex items-center gap-1">
                                Tarih
                                @if($sortField === 'sent_at')
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
                        <th class="px-6 py-3">Site</th>
                        <th class="px-6 py-3">Tip</th>
                        <th class="px-6 py-3">Konu</th>
                        <th class="px-6 py-3">Kanal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($notifications as $notif)
                        <tr class="hover:bg-gray-50" wire:key="notif-{{ $notif->id }}">
                            <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap">
                                {{ $notif->sent_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 font-medium">
                                @if($notif->site)
                                    <a href="{{ route('admin.sites.show', $notif->site) }}" class="text-blue-600 hover:underline">
                                        {{ $notif->site->name }}
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @switch($notif->type)
                                    @case('site_down')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Site Çöktü</span>
                                        @break
                                    @case('ssl_expiry')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">SSL Uyarı</span>
                                        @break
                                    @case('disk_warning')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Disk Uyarı</span>
                                        @break
                                    @case('update_available')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Güncelleme</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 text-gray-700 max-w-md truncate">
                                {{ $notif->subject }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">
                                    {{ $notif->channel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <p class="text-lg font-medium">Henüz bildirim yok</p>
                                <p class="text-sm mt-1">Siteler kontrol edildikçe bildirimler burada görünecek.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $notifications->links() }}
            </div>
        @endif

        <div class="px-6 py-3 bg-gray-50 text-xs text-gray-500 border-t">
            Toplam {{ $notifications->total() }} bildirim
        </div>
    </div>
</div>
