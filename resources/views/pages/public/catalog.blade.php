<x-app-layout title="Galeri Tumbuhan — Botani Phanerogamae">
    <div class="py-12 sm:py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest block mb-2">🌿 Katalog Herbarium Digital</span>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">
                Galeri Tumbuhan Phanerogamae
            </h1>
            <p class="text-slate-700 text-sm sm:text-base mt-3">
                Jelajahi dan pelajari 133+ spesimen tumbuhan berbiji (Gymnospermae & Angiospermae) hasil observasi ekosistem Sumatera Utara.
            </p>
        </div>

        <!-- Search & Filter Bar Component -->
        <x-search-filter :families="$families" :regencies="$regencies" :filters="$filters" />

        <!-- Results Summary Bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8 text-sm text-slate-600 pb-4 border-b border-emerald-200 min-w-0 break-words">
            <div>
                Menampilkan <span class="font-bold text-slate-900">{{ $plants->firstItem() ?? 0 }}</span> - <span class="font-bold text-slate-900">{{ $plants->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-emerald-700">{{ $plants->total() }}</span> Spesimen
            </div>
            @if(!empty($filters['search']) || (!empty($filters['group_type']) && $filters['group_type'] !== 'all') || (!empty($filters['cotyledon_type']) && $filters['cotyledon_type'] !== 'all') || (!empty($filters['family']) && $filters['family'] !== 'all') || (!empty($filters['regency']) && $filters['regency'] !== 'all'))
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs text-slate-500">Filter Aktif:</span>
                    @if(!empty($filters['search']))
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-700 text-xs border border-emerald-300">Cari: "{{ $filters['search'] }}"</span>
                    @endif
                    @if(!empty($filters['group_type']) && $filters['group_type'] !== 'all')
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-700 text-xs border border-emerald-300">{{ $filters['group_type'] }}</span>
                    @endif
                    @if(!empty($filters['cotyledon_type']) && $filters['cotyledon_type'] !== 'all')
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-700 text-xs border border-emerald-300">{{ $filters['cotyledon_type'] }}</span>
                    @endif
                    @if(!empty($filters['family']) && $filters['family'] !== 'all')
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-700 text-xs border border-emerald-300">Famili: {{ $filters['family'] }}</span>
                    @endif
                    @if(!empty($filters['regency']) && $filters['regency'] !== 'all')
                        <span class="px-2.5 py-1 rounded-lg bg-teal-100 text-teal-700 text-xs border border-teal-300">Lokasi: {{ $filters['regency'] }}</span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Plant Cards Grid -->
        @if($plants->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($plants as $plant)
                    <x-plant-card :plant="$plant" />
                @endforeach
            </div>

            <!-- Pagination Links -->
            <div class="mt-14">
                {{ $plants->appends(request()->query())->links('vendor.pagination.botani') }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white border border-emerald-200 rounded-3xl p-16 text-center max-w-lg mx-auto space-y-4 my-12">
                <div class="w-20 h-20 rounded-full bg-emerald-100 border border-emerald-300 flex items-center justify-center text-4xl mx-auto">
                    🔍
                </div>
                <h3 class="text-xl font-bold text-slate-900">Spesimen Tidak Ditemukan</h3>
                <p class="text-sm text-slate-600">
                    Kami tidak menemukan spesimen tumbuhan yang sesuai dengan kata kunci atau filter pencarian Anda.
                </p>
                <a href="{{ route('catalog') }}" class="inline-block mt-4 px-6 py-2.5 rounded-xl bg-emerald-600 text-slate-900 text-sm font-semibold hover:bg-emerald-500 transition-all">
                    Reset Semua Filter
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
