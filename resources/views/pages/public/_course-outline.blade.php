<div class="border-b border-slate-200 px-5 py-5">
    <a href="{{ route('modules') }}" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 hover:text-emerald-900">← Daftar Modul</a>
    <div class="mt-4 flex items-end justify-between gap-3">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Kurikulum Lengkap</p>
            <p class="mt-1 text-sm font-extrabold text-slate-900">Botani Phanerogamae</p>
        </div>
        <span class="shrink-0 rounded-lg bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">{{ $totalLessons }} lesson</span>
    </div>
</div>

<nav class="course-outline flex-1 overflow-y-auto px-3 py-4" aria-label="Daftar bab dan subbab">
    @foreach($courseModules as $chapter)
        <details class="group/chapter mb-2" @if($chapter->id === $module->id) open @endif>
            <summary class="flex cursor-pointer list-none items-start gap-3 rounded-xl px-3 py-3 transition hover:bg-emerald-50 [&::-webkit-details-marker]:hidden">
                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-emerald-200 bg-white text-[10px] font-extrabold text-emerald-700">{{ $chapter->module_order }}</span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-extrabold leading-5 {{ $chapter->id === $module->id ? 'text-emerald-800' : 'text-slate-800' }}">{{ $chapter->title }}</span>
                    <span class="mt-1 block text-[11px] text-slate-400">{{ $chapter->lessons->count() }} lesson</span>
                </span>
                <svg class="mt-1 h-4 w-4 shrink-0 text-slate-400 transition group-open/chapter:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
            </summary>

            <div class="ml-6 border-l border-slate-200 pb-2 pl-3">
                @foreach($chapter->lessons as $chapterLesson)
                    @php($isActiveLesson = $chapterLesson->id === $lesson->id)
                    <a href="{{ route('modules.detail', ['slug' => $chapter->slug, 'lessonSlug' => $chapterLesson->slug]) }}" class="relative mb-1 flex items-start gap-2.5 rounded-xl px-3 py-2.5 text-sm leading-5 transition {{ $isActiveLesson ? 'bg-emerald-100 font-bold text-emerald-900' : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-800' }}">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $isActiveLesson ? 'bg-emerald-600' : 'bg-slate-300' }}"></span>
                        <span>{{ $chapterLesson->title }}</span>
                    </a>
                @endforeach
            </div>
        </details>
    @endforeach
</nav>
