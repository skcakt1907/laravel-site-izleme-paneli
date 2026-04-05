<div class="flex items-center justify-center min-h-screen px-4" wire:poll.{{ $refreshInterval }}s>
    <div class="w-full max-w-xl text-center">

        {{-- Logo --}}
        <div class="fade-up mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl glass mb-6 relative">
                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-500/20 to-purple-500/20"></div>
                <svg class="w-9 h-9 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">DnKreatif</h1>
            <p class="text-sm text-gray-500 mt-1 font-light">Sistem Durum Sayfası</p>
        </div>

        {{-- Ana Durum Kartı --}}
        <div class="fade-up fade-up-delay-1 glass rounded-3xl p-10 {{ $allUp ? 'glow-green' : 'glow-yellow' }} relative overflow-hidden">

            {{-- Dekoratif üst çizgi --}}
            <div class="absolute top-0 left-1/2 -translate-x-1/2 h-[2px] w-32 bg-gradient-to-r {{ $allUp ? 'from-transparent via-green-500 to-transparent' : 'from-transparent via-yellow-500 to-transparent' }}"></div>

            @if($allUp)
                {{-- Yeşil tik - pulse animasyonlu --}}
                <div class="relative inline-flex items-center justify-center mb-8">
                    <div class="absolute w-28 h-28 rounded-full bg-green-500/10 pulse-ring"></div>
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-green-500/20 to-emerald-500/10 border border-green-500/20 flex items-center justify-center relative">
                        <svg class="w-11 h-11 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>

                <h2 class="text-3xl font-bold text-white mb-2">Sistemler Çevrimiçi</h2>
                <p class="text-gray-400 text-sm font-light">Tüm sistemler sorunsuz çalışmaktadır</p>
            @else
                {{-- Sarı uyarı --}}
                <div class="relative inline-flex items-center justify-center mb-8">
                    <div class="absolute w-28 h-28 rounded-full bg-yellow-500/10 pulse-ring"></div>
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-yellow-500/20 to-amber-500/10 border border-yellow-500/20 flex items-center justify-center relative">
                        <svg class="w-11 h-11 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>

                <h2 class="text-3xl font-bold text-white mb-2">Kısmi Kesinti</h2>
                <p class="text-gray-400 text-sm font-light">Bazı sistemlerde sorun tespit edildi</p>
            @endif

            {{-- Uptime Yüzdesi --}}
            <div class="mt-10 pt-8 border-t border-white/5">
                <p class="text-[11px] text-gray-500 uppercase tracking-[0.2em] font-medium mb-3">Genel Uptime &mdash; Son 24 Saat</p>
                <p class="text-6xl font-extrabold {{ $allUp ? 'text-green-400 number-glow-green' : 'text-yellow-400 number-glow-yellow' }} tabular-nums">
                    %{{ $uptimePercent }}
                </p>
            </div>
        </div>

        {{-- Alt bilgi --}}
        <div class="fade-up fade-up-delay-2 mt-8 flex items-center justify-center gap-3 text-gray-600 text-xs">
            <span class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full {{ $allUp ? 'bg-green-500' : 'bg-yellow-500' }} animate-pulse"></span>
                Canlı izleme
            </span>
            <span class="text-gray-800">&bull;</span>
            <span>Son kontrol: {{ now()->format('H:i') }}</span>
        </div>
    </div>
</div>
