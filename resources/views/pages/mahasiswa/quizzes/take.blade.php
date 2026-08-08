<x-app-layout title="Mengerjakan: {{ $attempt->quiz->title }} — Botani Phanerogamae">
    @php
        $endTime = $attempt->started_at->addMinutes($attempt->quiz->duration)->timestamp;
    @endphp

    <div class="py-8 sm:py-12 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" 
         x-data="{
             endTime: {{ $endTime }},
             remaining: 0,
             timerDisplay: '00:00',
             activeTab: 0,
             answeredCount: 0,
             totalQuestions: {{ $attempt->quiz->questions->count() }},
             initTimer() {
                 this.updateTimer();
                 setInterval(() => {
                     this.updateTimer();
                 }, 1000);
             },
             updateTimer() {
                 let now = Math.floor(Date.now() / 1000);
                 let diff = this.endTime - now;
                 if (diff <= 0) {
                     this.remaining = 0;
                     this.timerDisplay = '00:00';
                     // Auto submit when time expires
                     document.getElementById('quiz-submit-form').submit();
                 } else {
                     this.remaining = diff;
                     let mins = Math.floor(diff / 60);
                     let secs = diff % 60;
                     this.timerDisplay = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                 }
             },
             checkAnswered() {
                 let checked = document.querySelectorAll('input[type=radio]:checked').length;
                 this.answeredCount = checked;
             }
         }"
         x-init="initTimer()">
        
        <!-- Top Sticky Bar / Timer -->
        <div class="sticky top-4 z-40 bg-white  border border-emerald-300 rounded-2xl p-4 sm:p-6 shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-700 font-extrabold text-lg shrink-0">
                    ⏱️
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600 block">Sisa Waktu Pengerjaan</span>
                    <span class="text-2xl sm:text-3xl font-extrabold font-mono tracking-wider" 
                          :class="remaining <= 300 ? 'text-rose-700 animate-pulse' : 'text-emerald-700'"
                          x-text="timerDisplay">00:00</span>
                </div>
            </div>

            <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end">
                <div class="text-right">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600 block">Progress Terjawab</span>
                    <span class="text-base font-extrabold text-slate-900">
                        <span x-text="answeredCount">0</span> / {{ $attempt->quiz->questions->count() }} Soal
                    </span>
                </div>

                <button type="button" @click="if(confirm('Apakah Anda yakin telah selesai menjawab seluruh soal? Nilai akan langsung dihitung setelah diserahkan.')) { document.getElementById('quiz-submit-form').submit(); }" 
                        class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-slate-900 font-extrabold text-sm shadow-xl shadow-emerald-900/10 transition-all shrink-0">
                    Selesai & Kumpulkan &rarr;
                </button>
            </div>
        </div>

        <form id="quiz-submit-form" action="{{ route('mahasiswa.quizzes.submit', $attempt) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            @csrf

            <!-- Left / Question Navigator Grid -->
            <div class="lg:col-span-1 space-y-6 order-2 lg:order-1">
                <div class="bg-white border border-emerald-200 rounded-3xl p-6 shadow-xl space-y-4 sticky top-32">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider border-b border-emerald-200 pb-3">
                        🧭 Navigasi Soal
                    </h3>

                    <div class="grid grid-cols-5 gap-2.5">
                        @foreach($attempt->quiz->questions as $idx => $q)
                            <button type="button" @click="activeTab = {{ $idx }}" 
                                    :class="activeTab === {{ $idx }} ? 'bg-emerald-500 text-[#07130c] ring-2 ring-emerald-300 font-extrabold scale-105' : (document.querySelector('input[name=\'answers[{{ $q->id }}]\']:checked') ? 'bg-emerald-100 text-emerald-700 border border-emerald-300 font-bold' : 'bg-white text-slate-600 border border-emerald-200 hover:border-emerald-300')"
                                    class="w-full py-2.5 rounded-xl text-xs font-semibold transition-all flex items-center justify-center">
                                {{ $idx + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <div class="pt-3 border-t border-emerald-200 space-y-2 text-[11px] text-slate-600">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded bg-emerald-500"></span>
                            <span>Sedang Dibuka</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded bg-emerald-200 border border-emerald-500"></span>
                            <span>Sudah Dijawab</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded bg-white border border-emerald-200"></span>
                            <span>Belum Dijawab</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right / Question Display Cards -->
            <div class="lg:col-span-3 space-y-6 order-1 lg:order-2">
                @foreach($attempt->quiz->questions as $idx => $question)
                    <div x-show="activeTab === {{ $idx }}" x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" 
                         class="bg-white border border-emerald-200 rounded-3xl p-6 sm:p-10 shadow-lg space-y-8 min-h-[450px] flex flex-col justify-between">
                        
                        <div class="space-y-6">
                            <!-- Question Number and Weight -->
                            <div class="flex items-center justify-between border-b border-emerald-200 pb-4">
                                <span class="px-3.5 py-1.5 rounded-xl bg-emerald-100 text-emerald-700 font-extrabold text-sm border border-emerald-300">
                                    Pertanyaan Nomor #{{ $idx + 1 }}
                                </span>
                                <span class="text-xs font-bold text-amber-700">
                                    Bobot Poin: {{ $question->score_weight }}
                                </span>
                            </div>

                            <!-- Question Text -->
                            <div class="text-slate-900 font-medium text-lg sm:text-xl leading-relaxed">
                                {!! nl2br(e($question->question_text)) !!}
                            </div>

                            <!-- Options -->
                            @if($question->question_type === 'pilihan_ganda' && $question->options->count() > 0)
                                <div class="space-y-3.5 pt-4">
                                    @foreach($question->options as $optIdx => $option)
                                        <label class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-emerald-200 hover:border-emerald-300 cursor-pointer transition-all group relative">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" @change="checkAnswered()" 
                                                   class="text-emerald-700 focus:ring-emerald-500 bg-emerald-100 border-emerald-300 w-5 h-5 cursor-pointer shrink-0">
                                            <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 font-extrabold text-sm flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-slate-900 transition-colors">
                                                {{ chr(65 + $optIdx) }}
                                            </span>
                                            <span class="text-sm sm:text-base text-slate-700 group-hover:text-slate-900 font-medium transition-colors">
                                                {{ $option->option_text }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-6 rounded-2xl bg-white border border-emerald-200 text-slate-600 text-sm">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-emerald-700 mb-2">Jawaban Esai Anda:</label>
                                    <textarea name="answers_text[{{ $question->id }}]" rows="4" placeholder="Ketikkan uraian jawaban Anda di sini..." 
                                              class="w-full rounded-xl bg-white border border-emerald-200 focus:border-emerald-500 text-slate-900 text-sm p-3"></textarea>
                                </div>
                            @endif
                        </div>

                        <!-- Bottom Navigation Buttons -->
                        <div class="pt-6 border-t border-emerald-200 flex items-center justify-between gap-4">
                            <button type="button" @click="activeTab = Math.max(0, activeTab - 1)" :disabled="activeTab === 0" 
                                    :class="activeTab === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-emerald-100 text-slate-700'"
                                    class="px-5 py-2.5 rounded-xl bg-white border border-emerald-200 font-bold text-xs flex items-center gap-2 transition-all">
                                &larr; Soal Sebelumnya
                            </button>

                            <button type="button" @click="activeTab = Math.min(totalQuestions - 1, activeTab + 1)" :disabled="activeTab === totalQuestions - 1" 
                                    :class="activeTab === totalQuestions - 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-emerald-100 text-slate-700'"
                                    class="px-5 py-2.5 rounded-xl bg-white border border-emerald-200 font-bold text-xs flex items-center gap-2 transition-all">
                                Soal Selanjutnya &rarr;
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    </div>
</x-app-layout>
