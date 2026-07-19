<x-manage-layout title="Dashboard Dosen Pengampu — Botani Phanerogamae">
    <div class="py-6 sm:py-8 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#0c2214] via-emerald-950 to-[#07130c] border border-emerald-500/30 rounded-3xl p-8 sm:p-10 mb-8 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="space-y-2 text-center sm:text-left">
                <span class="px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 text-xs font-bold border border-teal-500/30">👨‍🏫 Panel Dosen Pengampu</span>
                <h1 class="text-3xl font-extrabold text-white">Dashboard Monitoring Pembelajaran</h1>
                <p class="text-sm text-slate-300">
                    Pantau analitik evaluasi mahasiswa dan kustomisasi materi deskripsi ilmiah spesimen tumbuhan.
                </p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-3 shrink-0">
                <a href="{{ route('manage.plants.create') }}" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 flex items-center gap-2 transition-all">
                    <span>+ Tambah Tumbuhan</span>
                </a>
                <a href="{{ route('manage.modules.index') }}" class="px-5 py-2.5 rounded-2xl bg-[#07130c] border border-emerald-500/40 text-emerald-300 hover:bg-emerald-900/60 font-semibold text-sm flex items-center gap-2 transition-all">
                    <span>📚 Modul Pembelajaran</span>
                </a>
                <a href="{{ route('manage.quizzes.index') }}" class="px-5 py-2.5 rounded-2xl bg-[#07130c] border border-amber-500/40 text-amber-300 hover:bg-amber-900/60 font-semibold text-sm flex items-center gap-2 transition-all">
                    <span>🎯 Bank Soal & Kuis</span>
                </a>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
            <div class="p-6 rounded-3xl bg-[#0c2214]/60 border border-emerald-900/50 shadow-lg">
                <span class="block text-4xl font-extrabold text-white mb-1">{{ $totalStudents ?? 0 }}</span>
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Total Mahasiswa Terdaftar</span>
            </div>
            <div class="p-6 rounded-3xl bg-[#0c2214]/60 border border-emerald-900/50 shadow-lg">
                <span class="block text-4xl font-extrabold text-amber-400 mb-1">{{ $totalAttempts ?? 0 }}</span>
                <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Sesi Kuis Dikerjakan</span>
            </div>
            <div class="p-6 rounded-3xl bg-[#0c2214]/60 border border-emerald-900/50 shadow-lg">
                <span class="block text-4xl font-extrabold text-teal-300 mb-1">{{ number_format($avgScore ?? 0, 1) }} / 100</span>
                <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Rata-rata Nilai Mahasiswa</span>
            </div>
        </div>

        <!-- Recent Student Attempts Analytics Table -->
        <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-3xl p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-emerald-900/40 pb-4">
                <h2 class="text-xl font-bold text-white">📈 Aktivitas Evaluasi Terakhir Mahasiswa</h2>
                <span class="text-xs text-slate-400">Menampilkan 10 percobaan terakhir</span>
            </div>

            @if($recentAttempts && $recentAttempts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-emerald-900/50 text-emerald-400 text-xs uppercase tracking-wider">
                                <th class="py-3 px-4 font-semibold">Mahasiswa</th>
                                <th class="py-3 px-4 font-semibold">Modul / Kuis</th>
                                <th class="py-3 px-4 font-semibold">Skor Peroleh</th>
                                <th class="py-3 px-4 font-semibold">Waktu Selesai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-950/60 text-slate-300">
                            @foreach($recentAttempts as $attempt)
                                <tr class="hover:bg-emerald-950/40 transition-colors">
                                    <td class="py-3.5 px-4 font-medium text-white">{{ $attempt->user ? $attempt->user->name : 'Mahasiswa' }}</td>
                                    <td class="py-3.5 px-4">{{ $attempt->quiz ? $attempt->quiz->title : '-' }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-lg {{ ($attempt->score ?? 0) >= 60 ? 'bg-emerald-500/20 text-emerald-300 font-bold' : 'bg-rose-500/20 text-rose-300 font-bold' }}">
                                            {{ $attempt->score ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-400">{{ $attempt->completed_at ? $attempt->completed_at->format('d M Y, H:i') : 'In progress' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-slate-400 space-y-2">
                    <span class="text-4xl">📊</span>
                    <p class="text-sm">Belum ada sesi kuis yang diselesaikan mahasiswa.</p>
                </div>
            @endif
        </div>
    </div>
</x-manage-layout>
