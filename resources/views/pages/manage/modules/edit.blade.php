<x-manage-layout title="Edit Modul: {{ $module->title }} — Botani Phanerogamae">
    <div class="space-y-8 max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-[#0c2214] via-emerald-950 to-[#07130c] border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-xl flex items-center justify-between gap-6">
            <div class="space-y-1.5">
                <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">✏️ Mode Edit Modul</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Edit Modul Pembelajaran</h1>
                <p class="text-sm text-slate-300">
                    Perbarui teks materi perkuliahan atau atur ulang spesimen tumbuhan herbarium terkait.
                </p>
            </div>
            <a href="{{ route('manage.modules.index') }}" class="px-5 py-2.5 rounded-2xl bg-[#07130c] border border-emerald-500/40 text-slate-300 hover:text-white hover:bg-emerald-900/60 font-semibold text-sm transition-all shrink-0">
                &larr; Kembali ke Daftar
            </a>
        </div>

        <!-- Edit Form -->
        <form action="{{ route('manage.modules.update', $module) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="sm:col-span-2">
                        <label for="title" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Judul Modul Pembelajaran <span class="text-rose-400">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $module->title) }}" required 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-bold">
                    </div>
                    <div>
                        <label for="module_order" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Urutan Modul <span class="text-rose-400">*</span></label>
                        <input type="number" name="module_order" id="module_order" value="{{ old('module_order', $module->module_order) }}" required min="1" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-bold text-center">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Deskripsi Singkat / Pengantar</label>
                    <textarea name="description" id="description" rows="2" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">{{ old('description', $module->description) }}</textarea>
                </div>

                <div>
                    <label for="content" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Isi Materi Kuliah Lengkap (WYSIWYG Editor) <span class="text-rose-400">*</span></label>
                    <textarea name="content" id="content" class="hidden">{{ old('content', $module->content) }}</textarea>
                    
                    <!-- Quill Editor Container -->
                    <div id="quill-editor" class="text-white">{!! old('content', $module->content) !!}</div>
                    <p class="text-[11px] text-slate-400 mt-1.5">💡 Gunakan toolbar di atas untuk memformat paragraf, daftar bullet, heading, atau kutipan ilmiah.</p>

                    <!-- Quill CDN & Custom Dark Emerald Styling -->
                    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
                    <style>
                        .ql-toolbar.ql-snow {
                            background-color: #0c2214 !important;
                            border-color: rgba(6, 78, 59, 0.6) !important;
                            border-top-left-radius: 0.75rem;
                            border-top-right-radius: 0.75rem;
                        }
                        .ql-container.ql-snow {
                            background-color: #07130c !important;
                            border-color: rgba(6, 78, 59, 0.6) !important;
                            border-bottom-left-radius: 0.75rem;
                            border-bottom-right-radius: 0.75rem;
                            color: #f1f5f9 !important;
                            font-family: inherit !important;
                            min-height: 320px;
                        }
                        .ql-snow .ql-stroke { stroke: #94a3b8 !important; }
                        .ql-snow .ql-fill { fill: #94a3b8 !important; }
                        .ql-snow .ql-picker { color: #94a3b8 !important; }
                        .ql-snow .ql-picker-options { background-color: #0c2214 !important; border-color: rgba(6, 78, 59, 0.6) !important; color: #fff !important; }
                        .ql-editor { font-size: 0.95rem !important; line-height: 1.7 !important; min-height: 320px; }
                        .ql-editor.ql-blank::before { color: #64748b !important; font-style: italic !important; }
                    </style>
                    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const quill = new Quill('#quill-editor', {
                                theme: 'snow',
                                placeholder: 'Tulis atau tempelkan materi teori lengkap mengenai klasifikasi, karakteristik, organ regeneratif...',
                                modules: {
                                    toolbar: [
                                        [{ 'header': [1, 2, 3, false] }],
                                        ['bold', 'italic', 'underline', 'strike'],
                                        ['blockquote', 'code-block'],
                                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                        ['link', 'clean']
                                    ]
                                }
                            });
                            const textarea = document.getElementById('content');
                            quill.on('text-change', function() {
                                textarea.value = quill.root.innerHTML;
                            });
                            const form = textarea.closest('form');
                            if (form) {
                                form.addEventListener('submit', function(e) {
                                    textarea.value = quill.root.innerHTML;
                                    if (quill.getText().trim().length === 0 && quill.root.innerHTML.indexOf('<img') === -1) {
                                        e.preventDefault();
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Materi Belum Diisi',
                                            text: 'Isi Materi Kuliah Lengkap tidak boleh kosong!',
                                            background: '#0c2214',
                                            color: '#fff',
                                            confirmButtonColor: '#10b981'
                                        });
                                    }
                                });
                            }
                        });
                    </script>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-2">🌿 Hubungkan Spesimen Tumbuhan Terkait (Opsional)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 max-h-64 overflow-y-auto p-4 rounded-2xl bg-[#07130c] border border-emerald-900/60 gap-3">
                        @forelse($plants as $pl)
                            <label class="flex items-center gap-3 p-2 rounded-xl hover:bg-emerald-950/50 cursor-pointer transition-colors border border-transparent hover:border-emerald-800/40">
                                <input type="checkbox" name="species[]" value="{{ $pl->id }}" {{ in_array($pl->id, old('species', $selectedSpecies ?? [])) ? 'checked' : '' }} 
                                       class="rounded bg-emerald-950 border-emerald-700 text-emerald-500 focus:ring-emerald-500 w-4 h-4">
                                <span class="text-xs text-white font-medium">{{ $pl->local_name }} <span class="text-slate-400 italic">({{ $pl->scientific_name }})</span></span>
                            </label>
                        @empty
                            <p class="text-xs text-slate-500 italic col-span-2 text-center py-4">Belum ada spesimen tumbuhan terpublikasi untuk dihubungkan.</p>
                        @endforelse
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2 border-t border-emerald-900/50">
                    <div>
                        <label for="status" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1.5">Status Akses Modul <span class="text-rose-400">*</span></label>
                        <select name="status" id="status" required class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-bold">
                            <option value="published" {{ old('status', $module->status) === 'published' ? 'selected' : '' }}>🟢 Dipublikasi (Dapat dipelajari Mahasiswa)</option>
                            <option value="draft" {{ old('status', $module->status) === 'draft' ? 'selected' : '' }}>🟡 Draf (Menunggu Penyempurnaan)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('manage.modules.index') }}" class="px-6 py-3 rounded-2xl bg-[#0c2214] border border-emerald-900/60 hover:bg-emerald-900/40 text-slate-300 font-bold text-sm transition-all">
                    Batal
                </a>
                <button type="submit" class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-sm shadow-xl shadow-emerald-600/30 transition-all">
                    Simpan Perubahan Modul
                </button>
            </div>
        </form>
    </div>
</x-manage-layout>
