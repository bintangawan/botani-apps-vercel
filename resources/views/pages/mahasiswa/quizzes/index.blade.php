<x-app-layout title="Kuis Evaluasi & Ujian — Botani Phanerogamae">
    <div class="py-12 sm:py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#0c2214] via-emerald-950 to-[#07130c] border border-emerald-500/30 rounded-3xl p-8 sm:p-10 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="space-y-2 text-center sm:text-left">
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">🎓 Pusat Ujian & Kuis Evaluasi</span>
                <h1 class="text-3xl font-extrabold text-white">Evaluasi Pemahaman Botani Phanerogamae</h1>
                <p class="text-sm text-slate-300 max-w-2xl">
                    Uji pemahaman Anda mengenai klasifikasi Gymnospermae dan Angiospermae, karakteristik morfologi, serta taksonomi tumbuhan tingkat tinggi.
                </p>
            </div>
            <a href="{{ route('mahasiswa.dashboard') }}" class="px-5 py-2.5 rounded-2xl bg-[#07130c] border border-emerald-500/40 text-slate-300 hover:text-white font-semibold text-sm transition-all shrink-0">
                &larr; Dashboard Mahasiswa
            </a>
        </div>

        <!-- Quizzes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($quizzes as $quiz)
                @php
                    $attempts = $userAttempts->get($quiz->id) ?? collect([]);
                    $bestAttempt = $attempts->sortByDesc('score')->first();
                    $hasAttempted = $attempts->count() > 0;
                @endphp

                <div class="bg-[#0c2214]/80 border border-emerald-900/60 rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col justify-between hover:border-emerald-500/40 transition-all group">
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <span class="px-3 py-1 rounded-full bg-emerald-950 text-emerald-300 border border-emerald-800/60 text-xs font-bold">
                                {{ $quiz->learningModule ? 'Modul: ' . $quiz->learningModule->title : 'Evaluasi Umum' }}
                            </span>
                            <span class="text-xs font-mono text-slate-400 shrink-0">⏱️ {{ $quiz->duration }}m</span>
                        </div>

                        <h3 class="text-xl font-extrabold text-white group-hover:text-emerald-300 transition-colors">
                            {{ $quiz->title }}
                        </h3>

                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-300">
                            <span class="px-2.5 py-1 rounded-lg bg-[#07130c] border border-emerald-900/50">
                                📝 {{ $quiz->questions_count }} Soal
                            </span>
                            <span class="px-2.5 py-1 rounded-lg bg-[#07130c] border border-amber-900/50 text-amber-300 font-bold">
                                🎯 KKM: {{ $quiz->passing_score }}%
                            </span>
                        </div>
                    </div>

                    <div class="pt-6 mt-6 border-t border-emerald-900/50 space-y-4">
                        @if($hasAttempted && $bestAttempt)
                            <div class="flex items-center justify-between bg-[#07130c] p-3.5 rounded-2xl border border-emerald-900/60">
                                <div>
                                    <span class="text-[11px] text-slate-400 block uppercase font-bold">Skor Terbaik Anda</span>
                                    <span class="text-xs text-slate-500">{{ $attempts->count() }}x Percobaan</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xl font-extrabold {{ $bestAttempt->score >= $quiz->passing_score ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $bestAttempt->score }} / 100
                                    </span>
                                    <span class="block text-[10px] font-bold uppercase {{ $bestAttempt->score >= $quiz->passing_score ? 'text-emerald-300' : 'text-rose-300' }}">
                                        {{ $bestAttempt->score >= $quiz->passing_score ? '✓ Lulus KKM' : '× Belum Tuntas' }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2.5">
                                <a href="{{ route('mahasiswa.quizzes.result', $bestAttempt) }}" class="py-2.5 px-3 rounded-xl bg-[#07130c] hover:bg-emerald-900/40 border border-emerald-900/60 text-slate-300 text-center font-bold text-xs transition-all">
                                    Lihat Pembahasan
                                </a>
                                <form action="{{ route('mahasiswa.quizzes.start', $quiz) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/30 transition-all text-center">
                                        Coba Ulangi &rarr;
                                    </button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('mahasiswa.quizzes.start', $quiz) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-sm shadow-xl shadow-emerald-600/30 flex items-center justify-center gap-2 transition-all">
                                    <span>Mulai Pengerjaan Kuis</span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-12 text-center text-slate-400 space-y-3 shadow-xl">
                    <span class="text-5xl block mb-2">📚</span>
                    <h3 class="text-lg font-bold text-white">Belum Ada Kuis Evaluasi Tersedia</h3>
                    <p class="text-sm max-w-md mx-auto">Dosen atau administrator saat ini sedang menyusun atau meninjau modul evaluasi pembelajaran. Silahkan kembali lagi nanti.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
