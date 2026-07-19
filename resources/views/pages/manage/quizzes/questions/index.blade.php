<x-manage-layout title="Bank Soal: {{ $quiz->title }} — Botani Phanerogamae">
    <div class="space-y-8 max-w-5xl mx-auto" x-data="{ createModalOpen: false, questionType: 'pilihan_ganda', editQuestionId: null }">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-[#0c2214] via-emerald-950 to-[#07130c] border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="space-y-1.5 text-center sm:text-left">
                <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">📝 Bank Soal & Kunci Jawaban</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">{{ $quiz->title }}</h1>
                <p class="text-sm text-slate-300">
                    Modul: <span class="text-emerald-300 font-bold">{{ $quiz->learningModule ? $quiz->learningModule->title : 'Mandiri' }}</span> &bull; Passing Score: <span class="text-amber-400 font-bold">{{ $quiz->passing_score }}%</span> &bull; Durasi: <span class="text-white font-mono">{{ $quiz->duration }}m</span>
                </p>
            </div>
            <div class="flex gap-3 shrink-0">
                <a href="{{ route('manage.quizzes.index') }}" class="px-5 py-2.5 rounded-2xl bg-[#07130c] border border-emerald-500/40 text-slate-300 hover:text-white font-semibold text-sm transition-all">
                    &larr; Kembali
                </a>
                <button type="button" @click="createModalOpen = true; questionType = 'pilihan_ganda'" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-sm shadow-lg shadow-emerald-600/30 flex items-center gap-2 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                    <span>+ Tambah Soal Baru</span>
                </button>
            </div>
        </div>

        <!-- Questions List -->
        <div class="space-y-6">
            @forelse($quiz->questions as $index => $q)
                <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6 relative hover:border-emerald-700/50 transition-colors" x-data="{ editing: false }">
                    <!-- View Mode -->
                    <div x-show="!editing" class="space-y-4">
                        <div class="flex items-start justify-between gap-4 border-b border-emerald-900/40 pb-4">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-2xl bg-emerald-600/20 border border-emerald-500/30 text-emerald-300 font-extrabold text-sm flex items-center justify-center shrink-0">
                                    #{{ $index + 1 }}
                                </span>
                                <div>
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-950 text-emerald-400 border border-emerald-800/60 mr-2">
                                        {{ str_replace('_', ' ', $q->question_type) }}
                                    </span>
                                    <span class="text-xs text-amber-400 font-bold">Bobot Nilai: {{ $q->score_weight }} Poin</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="editing = true" class="p-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-semibold flex items-center gap-1 transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    <span>Edit Soal</span>
                                </button>
                                <form id="delete-q-{{ $q->id }}" action="{{ route('manage.quizzes.questions.destroy', [$quiz, $q]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" @click="confirmDelete('delete-q-{{ $q->id }}', 'soal nomor {{ $index + 1 }}')" 
                                            class="p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-semibold flex items-center gap-1 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="text-white font-medium text-base sm:text-lg leading-relaxed">
                            {!! nl2br(e($q->question_text)) !!}
                        </div>

                        @if($q->question_type === 'pilihan_ganda' && $q->options && $q->options->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                                @foreach($q->options as $optIdx => $opt)
                                    <div class="p-4 rounded-2xl border flex items-center justify-between gap-3 {{ $opt->is_correct ? 'bg-emerald-900/40 border-emerald-500 text-white shadow-lg shadow-emerald-950/50' : 'bg-[#07130c]/80 border-emerald-950 text-slate-300' }}">
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-lg {{ $opt->is_correct ? 'bg-emerald-500 text-[#07130c]' : 'bg-emerald-950 text-emerald-400' }} font-extrabold text-xs flex items-center justify-center shrink-0">
                                                {{ chr(65 + $optIdx) }}
                                            </span>
                                            <span class="text-sm font-medium">{{ $opt->option_text }}</span>
                                        </div>
                                        @if($opt->is_correct)
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-bold shrink-0 flex items-center gap-1">
                                                <span>✓ Kunci Jawaban Benar</span>
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @elseif($q->question_type === 'esai')
                            <div class="p-4 rounded-2xl bg-[#07130c] border border-emerald-900/40 text-slate-400 text-sm italic">
                                ✍️ Soal bertipe esai mandiri. Mahasiswa akan mengetikkan uraian deskriptif dan dinilai oleh dosen/sistem.
                            </div>
                        @endif
                    </div>

                    <!-- Inline Edit Mode -->
                    <div x-show="editing" x-cloak style="display: none;" class="space-y-6 pt-2">
                        <div class="flex items-center justify-between border-b border-emerald-900/40 pb-3">
                            <h3 class="text-base font-extrabold text-white">✏️ Edit Soal Nomor #{{ $index + 1 }}</h3>
                            <button type="button" @click="editing = false" class="text-xs text-slate-400 hover:text-white">Batal Edit</button>
                        </div>

                        <form action="{{ route('manage.quizzes.questions.update', [$quiz, $q]) }}" method="POST" class="space-y-6" x-data="{ qType: '{{ $q->question_type }}' }">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Tipe Soal</label>
                                    <select name="question_type" x-model="qType" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-semibold">
                                        <option value="pilihan_ganda">Pilihan Ganda (Rekomendasi)</option>
                                        <option value="esai">Esai Mandiri</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Bobot Nilai Poin</label>
                                    <input type="number" name="score_weight" value="{{ $q->score_weight }}" required min="1" max="100" 
                                           class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-bold text-center">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Pertanyaan Soal Evaluasi <span class="text-rose-400">*</span></label>
                                <textarea name="question_text" rows="3" required class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ $q->question_text }}</textarea>
                            </div>

                            <div x-show="qType === 'pilihan_ganda'" class="space-y-3 bg-[#07130c] p-4 rounded-2xl border border-emerald-900/60">
                                <label class="block text-xs font-bold uppercase tracking-wider text-emerald-400">Pilihan Jawaban & Kunci Benar <span class="text-rose-400">*</span></label>
                                @php
                                    $correctIdx = 0;
                                    foreach($q->options as $oidx => $o) {
                                        if($o->is_correct) $correctIdx = $oidx;
                                    }
                                @endphp

                                @for($i = 0; $i < 4; $i++)
                                    @php
                                        $optVal = $q->options[$i]->option_text ?? '';
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="correct_option_index" value="{{ $i }}" {{ $correctIdx === $i ? 'checked' : '' }} title="Pilih sebagai Kunci Jawaban Benar" 
                                               class="text-emerald-500 focus:ring-emerald-500 bg-emerald-950 border-emerald-700 w-5 h-5 cursor-pointer">
                                        <span class="w-7 h-7 rounded-lg bg-emerald-900/50 text-emerald-300 font-extrabold text-xs flex items-center justify-center shrink-0">
                                            {{ chr(65 + $i) }}
                                        </span>
                                        <input type="text" name="options[{{ $i }}][text]" value="{{ $optVal }}" placeholder="Teks opsi pilihan {{ chr(65 + $i) }}..." 
                                               class="w-full rounded-xl bg-[#0c2214] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2 px-3.5">
                                    </div>
                                @endfor
                                <p class="text-[11px] text-slate-400 mt-1">💡 Klik radio button (lingkaran) di sebelah kiri opsi untuk menetapkan jawaban yang benar.</p>
                            </div>

                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" @click="editing = false" class="px-5 py-2 rounded-xl bg-[#07130c] text-slate-300 text-xs font-semibold">Batal</button>
                                <button type="submit" class="px-6 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 text-white text-xs font-extrabold shadow-lg">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-12 text-center text-slate-400 space-y-4 shadow-xl">
                    <span class="text-5xl block">📝</span>
                    <h3 class="text-lg font-bold text-white">Bank Soal Kuis Masih Kosong</h3>
                    <p class="text-sm max-w-md mx-auto">Belum ada butir soal yang ditambahkan pada kuis ini. Klik tombol di bawah atau di atas untuk menyusun pertanyaan evaluasi pertama Anda.</p>
                    <button type="button" @click="createModalOpen = true; questionType = 'pilihan_ganda'" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-extrabold text-sm shadow-lg shadow-emerald-600/30 inline-flex items-center gap-2">
                        <span>+ Tambah Soal Pertama</span>
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Modal Buat Soal Baru -->
        <div x-show="createModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
             @click="createModalOpen = false" style="display: none;" x-cloak>
            
            <div @click.stop class="bg-[#0c2214] border border-emerald-500/40 rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-emerald-900/50 pb-4">
                    <div>
                        <span class="text-xs font-bold uppercase text-emerald-400 tracking-wider">Penyusunan Evaluasi</span>
                        <h3 class="text-xl font-extrabold text-white">Tambah Soal & Kunci Jawaban Baru</h3>
                    </div>
                    <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form action="{{ route('manage.quizzes.questions.store', $quiz) }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Tipe Soal <span class="text-rose-400">*</span></label>
                            <select name="question_type" x-model="questionType" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-semibold">
                                <option value="pilihan_ganda">Pilihan Ganda (Rekomendasi)</option>
                                <option value="esai">Esai Mandiri</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Bobot Nilai Poin <span class="text-rose-400">*</span></label>
                            <input type="number" name="score_weight" value="10" required min="1" max="100" 
                                   class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-bold text-center">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Pertanyaan Soal Evaluasi <span class="text-rose-400">*</span></label>
                        <textarea name="question_text" rows="3" required placeholder="Tuliskan pertanyaan evaluasi untuk mahasiswa dengan jelas..." 
                                  class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4"></textarea>
                    </div>

                    <!-- Pilihan Ganda Options -->
                    <div x-show="questionType === 'pilihan_ganda'" class="space-y-3.5 bg-[#07130c] p-5 rounded-2xl border border-emerald-900/60">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold uppercase tracking-wider text-emerald-400">Pilihan Jawaban & Kunci Benar <span class="text-rose-400">*</span></label>
                            <span class="text-[11px] text-amber-400 font-bold">💡 Pilih radio kanan/kiri untuk kunci jawaban</span>
                        </div>

                        @for($i = 0; $i < 4; $i++)
                            <div class="flex items-center gap-3">
                                <input type="radio" name="correct_option_index" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }} title="Tetapkan sebagai Kunci Jawaban Benar" 
                                       class="text-emerald-500 focus:ring-emerald-500 bg-emerald-950 border-emerald-700 w-5 h-5 cursor-pointer shrink-0">
                                <span class="w-7 h-7 rounded-lg bg-emerald-900/60 text-emerald-300 font-extrabold text-xs flex items-center justify-center shrink-0">
                                    {{ chr(65 + $i) }}
                                </span>
                                <input type="text" name="options[{{ $i }}][text]" placeholder="Tulis opsi pilihan {{ chr(65 + $i) }}..." 
                                       class="w-full rounded-xl bg-[#0c2214] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2 px-3.5">
                            </div>
                        @endfor
                    </div>

                    <div class="pt-4 border-t border-emerald-900/50 flex items-center justify-end gap-3">
                        <button type="button" @click="createModalOpen = false" class="px-5 py-2.5 rounded-xl bg-[#07130c] hover:bg-emerald-900/40 text-slate-300 font-semibold text-sm transition-all">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-sm shadow-lg shadow-emerald-600/30 transition-all">
                            Simpan ke Bank Soal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-manage-layout>
