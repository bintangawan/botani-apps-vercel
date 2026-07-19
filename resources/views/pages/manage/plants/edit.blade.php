<x-manage-layout title="Edit Spesimen: {{ $plant->local_name }} — Botani Phanerogamae">
    <div class="space-y-8 max-w-5xl mx-auto">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-[#0c2214] via-emerald-950 to-[#07130c] border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-xl flex items-center justify-between gap-6">
            <div class="space-y-1.5">
                <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">✏️ Mode Edit Spesimen</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Edit: {{ $plant->local_name }}</h1>
                <p class="text-sm text-slate-300">
                    Perbarui data ilmiah, taksonomi, morfologi organ, atau foto spesimen herbarium.
                </p>
            </div>
            <a href="{{ route('manage.plants.index') }}" class="px-5 py-2.5 rounded-2xl bg-[#07130c] border border-emerald-500/40 text-slate-300 hover:text-white hover:bg-emerald-900/60 font-semibold text-sm transition-all shrink-0">
                &larr; Kembali ke Daftar
            </a>
        </div>

        <!-- Edit Form -->
        <form action="{{ route('manage.plants.update', $plant) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')
            
            <!-- Section 1: Identitas & Klasifikasi Dasar -->
            <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
                <h2 class="text-lg font-bold text-white border-b border-emerald-900/40 pb-3 flex items-center gap-2">
                    <span>📑 1. Identifikasi & Klasifikasi Utama</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="code" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Kode Spesimen <span class="text-rose-400">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code', $plant->code) }}" required placeholder="Contoh: BP-GYM-001" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 uppercase font-mono">
                    </div>
                    <div>
                        <label for="group_type" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Kelompok Tumbuhan <span class="text-rose-400">*</span></label>
                        <select name="group_type" id="group_type" required class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-semibold">
                            <option value="Gymnospermae" {{ old('group_type', $plant->group_type) === 'Gymnospermae' ? 'selected' : '' }}>Gymnospermae (Biji Terbuka)</option>
                            <option value="Angiospermae" {{ old('group_type', $plant->group_type) === 'Angiospermae' ? 'selected' : '' }}>Angiospermae (Biji Tertutup)</option>
                        </select>
                    </div>
                    <div>
                        <label for="cotyledon_type" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Tipe Kotiledon</label>
                        <select name="cotyledon_type" id="cotyledon_type" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">
                            <option value="">-- Tidak Spesifik --</option>
                            <option value="Monokotil" {{ old('cotyledon_type', $plant->cotyledon_type) === 'Monokotil' ? 'selected' : '' }}>Monokotil (Berkeping Satu)</option>
                            <option value="Dikotil" {{ old('cotyledon_type', $plant->cotyledon_type) === 'Dikotil' ? 'selected' : '' }}>Dikotil (Berkeping Dua)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="local_name" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Nama Lokal / Umum <span class="text-rose-400">*</span></label>
                        <input type="text" name="local_name" id="local_name" value="{{ old('local_name', $plant->local_name) }}" required 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label for="scientific_name" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Nama Ilmiah (Taksonomi) <span class="text-rose-400">*</span></label>
                        <input type="text" name="scientific_name" id="scientific_name" value="{{ old('scientific_name', $plant->scientific_name) }}" required 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-serif italic">
                    </div>
                    <div>
                        <label for="author_name" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Otoritas Penemu (Author)</label>
                        <input type="text" name="author_name" id="author_name" value="{{ old('author_name', $plant->author_name) }}" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">
                    </div>
                </div>
            </div>

            <!-- Section 2: Taksonomi Biologi -->
            <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
                <h2 class="text-lg font-bold text-white border-b border-emerald-900/40 pb-3 flex items-center gap-2">
                    <span>🧬 2. Hierarki Taksonomi Biologi</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <div>
                        <label for="kingdom" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Kingdom</label>
                        <input type="text" name="kingdom" id="kingdom" value="{{ old('kingdom', $taxaMap['kingdom'] ?? 'Plantae') }}" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 text-slate-300 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label for="divisi" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Divisi / Filum</label>
                        <input type="text" name="divisi" id="divisi" value="{{ old('divisi', $taxaMap['divisi'] ?? '') }}" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 text-slate-300 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label for="kelas" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Kelas</label>
                        <input type="text" name="kelas" id="kelas" value="{{ old('kelas', $taxaMap['kelas'] ?? '') }}" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 text-slate-300 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label for="ordo" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Ordo / Bangsa</label>
                        <input type="text" name="ordo" id="ordo" value="{{ old('ordo', $taxaMap['ordo'] ?? '') }}" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 text-slate-300 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label for="famili" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Famili / Suku</label>
                        <input type="text" name="famili" id="famili" value="{{ old('famili', $taxaMap['famili'] ?? '') }}" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 text-slate-300 text-sm py-2.5 px-4 font-semibold text-emerald-300">
                    </div>
                    <div>
                        <label for="genus" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Genus / Marga</label>
                        <input type="text" name="genus" id="genus" value="{{ old('genus', $taxaMap['genus'] ?? '') }}" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 text-slate-300 text-sm py-2.5 px-4 font-serif italic">
                    </div>
                </div>
            </div>

            <!-- Section 3: Morfologi Biologi Organ -->
            <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
                <h2 class="text-lg font-bold text-white border-b border-emerald-900/40 pb-3 flex items-center gap-2">
                    <span>🌿 3. Karakteristik Morfologi Organ</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="root" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Morfologi Akar</label>
                        <textarea name="root" id="root" rows="3" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('root', optional($plant->morphology)->root) }}</textarea>
                    </div>
                    <div>
                        <label for="stem" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Morfologi Batang</label>
                        <textarea name="stem" id="stem" rows="3" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('stem', optional($plant->morphology)->stem) }}</textarea>
                    </div>
                    <div>
                        <label for="leaf" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Morfologi Daun</label>
                        <textarea name="leaf" id="leaf" rows="3" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('leaf', optional($plant->morphology)->leaf) }}</textarea>
                    </div>
                    <div>
                        <label for="flower" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Morfologi Bunga / Strobilus</label>
                        <textarea name="flower" id="flower" rows="3" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('flower', optional($plant->morphology)->flower) }}</textarea>
                    </div>
                    <div>
                        <label for="fruit" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Morfologi Buah</label>
                        <textarea name="fruit" id="fruit" rows="3" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('fruit', optional($plant->morphology)->fruit) }}</textarea>
                    </div>
                    <div>
                        <label for="seed" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Morfologi Biji</label>
                        <textarea name="seed" id="seed" rows="3" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('seed', optional($plant->morphology)->seed) }}</textarea>
                    </div>
                </div>

                <div>
                    <label for="special_characteristics" class="block text-xs font-bold uppercase tracking-wider text-amber-400 mb-1.5">💡 Ciri Khusus / Karakteristik Spesifik</label>
                    <textarea name="special_characteristics" id="special_characteristics" rows="3" class="w-full rounded-xl bg-[#07130c] border border-amber-500/40 focus:border-amber-500 text-white text-sm py-2.5 px-4">{{ old('special_characteristics', optional($plant->morphology)->special_characteristics) }}</textarea>
                </div>
            </div>

            <!-- Section 4: Deskripsi Umum, Habitat & Manfaat -->
            <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
                <h2 class="text-lg font-bold text-white border-b border-emerald-900/40 pb-3 flex items-center gap-2">
                    <span>📖 4. Deskripsi Biologi, Habitat & Manfaat</span>
                </h2>

                <div class="space-y-6">
                    <div>
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Deskripsi Biologi Lengkap</label>
                        <textarea name="description" id="description" rows="4" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('description', $plant->description) }}</textarea>
                    </div>
                    <div>
                        <label for="habitat" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Habitat Alami & Ekologi</label>
                        <textarea name="habitat" id="habitat" rows="3" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('habitat', $plant->habitat) }}</textarea>
                    </div>
                    <div>
                        <label for="benefits" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Manfaat & Potensi Guna</label>
                        <textarea name="benefits" id="benefits" rows="3" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('benefits', $plant->benefits) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 5: Foto Dokumentasi & Status Publikasi -->
            <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
                <h2 class="text-lg font-bold text-white border-b border-emerald-900/40 pb-3 flex items-center gap-2">
                    <span>🖼️ 5. Foto Dokumentasi & Status Publikasi</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-start">
                    <div class="space-y-3">
                        <label for="image" class="block text-xs font-bold uppercase tracking-wider text-emerald-400">Ganti Gambar Spesimen Utama (JPG/PNG)</label>
                        @if($plant->image_url)
                            <div class="w-32 h-32 rounded-2xl border border-emerald-500/30 overflow-hidden mb-2">
                                <img src="{{ $plant->image_url }}" alt="{{ $plant->local_name }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <input type="file" name="image" id="image" accept="image/*" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-slate-300 text-sm py-2 px-3 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500">
                        <p class="text-[11px] text-slate-400">Biarkan kosong jika tidak ingin mengubah foto saat ini.</p>
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Status Katalog</label>
                        <select name="status" id="status" required class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-bold">
                            <option value="published" {{ old('status', $plant->status) === 'published' ? 'selected' : '' }}>🟢 Dipublikasi (Tampil di Galeri)</option>
                            <option value="draft" {{ old('status', $plant->status) === 'draft' ? 'selected' : '' }}>🟡 Draf (Menunggu Validasi/Review)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit Button Area -->
            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('manage.plants.index') }}" class="px-6 py-3 rounded-2xl bg-[#0c2214] border border-emerald-900/60 hover:bg-emerald-900/40 text-slate-300 font-bold text-sm transition-all">
                    Batal
                </a>
                <button type="submit" class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-sm shadow-xl shadow-emerald-600/30 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                    <span>Simpan Perubahan Spesimen</span>
                </button>
            </div>
        </form>
    </div>
</x-manage-layout>
