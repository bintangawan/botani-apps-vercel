<x-manage-layout title="Manajemen Pengguna & Akun Dosen — Botani Phanerogamae">
    <div class="space-y-8" x-data="{ createModalOpen: false }">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-[#0c2214] via-emerald-950 to-[#07130c] border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="space-y-1.5 text-center sm:text-left">
                <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">👥 Kontrol Eksklusif Administrator</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Manajemen Pengguna & Akun Dosen</h1>
                <p class="text-sm text-slate-300">
                    Kelola daftar pengguna aktif, buat akun baru untuk Dosen Pengampu, atau hapus akses pengguna dari sistem.
                </p>
            </div>
            <button type="button" @click="createModalOpen = true" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-sm shadow-lg shadow-emerald-600/30 flex items-center gap-2.5 shrink-0 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                <span>+ Buat Akun Dosen Baru</span>
            </button>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-2xl p-4 sm:p-6 shadow-lg">
            <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-2 space-y-1.5">
                    <label for="search" class="block text-xs font-bold uppercase tracking-wider text-emerald-400">Pencarian Pengguna</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau institusi..." 
                           class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-white placeholder-slate-500 text-sm py-2.5 px-4">
                </div>
                <div class="space-y-1.5">
                    <label for="role" class="block text-xs font-bold uppercase tracking-wider text-emerald-400">Filter Peran (Role)</label>
                    <select name="role" id="role" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-white text-sm py-2.5 px-4">
                        <option value="">-- Semua Peran --</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="dosen" {{ request('role') === 'dosen' ? 'selected' : '' }}>Dosen Pengampu</option>
                        <option value="mahasiswa" {{ request('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md transition-all">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'role']))
                        <a href="{{ route('admin.users.index') }}" class="py-2.5 px-4 rounded-xl bg-[#07130c] border border-emerald-900/60 hover:bg-emerald-900/40 text-slate-300 font-bold text-sm transition-all text-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-emerald-900/50 text-emerald-400 text-xs uppercase tracking-wider">
                            <th class="py-3.5 px-4 font-semibold">Nama Pengguna</th>
                            <th class="py-3.5 px-4 font-semibold">Email</th>
                            <th class="py-3.5 px-4 font-semibold">Peran (Role)</th>
                            <th class="py-3.5 px-4 font-semibold">Institusi</th>
                            <th class="py-3.5 px-4 font-semibold">Status</th>
                            <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-950/60 text-slate-300">
                        @forelse($users as $usr)
                            <tr class="hover:bg-emerald-950/40 transition-colors">
                                <td class="py-4 px-4 font-bold text-white flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-900/60 flex items-center justify-center text-emerald-300 font-extrabold text-xs">
                                        {{ substr($usr->name, 0, 2) }}
                                    </div>
                                    <span>{{ $usr->name }}</span>
                                </td>
                                <td class="py-4 px-4 text-slate-300 font-mono text-xs">{{ $usr->email }}</td>
                                <td class="py-4 px-4">
                                    <span class="px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider
                                        {{ $usr->role === 'admin' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : ($usr->role === 'dosen' ? 'bg-teal-500/20 text-teal-300 border border-teal-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30') }}">
                                        {{ $usr->role }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-slate-400 text-xs">{{ $usr->institution ?? '-' }}</td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded text-xs font-bold {{ $usr->status === 'active' ? 'text-emerald-400 bg-emerald-950/60' : 'text-rose-400 bg-rose-950/60' }}">
                                        {{ $usr->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    @if($usr->id !== auth()->id())
                                        <form id="delete-user-{{ $usr->id }}" action="{{ route('admin.users.destroy', $usr) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="confirmDelete('delete-user-{{ $usr->id }}', 'akun {{ addslashes($usr->name) }}')" 
                                                    class="p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 transition-all font-semibold text-xs inline-flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-500 italic">Akun Anda</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <span class="text-3xl block mb-2">👥</span>
                                    <span>Tidak ada data pengguna yang ditemukan sesuai pencarian.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Compact Pagination -->
            @if($users->hasPages())
                <div class="pt-4 border-t border-emerald-900/40">
                    {{ $users->links('vendor.pagination.botani') }}
                </div>
            @endif
        </div>

        <!-- Modal Form Buat Akun Baru (khusus Dosen/Pengguna) -->
        <div x-show="createModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
             @click="createModalOpen = false" style="display: none;">
            
            <div @click.stop class="bg-[#0c2214] border border-emerald-500/40 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-emerald-900/50 pb-4">
                    <div>
                        <span class="text-xs font-bold uppercase text-emerald-400 tracking-wider">Pembuatan Akun</span>
                        <h3 class="text-xl font-extrabold text-white">Buat Akun Dosen / Pengguna</h3>
                    </div>
                    <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="role" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1">Peran (Role) <span class="text-rose-400">*</span></label>
                        <select name="role" id="modal-role" required class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-semibold">
                            <option value="dosen" selected>👨‍🏫 Dosen Pengampu (Rekomendasi)</option>
                            <option value="admin">⚙️ Administrator Sistem</option>
                            <option value="mahasiswa">🎓 Mahasiswa</option>
                        </select>
                    </div>

                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1">Nama Lengkap & Gelar <span class="text-rose-400">*</span></label>
                        <input type="text" name="name" id="modal-name" required placeholder="Contoh: Dr. Budi Santoso, M.Si." 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1">Alamat Email <span class="text-rose-400">*</span></label>
                        <input type="email" name="email" id="modal-email" required placeholder="dosen@botani.ac.id" 
                               class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1">Password <span class="text-rose-400">*</span></label>
                            <input type="password" name="password" id="modal-password" required minlength="8" placeholder="Minimal 8 karakter" 
                                   class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1">Konfirmasi Password <span class="text-rose-400">*</span></label>
                            <input type="password" name="password_confirmation" id="modal-password-confirm" required minlength="8" placeholder="Ulangi password" 
                                   class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="institution" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1">Institusi / Kampus</label>
                            <input type="text" name="institution" id="modal-institution" value="Universitas Negeri Medan" 
                                   class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4">
                        </div>
                        <div>
                            <label for="status" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1">Status Akun</label>
                            <select name="status" id="modal-status" class="w-full rounded-xl bg-[#07130c] border border-emerald-900/60 focus:border-emerald-500 text-white text-sm py-2.5 px-4 font-semibold">
                                <option value="active" selected>Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-emerald-900/50 flex items-center justify-end gap-3">
                        <button type="button" @click="createModalOpen = false" class="px-5 py-2.5 rounded-xl bg-[#07130c] hover:bg-emerald-900/40 text-slate-300 font-semibold text-sm transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-sm shadow-lg shadow-emerald-600/30 transition-all">
                            Simpan & Buat Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-manage-layout>
