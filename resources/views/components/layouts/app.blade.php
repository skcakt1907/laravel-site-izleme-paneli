<!DOCTYPE html>
<html lang="tr" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SiteWatch Panel' }}</title>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Stilleri -->
    @livewireStyles
</head>
<body class="bg-gray-100 dark:bg-gray-950 min-h-screen flex transition-colors duration-300" x-data="{ sidebarOpen: true }">

    {{-- Sol Menü (Sidebar) --}}
    <aside
        class="bg-gray-900 dark:bg-gray-900 text-white flex flex-col transition-all duration-300"
        :class="sidebarOpen ? 'w-64' : 'w-16'"
    >
        {{-- Logo Alanı --}}
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-700">
            <span class="text-lg font-bold whitespace-nowrap" x-show="sidebarOpen" x-transition>
                SiteWatch
            </span>
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        {{-- Menü Linkleri --}}
        <nav class="flex-1 mt-4 space-y-1 px-2">
            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                      {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Dashboard</span>
            </a>

            @if(auth()->user()->hasRole('super_admin', 'admin'))
                <a href="{{ route('admin.servers.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.servers.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Sunucular</span>
                </a>

                <a href="{{ route('admin.sites.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.sites.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Siteler</span>
                </a>

                <a href="{{ route('admin.notifications.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.notifications.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Bildirimler</span>
                </a>

                <a href="{{ route('admin.reports.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.reports.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Raporlar</span>
                </a>
            @endif

            @if(auth()->user()->isSuperAdmin())
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                              {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Kullanıcılar</span>
                    </a>

                    <a href="{{ route('admin.settings') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                              {{ request()->routeIs('admin.settings') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Ayarlar</span>
                    </a>
                </div>
            @endif
        </nav>

        {{-- Kullanıcı Bilgisi & Çıkış --}}
        <div class="p-4 border-t border-gray-700">
            <div x-show="sidebarOpen" x-transition class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">
                            @switch(auth()->user()->role)
                                @case('super_admin') Super Admin @break
                                @case('admin') Admin @break
                                @case('viewer') Viewer @break
                            @endswitch
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Çıkış Yap
                    </button>
                </form>
            </div>
            <span x-show="!sidebarOpen" x-transition class="text-xs text-gray-500">v1.0</span>
        </div>
    </aside>

    {{-- Ana İçerik --}}
    <div class="flex-1 flex flex-col">
        {{-- Üst Bar --}}
        <header class="bg-white dark:bg-gray-900 shadow-sm h-16 flex items-center justify-between px-6 transition-colors duration-300">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">{{ $header ?? 'SiteWatch Panel' }}</h1>
            <div class="flex items-center gap-4">
                {{-- Karanlık/Aydınlık Mod Toggle --}}
                <button @click="darkMode = !darkMode"
                        class="relative w-14 h-7 rounded-full transition-colors duration-300 focus:outline-none"
                        :class="darkMode ? 'bg-blue-600' : 'bg-gray-300'"
                        title="Karanlık/Aydınlık Mod">
                    {{-- Slider --}}
                    <span class="absolute top-0.5 left-0.5 w-6 h-6 bg-white rounded-full shadow transition-transform duration-300 flex items-center justify-center"
                          :class="darkMode ? 'translate-x-7' : 'translate-x-0'">
                        {{-- Güneş (light) --}}
                        <svg x-show="!darkMode" class="w-3.5 h-3.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                        </svg>
                        {{-- Ay (dark) --}}
                        <svg x-show="darkMode" class="w-3.5 h-3.5 text-blue-300" fill="currentColor" viewBox="0 0 20 20" style="display:none">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                        </svg>
                    </span>
                </button>

                <span class="text-sm text-gray-500 dark:text-gray-400">{{ now()->format('d.m.Y H:i') }}</span>
            </div>
        </header>

        {{-- Sayfa İçeriği --}}
        <main class="flex-1 p-6">
            {{ $slot }}
        </main>
    </div>

    <!-- Livewire Scriptleri -->
    @livewireScripts
</body>
</html>
