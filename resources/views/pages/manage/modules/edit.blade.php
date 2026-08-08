<x-manage-layout title="Edit Bab: {{ $module->title }} — Botani Phanerogamae">
    @php
        $databaseLessons = $module->lessons->map(fn ($lesson) => [
            'id' => $lesson->id,
            'title' => $lesson->title,
            'slug' => $lesson->slug,
            'content' => $lesson->content,
            'key_points' => implode("\n", $lesson->key_points ?? []),
            'source_sections' => implode(', ', $lesson->source_sections ?? []),
            'lesson_order' => $lesson->lesson_order,
        ])->all();
        $formLessons = old('lessons', $databaseLessons);
        $selectedSpecies = old('species', $selectedSpecies);
    @endphp

    <div class="mx-auto max-w-6xl space-y-8">
        <div class="flex flex-col gap-5 rounded-3xl border border-amber-300 bg-amber-50 p-6 shadow-xl sm:flex-row sm:items-center sm:justify-between sm:p-8">
            <div>
                <span class="inline-flex rounded-full border border-amber-300 bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">Mode Edit Bab</span>
                <h1 class="mt-3 text-2xl font-extrabold text-slate-900 sm:text-3xl">Edit Bab & Subbab Pembelajaran</h1>
                <p class="mt-1 text-sm text-slate-700">Perubahan pada setiap editor disimpan sebagai subbab terpisah di database.</p>
            </div>
            <a href="{{ route('manage.modules.index') }}" class="shrink-0 rounded-2xl border border-emerald-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-emerald-50">← Kembali ke Daftar</a>
        </div>

        <form action="{{ route('manage.modules.update', $module) }}" method="POST" class="space-y-8" data-module-form>
            @csrf
            @method('PUT')
            @include('pages.manage.modules._form', [
                'formLessons' => $formLessons,
                'selectedSpecies' => $selectedSpecies,
                'module' => $module,
                'moduleOrder' => old('module_order', $module->module_order),
                'summaryValue' => old('chapter_summary', implode("\n", $module->chapter_summary ?? [])),
                'submitLabel' => 'Simpan Semua Perubahan',
            ])
        </form>
    </div>
</x-manage-layout>
