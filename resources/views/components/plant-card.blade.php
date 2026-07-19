@props(['plant'])

<div class="group bg-[#0c2214]/80 border border-emerald-900/40 rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl hover:border-emerald-500/50 transition-all duration-500 flex flex-col h-full relative">
    <!-- Image & Badge Container -->
    <div class="relative aspect-[4/3] w-full overflow-hidden bg-[#07130c]">
        @if($plant->image_url)
            <img src="{{ $plant->image_url }}" alt="{{ $plant->local_name }} - {{ $plant->scientific_name }}" 
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                 loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-emerald-950 to-[#07130c]">
                <span class="text-4xl opacity-40">🌱</span>
            </div>
        @endif

        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#0c2214] via-transparent to-black/30 opacity-80"></div>

        <!-- Taxonomy Group Badge -->
        <div class="absolute top-3.5 left-3.5 flex flex-wrap gap-1.5 z-10">
            <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider backdrop-blur-md shadow-sm border
                {{ $plant->group_type === 'Gymnospermae' 
                    ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' 
                    : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' }}">
                {{ $plant->group_type }}
            </span>

            @if($plant->cotyledon_type !== 'Tidak Berlaku')
                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-black/50 text-slate-200 border border-white/10 backdrop-blur-md shadow-sm">
                    {{ $plant->cotyledon_type }}
                </span>
            @endif
        </div>

    </div>

    <!-- Content Area -->
    <div class="p-6 flex-grow flex flex-col justify-between">
        <div>
            <!-- Family breadcrumb (if loaded) -->
            @php
                $family = $plant->taxa->where('rank', 'famili')->first();
            @endphp
            @if($family)
                <span class="text-xs font-semibold text-emerald-400/90 uppercase tracking-wider block mb-1">
                    Famili: {{ $family->name }}
                </span>
            @endif

            <!-- Local Name -->
            <h3 class="text-xl font-bold text-white group-hover:text-emerald-300 transition-colors line-clamp-1">
                {{ $plant->local_name }}
            </h3>

            <!-- Scientific Name (Italicized) -->
            <p class="text-sm italic text-slate-300 font-serif mt-0.5 mb-3 line-clamp-1">
                {{ $plant->scientific_name }}
            </p>

            <!-- Short Excerpt / Description -->
            <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed">
                {{ Str::limit(strip_tags($plant->description), 110) }}
            </p>
        </div>

        <!-- Card Footer -->
        <div class="mt-6 pt-4 border-t border-emerald-900/30 flex items-center justify-between gap-2">
            <!-- Location indicator -->
            @php
                $observation = $plant->observations->first();
            @endphp
            <div class="flex items-center gap-1.5 text-xs text-slate-400 truncate max-w-[60%]">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="truncate">{{ $observation && $observation->location ? $observation->location->regency : 'Sumatera Utara' }}</span>
            </div>

            <!-- Action Button -->
            <a href="{{ route('catalog.detail', $plant->slug) }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-600/20 text-emerald-300 border border-emerald-500/30 group-hover:bg-emerald-600 group-hover:text-white group-hover:border-emerald-600 transition-all text-xs font-semibold shrink-0">
                <span>Pelajari</span>
                <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>
