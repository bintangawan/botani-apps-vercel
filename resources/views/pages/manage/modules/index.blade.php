<x-manage-layout title="Manajemen Modul Pembelajaran — Botani Phanerogamae">
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-[#0c2214] via-emerald-950 to-[#07130c] border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="space-y-1.5 text-center sm:text-left">
                <span class="px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 text-xs font-bold border border-teal-500/30">📚 Materi & Kurikulum</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Manajemen Modul Pembelajaran</h1>
                <p class="text-sm text-slate-300">
                    Susun urutan materi pengantar, teori botani phanerogamae, serta hubungkan spesimen dengan modul kuliah.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('manage.quizzes.index') }}" class="px-5 py-3 rounded-2xl bg-[#07130c] border border-amber-500/40 text-amber-300 hover:bg-amber-500/20 hover:text-white font-extrabold text-sm shadow-lg flex items-center gap-2 transition-all">
                    <span>🎯 Kelola Bank Soal & Kuis</span>
                </a>
                <a href="{{ route('manage.modules.create') }}" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-sm shadow-lg shadow-emerald-600/30 flex items-center gap-2.5 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                    <span>+ Tambah Modul Baru</span>
                </a>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-2xl p-4 sm:p-6 shadow-lg">
            <form action="{{ route('manage.modules.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-center">
                <div class="flex-1 w-full relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul modul atau deskripsi materi..." 
                           class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-white placeholder-slate-500 text-sm py-2.5 px-4">
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="submit" class="py-2.5 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md transition-all">
                        Cari Modul
                    </button>
                    @if(request('search'))
                        <a href="{{ route('manage.modules.index') }}" class="py-2.5 px-4 rounded-xl bg-[#07130c] border border-emerald-900/60 hover:bg-emerald-900/40 text-slate-300 font-bold text-sm transition-all">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Modules Table -->
        <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-emerald-900/50 text-emerald-400 text-xs uppercase tracking-wider">
                            <th class="py-3.5 px-4 font-semibold w-20 text-center">Urutan</th>
                            <th class="py-3.5 px-4 font-semibold">Judul & Deskripsi Modul</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Tumbuhan Terkait</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Kuis Terkait</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Status</th>
                            <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-950/60 text-slate-300">
                        @forelse($modules as $mod)
                            <tr class="hover:bg-emerald-950/40 transition-colors">
                                <td class="py-4 px-4 text-center">
                                    <span class="w-8 h-8 rounded-xl bg-emerald-900/60 text-emerald-300 font-extrabold text-sm inline-flex items-center justify-center border border-emerald-500/30">
                                        {{ $mod->module_order }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="font-extrabold text-white text-base block mb-0.5">{{ $mod->title }}</span>
                                    <p class="text-xs text-slate-400 line-clamp-2 max-w-xl">{{ $mod->description ?? 'Tidak ada deskripsi singkat.' }}</p>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="px-3 py-1 rounded-full bg-[#07130c] border border-emerald-900/60 text-emerald-300 font-bold text-xs">
                                        {{ $mod->plant_species_count ?? 0 }} Spesimen
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="px-3 py-1 rounded-full bg-[#07130c] border border-amber-900/60 text-amber-300 font-bold text-xs">
                                        {{ $mod->quizzes_count ?? 0 }} Kuis
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded text-xs font-bold {{ $mod->status === 'published' ? 'text-emerald-400 bg-emerald-950/60' : 'text-amber-400 bg-amber-950/60' }}">
                                        {{ $mod->status === 'published' ? 'Dipublikasi' : 'Draf' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right space-x-2">
                                    <a href="{{ route('modules.detail', $mod->slug) }}" target="_blank" title="Lihat Publik" 
                                       class="p-2 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 transition-all font-semibold text-xs inline-flex items-center">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    <a href="{{ route('manage.modules.edit', $mod) }}" title="Edit Modul" 
                                       class="p-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30 transition-all font-semibold text-xs inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        <span>Edit</span>
                                    </a>
                                    <form id="delete-module-{{ $mod->id }}" action="{{ route('manage.modules.destroy', $mod) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="confirmDelete('delete-module-{{ $mod->id }}', 'modul {{ addslashes($mod->title) }}')" title="Hapus Modul" 
                                                class="p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 transition-all font-semibold text-xs inline-flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <span class="text-3xl block mb-2">📚</span>
                                    <span>Belum ada data modul pembelajaran kuliah.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($modules->hasPages())
                <div class="pt-4 border-t border-emerald-900/40">
                    {{ $modules->links('vendor.pagination.botani') }}
                </div>
            @endif
        </div>
    </div>
</x-manage-layout>
