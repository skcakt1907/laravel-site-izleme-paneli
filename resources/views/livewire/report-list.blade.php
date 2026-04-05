<div>
    {{-- Başarı Mesajı --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            {{ session('message') }}
        </div>
    @endif

    {{-- Üst Bar: Arama + Filtre + Rapor Oluştur --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex gap-3 items-center w-full md:w-auto">
                {{-- Arama --}}
                <div class="relative w-full md:w-64">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Site adı ara..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                {{-- Dönem Filtresi --}}
                <select wire:model.live="filterPeriod" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Tüm Dönemler</option>
                    @foreach($periods as $period)
                        <option value="{{ $period }}">{{ $period }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Rapor Oluştur Butonu --}}
            <button wire:click="openGenerateModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Rapor Oluştur
            </button>
        </div>
    </div>

    {{-- Rapor Tablosu --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Dönem</th>
                        <th class="px-6 py-3">Site</th>
                        <th class="px-6 py-3">Oluşturulma</th>
                        <th class="px-6 py-3 text-right">İndir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-gray-50" wire:key="report-{{ $report->id }}">
                            {{-- Dönem --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $report->period }}
                                </span>
                            </td>

                            {{-- Site Adı --}}
                            <td class="px-6 py-4 font-medium">
                                @if($report->site)
                                    <a href="{{ route('admin.sites.show', $report->site) }}" class="text-blue-600 hover:underline">
                                        {{ $report->site->name }}
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Genel Özet
                                    </span>
                                @endif
                            </td>

                            {{-- Oluşturulma Tarihi --}}
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                {{ $report->generated_at->format('d.m.Y H:i') }}
                            </td>

                            {{-- İndirme Butonu --}}
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.reports.download', $report) }}"
                                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm font-medium transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    PDF İndir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-medium">Henüz rapor oluşturulmamış</p>
                                <p class="text-sm mt-1">"Rapor Oluştur" butonuna tıklayarak başlayın.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $reports->links() }}
            </div>
        @endif

        <div class="px-6 py-3 bg-gray-50 text-xs text-gray-500 border-t">
            Toplam {{ $reports->total() }} rapor
        </div>
    </div>

    {{-- ==================== RAPOR OLUŞTURMA MODALI ==================== --}}
    @if($showGenerateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-data x-transition>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
                <h2 class="text-lg font-semibold mb-4">Aylık Rapor Oluştur</h2>
                <form wire:submit="generateReport" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rapor Dönemi *</label>
                        <input type="month" wire:model="generateMonth"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      @error('generateMonth') border-red-500 @enderror">
                        @error('generateMonth') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-400 mt-1">Tüm aktif siteler için seçilen dönemin raporu oluşturulacaktır.</p>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showGenerateModal', false)"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            İptal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                            <svg class="w-4 h-4" wire:loading wire:target="generateReport" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span wire:loading.remove wire:target="generateReport">Oluştur</span>
                            <span wire:loading wire:target="generateReport">Oluşturuluyor...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
