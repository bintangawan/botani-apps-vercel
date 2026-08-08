<x-manage-layout title="Manajemen Katalog Spesimen Tumbuhan — Botani Phanerogamae">
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="bg-emerald-100 border border-emerald-300 rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="space-y-1.5 text-center sm:text-left">
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-300">🌱 Katalog & Taksonomi</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Manajemen Spesimen Tumbuhan</h1>
                <p class="text-sm text-slate-700">
                    Kelola data taksonomi, morfologi biologi, serta dokumentasi herbarium digital untuk seluruh tumbuhan Gymnospermae & Angiospermae.
                </p>
            </div>
            <a href="{{ route('manage.plants.create') }}" class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-slate-900 font-extrabold text-sm shadow-lg shadow-emerald-900/10 flex items-center gap-2.5 shrink-0 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                <span>+ Tambah Spesimen Baru</span>
            </a>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white border border-emerald-200 rounded-2xl p-4 sm:p-6 shadow-lg">
            <form action="{{ route('manage.plants.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-2 space-y-1.5">
                    <label for="search" class="block text-xs font-bold uppercase tracking-wider text-emerald-700">Pencarian Spesimen</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari nama lokal, ilmiah, atau kode..." 
                           class="w-full rounded-xl bg-white border border-emerald-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-slate-900 placeholder-slate-400 text-sm py-2.5 px-4">
                </div>
                <div class="space-y-1.5">
                    <label for="group_type" class="block text-xs font-bold uppercase tracking-wider text-emerald-700">Kelompok Tumbuhan</label>
                    <select name="group_type" id="group_type" class="w-full rounded-xl bg-white border border-emerald-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-slate-900 text-sm py-2.5 px-4">
                        <option value="">-- Semua Kelompok --</option>
                        <option value="Gymnospermae" {{ request('group_type') === 'Gymnospermae' ? 'selected' : '' }}>Gymnospermae (Terbuka)</option>
                        <option value="Angiospermae" {{ request('group_type') === 'Angiospermae' ? 'selected' : '' }}>Angiospermae (Tertutup)</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-slate-900 font-bold text-sm shadow-md transition-all">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'group_type']))
                        <a href="{{ route('manage.plants.index') }}" class="py-2.5 px-4 rounded-xl bg-white border border-emerald-200 hover:bg-emerald-100 text-slate-700 font-bold text-sm transition-all text-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Plants Table -->
        <div class="bg-white border border-emerald-200 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-emerald-200 text-emerald-700 text-xs uppercase tracking-wider">
                            <th class="py-3.5 px-4 font-semibold">Kode & Gambar</th>
                            <th class="py-3.5 px-4 font-semibold">Nama Lokal / Umum</th>
                            <th class="py-3.5 px-4 font-semibold">Nama Ilmiah (Taksonomi)</th>
                            <th class="py-3.5 px-4 font-semibold">Kelompok</th>
                            <th class="py-3.5 px-4 font-semibold">Status</th>
                            <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-100 text-slate-700">
                        @forelse($plants as $pl)
                            <tr class="hover:bg-emerald-100 transition-colors">
                                <td class="py-4 px-4 font-bold text-slate-900 flex items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-xl bg-white border border-emerald-200 overflow-hidden shrink-0 flex items-center justify-center">
                                        @if($pl->image_url)
                                            <img src="{{ $pl->image_url }}" alt="{{ $pl->local_name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-xl">🌿</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-mono text-xs text-emerald-700 block">{{ $pl->code }}</span>
                                        <span class="text-[10px] text-slate-500 uppercase font-mono tracking-widest">{{ $pl->cotyledon_type ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $pl->local_name }}</td>
                                <td class="py-4 px-4 font-serif italic text-emerald-700 text-base">
                                    {{ $pl->scientific_name }}
                                    @if($pl->author_name)
                                        <span class="text-xs font-sans not-italic text-slate-600">{{ $pl->author_name }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider {{ $pl->group_type === 'Gymnospermae' ? 'bg-amber-100 text-amber-700 border border-amber-300' : 'bg-emerald-100 text-emerald-700 border border-emerald-300' }}">
                                        {{ $pl->group_type }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded text-xs font-bold {{ $pl->status === 'published' ? 'text-emerald-700 bg-emerald-50' : 'text-amber-700 bg-amber-50' }}">
                                        {{ $pl->status === 'published' ? 'Dipublikasi' : 'Draf' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right space-x-2">
                                    <a href="{{ route('catalog.detail', $pl->slug) }}" target="_blank" title="Lihat Publik" 
                                       class="p-2 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-300 transition-all font-semibold text-xs inline-flex items-center">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    <a href="{{ route('manage.plants.edit', $pl) }}" title="Edit Spesimen" 
                                       class="p-2 rounded-xl bg-amber-100 hover:bg-amber-100 text-amber-700 border border-amber-300 transition-all font-semibold text-xs inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        <span>Edit</span>
                                    </a>
                                    <form id="delete-plant-{{ $pl->id }}" action="{{ route('manage.plants.destroy', $pl) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="confirmDelete('delete-plant-{{ $pl->id }}', 'spesimen {{ addslashes($pl->local_name) }}')" title="Hapus Spesimen" 
                                                class="p-2 rounded-xl bg-rose-100 hover:bg-rose-100 text-rose-700 border border-rose-300 transition-all font-semibold text-xs inline-flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-600">
                                    <span class="text-3xl block mb-2">🌱</span>
                                    <span>Belum ada data spesimen tumbuhan terdaftar.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Compact Pagination -->
            @if($plants->hasPages())
                <div class="pt-4 border-t border-emerald-200">
                    {{ $plants->links('vendor.pagination.botani') }}
                </div>
            @endif
        </div>
    </div>
</x-manage-layout>
