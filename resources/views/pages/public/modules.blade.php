<x-app-layout title="Modul Pembelajaran Botani Phanerogamae">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
        <section class="relative overflow-hidden rounded-[2rem] border border-emerald-300 bg-emerald-100 px-6 py-10 shadow-xl sm:px-10 lg:px-14 lg:py-14">
            <div class="relative z-10 max-w-3xl">
                <span class="inline-flex rounded-full border border-emerald-300 bg-white/70 px-3 py-1 text-xs font-bold uppercase tracking-widest text-emerald-700">Kurikulum Botani Phanerogamae</span>
                <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-5xl">Belajar terstruktur dari bab ke subbab</h1>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-700 sm:text-base">Materi disusun menjadi 9 bab dan {{ $modules->sum('lessons_count') }} lesson. Pilih bab, lalu ikuti setiap subbab melalui navigasi pembelajaran yang berurutan.</p>
                <div class="mt-7 flex flex-wrap gap-3 text-xs font-bold text-emerald-800">
                    <span class="rounded-xl border border-emerald-300 bg-white/70 px-3 py-2">{{ $modules->count() }} Bab</span>
                    <span class="rounded-xl border border-emerald-300 bg-white/70 px-3 py-2">{{ $modules->sum('lessons_count') }} Subbab</span>
                    <span class="rounded-xl border border-emerald-300 bg-white/70 px-3 py-2">{{ $modules->sum('estimated_minutes') }} Menit</span>
                </div>
            </div>
            <div class="absolute -bottom-20 -right-16 h-64 w-64 rounded-full bg-emerald-300/40"></div>
            <div class="absolute -right-4 top-8 h-28 w-28 rounded-full border-[20px] border-white/30"></div>
        </section>

        <section class="mt-12">
            <div class="mb-6 flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">Daftar Materi</p>
                    <h2 class="mt-1 text-2xl font-extrabold text-slate-900">9 Bab Pembelajaran</h2>
                </div>
                <p class="hidden text-sm text-slate-500 sm:block">Mulai dari bab mana saja</p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($modules as $module)
                    @php($firstLesson = $module->lessons->first())
                    <article class="group flex h-full flex-col overflow-hidden rounded-3xl border border-emerald-200 bg-white shadow-lg transition duration-300 hover:-translate-y-1 hover:border-emerald-400 hover:shadow-xl">
                        <div class="border-b border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-6">
                            <div class="flex items-center justify-between gap-4">
                                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-sm font-extrabold text-white shadow-md">{{ str_pad($module->module_order, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="rounded-full border border-emerald-200 bg-white px-3 py-1 text-xs font-bold text-emerald-700">{{ $module->lessons_count }} lesson</span>
                            </div>
                            <h3 class="mt-5 text-xl font-extrabold leading-snug text-slate-900 transition group-hover:text-emerald-700">{{ $module->title }}</h3>
                            <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">{{ $module->description }}</p>
                        </div>

                        <div class="flex flex-1 flex-col p-6">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Subbab dalam bab ini</p>
                            <ol class="mt-3 space-y-2.5">
                                @foreach($module->lessons->take(3) as $lesson)
                                    <li class="flex items-start gap-2.5 text-sm text-slate-600">
                                        <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-400"></span>
                                        <span class="line-clamp-1">{{ $lesson->title }}</span>
                                    </li>
                                @endforeach
                            </ol>
                            @if($module->lessons_count > 3)
                                <p class="mt-2 text-xs font-semibold text-emerald-700">+ {{ $module->lessons_count - 3 }} subbab lainnya</p>
                            @endif

                            <div class="mt-auto flex items-center justify-between border-t border-emerald-100 pt-5">
                                <span class="text-xs font-semibold text-slate-500">± {{ $module->estimated_minutes }} menit</span>
                                @if($firstLesson)
                                    <a href="{{ route('modules.detail', ['slug' => $module->slug, 'lessonSlug' => $firstLesson->slug]) }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-extrabold text-white transition hover:bg-emerald-700">Mulai Belajar <span>→</span></a>
                                @else
                                    <span class="rounded-xl bg-slate-100 px-4 py-2.5 text-xs font-bold text-slate-400">Belum tersedia</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
