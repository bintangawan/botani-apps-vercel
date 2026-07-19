<x-app-layout :title="$module->title . ' — Modul Botani Phanerogamae'">
    <div class="py-12 sm:py-20 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb & Back Link -->
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('modules') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                <span>Daftar Modul Pembelajaran</span>
            </a>
            <span class="text-xs font-bold text-emerald-400 bg-emerald-950 px-3 py-1 rounded-full border border-emerald-500/30">
                Modul #{{ $module->module_order }}
            </span>
        </div>

        <!-- Module Header Banner -->
        <div class="bg-gradient-to-br from-[#0c2214] via-[#091b11] to-[#07130c] border border-emerald-500/30 rounded-3xl p-8 sm:p-12 mb-12 shadow-2xl relative overflow-hidden">
            <div class="max-w-3xl relative z-10 space-y-4">
                <span class="text-xs font-bold text-amber-400 uppercase tracking-widest block">📖 Teori & Kurikulum Botani</span>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight">
                    {{ $module->title }}
                </h1>
                <p class="text-base sm:text-lg text-slate-300 leading-relaxed">
                    {{ $module->description }}
                </p>
                <div class="flex flex-wrap items-center gap-4 pt-4 text-xs font-semibold text-emerald-300">
                    <span class="px-3 py-1.5 rounded-xl bg-emerald-950/80 border border-emerald-500/20">✨ Materi Terverifikasi Dosen</span>
                    <span class="px-3 py-1.5 rounded-xl bg-emerald-950/80 border border-emerald-500/20">🌱 {{ $module->plantSpecies->count() }} Spesimen Terkait</span>
                </div>
            </div>
            <div class="absolute right-0 bottom-0 top-0 w-1/3 bg-emerald-500/10 blur-3xl pointer-events-none"></div>
        </div>

        <!-- Theory Content HTML -->
        <article class="bg-[#0c2214]/60 border border-emerald-900/40 rounded-3xl p-8 sm:p-12 shadow-xl prose prose-invert prose-emerald max-w-none leading-relaxed text-slate-200">
            {!! $module->content !!}
        </article>

        <!-- Quiz Trigger Banner -->
        <div class="mt-12 bg-gradient-to-r from-emerald-950 via-[#0c2214] to-emerald-900/80 border border-emerald-500/40 rounded-3xl p-8 sm:p-10 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-2xl">
            <div class="space-y-2 text-center sm:text-left">
                <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">📝 Evaluasi Kuis Tersedia</span>
                <h3 class="text-2xl font-bold text-white">Uji Pemahaman Materi Modul #{{ $module->module_order }}</h3>
                <p class="text-sm text-slate-300">
                    Ikuti kuis latihan interaktif untuk mengukur pemahaman taksonomi & morfologi setelah mempelajari materi ini.
                </p>
            </div>
            @auth
                @if(auth()->user()->role === 'mahasiswa')
                    <a href="{{ route('mahasiswa.dashboard') }}" class="px-7 py-3.5 rounded-2xl bg-emerald-500 text-white font-bold text-sm shadow-xl hover:bg-emerald-400 transition-all shrink-0">
                        Buka Kuis di Dashboard →
                    </a>
                @else
                    <span class="px-5 py-2.5 rounded-xl bg-black/40 text-slate-300 text-xs border border-emerald-500/20 font-medium">
                        Kuis Dikelola dari Dashboard {{ ucfirst(auth()->user()->role) }}
                    </span>
                @endif
            @else
                <a href="{{ route('login') }}" class="px-7 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-bold text-sm shadow-xl hover:scale-105 transition-all shrink-0">
                    Masuk untuk Mulai Kuis
                </a>
            @endauth
        </div>
    </div>
</x-app-layout>
