<x-manage-layout title="Manajemen Kuis Evaluasi & Bank Soal — Botani Phanerogamae">
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-[#0c2214] via-emerald-950 to-[#07130c] border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="space-y-1.5 text-center sm:text-left">
                <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">🎯 Bank Soal & Kuis</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Manajemen Kuis & Evaluasi</h1>
                <p class="text-sm text-slate-300">
                    Kelola konfigurasi ujian kuis, nilai ketuntasan minimal (passing score), serta susun bank soal dan kunci jawaban.
                </p>
            </div>
            <a href="{{ route('manage.quizzes.create') }}" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-sm shadow-lg shadow-emerald-600/30 flex items-center gap-2.5 shrink-0 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                <span>+ Buat Kuis Baru</span>
            </a>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-2xl p-4 sm:p-6 shadow-lg">
            <form action="{{ route('manage.quizzes.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-2 space-y-1.5">
                    <label for="search" class="block text-xs font-bold uppercase tracking-wider text-emerald-400">Pencarian Kuis</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari judul evaluasi atau kuis..." 
                           class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-white placeholder-slate-500 text-sm py-2.5 px-4">
                </div>
                <div class="space-y-1.5">
                    <label for="module_id" class="block text-xs font-bold uppercase tracking-wider text-emerald-400">Filter Modul Perkuliahan</label>
                    <select name="module_id" id="module_id" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">
                        <option value="">-- Semua Modul --</option>
                        @foreach($modules as $mod)
                            <option value="{{ $mod->id }}" {{ request('module_id') == $mod->id ? 'selected' : '' }}>{{ $mod->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md transition-all">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'module_id']))
                        <a href="{{ route('manage.quizzes.index') }}" class="py-2.5 px-4 rounded-xl bg-[#07130c] border border-emerald-900/60 hover:bg-emerald-900/40 text-slate-300 font-bold text-sm transition-all text-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Quizzes Table -->
        <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-emerald-900/50 text-emerald-400 text-xs uppercase tracking-wider">
                            <th class="py-3.5 px-4 font-semibold">Judul Evaluasi & Kuis</th>
                            <th class="py-3.5 px-4 font-semibold">Modul Terkait</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Passing Score</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Durasi</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Bank Soal</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Status</th>
                            <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-950/60 text-slate-300">
                        @forelse($quizzes as $qz)
                            <tr class="hover:bg-emerald-950/40 transition-colors">
                                <td class="py-4 px-4">
                                    <span class="font-extrabold text-white text-base block mb-0.5">{{ $qz->title }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-900/60 text-emerald-300 border border-emerald-500/30">
                                        {{ str_replace('_', ' ', $qz->quiz_type) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-slate-300 font-medium">
                                    {{ $qz->learningModule ? $qz->learningModule->title : 'Modul Umum / Mandiri' }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-300 font-extrabold text-xs border border-emerald-500/30">
                                        {{ $qz->passing_score }} / 100
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center font-mono text-xs text-slate-300">
                                    {{ $qz->duration }} Menit
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <a href="{{ route('manage.quizzes.questions.index', $qz) }}" 
                                       class="px-3.5 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 transition-all font-bold text-xs inline-flex items-center gap-1.5 shadow-sm">
                                        <span>📝 Kelola Soal ({{ $qz->questions_count ?? 0 }})</span>
                                    </a>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded text-xs font-bold {{ $qz->status === 'published' ? 'text-emerald-400 bg-emerald-950/60' : 'text-amber-400 bg-amber-950/60' }}">
                                        {{ $qz->status === 'published' ? 'Dipublikasi' : 'Draf' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right space-x-2">
                                    <a href="{{ route('manage.quizzes.edit', $qz) }}" title="Edit Pengaturan Kuis" 
                                       class="p-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30 transition-all font-semibold text-xs inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        <span>Config</span>
                                    </a>
                                    <form id="delete-quiz-{{ $qz->id }}" action="{{ route('manage.quizzes.destroy', $qz) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="confirmDelete('delete-quiz-{{ $qz->id }}', 'kuis {{ addslashes($qz->title) }}')" title="Hapus Kuis" 
                                                class="p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 transition-all font-semibold text-xs inline-flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <span class="text-3xl block mb-2">🎯</span>
                                    <span>Belum ada data kuis evaluasi. Klik '+ Buat Kuis Baru' untuk mulai menyusun.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($quizzes->hasPages())
                <div class="pt-4 border-t border-emerald-900/40">
                    {{ $quizzes->links('vendor.pagination.botani') }}
                </div>
            @endif
        </div>
    </div>
</x-manage-layout>
