<x-manage-layout title="Tambah Bab Pembelajaran — Botani Phanerogamae">
    @php
        $formLessons = old('lessons', [[
            'title' => '',
            'slug' => '',
            'content' => '',
            'key_points' => '',
            'source_sections' => '',
            'lesson_order' => 1,
        ]]);
        $selectedSpecies = old('species', []);
    @endphp

    <div class="mx-auto max-w-6xl space-y-8">
        <div class="flex flex-col gap-5 rounded-3xl border border-emerald-300 bg-emerald-100 p-6 shadow-xl sm:flex-row sm:items-center sm:justify-between sm:p-8">
            <div>
                <span class="inline-flex rounded-full border border-teal-300 bg-teal-100 px-3 py-1 text-xs font-bold text-teal-700">Formulir Bab & Subbab</span>
                <h1 class="mt-3 text-2xl font-extrabold text-slate-900 sm:text-3xl">Tambah Bab Pembelajaran</h1>
                <p class="mt-1 text-sm text-slate-700">Satu bab dapat memuat banyak subbab dengan isi materi yang dikelola terpisah.</p>
            </div>
            <a href="{{ route('manage.modules.index') }}" class="shrink-0 rounded-2xl border border-emerald-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-emerald-50">← Kembali ke Daftar</a>
        </div>

        <form action="{{ route('manage.modules.store') }}" method="POST" class="space-y-8" data-module-form>
            @csrf
            @include('pages.manage.modules._form', [
                'formLessons' => $formLessons,
                'selectedSpecies' => $selectedSpecies,
                'module' => null,
                'moduleOrder' => old('module_order', $nextOrder),
                'summaryValue' => old('chapter_summary', ''),
                'submitLabel' => 'Simpan Bab & Subbab',
            ])
        </form>
    </div>
</x-manage-layout>
