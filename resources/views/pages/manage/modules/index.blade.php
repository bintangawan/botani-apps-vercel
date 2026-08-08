<x-manage-layout title="Manajemen Modul Pembelajaran — Botani Phanerogamae">
    <div class="space-y-8">
        <div class="flex flex-col items-center justify-between gap-6 rounded-3xl border border-emerald-300 bg-emerald-100 p-6 shadow-xl sm:flex-row sm:p-8">
            <div class="space-y-1.5 text-center sm:text-left">
                <span class="inline-flex rounded-full border border-teal-300 bg-teal-100 px-3 py-1 text-xs font-bold text-teal-700">Materi & Kurikulum</span>
                <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">Manajemen Modul Pembelajaran</h1>
                <p class="text-sm text-slate-700">Kelola bab pembelajaran dan seluruh subbabnya dari satu tempat.</p>
            </div>
            <div class="flex shrink-0 flex-wrap items-center gap-3">
                <a href="{{ route('manage.quizzes.index') }}" class="rounded-2xl border border-amber-300 bg-white px-5 py-3 text-sm font-extrabold text-amber-700 shadow-lg transition hover:bg-amber-50">Kelola Kuis</a>
                <a href="{{ route('manage.modules.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg transition hover:bg-emerald-700">
                    <span class="text-lg leading-none">+</span> Tambah Bab Baru
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-emerald-200 bg-white p-4 shadow-lg sm:p-6">
            <form action="{{ route('manage.modules.index') }}" method="GET" class="flex flex-col items-center gap-4 sm:flex-row">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul bab, deskripsi, atau judul subbab..." class="w-full flex-1 rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-emerald-500">
                <div class="flex w-full gap-2 sm:w-auto">
                    <button type="submit" class="flex-1 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-700">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('manage.modules.index') }}" class="rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-emerald-50">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="space-y-6 rounded-3xl border border-emerald-200 bg-white p-6 shadow-xl sm:p-8">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="border-b border-emerald-200 text-xs uppercase tracking-wider text-emerald-700">
                            <th class="w-20 px-4 py-3.5 text-center font-semibold">Urutan</th>
                            <th class="px-4 py-3.5 font-semibold">Judul & Deskripsi Bab</th>
                            <th class="px-4 py-3.5 text-center font-semibold">Subbab</th>
                            <th class="px-4 py-3.5 text-center font-semibold">Spesimen</th>
                            <th class="px-4 py-3.5 text-center font-semibold">Kuis</th>
                            <th class="px-4 py-3.5 text-center font-semibold">Status</th>
                            <th class="px-4 py-3.5 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-100 text-slate-700">
                        @forelse($modules as $mod)
                            <tr class="transition hover:bg-emerald-50/70">
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-emerald-300 bg-emerald-100 text-sm font-extrabold text-emerald-700">{{ $mod->module_order }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="mb-0.5 block text-base font-extrabold text-slate-900">{{ $mod->title }}</span>
                                    <p class="line-clamp-2 max-w-xl text-xs text-slate-600">{{ $mod->description ?: 'Tidak ada deskripsi singkat.' }}</p>
                                    <span class="mt-1 block text-[11px] text-slate-400">Estimasi {{ $mod->estimated_minutes }} menit</span>
                                </td>
                                <td class="px-4 py-4 text-center"><span class="whitespace-nowrap rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">{{ $mod->lessons_count }} Lesson</span></td>
                                <td class="px-4 py-4 text-center"><span class="whitespace-nowrap rounded-full border border-emerald-200 bg-white px-3 py-1 text-xs font-bold text-emerald-700">{{ $mod->plant_species_count }} Spesimen</span></td>
                                <td class="px-4 py-4 text-center"><span class="whitespace-nowrap rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">{{ $mod->quizzes_count }} Kuis</span></td>
                                <td class="px-4 py-4 text-center">
                                    <span class="rounded px-2.5 py-1 text-xs font-bold {{ $mod->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $mod->status === 'published' ? 'Dipublikasi' : 'Draf' }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-right">
                                    @if($mod->status === 'published' && $mod->lessons_count > 0)
                                        <a href="{{ route('modules.detail', $mod->slug) }}" target="_blank" title="Lihat publik" class="mr-1 inline-flex rounded-xl border border-emerald-300 bg-emerald-50 p-2 text-emerald-700 transition hover:bg-emerald-100">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('manage.modules.edit', $mod) }}" class="mr-1 inline-flex items-center gap-1 rounded-xl border border-amber-300 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">Edit</a>
                                    <form id="delete-module-{{ $mod->id }}" action="{{ route('manage.modules.destroy', $mod) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="confirmDelete('delete-module-{{ $mod->id }}', 'bab {{ addslashes($mod->title) }} beserta seluruh subbabnya')" class="rounded-xl border border-rose-300 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-12 text-center text-slate-600">Belum ada data bab pembelajaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($modules->hasPages())
                <div class="border-t border-emerald-200 pt-4">{{ $modules->links('vendor.pagination.botani') }}</div>
            @endif
        </div>
    </div>
</x-manage-layout>
