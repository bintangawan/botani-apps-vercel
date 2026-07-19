<x-manage-layout title="Tambah Spesimen Tumbuhan Baru — Botani Phanerogamae">
    <div class="space-y-8 max-w-5xl mx-auto">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-[#0c2214] via-emerald-950 to-[#07130c] border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-xl flex items-center justify-between gap-6">
            <div class="space-y-1.5">
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">🌱 Formulir Katalog</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Tambah Spesimen Tumbuhan</h1>
                <p class="text-sm text-slate-300">
                    Masukkan data identifikasi ilmiah, klasifikasi taksonomi, karakteristik morfologi, serta foto herbarium.
                </p>
            </div>
            <a href="{{ route('manage.plants.index') }}" class="px-5 py-2.5 rounded-2xl bg-[#07130c] border border-emerald-500/40 text-slate-300 hover:text-white hover:bg-emerald-900/60 font-semibold text-sm transition-all shrink-0">
                &larr; Kembali ke Daftar
            </a>
        </div>

        <!-- Create Form -->
        <form action="{{ route('manage.plants.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <!-- Section 1: Identitas & Klasifikasi Dasar -->
            <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
                <h2 class="text-lg font-bold text-white border-b border-emerald-900/40 pb-3 flex items-center gap-2">
                    <span>📑 1. Identifikasi & Klasifikasi Utama</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="code" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Kode Spesimen <span class="text-rose-400">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" required placeholder="Contoh: BP-GYM-001" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 uppercase font-mono">
                    </div>
                    <div>
                        <label for="group_type" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Kelompok Tumbuhan <span class="text-rose-400">*</span></label>
                        <select name="group_type" id="group_type" required class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-semibold">
                            <option value="Gymnospermae" {{ old('group_type') === 'Gymnospermae' ? 'selected' : '' }}>Gymnospermae (Biji Terbuka)</option>
                            <option value="Angiospermae" {{ old('group_type') === 'Angiospermae' ? 'selected' : '' }}>Angiospermae (Biji Tertutup)</option>
                        </select>
                    </div>
                    <div>
                        <label for="cotyledon_type" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Tipe Kotiledon</label>
                        <select name="cotyledon_type" id="cotyledon_type" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">
                            <option value="">-- Tidak Spesifik --</option>
                            <option value="Monokotil" {{ old('cotyledon_type') === 'Monokotil' ? 'selected' : '' }}>Monokotil (Berkeping Satu)</option>
                            <option value="Dikotil" {{ old('cotyledon_type') === 'Dikotil' ? 'selected' : '' }}>Dikotil (Berkeping Dua)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="local_name" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Nama Lokal / Umum <span class="text-rose-400">*</span></label>
                        <input type="text" name="local_name" id="local_name" value="{{ old('local_name') }}" required placeholder="Contoh: Pinus Sumatera" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label for="scientific_name" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Nama Ilmiah (Taksonomi) <span class="text-rose-400">*</span></label>
                        <input type="text" name="scientific_name" id="scientific_name" value="{{ old('scientific_name') }}" required placeholder="Contoh: Pinus merkusii" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-serif italic">
                    </div>
                    <div>
                        <label for="author_name" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Otoritas Penemu (Author)</label>
                        <input type="text" name="author_name" id="author_name" value="{{ old('author_name') }}" placeholder="Contoh: Jungh. & de Vriese" 
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
                        <input type="text" name="kingdom" id="kingdom" value="{{ old('kingdom', 'Plantae') }}" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 text-slate-300 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label for="divisi" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Divisi / Filum</label>
                        <input type="text" name="divisi" id="divisi" value="{{ old('divisi') }}" placeholder="Contoh: Tracheophyta / Coniferophyta" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 text-slate-300 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label for="kelas" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Kelas</label>
                        <input type="text" name="kelas" id="kelas" value="{{ old('kelas') }}" placeholder="Contoh: Pinopsida" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 text-slate-300 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label for="ordo" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Ordo / Bangsa</label>
                        <input type="text" name="ordo" id="ordo" value="{{ old('ordo') }}" placeholder="Contoh: Pinales" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 text-slate-300 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label for="famili" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Famili / Suku</label>
                        <input type="text" name="famili" id="famili" value="{{ old('famili') }}" placeholder="Contoh: Pinaceae" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 text-slate-300 text-sm py-2.5 px-4 font-semibold text-emerald-300">
                    </div>
                    <div>
                        <label for="genus" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Genus / Marga</label>
                        <input type="text" name="genus" id="genus" value="{{ old('genus') }}" placeholder="Contoh: Pinus" 
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
                        <textarea name="root" id="root" rows="3" placeholder="Sistem perakaran tunggang/serabut, warna, struktur..." 
                                  class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('root') }}</textarea>
                    </div>
                    <div>
                        <label for="stem" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Morfologi Batang</label>
                        <textarea name="stem" id="stem" rows="3" placeholder="Berkayu, percabangan monopodial/simpodial, permukaan kulit..." 
                                  class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('stem') }}</textarea>
                    </div>
                    <div>
                        <label for="leaf" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Morfologi Daun</label>
                        <textarea name="leaf" id="leaf" rows="3" placeholder="Bentuk daun jarum/menyirip/menjari, tata letak, pertulangan..." 
                                  class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('leaf') }}</textarea>
                    </div>
                    <div>
                        <label for="flower" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Morfologi Bunga / Strobilus</label>
                        <textarea name="flower" id="flower" rows="3" placeholder="Bunga tunggal/majemuk, runjung jantan/betina (strobilus)..." 
                                  class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('flower') }}</textarea>
                    </div>
                    <div>
                        <label for="fruit" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Morfologi Buah</label>
                        <textarea name="fruit" id="fruit" rows="3" placeholder="Buah kotak/sejati/semu, bentuk, warna, ukuran..." 
                                  class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('fruit') }}</textarea>
                    </div>
                    <div>
                        <label for="seed" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Morfologi Biji</label>
                        <textarea name="seed" id="seed" rows="3" placeholder="Terbuka/tertutup, bersayap, endosperm, testa..." 
                                  class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('seed') }}</textarea>
                    </div>
                </div>

                <div>
                    <label for="special_characteristics" class="block text-xs font-bold uppercase tracking-wider text-amber-400 mb-1.5">💡 Ciri Khusus / Karakteristik Spesifik</label>
                    <textarea name="special_characteristics" id="special_characteristics" rows="3" placeholder="Mengeluarkan resin/getah aromatik, adaptasi kebakaran hutan, adaptasi habitat kering..." 
                              class="w-full rounded-xl bg-[#07130c] border border-amber-500/40 focus:border-amber-500 text-white text-sm py-2.5 px-4">{{ old('special_characteristics') }}</textarea>
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
                        <textarea name="description" id="description" rows="4" placeholder="Uraian umum mengenai kebiasaan tumbuh, sebaran alami di Sumatera, ekologi..." 
                                  class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label for="habitat" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Habitat Alami & Ekologi</label>
                        <textarea name="habitat" id="habitat" rows="3" placeholder="Ketinggian tempat (dpl), jenis tanah, kelembaban, curah hujan..." 
                                  class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('habitat') }}</textarea>
                    </div>
                    <div>
                        <label for="benefits" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Manfaat & Potensi Guna</label>
                        <textarea name="benefits" id="benefits" rows="3" placeholder="Penghasil getah gondorukem, kayu bangunan, konservasi lereng, obat tradisional..." 
                                  class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('benefits') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 5: Foto Dokumentasi & Status Publikasi -->
            <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
                <h2 class="text-lg font-bold text-white border-b border-emerald-900/40 pb-3 flex items-center gap-2">
                    <span>🖼️ 5. Foto Dokumentasi & Status Publikasi</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                    <div>
                        <label for="image" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Unggah Gambar Spesimen Utama (JPG/PNG)</label>
                        <input type="file" name="image" id="image" accept="image/*" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-slate-300 text-sm py-2 px-3 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500">
                        <p class="text-[11px] text-slate-400 mt-1.5">Ukuran maksimal gambar: 4 MB. Rekomendasi resolusi 1200x800 px.</p>
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Status Katalog</label>
                        <select name="status" id="status" required class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-bold">
                            <option value="published" selected>🟢 Dipublikasi (Tampil di Galeri)</option>
                            <option value="draft">🟡 Draf (Menunggu Validasi/Review)</option>
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
                    <span>Simpan Spesimen ke Katalog</span>
                </button>
            </div>
        </form>
    </div>
</x-manage-layout>
