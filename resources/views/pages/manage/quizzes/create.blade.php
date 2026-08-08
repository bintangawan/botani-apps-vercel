<x-manage-layout title="Buat Kuis Evaluasi Baru — Botani Phanerogamae">
    <div class="space-y-8 max-w-3xl mx-auto">
        <!-- Page Header -->
        <div class="bg-emerald-100 border border-emerald-300 rounded-3xl p-6 sm:p-8 shadow-xl flex items-center justify-between gap-6">
            <div class="space-y-1.5">
                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold border border-amber-300">🎯 Formulir Konfigurasi Kuis</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Buat Kuis Evaluasi Baru</h1>
                <p class="text-sm text-slate-700">
                    Tentukan judul kuis, durasi waktu pengerjaan, serta standar nilai ketuntasan (passing score).
                </p>
            </div>
            <a href="{{ route('manage.quizzes.index') }}" class="px-5 py-2.5 rounded-2xl bg-white border border-emerald-300 text-slate-700 hover:text-emerald-800 hover:bg-emerald-100 font-semibold text-sm transition-all shrink-0">
                &larr; Kembali
            </a>
        </div>

        <!-- Create Form -->
        <form action="{{ route('manage.quizzes.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="bg-white border border-emerald-200 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
                <div>
                    <label for="module_id" class="block text-xs font-bold uppercase tracking-wider text-emerald-700 mb-1.5">Modul Perkuliahan Terkait <span class="text-rose-700">*</span></label>
                    <select name="module_id" id="module_id" required class="w-full rounded-xl bg-white border border-emerald-200 focus:border-emerald-500 text-slate-900 text-sm py-2.5 px-4 font-semibold">
                        <option value="">-- Pilih Modul Perkuliahan --</option>
                        @foreach($modules as $mod)
                            <option value="{{ $mod->id }}" {{ old('module_id', request('module_id')) == $mod->id ? 'selected' : '' }}>
                                Modul {{ $mod->module_order }}: {{ $mod->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="title" class="block text-xs font-bold uppercase tracking-wider text-emerald-700 mb-1.5">Judul Evaluasi / Kuis <span class="text-rose-700">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Contoh: Kuis Evaluasi Modul 1: Taksonomi & Morfologi" 
                           class="w-full rounded-xl bg-white border border-emerald-200 focus:border-emerald-500 text-slate-900 text-sm py-2.5 px-4 font-bold">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label for="quiz_type" class="block text-xs font-bold uppercase tracking-wider text-emerald-700 mb-1.5">Tipe Kuis <span class="text-rose-700">*</span></label>
                        <select name="quiz_type" id="quiz_type" required class="w-full rounded-xl bg-white border border-emerald-200 focus:border-emerald-500 text-slate-900 text-sm py-2.5 px-4 font-semibold">
                            <option value="pilihan_ganda" selected>Pilihan Ganda (Rekomendasi)</option>
                            <option value="esai">Esai Mandiri</option>
                            <option value="campuran">Campuran</option>
                        </select>
                    </div>
                    <div>
                        <label for="passing_score" class="block text-xs font-bold uppercase tracking-wider text-emerald-700 mb-1.5">Passing Score (0-100) <span class="text-rose-700">*</span></label>
                        <input type="number" name="passing_score" id="passing_score" value="{{ old('passing_score', 70) }}" required min="0" max="100" 
                               class="w-full rounded-xl bg-white border border-emerald-200 focus:border-emerald-500 text-slate-900 text-sm py-2.5 px-4 font-bold text-center">
                    </div>
                    <div>
                        <label for="duration" class="block text-xs font-bold uppercase tracking-wider text-emerald-700 mb-1.5">Durasi Waktu (Menit) <span class="text-rose-700">*</span></label>
                        <input type="number" name="duration" id="duration" value="{{ old('duration', 30) }}" required min="1" max="360" 
                               class="w-full rounded-xl bg-white border border-emerald-200 focus:border-emerald-500 text-slate-900 text-sm py-2.5 px-4 font-bold text-center">
                    </div>
                </div>

                <div class="pt-2 border-t border-emerald-200">
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-emerald-700 mb-1.5">Status Publikasi Kuis <span class="text-rose-700">*</span></label>
                    <select name="status" id="status" required class="w-full rounded-xl bg-white border border-emerald-200 focus:border-emerald-500 text-slate-900 text-sm py-2.5 px-4 font-bold">
                        <option value="published" selected>🟢 Dipublikasi (Aktif dikerjakan Mahasiswa)</option>
                        <option value="draft">🟡 Draf (Menunggu Penyelesaian Bank Soal)</option>
                    </select>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('manage.quizzes.index') }}" class="px-6 py-3 rounded-2xl bg-white border border-emerald-200 hover:bg-emerald-100 text-slate-700 font-bold text-sm transition-all">
                    Batal
                </a>
                <button type="submit" class="px-8 py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-slate-900 font-extrabold text-sm shadow-xl shadow-emerald-900/10 transition-all">
                    Simpan & Lanjutkan ke Bank Soal &rarr;
                </button>
            </div>
        </form>
    </div>
</x-manage-layout>
