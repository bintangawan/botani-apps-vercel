@props(['plant'])

<div class="group bg-white border border-emerald-200 rounded-3xl overflow-hidden shadow-xl hover:shadow-lg hover:border-emerald-300 transition-all duration-500 flex flex-col h-full relative">
    <!-- Image & Badge Container -->
    <div class="relative aspect-[4/3] w-full overflow-hidden bg-white">
        @if($plant->image_url)
            <img src="{{ $plant->image_url }}" alt="{{ $plant->local_name }} - {{ $plant->scientific_name }}" 
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                 loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center bg-emerald-100">
                <span class="text-4xl opacity-40">🌱</span>
            </div>
        @endif

        <!-- Taxonomy Group Badge -->
        <div class="absolute top-3.5 left-3.5 flex flex-wrap gap-1.5 z-10">
            <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider  shadow-sm border
                {{ $plant->group_type === 'Gymnospermae' 
                    ? 'bg-amber-100 text-amber-700 border-amber-300'
                    : 'bg-emerald-100 text-emerald-700 border-emerald-300' }}">
                {{ $plant->group_type }}
            </span>

            @if($plant->cotyledon_type !== 'Tidak Berlaku')
                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-white/90 text-slate-700 border border-emerald-200  shadow-sm">
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
                <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider block mb-1">
                    Famili: {{ $family->name }}
                </span>
            @endif

            <!-- Local Name -->
            <h3 class="text-xl font-bold text-slate-900 group-hover:text-emerald-700 transition-colors line-clamp-1">
                {{ $plant->local_name }}
            </h3>

            <!-- Scientific Name (Italicized) -->
            <p class="text-sm italic text-slate-700 font-serif mt-0.5 mb-3 line-clamp-1">
                {{ $plant->scientific_name }}
            </p>

            <!-- Short Excerpt / Description -->
            <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                {{ Str::limit(strip_tags($plant->description), 110) }}
            </p>
        </div>

        <!-- Card Footer -->
        <div class="mt-6 pt-4 border-t border-emerald-200 flex items-center justify-between gap-2">
            <!-- Location indicator -->
            @php
                $observation = $plant->observations->first();
            @endphp
            <div class="flex items-center gap-1.5 text-xs text-slate-600 truncate max-w-[60%]">
                <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="truncate">{{ $observation && $observation->location ? $observation->location->regency : 'Sumatera Utara' }}</span>
            </div>

            <!-- Action Button -->
            <a href="{{ route('catalog.detail', $plant->slug) }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-100 text-emerald-700 border border-emerald-300 group-hover:bg-emerald-600 group-hover:text-slate-900 group-hover:border-emerald-600 transition-all text-xs font-semibold shrink-0">
                <span>Pelajari</span>
                <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>
