@props(['families' => collect(), 'regencies' => collect(), 'filters' => []])

<form action="{{ route('catalog') }}" method="GET" class="bg-[#0c2214]/90 border border-emerald-900/50 rounded-3xl p-6 shadow-2xl backdrop-blur-xl mb-10 min-w-0 break-words">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
        <!-- Keyword Search -->
        <div class="sm:col-span-2 lg:col-span-1">
            <label for="search" class="block text-xs font-semibold uppercase tracking-wider text-emerald-400 mb-2">
                🔍 Cari Spesimen
            </label>
            <div class="relative">
                <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Nama Lokal atau Ilmiah..."
                       class="w-full bg-[#07130c] border border-emerald-800/60 rounded-2xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                @if(!empty($filters['search']))
                    <a href="{{ route('catalog', array_merge(request()->query(), ['search' => null])) }}" class="absolute right-3 top-3.5 text-xs text-slate-400 hover:text-white">✕</a>
                @endif
            </div>
        </div>

        <!-- Filter by Divisi/Group -->
        <div>
            <label for="group_type" class="block text-xs font-semibold uppercase tracking-wider text-emerald-400 mb-2">
                🌿 Kelompok Utama
            </label>
            <select name="group_type" id="group_type"
                    class="w-full bg-[#07130c] border border-emerald-800/60 rounded-2xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500 transition-all">
                <option value="all">Semua Kelompok</option>
                <option value="Gymnospermae" {{ ($filters['group_type'] ?? '') === 'Gymnospermae' ? 'selected' : '' }}>Gymnospermae (Biji Terbuka)</option>
                <option value="Angiospermae" {{ ($filters['group_type'] ?? '') === 'Angiospermae' ? 'selected' : '' }}>Angiospermae (Biji Tertutup)</option>
            </select>
        </div>

        <!-- Filter by Cotyledon Type -->
        <div>
            <label for="cotyledon_type" class="block text-xs font-semibold uppercase tracking-wider text-emerald-400 mb-2">
                🌱 Kelas Kotiledon
            </label>
            <select name="cotyledon_type" id="cotyledon_type"
                    class="w-full bg-[#07130c] border border-emerald-800/60 rounded-2xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500 transition-all">
                <option value="all">Semua Kelas</option>
                <option value="Monokotil" {{ ($filters['cotyledon_type'] ?? '') === 'Monokotil' ? 'selected' : '' }}>Monokotil (Liliopsida)</option>
                <option value="Dikotil" {{ ($filters['cotyledon_type'] ?? '') === 'Dikotil' ? 'selected' : '' }}>Dikotil (Magnoliopsida)</option>
            </select>
        </div>

        <!-- Filter by Family Dropdown -->
        <div>
            <label for="family" class="block text-xs font-semibold uppercase tracking-wider text-emerald-400 mb-2">
                🏷️ Famili Taksonomi
            </label>
            <select name="family" id="family"
                    class="w-full bg-[#07130c] border border-emerald-800/60 rounded-2xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500 transition-all">
                <option value="all">Semua Famili</option>
                @foreach($families as $fam)
                    <option value="{{ $fam->name }}" {{ ($filters['family'] ?? '') === $fam->name ? 'selected' : '' }}>
                        {{ $fam->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filter by Regency (Kabupaten) Dropdown -->
        <div>
            <label for="regency" class="block text-xs font-semibold uppercase tracking-wider text-emerald-400 mb-2">
                📍 Lokasi Kabupaten
            </label>
            <select name="regency" id="regency"
                    class="w-full bg-[#07130c] border border-emerald-800/60 rounded-2xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500 transition-all">
                <option value="all">Semua Kabupaten</option>
                @foreach($regencies as $reg)
                    <option value="{{ $reg }}" {{ ($filters['regency'] ?? '') === $reg ? 'selected' : '' }}>
                        {{ $reg }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mt-5 pt-4 border-t border-emerald-950 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 max-w-full text-xs">
            @php
                $hasActiveFilters = !empty($filters['search']) || (!empty($filters['group_type']) && $filters['group_type'] !== 'all') || (!empty($filters['cotyledon_type']) && $filters['cotyledon_type'] !== 'all') || (!empty($filters['family']) && $filters['family'] !== 'all') || (!empty($filters['regency']) && $filters['regency'] !== 'all');
            @endphp

            @if($hasActiveFilters)
                <span class="text-slate-400 font-medium shrink-0">Filter Aktif:</span>
                @if(!empty($filters['group_type']) && $filters['group_type'] !== 'all')
                    <span class="px-3 py-1 rounded-xl bg-emerald-500/20 border border-emerald-500 text-emerald-300 font-bold shrink-0">Kelompok: {{ $filters['group_type'] }}</span>
                @endif
                @if(!empty($filters['cotyledon_type']) && $filters['cotyledon_type'] !== 'all')
                    <span class="px-3 py-1 rounded-xl bg-emerald-500/20 border border-emerald-500 text-emerald-300 font-bold shrink-0">Kelas: {{ $filters['cotyledon_type'] }}</span>
                @endif
                @if(!empty($filters['family']) && $filters['family'] !== 'all')
                    <span class="px-3 py-1 rounded-xl bg-amber-500/20 border border-amber-500 text-amber-300 font-bold shrink-0">Famili: {{ $filters['family'] }}</span>
                @endif
                @if(!empty($filters['regency']) && $filters['regency'] !== 'all')
                    <span class="px-3 py-1 rounded-xl bg-teal-500/20 border border-teal-500 text-teal-300 font-bold shrink-0">Lokasi: {{ $filters['regency'] }}</span>
                @endif

                <a href="{{ route('catalog') }}" class="px-3 py-1 rounded-xl bg-rose-950/60 border border-rose-500/40 text-rose-300 hover:bg-rose-900 transition-all shrink-0 font-semibold">✕ Reset Semua Filter</a>
            @else
                <span class="text-slate-400 italic">Pilih filter dropdown di atas untuk menyaring katalog spesimen berdasarkan klasifikasi dan lokasi observasi.</span>
            @endif
        </div>

        <button type="submit" class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 text-white text-sm font-semibold shadow-lg shadow-emerald-600/30 hover:scale-105 transition-all ml-auto shrink-0">
            Terapkan Filter
        </button>
    </div>
</form>
