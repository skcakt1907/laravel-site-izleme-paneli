<div wire:poll.{{ $refreshInterval }}s>

    {{-- ==================== ÜST İSTATİSTİK KARTLARI ==================== --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        {{-- Toplam Site --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="text-xs text-gray-500 uppercase font-medium">Toplam Site</div>
            <div class="text-3xl font-bold text-gray-900 mt-1">{{ $totalSites }}</div>
        </div>

        {{-- Aktif --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="text-xs text-gray-500 uppercase font-medium">Aktif</div>
            <div class="text-3xl font-bold text-green-600 mt-1">{{ $activeSites }}</div>
        </div>

        {{-- Çöken --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="text-xs text-gray-500 uppercase font-medium">Çöken</div>
            <div class="text-3xl font-bold {{ $downSites->count() > 0 ? 'text-red-600' : 'text-gray-400' }} mt-1">
                {{ $downSites->count() }}
            </div>
        </div>

        {{-- Uptime (24s) --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="text-xs text-gray-500 uppercase font-medium">Uptime (24s)</div>
            <div class="text-3xl font-bold {{ $uptimePercent >= 99 ? 'text-green-600' : ($uptimePercent >= 95 ? 'text-yellow-600' : 'text-red-600') }} mt-1">
                %{{ $uptimePercent }}
            </div>
        </div>

        {{-- Ort. Yanıt --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="text-xs text-gray-500 uppercase font-medium">Ort. Yanıt</div>
            <div class="text-3xl font-bold text-gray-800 mt-1">
                {{ $avgResponseTime ? round($avgResponseTime) . 'ms' : '-' }}
            </div>
        </div>

        {{-- SSL Uyarı --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="text-xs text-gray-500 uppercase font-medium">SSL Uyarı</div>
            <div class="text-3xl font-bold {{ ($sslWarnings->count() + $sslExpired->count()) > 0 ? 'text-yellow-600' : 'text-gray-400' }} mt-1">
                {{ $sslWarnings->count() + $sslExpired->count() }}
            </div>
        </div>
    </div>

    {{-- ==================== SITE TİP DAĞILIMI ==================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- WordPress --}}
        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <span class="text-blue-600 font-bold text-lg">W</span>
            </div>
            <div>
                <div class="text-sm text-gray-500">WordPress</div>
                <div class="text-2xl font-bold text-gray-900">{{ $wpSites }}</div>
            </div>
            @if($totalSites > 0)
                <div class="ml-auto text-sm text-gray-400">%{{ round(($wpSites / $totalSites) * 100) }}</div>
            @endif
        </div>

        {{-- Laravel --}}
        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <span class="text-red-600 font-bold text-lg">L</span>
            </div>
            <div>
                <div class="text-sm text-gray-500">Laravel</div>
                <div class="text-2xl font-bold text-gray-900">{{ $laravelSites }}</div>
            </div>
            @if($totalSites > 0)
                <div class="ml-auto text-sm text-gray-400">%{{ round(($laravelSites / $totalSites) * 100) }}</div>
            @endif
        </div>

        {{-- Diğer --}}
        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                <span class="text-gray-600 font-bold text-lg">D</span>
            </div>
            <div>
                <div class="text-sm text-gray-500">Diğer</div>
                <div class="text-2xl font-bold text-gray-900">{{ $otherSites }}</div>
            </div>
            @if($totalSites > 0)
                <div class="ml-auto text-sm text-gray-400">%{{ round(($otherSites / $totalSites) * 100) }}</div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- ==================== ÇÖKEN SİTELER ==================== --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Çöken Siteler</h3>
                @if($downSites->count() > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ $downSites->count() }}</span>
                @endif
            </div>
            <div class="p-5">
                @forelse ($downSites as $site)
                    <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                        <div>
                            <a href="{{ route('admin.sites.show', $site) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">{{ $site->name }}</a>
                            <div class="text-xs text-gray-400">{{ $site->url }}</div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">Kapalı</span>
                            @if($site->latestCheck)
                                <div class="text-xs text-gray-400 mt-0.5">{{ $site->latestCheck->checked_at->diffForHumans() }}</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p class="text-sm">Tüm siteler çalışıyor</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ==================== SSL UYARILARI ==================== --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">SSL Sertifika Uyarıları</h3>
            </div>
            <div class="p-5">
                @if($sslExpired->count() > 0)
                    @foreach ($sslExpired as $cert)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <a href="{{ route('admin.sites.show', $cert->site) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">{{ $cert->site->name }}</a>
                            <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">Süresi Dolmuş</span>
                        </div>
                    @endforeach
                @endif

                @forelse ($sslWarnings as $cert)
                    <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                        <a href="{{ route('admin.sites.show', $cert->site) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">{{ $cert->site->name }}</a>
                        <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">{{ $cert->days_remaining }} gün</span>
                    </div>
                @empty
                    @if($sslExpired->count() === 0)
                        <div class="text-center py-6 text-gray-400">
                            <svg class="w-8 h-8 mx-auto mb-2 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <p class="text-sm">Tüm sertifikalar güncel</p>
                        </div>
                    @endif
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- ==================== SON HTTP KONTROLLERİ ==================== --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Son HTTP Kontrolleri</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left">Site</th>
                            <th class="px-4 py-2 text-left">Durum</th>
                            <th class="px-4 py-2 text-left">Süre</th>
                            <th class="px-4 py-2 text-left">Zaman</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($recentChecks as $check)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 font-medium">
                                    <a href="{{ route('admin.sites.show', $check->site) }}" class="hover:text-blue-600">{{ $check->site->name }}</a>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-0.5 rounded-full text-xs {{ $check->is_up ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $check->is_up ? $check->status_code : 'Kapalı' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-gray-500">{{ $check->response_time_ms ? $check->response_time_ms . 'ms' : '-' }}</td>
                                <td class="px-4 py-2 text-xs text-gray-400">{{ $check->checked_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Henüz kontrol yapılmamış</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ==================== SON BİLDİRİMLER ==================== --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Son Bildirimler</h3>
            </div>
            <div class="p-5">
                @forelse ($recentNotifications as $notif)
                    <div class="flex items-start gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                        {{-- Bildirim tip ikonu --}}
                        @switch($notif->type)
                            @case('site_down')
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                @break
                            @case('ssl_expiry')
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m5.657-9.657a8 8 0 11-11.314 0"/></svg>
                                </div>
                                @break
                            @case('disk_warning')
                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7"/></svg>
                                </div>
                                @break
                            @default
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                        @endswitch

                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800 truncate">{{ $notif->subject }}</p>
                            <p class="text-xs text-gray-400">{{ $notif->site->name }} &middot; {{ $notif->sent_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-400">
                        <p class="text-sm">Henüz bildirim yok</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
