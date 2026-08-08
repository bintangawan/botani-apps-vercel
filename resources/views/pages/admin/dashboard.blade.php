<x-manage-layout title="Dashboard Administrator — Botani Phanerogamae">
    <div class="py-6 sm:py-8 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-emerald-100 border border-emerald-300 rounded-3xl p-8 sm:p-10 mb-8 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="space-y-2 text-center sm:text-left">
                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold border border-amber-300">⚙️ Panel Administrator Sistem</span>
                <h1 class="text-3xl font-extrabold text-slate-900">Kendali Sistem & Database Botani</h1>
                <p class="text-sm text-slate-700">
                    Kelola data spesimen herbarium, modul pembelajaran, observasi lapangan, serta hak akses pengguna.
                </p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-3 shrink-0">
                <a href="{{ route('manage.plants.create') }}" class="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-slate-900 font-bold text-sm shadow-lg shadow-emerald-900/10 flex items-center gap-2 transition-all">
                    <span>+ Tambah Tumbuhan</span>
                </a>
                <a href="{{ route('manage.quizzes.index') }}" class="px-5 py-2.5 rounded-2xl bg-white border border-amber-300 text-amber-700 hover:bg-amber-100 font-semibold text-sm flex items-center gap-2 transition-all">
                    <span>🎯 Bank Soal & Kuis</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-2xl bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-100 font-semibold text-sm flex items-center gap-2 transition-all">
                    <span>👥 Kelola User & Dosen</span>
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
            <div class="p-6 rounded-3xl bg-white border border-emerald-200 shadow-lg">
                <span class="block text-3xl sm:text-4xl font-extrabold text-slate-900 mb-1">{{ $stats['total_species'] ?? 133 }}</span>
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Spesimen Tumbuhan</span>
            </div>
            <div class="p-6 rounded-3xl bg-white border border-emerald-200 shadow-lg">
                <span class="block text-3xl sm:text-4xl font-extrabold text-amber-700 mb-1">{{ $stats['total_observations'] ?? 133 }}</span>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Titik Observasi</span>
            </div>
            <div class="p-6 rounded-3xl bg-white border border-emerald-200 shadow-lg">
                <span class="block text-3xl sm:text-4xl font-extrabold text-teal-700 mb-1">{{ $stats['total_modules'] ?? 5 }}</span>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Modul Pembelajaran</span>
            </div>
            <div class="p-6 rounded-3xl bg-white border border-emerald-200 shadow-lg">
                <span class="block text-3xl sm:text-4xl font-extrabold text-emerald-700 mb-1">{{ $stats['total_users'] ?? 3 }}</span>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Akun Pengguna</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Species Added -->
            <div class="bg-white border border-emerald-200 rounded-3xl p-6 sm:p-8 space-y-4">
                <h2 class="text-lg font-bold text-slate-900 border-b border-emerald-200 pb-3">🌱 Spesimen Tumbuhan Terkatalog Terakhir</h2>
                <div class="space-y-3">
                    @foreach($recentSpecies as $sp)
                        <div class="p-3.5 rounded-xl bg-white border border-emerald-200 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-sm text-slate-900 block">{{ $sp->local_name }}</span>
                                <span class="text-xs italic text-emerald-700 font-serif">{{ $sp->scientific_name }}</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-mono bg-emerald-950/30 text-slate-600 border border-emerald-200">
                                {{ $sp->code }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Users Registered -->
            <div class="bg-white border border-emerald-200 rounded-3xl p-6 sm:p-8 space-y-4">
                <h2 class="text-lg font-bold text-slate-900 border-b border-emerald-200 pb-3">👥 Akun Pengguna Terakhir</h2>
                <div class="space-y-3">
                    @foreach($recentUsers as $usr)
                        <div class="p-3.5 rounded-xl bg-white border border-emerald-200 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-sm text-slate-900 block">{{ $usr->name }}</span>
                                <span class="text-xs text-slate-600">{{ $usr->email }}</span>
                            </div>
                            <span class="px-2.5 py-1 rounded text-xs font-bold uppercase tracking-wider
                                {{ $usr->role === 'admin' ? 'bg-amber-100 text-amber-700 border border-amber-300' : ($usr->role === 'dosen' ? 'bg-teal-100 text-teal-700 border border-teal-300' : 'bg-emerald-100 text-emerald-700 border border-emerald-300') }}">
                                {{ $usr->role }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-manage-layout>
