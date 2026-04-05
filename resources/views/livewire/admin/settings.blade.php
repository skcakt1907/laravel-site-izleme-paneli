<div class="max-w-4xl space-y-6">

    {{-- ==================== MAIL SMTP AYARLARI ==================== --}}
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Mail SMTP Ayarları
            </h3>
        </div>
        <form wire:submit="saveMail" class="p-6 space-y-4">
            @if(session()->has('message_mail'))
                <div class="p-3 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm"
                     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
                    {{ session('message_mail') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host *</label>
                    <input type="text" wire:model="mail_host" placeholder="smtp.gmail.com"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('mail_host') border-red-500 @enderror">
                    @error('mail_host') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Port *</label>
                    <input type="number" wire:model="mail_port" placeholder="587"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('mail_port') border-red-500 @enderror">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kullanıcı Adı</label>
                    <input type="text" wire:model="mail_username" placeholder="user@gmail.com"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şifre</label>
                    <input type="password" wire:model="mail_password" placeholder="Değiştirmek için girin"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şifreleme *</label>
                    <select wire:model="mail_encryption" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="tls">TLS</option>
                        <option value="ssl">SSL</option>
                        <option value="null">Yok</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gönderici E-posta *</label>
                    <input type="email" wire:model="mail_from_address" placeholder="noreply@example.com"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('mail_from_address') border-red-500 @enderror">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gönderici Adı *</label>
                    <input type="text" wire:model="mail_from_name" placeholder="SiteWatch"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('mail_from_name') border-red-500 @enderror">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                    Mail Ayarlarını Kaydet
                </button>
            </div>
        </form>
    </div>

    {{-- ==================== UYARI EŞİKLERİ ==================== --}}
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                Uyarı Eşikleri
            </h3>
        </div>
        <form wire:submit="saveThresholds" class="p-6 space-y-4">
            @if(session()->has('message_thresholds'))
                <div class="p-3 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm"
                     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
                    {{ session('message_thresholds') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Disk Uyarı Eşiği (%)</label>
                    <input type="number" wire:model="disk_warning_percent" min="1" max="100"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-400 mt-1">Disk kullanımı bu yüzdeyi geçince uyarı</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SSL Uyarı (gün)</label>
                    <input type="number" wire:model="ssl_warning_days" min="1" max="365"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-400 mt-1">SSL sertifikası bu kadar gün kala uyarı</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Domain Uyarı (gün)</label>
                    <input type="number" wire:model="domain_warning_days" min="1" max="365"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-400 mt-1">Domain süresi bu kadar gün kala uyarı</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hata Eşiği (ardışık)</label>
                    <input type="number" wire:model="failure_threshold" min="1" max="50"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-400 mt-1">Arka arkaya bu kadar hata sonrası bildirim</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kontrol Aralığı (dk)</label>
                    <input type="number" wire:model="check_interval" min="1" max="1440"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-400 mt-1">HTTP kontrolleri arasındaki süre</p>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                    Eşik Ayarlarını Kaydet
                </button>
            </div>
        </form>
    </div>

    {{-- ==================== API & BİLDİRİM ==================== --}}
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                API Anahtarları & Bildirim
            </h3>
        </div>
        <form wire:submit="saveApi" class="p-6 space-y-4">
            @if(session()->has('message_api'))
                <div class="p-3 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm"
                     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
                    {{ session('message_api') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WHOIS API Key</label>
                    <input type="text" wire:model="whois_api_key" placeholder="whoisjson.com API anahtarı"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PageSpeed API Key</label>
                    <input type="text" wire:model="pagespeed_api_key" placeholder="Google PageSpeed API anahtarı"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Bildirim E-postası</label>
                <input type="email" wire:model="admin_email" placeholder="admin@example.com"
                       class="w-full md:w-1/2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400 mt-1">Tüm uyarı mailleri bu adrese gönderilir</p>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                    API & Bildirim Ayarlarını Kaydet
                </button>
            </div>
        </form>
    </div>
</div>
