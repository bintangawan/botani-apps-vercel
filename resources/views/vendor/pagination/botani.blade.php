@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4 py-8 mt-12 border-t border-emerald-200">
        <!-- Summary Info -->
        <div class="text-xs sm:text-sm text-slate-600 font-medium">
            Menampilkan <span class="font-bold text-emerald-700">{{ $paginator->firstItem() }}</span>
            hingga <span class="font-bold text-emerald-700">{{ $paginator->lastItem() }}</span>
            dari <span class="font-bold text-slate-900">{{ $paginator->total() }}</span> spesimen herbarium
        </div>

        <!-- Navigation Links -->
        <div class="flex flex-wrap sm:flex-nowrap items-center justify-center gap-1.5 sm:gap-2 text-xs sm:text-sm">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1 px-3 py-2 sm:px-4 sm:py-2.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-slate-600 font-semibold cursor-not-allowed shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    <span>Previous</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1 px-3 py-2 sm:px-4 sm:py-2.5 rounded-2xl bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-600 hover:text-emerald-800 hover:border-emerald-600 font-semibold transition-all shadow-md shadow-emerald-900/10 shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    <span>Previous</span>
                </a>
            @endif

            {{-- Custom Compact Pagination Elements (Exactly 2 numbers + ... + Akhir) --}}
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
                $pages = [];

                if ($last <= 3) {
                    for ($i = 1; $i <= $last; $i++) {
                        $pages[] = $i;
                    }
                } else {
                    if ($current === $last && $last > 1) {
                        $pages[] = max(1, $current - 1);
                        $pages[] = $current;
                    } else {
                        $pages[] = $current;
                        if ($current + 1 <= $last) {
                            $pages[] = $current + 1;
                        }
                    }

                    $maxInGroup = max($pages);
                    if ($maxInGroup < $last - 1) {
                        $pages[] = '...';
                    }

                    if (!in_array($last, $pages, true)) {
                        $pages[] = $last;
                    }
                }
            @endphp

            @foreach ($pages as $page)
                @if ($page === '...')
                    <span class="px-2 sm:px-3 py-2 text-slate-500 font-bold tracking-widest shrink-0">...</span>
                @else
                    @php
                        $label = ($page === $last && $last > 1) ? "Akhir ({$page})" : $page;
                        $url = $paginator->url($page);
                    @endphp

                    @if ($page == $current)
                        <span aria-current="page" class="px-3 py-2 sm:px-4 sm:py-2.5 rounded-2xl bg-emerald-600 text-slate-900 font-extrabold shadow-lg shadow-emerald-900/10 border border-emerald-300 shrink-0">
                            {{ $label }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-2 sm:px-4 sm:py-2.5 rounded-2xl bg-white border border-emerald-200 text-slate-700 hover:border-emerald-500 hover:text-emerald-700 font-semibold transition-all shrink-0">
                            {{ $label }}
                        </a>
                    @endif
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1 px-3 py-2 sm:px-4 sm:py-2.5 rounded-2xl bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-600 hover:text-emerald-800 hover:border-emerald-600 font-semibold transition-all shadow-md shadow-emerald-900/10 shrink-0">
                    <span>Next</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            @else
                <span class="inline-flex items-center gap-1 px-3 py-2 sm:px-4 sm:py-2.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-slate-600 font-semibold cursor-not-allowed shrink-0">
                    <span>Next</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
