<x-app-layout title="Hasil Kuis: {{ $attempt->quiz->title }} — Botani Phanerogamae">
    <div class="py-12 sm:py-16 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <!-- Celebration Header -->
        <div class="bg-gradient-to-r from-[#0c2214] via-emerald-950 to-[#07130c] border border-emerald-500/30 rounded-3xl p-8 sm:p-12 shadow-2xl text-center space-y-6">
            <span class="px-3.5 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30 inline-block">
                🎉 Rekapitulasi & Evaluasi Nilai Akhir
            </span>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-white">
                {{ $attempt->quiz->title }}
            </h1>

            <!-- Score Display -->
            <div class="py-6 flex flex-col items-center justify-center gap-2">
                <div class="w-36 h-36 sm:w-44 sm:h-44 rounded-full border-4 {{ $attempt->score >= $attempt->quiz->passing_score ? 'border-emerald-500 bg-emerald-950/80 shadow-emerald-500/30' : 'border-rose-500 bg-rose-950/80 shadow-rose-500/30' }} shadow-2xl flex flex-col items-center justify-center">
                    <span class="text-4xl sm:text-5xl font-extrabold text-white">{{ $attempt->score }}</span>
                    <span class="text-xs text-slate-300 uppercase font-bold mt-1">/ 100 Poin</span>
                </div>

                <div class="mt-4">
                    @if($attempt->score >= $attempt->quiz->passing_score)
                        <span class="px-5 py-2 rounded-2xl bg-emerald-500/20 text-emerald-300 font-extrabold text-sm border border-emerald-500/40 inline-flex items-center gap-2 shadow-lg">
                            <span>✓ LULUS (Ketuntasan Minimal: {{ $attempt->quiz->passing_score }}%)</span>
                        </span>
                    @else
                        <span class="px-5 py-2 rounded-2xl bg-rose-500/20 text-rose-300 font-extrabold text-sm border border-rose-500/40 inline-flex items-center gap-2 shadow-lg">
                            <span>× BELUM TUNTAS (KKM: {{ $attempt->quiz->passing_score }}%)</span>
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-2xl mx-auto pt-4 text-left">
                <div class="bg-[#07130c] p-4 rounded-2xl border border-emerald-900/60">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Benar</span>
                    <span class="text-lg font-extrabold text-emerald-400">{{ $attempt->total_correct }} Soal</span>
                </div>
                <div class="bg-[#07130c] p-4 rounded-2xl border border-emerald-900/60">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Salah / Kosong</span>
                    <span class="text-lg font-extrabold text-rose-400">{{ $attempt->quiz->questions->count() - $attempt->total_correct }} Soal</span>
                </div>
                <div class="bg-[#07130c] p-4 rounded-2xl border border-emerald-900/60">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Waktu Mulai</span>
                    <span class="text-sm font-bold text-white">{{ $attempt->started_at->format('H:i') }} WIB</span>
                </div>
                <div class="bg-[#07130c] p-4 rounded-2xl border border-emerald-900/60">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Waktu Selesai</span>
                    <span class="text-sm font-bold text-white">{{ $attempt->completed_at ? $attempt->completed_at->format('H:i') : '-' }} WIB</span>
                </div>
            </div>

            <div class="pt-6 border-t border-emerald-900/50 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('mahasiswa.quizzes.index') }}" class="px-6 py-3 rounded-2xl bg-[#07130c] border border-emerald-500/40 text-slate-300 hover:text-white font-semibold text-sm transition-all">
                    &larr; Kembali ke Daftar Kuis
                </a>
                <form action="{{ route('mahasiswa.quizzes.start', $attempt->quiz) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-sm shadow-lg shadow-emerald-600/30 transition-all">
                        Coba Ulang Kuis Ini &rarr;
                    </button>
                </form>
            </div>
        </div>

        <!-- Detailed Review Section -->
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-emerald-900/50 pb-4">
                <h2 class="text-xl font-extrabold text-white">📝 Pembahasan & Tinjauan Soal</h2>
                <span class="text-xs text-slate-400">Total {{ $attempt->quiz->questions->count() }} Pertanyaan</span>
            </div>

            <div class="space-y-6">
                @foreach($attempt->quiz->questions as $idx => $question)
                    @php
                        $userAnswer = $attempt->answers->where('question_id', $question->id)->first();
                        $selectedId = $userAnswer ? $userAnswer->selected_option_id : null;
                        $isCorrect = $userAnswer && $userAnswer->is_correct;
                    @endphp

                    <div class="bg-[#0c2214]/80 border {{ $isCorrect ? 'border-emerald-500/40' : 'border-rose-500/40' }} rounded-3xl p-6 sm:p-8 shadow-xl space-y-6 relative">
                        <!-- Top status badge -->
                        <div class="flex items-start justify-between gap-4 border-b border-emerald-900/40 pb-4">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-2xl {{ $isCorrect ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border-rose-500/40' }} border font-extrabold text-sm flex items-center justify-center shrink-0">
                                    #{{ $idx + 1 }}
                                </span>
                                <div>
                                    <span class="text-xs font-bold uppercase {{ $isCorrect ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $isCorrect ? '✓ Benar (+'.$question->score_weight.' Poin)' : '× Salah (0 Poin)' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Question text -->
                        <div class="text-white font-medium text-base sm:text-lg leading-relaxed">
                            {!! nl2br(e($question->question_text)) !!}
                        </div>

                        <!-- Options breakdown -->
                        @if($question->question_type === 'pilihan_ganda')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                @foreach($question->options as $optIdx => $opt)
                                    @php
                                        $isSelected = ($selectedId == $opt->id);
                                        $isKeyCorrect = $opt->is_correct;
                                    @endphp

                                    <div class="p-4 rounded-2xl border flex items-center justify-between gap-3
                                        {{ $isKeyCorrect ? 'bg-emerald-900/40 border-emerald-500 text-white' : ($isSelected ? 'bg-rose-950/40 border-rose-500 text-rose-200' : 'bg-[#07130c]/60 border-emerald-950 text-slate-400') }}">
                                        
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-lg font-extrabold text-xs flex items-center justify-center shrink-0
                                                {{ $isKeyCorrect ? 'bg-emerald-500 text-[#07130c]' : ($isSelected ? 'bg-rose-500 text-white' : 'bg-emerald-950 text-emerald-400') }}">
                                                {{ chr(65 + $optIdx) }}
                                            </span>
                                            <span class="text-sm font-medium">{{ $opt->option_text }}</span>
                                        </div>

                                        <div class="flex items-center gap-1.5 shrink-0">
                                            @if($isSelected)
                                                <span class="px-2 py-0.5 rounded bg-[#07130c] text-[10px] font-bold uppercase {{ $isKeyCorrect ? 'text-emerald-300' : 'text-rose-300' }}">
                                                    Jawaban Anda
                                                </span>
                                            @endif
                                            @if($isKeyCorrect)
                                                <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-[10px] font-bold uppercase">
                                                    ✓ Kunci Benar
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
