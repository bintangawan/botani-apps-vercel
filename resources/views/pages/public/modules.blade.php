<x-app-layout title="Modul Pembelajaran Botani Phanerogamae">
    <div class="py-12 sm:py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest block mb-2">📚 Media Pembelajaran Digital</span>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                Modul & Teori Botani Phanerogamae
            </h1>
            <p class="text-slate-300 text-sm sm:text-base mt-3">
                Pelajari materi terstruktur mengenai klasifikasi, morfologi, anatomi, dan reproduksi tumbuhan berbiji disertai evaluasi pemahaman (pretest & posttest).
            </p>
        </div>

        <!-- Modules List -->
        <div class="space-y-8 max-w-5xl mx-auto">
            @foreach($modules as $module)
                <div class="bg-[#0c2214]/80 border border-emerald-900/50 rounded-3xl p-6 sm:p-10 shadow-xl hover:border-emerald-500/50 transition-all flex flex-col md:flex-row items-start md:items-center justify-between gap-8 group">
                    <div class="space-y-4 max-w-2xl">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">
                                Modul #{{ $module->module_order }}
                            </span>
                            <span class="text-xs text-slate-400 font-medium">✨ Kurikulum Biologi</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white group-hover:text-emerald-300 transition-colors">
                            {{ $module->title }}
                        </h2>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            {{ $module->description }}
                        </p>
                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 pt-2">
                            <span class="flex items-center gap-1">⏱️ Estimasi Waktu: 45 Menit</span>
                            <span class="flex items-center gap-1">📝 Evaluasi Kuis: Terintegrasi</span>
                        </div>
                    </div>
                    <div class="w-full md:w-auto shrink-0">
                        <a href="{{ route('modules.detail', $module->slug) }}" 
                           class="w-full md:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-bold text-sm shadow-xl shadow-emerald-600/30 transition-all flex items-center justify-center gap-2">
                            <span>Mulai Belajar</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
