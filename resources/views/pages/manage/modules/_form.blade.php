<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<section class="space-y-6 rounded-3xl border border-emerald-200 bg-white p-6 shadow-xl sm:p-8">
    <div class="flex items-center gap-3 border-b border-emerald-100 pb-4">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 font-extrabold text-emerald-700">1</span>
        <div>
            <h2 class="font-extrabold text-slate-900">Informasi Bab</h2>
            <p class="text-xs text-slate-500">Data utama yang tampil pada daftar dan navigasi pembelajaran.</p>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-6">
        <div class="sm:col-span-4">
            <label for="title" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-emerald-700">Judul Bab <span class="text-rose-600">*</span></label>
            <input id="title" name="title" type="text" required value="{{ old('title', $module?->title) }}" placeholder="Contoh: Struktur dan Fungsi Biji" class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-900 focus:border-emerald-500">
        </div>
        <div>
            <label for="module_order" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-emerald-700">Urutan Bab <span class="text-rose-600">*</span></label>
            <input id="module_order" name="module_order" type="number" required min="1" value="{{ $moduleOrder }}" class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-center text-sm font-bold text-slate-900 focus:border-emerald-500">
        </div>
        <div>
            <label for="estimated_minutes" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-emerald-700">Estimasi (menit) <span class="text-rose-600">*</span></label>
            <input id="estimated_minutes" name="estimated_minutes" type="number" required min="0" value="{{ old('estimated_minutes', $module?->estimated_minutes ?? 30) }}" class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-center text-sm font-bold text-slate-900 focus:border-emerald-500">
        </div>
    </div>

    <div>
        <label for="description" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-emerald-700">Deskripsi Bab</label>
        <textarea id="description" name="description" rows="3" placeholder="Gambaran materi yang dipelajari pada bab ini..." class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-emerald-500">{{ old('description', $module?->description) }}</textarea>
    </div>

    <div>
        <label for="chapter_summary" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-emerald-700">Ringkasan Bab</label>
        <textarea id="chapter_summary" name="chapter_summary" rows="4" placeholder="Tulis satu poin ringkasan per baris" class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-emerald-500">{{ $summaryValue }}</textarea>
        <p class="mt-1.5 text-[11px] text-slate-500">Setiap baris akan ditampilkan sebagai satu poin ringkasan setelah lesson terakhir.</p>
    </div>
</section>

<section class="space-y-6 rounded-3xl border border-emerald-200 bg-white p-6 shadow-xl sm:p-8">
    <div class="flex flex-col gap-4 border-b border-emerald-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 font-extrabold text-emerald-700">2</span>
            <div>
                <h2 class="font-extrabold text-slate-900">Subbab / Lesson</h2>
                <p class="text-xs text-slate-500">Tambahkan minimal satu lesson. Setiap lesson memiliki editor Quill sendiri.</p>
            </div>
        </div>
        <button type="button" data-add-lesson class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-emerald-700">
            <span class="text-lg leading-none">+</span> Tambah Subbab
        </button>
    </div>

    <div data-lessons-container class="space-y-6">
        @foreach($formLessons as $index => $formLesson)
            <article data-lesson-card class="overflow-hidden rounded-2xl border border-emerald-200 bg-emerald-50/40">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-emerald-200 bg-emerald-50 px-4 py-3 sm:px-5">
                    <div class="flex items-center gap-3">
                        <span data-lesson-number class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-xs font-extrabold text-white">{{ $loop->iteration }}</span>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900">Subbab <span data-lesson-label>{{ $loop->iteration }}</span></h3>
                            <p class="text-[11px] text-slate-500">ID database dipertahankan saat subbab diedit.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" data-move-up title="Geser ke atas" class="rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100">↑</button>
                        <button type="button" data-move-down title="Geser ke bawah" class="rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100">↓</button>
                        <button type="button" data-remove-lesson class="rounded-lg border border-rose-200 bg-white px-3 py-1.5 text-xs font-bold text-rose-600 hover:bg-rose-50">Hapus</button>
                    </div>
                </div>

                <div class="space-y-5 p-4 sm:p-5">
                    @if(!empty($formLesson['id']))
                        <input type="hidden" name="lessons[{{ $index }}][id]" value="{{ $formLesson['id'] }}">
                    @endif
                    <input data-lesson-order type="hidden" name="lessons[{{ $index }}][lesson_order]" value="{{ $loop->iteration }}">

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-700">Judul Subbab <span class="text-rose-600">*</span></label>
                            <input type="text" name="lessons[{{ $index }}][title]" required value="{{ $formLesson['title'] ?? '' }}" placeholder="Contoh: Struktur Utama Biji" class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-700">Slug URL <span class="font-normal text-slate-400">(opsional)</span></label>
                            <input type="text" name="lessons[{{ $index }}][slug]" value="{{ $formLesson['slug'] ?? '' }}" placeholder="Otomatis dari judul jika kosong" class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700">Isi Materi Subbab <span class="text-rose-600">*</span></label>
                        <textarea data-lesson-content name="lessons[{{ $index }}][content]" class="hidden">{{ $formLesson['content'] ?? '' }}</textarea>
                        <div data-lesson-editor class="lesson-quill-editor bg-white text-slate-900">{!! $formLesson['content'] ?? '' !!}</div>
                        <p class="mt-1.5 text-[11px] text-slate-500">Gunakan toolbar untuk heading, daftar, kutipan, kode, dan tautan.</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-700">Poin Penting</label>
                            <textarea name="lessons[{{ $index }}][key_points]" rows="4" placeholder="Satu poin per baris" class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-500">{{ $formLesson['key_points'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-700">Bagian Sumber</label>
                            <textarea name="lessons[{{ $index }}][source_sections]" rows="4" placeholder="Contoh: 1.1, 1.2" class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-500">{{ $formLesson['source_sections'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <template data-lesson-template>
        <article data-lesson-card class="overflow-hidden rounded-2xl border border-emerald-200 bg-emerald-50/40">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-emerald-200 bg-emerald-50 px-4 py-3 sm:px-5">
                <div class="flex items-center gap-3">
                    <span data-lesson-number class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-xs font-extrabold text-white">__NUMBER__</span>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Subbab <span data-lesson-label>__NUMBER__</span></h3>
                        <p class="text-[11px] text-slate-500">Subbab baru akan mendapat ID database setelah disimpan.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" data-move-up class="rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100">↑</button>
                    <button type="button" data-move-down class="rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100">↓</button>
                    <button type="button" data-remove-lesson class="rounded-lg border border-rose-200 bg-white px-3 py-1.5 text-xs font-bold text-rose-600 hover:bg-rose-50">Hapus</button>
                </div>
            </div>
            <div class="space-y-5 p-4 sm:p-5">
                <input data-lesson-order type="hidden" name="lessons[__INDEX__][lesson_order]" value="__NUMBER__">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700">Judul Subbab <span class="text-rose-600">*</span></label>
                        <input type="text" name="lessons[__INDEX__][title]" required placeholder="Contoh: Struktur Utama Biji" class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700">Slug URL <span class="font-normal text-slate-400">(opsional)</span></label>
                        <input type="text" name="lessons[__INDEX__][slug]" placeholder="Otomatis dari judul jika kosong" class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:border-emerald-500">
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold text-slate-700">Isi Materi Subbab <span class="text-rose-600">*</span></label>
                    <textarea data-lesson-content name="lessons[__INDEX__][content]" class="hidden"></textarea>
                    <div data-lesson-editor class="lesson-quill-editor bg-white text-slate-900"></div>
                    <p class="mt-1.5 text-[11px] text-slate-500">Gunakan toolbar untuk heading, daftar, kutipan, kode, dan tautan.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700">Poin Penting</label>
                        <textarea name="lessons[__INDEX__][key_points]" rows="4" placeholder="Satu poin per baris" class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-500"></textarea>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700">Bagian Sumber</label>
                        <textarea name="lessons[__INDEX__][source_sections]" rows="4" placeholder="Contoh: 1.1, 1.2" class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-500"></textarea>
                    </div>
                </div>
            </div>
        </article>
    </template>
</section>

<section class="space-y-6 rounded-3xl border border-emerald-200 bg-white p-6 shadow-xl sm:p-8">
    <div class="flex items-center gap-3 border-b border-emerald-100 pb-4">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 font-extrabold text-emerald-700">3</span>
        <div>
            <h2 class="font-extrabold text-slate-900">Publikasi & Keterkaitan</h2>
            <p class="text-xs text-slate-500">Pengaturan ini tetap berlaku di level bab.</p>
        </div>
    </div>

    <div>
        <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-emerald-700">Spesimen Tumbuhan Terkait <span class="font-normal normal-case text-slate-400">(opsional)</span></label>
        <div class="grid max-h-64 grid-cols-1 gap-3 overflow-y-auto rounded-2xl border border-emerald-200 bg-emerald-50/30 p-4 sm:grid-cols-2">
            @forelse($plants as $plant)
                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-transparent p-2 transition hover:border-emerald-200 hover:bg-white">
                    <input type="checkbox" name="species[]" value="{{ $plant->id }}" @checked(in_array($plant->id, $selectedSpecies)) class="h-4 w-4 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-xs font-medium text-slate-800">{{ $plant->local_name }} <span class="italic text-slate-500">({{ $plant->scientific_name }})</span></span>
                </label>
            @empty
                <p class="py-4 text-center text-xs italic text-slate-500 sm:col-span-2">Belum ada spesimen terpublikasi untuk dihubungkan.</p>
            @endforelse
        </div>
    </div>

    <div class="max-w-md">
        <label for="status" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-emerald-700">Status Akses <span class="text-rose-600">*</span></label>
        <select id="status" name="status" required class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-900 focus:border-emerald-500">
            <option value="published" @selected(old('status', $module?->status ?? 'published') === 'published')>Dipublikasi — dapat dipelajari mahasiswa</option>
            <option value="draft" @selected(old('status', $module?->status ?? 'published') === 'draft')>Draf — belum tampil di halaman publik</option>
        </select>
    </div>
</section>

<div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('manage.modules.index') }}" class="rounded-2xl border border-emerald-200 bg-white px-6 py-3 text-center text-sm font-bold text-slate-700 transition hover:bg-emerald-50">Batal</a>
    <button type="submit" class="rounded-2xl bg-emerald-600 px-8 py-3.5 text-sm font-extrabold text-white shadow-xl transition hover:bg-emerald-700">{{ $submitLabel }}</button>
</div>

<style>
    .ql-toolbar.ql-snow { border-color: #a7f3d0; border-radius: .75rem .75rem 0 0; background: #ecfdf5; }
    .ql-container.ql-snow { min-height: 260px; border-color: #a7f3d0; border-radius: 0 0 .75rem .75rem; font-family: inherit; }
    .ql-editor { min-height: 260px; font-size: .95rem; line-height: 1.75; }
    .ql-snow .ql-stroke { stroke: #047857; }
    .ql-snow .ql-fill { fill: #047857; }
    .ql-snow .ql-picker { color: #047857; }
    [data-move-up]:disabled, [data-move-down]:disabled { cursor: not-allowed; opacity: .35; }
</style>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('[data-module-form]');
        const container = form.querySelector('[data-lessons-container]');
        const template = form.querySelector('[data-lesson-template]');
        const instances = new Map();
        let nextIndex = Date.now();

        const toolbar = [
            [{ header: [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['link', 'clean'],
        ];

        const initialiseEditor = (card) => {
            const editor = card.querySelector('[data-lesson-editor]');
            const textarea = card.querySelector('[data-lesson-content]');
            if (!editor || instances.has(editor)) return;

            const quill = new Quill(editor, {
                theme: 'snow',
                placeholder: 'Tulis materi untuk subbab ini...',
                modules: { toolbar },
            });
            quill.on('text-change', () => textarea.value = quill.root.innerHTML);
            instances.set(editor, { quill, textarea });
        };

        const refreshOrder = () => {
            const cards = container.querySelectorAll('[data-lesson-card]');
            cards.forEach((card, index) => {
                const number = index + 1;
                card.querySelector('[data-lesson-number]').textContent = number;
                card.querySelector('[data-lesson-label]').textContent = number;
                card.querySelector('[data-lesson-order]').value = number;
                card.querySelector('[data-move-up]').disabled = index === 0;
                card.querySelector('[data-move-down]').disabled = index === cards.length - 1;
            });
        };

        container.querySelectorAll('[data-lesson-card]').forEach(initialiseEditor);
        refreshOrder();

        form.querySelector('[data-add-lesson]').addEventListener('click', () => {
            const number = container.querySelectorAll('[data-lesson-card]').length + 1;
            const wrapper = document.createElement('div');
            wrapper.innerHTML = template.innerHTML
                .replaceAll('__INDEX__', String(nextIndex++))
                .replaceAll('__NUMBER__', String(number));
            const card = wrapper.firstElementChild;
            container.appendChild(card);
            initialiseEditor(card);
            refreshOrder();
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });

        container.addEventListener('click', (event) => {
            const card = event.target.closest('[data-lesson-card]');
            if (!card) return;

            if (event.target.closest('[data-remove-lesson]')) {
                if (container.querySelectorAll('[data-lesson-card]').length === 1) {
                    Swal.fire({ icon: 'warning', title: 'Minimal Satu Subbab', text: 'Sebuah bab harus memiliki minimal satu subbab.', confirmButtonColor: '#059669' });
                    return;
                }
                instances.delete(card.querySelector('[data-lesson-editor]'));
                card.remove();
                refreshOrder();
                return;
            }

            if (event.target.closest('[data-move-up]') && card.previousElementSibling) {
                container.insertBefore(card, card.previousElementSibling);
                refreshOrder();
            } else if (event.target.closest('[data-move-down]') && card.nextElementSibling) {
                container.insertBefore(card.nextElementSibling, card);
                refreshOrder();
            }
        });

        form.addEventListener('submit', (event) => {
            refreshOrder();
            let emptyLesson = null;

            instances.forEach(({ quill, textarea }, editor) => {
                textarea.value = quill.root.innerHTML;
                if (!emptyLesson && quill.getText().trim().length === 0 && !quill.root.querySelector('img')) {
                    emptyLesson = editor.closest('[data-lesson-card]');
                }
            });

            if (emptyLesson) {
                event.preventDefault();
                emptyLesson.scrollIntoView({ behavior: 'smooth', block: 'center' });
                Swal.fire({ icon: 'warning', title: 'Materi Belum Diisi', text: 'Isi materi pada setiap subbab wajib diisi.', confirmButtonColor: '#059669' });
            }
        });
    });
</script>
