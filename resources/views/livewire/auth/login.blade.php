<div class="flex items-center justify-center min-h-screen px-4">
    <div class="w-full max-w-md">

        {{-- Logo & Başlık --}}
        <div class="fade-up text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl glass mb-6 relative">
                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-500/20 to-purple-500/20"></div>
                <svg class="w-9 h-9 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">SiteWatch</h1>
            <p class="text-sm text-gray-500 mt-1 font-light">Yönetim paneline giriş yapın</p>
        </div>

        {{-- Login Kartı --}}
        <div class="fade-up fade-up-delay-1 glass rounded-3xl glow-blue relative overflow-hidden">

            {{-- Dekoratif üst çizgi --}}
            <div class="absolute top-0 left-1/2 -translate-x-1/2 h-[2px] w-32 bg-gradient-to-r from-transparent via-blue-500 to-transparent"></div>

            <form wire:submit="login" class="p-8 space-y-6">

                {{-- Hata mesajı --}}
                @error('email')
                    <div class="shake flex items-center gap-3 p-4 rounded-xl bg-red-500/10 border border-red-500/20">
                        <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <p class="text-red-300 text-sm">{{ $message }}</p>
                    </div>
                @enderror

                {{-- E-posta --}}
                <div>
                    <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">E-posta</label>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input type="email" wire:model="email" autofocus placeholder="admin@example.com"
                               class="w-full glass-input text-white rounded-xl pl-12 pr-4 py-3.5 text-sm
                                      placeholder-gray-600 outline-none
                                      @error('email') !border-red-500/40 @enderror">
                    </div>
                </div>

                {{-- Şifre --}}
                <div x-data="{ show: false }">
                    <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Şifre</label>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" wire:model="password" placeholder="••••••••"
                               class="w-full glass-input text-white rounded-xl pl-12 pr-12 py-3.5 text-sm
                                      placeholder-gray-600 outline-none
                                      @error('password') !border-red-500/40 @enderror">
                        <button type="button" @click="show = !show"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Beni Hatırla --}}
                <div class="flex items-center">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" wire:model="remember" class="sr-only peer">
                            <div class="w-5 h-5 rounded-md border border-white/10 bg-white/5 peer-checked:bg-blue-600/30 peer-checked:border-blue-500/50 transition-all"></div>
                            <svg class="w-3 h-3 text-blue-400 absolute top-1 left-1 hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-500 group-hover:text-gray-400 transition">Beni hatırla</span>
                    </label>
                </div>

                {{-- Giriş Butonu --}}
                <button type="submit"
                        class="btn-glow w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold
                               py-3.5 rounded-xl flex items-center justify-center gap-2 text-sm">
                    <svg wire:loading wire:target="login" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="login">Giriş Yap</span>
                    <span wire:loading wire:target="login">Doğrulanıyor...</span>
                    <svg wire:loading.remove wire:target="login" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>
        </div>

        {{-- Alt bilgi --}}
        <div class="fade-up fade-up-delay-2 mt-8 text-center">
            <p class="text-gray-700 text-xs">SiteWatch v1.0 &mdash; DnKreatif</p>
        </div>
    </div>
</div>
