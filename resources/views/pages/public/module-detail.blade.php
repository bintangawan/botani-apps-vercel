<x-app-layout :title="$lesson->title . ' — ' . $module->title">
    <div x-data="{ courseMenuOpen: false }" class="relative mx-auto max-w-[1500px] px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid items-start gap-8 lg:grid-cols-[320px_minmax(0,1fr)]">
            <aside class="sticky top-24 hidden h-[calc(100vh-7rem)] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg lg:flex">
                @include('pages.public._course-outline')
            </aside>

            <div x-cloak x-show="courseMenuOpen" class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true">
                <div x-show="courseMenuOpen" x-transition.opacity class="absolute inset-0 bg-slate-950/40" @click="courseMenuOpen = false"></div>
                <aside x-show="courseMenuOpen" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="absolute inset-y-0 left-0 flex w-[min(90vw,340px)] flex-col bg-white shadow-2xl">
                    <button type="button" @click="courseMenuOpen = false" class="absolute right-3 top-3 z-10 rounded-lg border border-slate-200 bg-white p-2 text-slate-600" aria-label="Tutup daftar materi">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                    @include('pages.public._course-outline')
                </aside>
            </div>

            <main class="min-w-0">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <button type="button" @click="courseMenuOpen = true" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm font-bold text-emerald-700 shadow-sm lg:hidden">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        Daftar Materi
                    </button>
                    <a href="{{ route('modules') }}" class="hidden items-center gap-2 text-sm font-bold text-emerald-700 hover:text-emerald-900 lg:inline-flex">← Daftar Modul Pembelajaran</a>
                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Lesson {{ $lessonNumber }} dari {{ $totalLessons }}</span>
                </div>

                <header class="overflow-hidden rounded-3xl border border-emerald-300 bg-emerald-100 p-6 shadow-lg sm:p-9">
                    <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-emerald-800">
                        <span class="rounded-full border border-emerald-300 bg-white/70 px-3 py-1">Bab {{ $module->module_order }}</span>
                        <span>{{ $module->title }}</span>
                    </div>
                    <h1 class="mt-5 max-w-4xl text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">{{ $lesson->title }}</h1>
                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-700">{{ $module->description }}</p>
                    <div class="mt-6 flex flex-wrap gap-3 text-xs font-semibold text-slate-600">
                        <span class="rounded-xl border border-emerald-200 bg-white/70 px-3 py-2">Subbab {{ $lesson->lesson_order }} dari {{ $module->lessons->count() }}</span>
                        <span class="rounded-xl border border-emerald-200 bg-white/70 px-3 py-2">Estimasi bab {{ $module->estimated_minutes }} menit</span>
                        @if(!empty($lesson->source_sections))
                            <span class="rounded-xl border border-emerald-200 bg-white/70 px-3 py-2">Sumber bagian {{ implode(', ', $lesson->source_sections) }}</span>
                        @endif
                    </div>
                </header>

                <article class="lesson-body mt-6 rounded-3xl border border-slate-200 bg-white p-6 text-slate-700 shadow-lg sm:p-10">
                    {!! $lesson->content !!}
                </article>

                @if(!empty($lesson->key_points))
                    <section class="mt-6 rounded-3xl border border-amber-200 bg-amber-50 p-6 sm:p-8">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 text-lg">✓</span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-amber-700">Rangkuman Lesson</p>
                                <h2 class="text-lg font-extrabold text-slate-900">Yang perlu diingat</h2>
                            </div>
                        </div>
                        <ul class="mt-5 grid gap-3 sm:grid-cols-2">
                            @foreach($lesson->key_points as $point)
                                <li class="flex items-start gap-3 rounded-xl border border-amber-100 bg-white/70 p-3 text-sm leading-6 text-slate-700">
                                    <span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-amber-500"></span>
                                    <span>{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if($lesson->is($module->lessons->last()) && !empty($module->chapter_summary))
                    <section class="mt-6 rounded-3xl border border-emerald-200 bg-emerald-50 p-6 sm:p-8">
                        <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">Bab {{ $module->module_order }} Selesai</p>
                        <h2 class="mt-1 text-xl font-extrabold text-slate-900">Ringkasan {{ $module->title }}</h2>
                        <ul class="mt-4 space-y-3">
                            @foreach($module->chapter_summary as $summary)
                                <li class="flex items-start gap-3 text-sm leading-6 text-slate-700"><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-emerald-500"></span><span>{{ $summary }}</span></li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if($lesson->is($module->lessons->last()) && $module->quizzes->isNotEmpty())
                    <section class="mt-6 flex flex-col items-start justify-between gap-5 rounded-3xl border border-emerald-300 bg-emerald-100 p-6 sm:flex-row sm:items-center sm:p-8">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-widest text-emerald-700">Evaluasi Bab</span>
                            <h2 class="mt-1 text-xl font-extrabold text-slate-900">{{ $module->quizzes->count() }} kuis tersedia untuk bab ini</h2>
                            <p class="mt-1 text-sm text-slate-600">Uji pemahaman setelah menyelesaikan seluruh subbab.</p>
                        </div>
                        @auth
                            @if(auth()->user()->role === 'mahasiswa')
                                <a href="{{ route('mahasiswa.quizzes.index') }}" class="shrink-0 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-extrabold text-white">Buka Kuis →</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="shrink-0 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-extrabold text-white">Masuk untuk Kuis</a>
                        @endauth
                    </section>
                @endif

                <nav class="mt-8 grid gap-4 border-t border-slate-200 pt-6 sm:grid-cols-2" aria-label="Navigasi subbab">
                    @if($previousLesson)
                        <a href="{{ route('modules.detail', ['slug' => $previousLesson['module_slug'], 'lessonSlug' => $previousLesson['lesson_slug']]) }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md">
                            <span class="text-xs font-bold text-slate-400">← Sebelumnya</span>
                            <span class="mt-1 block text-sm font-extrabold text-slate-800 group-hover:text-emerald-700">{{ $previousLesson['lesson_title'] }}</span>
                        </a>
                    @else
                        <span></span>
                    @endif

                    @if($nextLesson)
                        <a href="{{ route('modules.detail', ['slug' => $nextLesson['module_slug'], 'lessonSlug' => $nextLesson['lesson_slug']]) }}" class="group rounded-2xl border border-emerald-300 bg-emerald-50 p-5 text-right shadow-sm transition hover:bg-emerald-100 hover:shadow-md">
                            <span class="text-xs font-bold text-emerald-600">Selanjutnya →</span>
                            <span class="mt-1 block text-sm font-extrabold text-slate-900 group-hover:text-emerald-800">{{ $nextLesson['lesson_title'] }}</span>
                        </a>
                    @endif
                </nav>
            </main>
        </div>
    </div>

    <style>
        .course-outline summary:focus-visible { outline: 2px solid #10b981; outline-offset: 2px; }
        .lesson-body { font-size: 1rem; line-height: 1.85; }
        .lesson-body > :first-child { margin-top: 0; }
        .lesson-body > :last-child { margin-bottom: 0; }
        .lesson-body h1, .lesson-body h2, .lesson-body h3, .lesson-body h4 { color: #0f172a; font-weight: 800; line-height: 1.3; margin: 2rem 0 .8rem; }
        .lesson-body h1 { font-size: 2rem; }
        .lesson-body h2 { font-size: 1.6rem; }
        .lesson-body h3 { border-bottom: 1px solid #d1fae5; padding-bottom: .55rem; font-size: 1.3rem; }
        .lesson-body h4 { font-size: 1.1rem; }
        .lesson-body p { margin: 1rem 0; }
        .lesson-body ul, .lesson-body ol { margin: 1rem 0; padding-left: 1.5rem; }
        .lesson-body ul { list-style: disc; }
        .lesson-body ol { list-style: decimal; }
        .lesson-body li { margin: .45rem 0; padding-left: .25rem; }
        .lesson-body strong { color: #0f172a; font-weight: 750; }
        .lesson-body blockquote { margin: 1.5rem 0; border-left: 4px solid #10b981; border-radius: 0 .75rem .75rem 0; background: #ecfdf5; padding: 1rem 1.25rem; }
        .lesson-body a { color: #047857; font-weight: 650; text-decoration: underline; }
        .lesson-body code { border-radius: .35rem; background: #f1f5f9; padding: .15rem .35rem; font-size: .9em; color: #0f766e; }
        .lesson-body pre { overflow-x: auto; border-radius: .9rem; background: #0f172a; padding: 1.25rem; color: #e2e8f0; }
        .lesson-body pre code { background: transparent; padding: 0; color: inherit; }
        .lesson-body table { display: block; width: 100%; overflow-x: auto; border-collapse: collapse; margin: 1.5rem 0; }
        .lesson-body th, .lesson-body td { min-width: 140px; border: 1px solid #dbe4e8; padding: .75rem 1rem; text-align: left; }
        .lesson-body th { background: #ecfdf5; color: #065f46; font-weight: 800; }
        .lesson-body hr { margin: 2rem 0; border-color: #e2e8f0; }
    </style>
</x-app-layout>
