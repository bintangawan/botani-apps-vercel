<x-manage-layout title="Edit Kuis: {{ $quiz->title }} — Botani Phanerogamae">
    <div class="space-y-8 max-w-3xl mx-auto">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-[#0c2214] via-emerald-950 to-[#07130c] border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-xl flex items-center justify-between gap-6">
            <div class="space-y-1.5">
                <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">✏️ Mode Edit Konfigurasi</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Edit Konfigurasi Kuis</h1>
                <p class="text-sm text-slate-300">
                    Perbarui judul, modul terkait, passing score, serta durasi waktu pengerjaan evaluasi.
                </p>
            </div>
            <a href="{{ route('manage.quizzes.index') }}" class="px-5 py-2.5 rounded-2xl bg-[#07130c] border border-emerald-500/40 text-slate-300 hover:text-white hover:bg-emerald-900/60 font-semibold text-sm transition-all shrink-0">
                &larr; Kembali
            </a>
        </div>

        <!-- Edit Form -->
        <form action="{{ route('manage.quizzes.update', $quiz) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
                <div>
                    <label for="module_id" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Modul Perkuliahan Terkait <span class="text-rose-400">*</span></label>
                    <select name="module_id" id="module_id" required class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-semibold">
                        <option value="">-- Pilih Modul Perkuliahan --</option>
                        @foreach($modules as $mod)
                            <option value="{{ $mod->id }}" {{ old('module_id', $quiz->module_id) == $mod->id ? 'selected' : '' }}>
                                Modul {{ $mod->module_order }}: {{ $mod->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="title" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Judul Evaluasi / Kuis <span class="text-rose-400">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $quiz->title) }}" required 
                           class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-bold">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label for="quiz_type" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Tipe Kuis <span class="text-rose-400">*</span></label>
                        <select name="quiz_type" id="quiz_type" required class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-semibold">
                            <option value="pilihan_ganda" {{ old('quiz_type', $quiz->quiz_type) === 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda (Rekomendasi)</option>
                            <option value="esai" {{ old('quiz_type', $quiz->quiz_type) === 'esai' ? 'selected' : '' }}>Esai Mandiri</option>
                            <option value="campuran" {{ old('quiz_type', $quiz->quiz_type) === 'campuran' ? 'selected' : '' }}>Campuran</option>
                        </select>
                    </div>
                    <div>
                        <label for="passing_score" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Passing Score (0-100) <span class="text-rose-400">*</span></label>
                        <input type="number" name="passing_score" id="passing_score" value="{{ old('passing_score', $quiz->passing_score) }}" required min="0" max="100" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-bold text-center">
                    </div>
                    <div>
                        <label for="duration" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Durasi Waktu (Menit) <span class="text-rose-400">*</span></label>
                        <input type="number" name="duration" id="duration" value="{{ old('duration', $quiz->duration) }}" required min="1" max="360" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-bold text-center">
                    </div>
                </div>

                <div class="pt-2 border-t border-emerald-900/50">
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Status Publikasi Kuis <span class="text-rose-400">*</span></label>
                    <select name="status" id="status" required class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-bold">
                        <option value="published" {{ old('status', $quiz->status) === 'published' ? 'selected' : '' }}>🟢 Dipublikasi (Aktif dikerjakan Mahasiswa)</option>
                        <option value="draft" {{ old('status', $quiz->status) === 'draft' ? 'selected' : '' }}>🟡 Draf (Menunggu Penyelesaian Bank Soal)</option>
                    </select>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('manage.quizzes.index') }}" class="px-6 py-3 rounded-2xl bg-[#0c2214] border border-emerald-900/60 hover:bg-emerald-900/40 text-slate-300 font-bold text-sm transition-all">
                    Batal
                </a>
                <button type="submit" class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-sm shadow-xl shadow-emerald-600/30 transition-all">
                    Simpan Perubahan Konfigurasi
                </button>
            </div>
        </form>
    </div>
</x-manage-layout>
