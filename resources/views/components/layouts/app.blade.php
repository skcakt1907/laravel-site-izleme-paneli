<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SiteWatch Panel' }}</title>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Stilleri -->
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen flex" x-data="{ sidebarOpen: true }">

    {{-- Sol Menü (Sidebar) --}}
    <aside
        class="bg-gray-900 text-white flex flex-col transition-all duration-300"
        :class="sidebarOpen ? 'w-64' : 'w-16'"
    >
        {{-- Logo Alanı --}}
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-700">
            <span class="text-lg font-bold whitespace-nowrap" x-show="sidebarOpen" x-transition>
                SiteWatch
            </span>
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white">
                {{-- Hamburger / Kapat ikonu --}}
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        {{-- Menü Linkleri --}}
        <nav class="flex-1 mt-4 space-y-1 px-2">
            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                      {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Dashboard</span>
            </a>

            {{-- Siteler --}}
            <a href="{{ route('sites.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                      {{ request()->routeIs('sites.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Siteler</span>
            </a>

            {{-- Bildirimler --}}
            <a href="#"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Bildirimler</span>
            </a>

            {{-- Raporlar --}}
            <a href="#"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Raporlar</span>
            </a>
        </nav>

        {{-- Alt Bilgi --}}
        <div class="p-4 border-t border-gray-700">
            <span x-show="sidebarOpen" x-transition class="text-xs text-gray-500">SiteWatch v1.0</span>
        </div>
    </aside>

    {{-- Ana İçerik --}}
    <div class="flex-1 flex flex-col">
        {{-- Üst Bar --}}
        <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6">
            <h1 class="text-xl font-semibold text-gray-800">{{ $header ?? 'SiteWatch Panel' }}</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">{{ now()->format('d.m.Y H:i') }}</span>
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
