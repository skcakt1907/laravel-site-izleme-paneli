<div>
    {{-- Başarı Mesajı --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            {{ session('message') }}
        </div>
    @endif

    {{-- Geri Butonu + Site Özeti --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.sites.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Sitelere Dön
        </a>
        <div class="flex items-center gap-3">
            @switch($site->type)
                @case('wordpress')
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">WordPress</span>
                    @break
                @case('laravel')
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Laravel</span>
                    @break
                @default
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Diğer</span>
            @endswitch
            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $site->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $site->is_active ? 'Aktif' : 'Pasif' }}
            </span>
        </div>
    </div>

    {{-- Sekmeler --}}
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex gap-0 px-4">
                @foreach ([
                    'overview'    => 'Genel Bakış',
                    'checks'     => 'HTTP Kontrolleri',
                    'ssl'        => 'SSL Sertifika',
                    'pagespeed'  => 'Hız Skoru',
                    'cpanel'     => 'cPanel',
                    'wordpress'  => 'WordPress',
                    'notifications' => 'Bildirimler',
                ] as $tab => $label)
                    @if ($tab === 'wordpress' && $site->type !== 'wordpress') @continue @endif
                    <button wire:click="setTab('{{ $tab }}')"
                            class="px-4 py-3 text-sm font-medium border-b-2 transition
                                   {{ $activeTab === $tab ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        <div class="p-6">
            {{-- ==================== GENEL BAKIŞ ==================== --}}
            @if ($activeTab === 'overview')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Site Bilgileri --}}
                    <div class="space-y-3">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Site Bilgileri</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between"><dt class="text-gray-500">URL</dt><dd><a href="{{ $site->url }}" target="_blank" class="text-blue-600 hover:underline">{{ $site->url }}</a></dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Müşteri E-posta</dt><dd>{{ $site->customer_email ?? '-' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Hosting</dt><dd>{{ $site->hosting_provider ?? '-' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Sunucu IP</dt><dd>{{ $site->server_ip ?? '-' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">PHP Versiyon</dt><dd>{{ $site->php_version ?? '-' }}</dd></div>
                        </dl>
                    </div>

                    {{-- Durum Kartları --}}
                    <div class="space-y-3">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Durum Özeti</h3>
                        @php $lastCheck = $site->checks->first(); @endphp
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            {{-- Son HTTP Durumu --}}
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-500">HTTP Durumu</div>
                                @if($lastCheck)
                                    <div class="text-lg font-bold {{ $lastCheck->is_up ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $lastCheck->is_up ? 'Açık' : 'Kapalı' }}
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $lastCheck->checked_at->diffForHumans() }}</div>
                                @else
                                    <div class="text-lg font-bold text-gray-400">-</div>
                                @endif
                            </div>

                            {{-- SSL Durumu --}}
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-500">SSL Sertifika</div>
                                @if($site->sslCertificate)
                                    <div class="text-lg font-bold {{ $site->sslCertificate->days_remaining > 30 ? 'text-green-600' : ($site->sslCertificate->days_remaining > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $site->sslCertificate->days_remaining }} gün
                                    </div>
                                @else
                                    <div class="text-lg font-bold text-gray-400">-</div>
                                @endif
                            </div>

                            {{-- Disk Kullanımı --}}
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-500">Disk Kullanımı</div>
                                @if($site->cpanelAccount && $site->cpanelAccount->disk_usage_percent)
                                    <div class="text-lg font-bold {{ $site->cpanelAccount->disk_usage_percent < 90 ? 'text-green-600' : 'text-red-600' }}">
                                        %{{ $site->cpanelAccount->disk_usage_percent }}
                                    </div>
                                @else
                                    <div class="text-lg font-bold text-gray-400">-</div>
                                @endif
                            </div>

                            {{-- Yanıt Süresi --}}
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-500">Yanıt Süresi</div>
                                @if($lastCheck && $lastCheck->response_time_ms)
                                    <div class="text-lg font-bold text-gray-800">{{ $lastCheck->response_time_ms }}ms</div>
                                @else
                                    <div class="text-lg font-bold text-gray-400">-</div>
                                @endif
                            </div>

                            {{-- PageSpeed Mobil --}}
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-500">Hız (Mobil)</div>
                                @if($site->latestPageSpeed)
                                    <div class="text-lg font-bold {{ $site->latestPageSpeed->mobile_score >= 90 ? 'text-green-600' : ($site->latestPageSpeed->mobile_score >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $site->latestPageSpeed->mobile_score }}
                                    </div>
                                @else
                                    <div class="text-lg font-bold text-gray-400">-</div>
                                @endif
                            </div>

                            {{-- PageSpeed Desktop --}}
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-500">Hız (Desktop)</div>
                                @if($site->latestPageSpeed)
                                    <div class="text-lg font-bold {{ $site->latestPageSpeed->desktop_score >= 90 ? 'text-green-600' : ($site->latestPageSpeed->desktop_score >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $site->latestPageSpeed->desktop_score }}
                                    </div>
                                @else
                                    <div class="text-lg font-bold text-gray-400">-</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Uptime Grafiği (Son 30 Gün) --}}
                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Uptime Grafiği — Son 30 Gün</h3>
                    <div class="bg-gray-50 rounded-xl p-4" style="height: 280px;">
                        <canvas id="uptimeChart"
                                x-data="{
                                    chart: null,
                                    init() {
                                        this.chart = new Chart(document.getElementById('uptimeChart'), {
                                            type: 'line',
                                            data: {
                                                labels: @js($uptimeChart['labels']),
                                                datasets: [{
                                                    label: 'Uptime %',
                                                    data: @js($uptimeChart['data']),
                                                    borderColor: '#3b82f6',
                                                    backgroundColor: 'rgba(59, 130, 246, 0.08)',
                                                    borderWidth: 2.5,
                                                    fill: true,
                                                    tension: 0.35,
                                                    pointRadius: 0,
                                                    pointHoverRadius: 5,
                                                    pointHoverBackgroundColor: '#3b82f6',
                                                    pointHoverBorderColor: '#fff',
                                                    pointHoverBorderWidth: 2,
                                                    spanGaps: true,
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                interaction: { intersect: false, mode: 'index' },
                                                plugins: {
                                                    legend: { display: false },
                                                    tooltip: {
                                                        backgroundColor: '#1f2937',
                                                        titleColor: '#9ca3af',
                                                        bodyColor: '#fff',
                                                        titleFont: { size: 11 },
                                                        bodyFont: { size: 13, weight: 'bold' },
                                                        padding: 10,
                                                        cornerRadius: 8,
                                                        displayColors: false,
                                                        callbacks: {
                                                            label: (ctx) => ctx.parsed.y !== null ? '%' + ctx.parsed.y : 'Veri yok'
                                                        }
                                                    }
                                                },
                                                scales: {
                                                    y: {
                                                        min: 0, max: 100,
                                                        ticks: { callback: (v) => '%' + v, color: '#9ca3af', font: { size: 11 } },
                                                        grid: { color: 'rgba(0,0,0,0.04)' },
                                                        border: { display: false }
                                                    },
                                                    x: {
                                                        ticks: { color: '#9ca3af', font: { size: 10 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 10 },
                                                        grid: { display: false },
                                                        border: { display: false }
                                                    }
                                                }
                                            }
                                        });
                                    }
                                }"
                                x-init="init()">
                        </canvas>
                    </div>
                </div>

                {{-- Domain Süresi --}}
                @if($site->domain_expires_at)
                    <div class="mt-6 p-4 rounded-lg {{ $site->isDomainExpired() ? 'bg-red-50 border border-red-200' : ($site->isDomainExpiringSoon() ? 'bg-yellow-50 border border-yellow-200' : 'bg-green-50 border border-green-200') }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-600">Domain Süresi</h3>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $site->domain }} &mdash; Son kontrol: {{ $site->domain_checked_at?->diffForHumans() ?? '-' }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold {{ $site->isDomainExpired() ? 'text-red-600' : ($site->isDomainExpiringSoon() ? 'text-yellow-600' : 'text-green-600') }}">
                                    @if($site->isDomainExpired())
                                        Süresi Dolmuş
                                    @else
                                        {{ $site->domain_expires_at->diffInDays(now()) }} gün kaldı
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">{{ $site->domain_expires_at->format('d.m.Y') }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Notlar --}}
                @if($site->notes)
                    <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Notlar</h3>
                        <p class="text-sm text-gray-700">{{ $site->notes }}</p>
                    </div>
                @endif

            {{-- ==================== HTTP KONTROLLERİ ==================== --}}
            @elseif ($activeTab === 'checks')
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left">Zaman</th>
                            <th class="px-4 py-2 text-left">Durum</th>
                            <th class="px-4 py-2 text-left">HTTP Kodu</th>
                            <th class="px-4 py-2 text-left">Yanıt Süresi</th>
                            <th class="px-4 py-2 text-left">Ard Arda Hata</th>
                            <th class="px-4 py-2 text-left">Hata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($site->checks as $check)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-gray-500">{{ $check->checked_at->format('d.m.Y H:i') }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-0.5 rounded-full text-xs {{ $check->is_up ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $check->is_up ? 'Açık' : 'Kapalı' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">{{ $check->status_code ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $check->response_time_ms ? $check->response_time_ms . 'ms' : '-' }}</td>
                                <td class="px-4 py-2">{{ $check->consecutive_failures ?: '-' }}</td>
                                <td class="px-4 py-2 text-xs text-red-500 max-w-xs truncate">{{ $check->error_message ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Henüz kontrol yapılmamış</td></tr>
                        @endforelse
                    </tbody>
                </table>

            {{-- ==================== SSL SERTİFİKA ==================== --}}
            @elseif ($activeTab === 'ssl')
                @if ($site->sslCertificate)
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500">Sağlayıcı</div>
                            <div class="text-sm font-semibold mt-1">{{ $site->sslCertificate->issuer }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500">Son Kullanma</div>
                            <div class="text-sm font-semibold mt-1">{{ $site->sslCertificate->expires_at->format('d.m.Y') }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500">Kalan Gün</div>
                            <div class="text-sm font-semibold mt-1 {{ $site->sslCertificate->days_remaining > 30 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $site->sslCertificate->days_remaining }} gün
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500">Son Kontrol</div>
                            <div class="text-sm font-semibold mt-1">{{ $site->sslCertificate->last_checked_at?->diffForHumans() ?? '-' }}</div>
                        </div>
                    </div>
                @else
                    <p class="text-center text-gray-400 py-8">SSL sertifika bilgisi henüz kontrol edilmemiş. <code>php artisan sites:check-ssl</code> komutunu çalıştırın.</p>
                @endif

            {{-- ==================== cPanel ==================== --}}
            @elseif ($activeTab === 'cpanel')
                @if ($site->cpanelAccount)
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500">Domain</div>
                            <div class="text-sm font-semibold mt-1">{{ $site->cpanelAccount->domain }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500">Kullanıcı</div>
                            <div class="text-sm font-semibold mt-1">{{ $site->cpanelAccount->username }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500">Disk Kullanımı</div>
                            @if($site->cpanelAccount->disk_used_mb)
                                <div class="text-sm font-semibold mt-1">{{ $site->cpanelAccount->disk_used_mb }}MB / {{ $site->cpanelAccount->disk_limit_mb }}MB</div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                    <div class="h-2 rounded-full {{ $site->cpanelAccount->disk_usage_percent < 90 ? 'bg-green-500' : 'bg-red-500' }}"
                                         style="width: {{ min($site->cpanelAccount->disk_usage_percent, 100) }}%"></div>
                                </div>
                            @else
                                <div class="text-sm text-gray-400 mt-1">Henüz kontrol edilmedi</div>
                            @endif
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500">Son Kontrol</div>
                            <div class="text-sm font-semibold mt-1">{{ $site->cpanelAccount->last_checked_at?->diffForHumans() ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="openCpanelModal" class="text-sm text-blue-600 hover:underline">Düzenle</button>
                        <button wire:click="deleteCpanel" wire:confirm="cPanel bilgilerini silmek istediğinize emin misiniz?" class="text-sm text-red-600 hover:underline">Sil</button>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-400 mb-4">Bu site için cPanel bilgisi tanımlanmamış.</p>
                        <button wire:click="openCpanelModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">cPanel Ekle</button>
                    </div>
                @endif

            {{-- ==================== WordPress ==================== --}}
            @elseif ($activeTab === 'wordpress')
                @if ($site->wordpressSite)
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500">WP Versiyonu</div>
                            <div class="text-sm font-semibold mt-1">{{ $site->wordpressSite->wp_version ?? '-' }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500">Plugin Güncelleme</div>
                            <div class="text-sm font-semibold mt-1 {{ $site->wordpressSite->plugins_update_available > 0 ? 'text-yellow-600' : 'text-green-600' }}">
                                {{ $site->wordpressSite->plugins_update_available }} / {{ $site->wordpressSite->plugins_total }}
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500">Tema Güncelleme</div>
                            <div class="text-sm font-semibold mt-1 {{ $site->wordpressSite->themes_update_available > 0 ? 'text-yellow-600' : 'text-green-600' }}">
                                {{ $site->wordpressSite->themes_update_available }}
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-xs text-gray-500">Son Kontrol</div>
                            <div class="text-sm font-semibold mt-1">{{ $site->wordpressSite->last_checked_at?->diffForHumans() ?? '-' }}</div>
                        </div>
                    </div>
                    @if($site->wordpressSite->admin_url)
                        <a href="{{ $site->wordpressSite->admin_url }}" target="_blank" class="text-sm text-blue-600 hover:underline mr-4">WP Admin Panel</a>
                    @endif
                    <button wire:click="openWordpressModal" class="text-sm text-blue-600 hover:underline mr-2">Düzenle</button>
                    <button wire:click="deleteWordpress" wire:confirm="WordPress bilgilerini silmek istediğinize emin misiniz?" class="text-sm text-red-600 hover:underline">Sil</button>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-400 mb-4">Bu site için WordPress bilgisi tanımlanmamış.</p>
                        <button wire:click="openWordpressModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">WordPress Bilgisi Ekle</button>
                    </div>
                @endif

            {{-- ==================== SAYFA HIZ SKORU ==================== --}}
            @elseif ($activeTab === 'pagespeed')
                @if ($site->latestPageSpeed)
                    @php $ps = $site->latestPageSpeed; @endphp

                    {{-- Skor Kartları --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        {{-- Mobil Skor --}}
                        <div class="bg-gray-50 rounded-xl p-6 text-center">
                            <div class="text-xs text-gray-500 uppercase font-medium mb-3">Mobil Skor</div>
                            <div class="inline-flex items-center justify-center w-28 h-28 rounded-full border-4
                                        {{ $ps->mobile_score >= 90 ? 'border-green-500' : ($ps->mobile_score >= 50 ? 'border-yellow-500' : 'border-red-500') }}">
                                <span class="text-4xl font-bold {{ $ps->mobile_score >= 90 ? 'text-green-600' : ($ps->mobile_score >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $ps->mobile_score ?? '-' }}
                                </span>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <div class="text-gray-400 text-xs">FCP</div>
                                    <div class="font-semibold">{{ $ps->mobile_fcp ? number_format($ps->mobile_fcp / 1000, 1) . 's' : '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-400 text-xs">LCP</div>
                                    <div class="font-semibold">{{ $ps->mobile_lcp ? number_format($ps->mobile_lcp / 1000, 1) . 's' : '-' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Masaüstü Skor --}}
                        <div class="bg-gray-50 rounded-xl p-6 text-center">
                            <div class="text-xs text-gray-500 uppercase font-medium mb-3">Masaüstü Skor</div>
                            <div class="inline-flex items-center justify-center w-28 h-28 rounded-full border-4
                                        {{ $ps->desktop_score >= 90 ? 'border-green-500' : ($ps->desktop_score >= 50 ? 'border-yellow-500' : 'border-red-500') }}">
                                <span class="text-4xl font-bold {{ $ps->desktop_score >= 90 ? 'text-green-600' : ($ps->desktop_score >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $ps->desktop_score ?? '-' }}
                                </span>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <div class="text-gray-400 text-xs">FCP</div>
                                    <div class="font-semibold">{{ $ps->desktop_fcp ? number_format($ps->desktop_fcp / 1000, 1) . 's' : '-' }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-400 text-xs">LCP</div>
                                    <div class="font-semibold">{{ $ps->desktop_lcp ? number_format($ps->desktop_lcp / 1000, 1) . 's' : '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 mb-4">Son kontrol: {{ $ps->checked_at->format('d.m.Y H:i') }} ({{ $ps->checked_at->diffForHumans() }})</p>

                    {{-- Geçmiş Skorlar Tablosu --}}
                    @if($site->pageSpeedScores->count() > 1)
                        <h4 class="text-sm font-semibold text-gray-500 uppercase mb-3">Skor Geçmişi</h4>
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-2 text-left">Tarih</th>
                                    <th class="px-4 py-2 text-center">Mobil</th>
                                    <th class="px-4 py-2 text-center">Masaüstü</th>
                                    <th class="px-4 py-2 text-center">Mobil FCP</th>
                                    <th class="px-4 py-2 text-center">Masaüstü FCP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($site->pageSpeedScores as $score)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-gray-500">{{ $score->checked_at->format('d.m.Y H:i') }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="inline-flex items-center justify-center w-10 h-6 rounded text-xs font-bold
                                                         {{ $score->mobile_score >= 90 ? 'bg-green-100 text-green-700' : ($score->mobile_score >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $score->mobile_score ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="inline-flex items-center justify-center w-10 h-6 rounded text-xs font-bold
                                                         {{ $score->desktop_score >= 90 ? 'bg-green-100 text-green-700' : ($score->desktop_score >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $score->desktop_score ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center text-gray-500">{{ $score->mobile_fcp ? number_format($score->mobile_fcp / 1000, 1) . 's' : '-' }}</td>
                                        <td class="px-4 py-2 text-center text-gray-500">{{ $score->desktop_fcp ? number_format($score->desktop_fcp / 1000, 1) . 's' : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @else
                    <p class="text-center text-gray-400 py-8">Sayfa hız kontrolü henüz yapılmamış. <code>php artisan sites:check-pagespeed --site={{ $site->id }}</code> komutunu çalıştırın.</p>
                @endif

            {{-- ==================== BİLDİRİMLER ==================== --}}
            @elseif ($activeTab === 'notifications')
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left">Zaman</th>
                            <th class="px-4 py-2 text-left">Tip</th>
                            <th class="px-4 py-2 text-left">Konu</th>
                            <th class="px-4 py-2 text-left">Kanal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($site->notifications as $notification)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-gray-500">{{ $notification->sent_at->format('d.m.Y H:i') }}</td>
                                <td class="px-4 py-2">
                                    @switch($notification->type)
                                        @case('site_down') <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-800">Site Çöktü</span> @break
                                        @case('ssl_expiry') <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-800">SSL Uyarı</span> @break
                                        @case('disk_warning') <span class="px-2 py-0.5 rounded-full text-xs bg-orange-100 text-orange-800">Disk Uyarı</span> @break
                                        @case('update_available') <span class="px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800">Güncelleme</span> @break
                                        @case('domain_expiry') <span class="px-2 py-0.5 rounded-full text-xs bg-orange-100 text-orange-800">Domain</span> @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-2">{{ $notification->subject }}</td>
                                <td class="px-4 py-2 text-xs text-gray-500">{{ $notification->channel }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Henüz bildirim yok</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- ==================== cPanel MODALI ==================== --}}
    @if($showCpanelModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6">
                <h2 class="text-lg font-semibold mb-4">cPanel Bilgileri</h2>
                <form wire:submit="saveCpanel" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">cPanel Domain *</label>
                        <input type="text" wire:model="cpanel_domain" placeholder="cpanel.example.com"
                               class="w-full border rounded-lg px-3 py-2 @error('cpanel_domain') border-red-500 @enderror">
                        @error('cpanel_domain') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kullanıcı Adı *</label>
                        <input type="text" wire:model="cpanel_username" placeholder="cpanel_user"
                               class="w-full border rounded-lg px-3 py-2 @error('cpanel_username') border-red-500 @enderror">
                        @error('cpanel_username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">API Token {{ $site->cpanelAccount ? '(boş bırakılırsa mevcut korunur)' : '*' }}</label>
                        <input type="password" wire:model="cpanel_api_token" placeholder="••••••••"
                               class="w-full border rounded-lg px-3 py-2 @error('cpanel_api_token') border-red-500 @enderror">
                        @error('cpanel_api_token') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showCpanelModal', false)" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">İptal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ==================== WordPress MODALI ==================== --}}
    @if($showWordpressModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6">
                <h2 class="text-lg font-semibold mb-4">WordPress Bilgileri</h2>
                <form wire:submit="saveWordpress" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WP Admin URL</label>
                        <input type="url" wire:model="wp_admin_url" placeholder="https://example.com/wp-admin"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">REST API Key (Application Password)</label>
                        <input type="password" wire:model="wp_api_key" placeholder="••••••••"
                               class="w-full border rounded-lg px-3 py-2">
                        <p class="text-xs text-gray-400 mt-1">WordPress > Kullanıcılar > Profil > Uygulama Parolaları bölümünden oluşturabilirsiniz.</p>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showWordpressModal', false)" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">İptal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
