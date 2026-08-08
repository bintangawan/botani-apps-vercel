<x-manage-layout title="Dashboard Dosen Pengampu — Botani Phanerogamae">
    <div class="py-6 sm:py-8 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-emerald-100 border border-emerald-300 rounded-3xl p-8 sm:p-10 mb-8 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="space-y-2 text-center sm:text-left">
                <span class="px-3 py-1 rounded-full bg-teal-100 text-teal-700 text-xs font-bold border border-teal-300">👨‍🏫 Panel Dosen Pengampu</span>
                <h1 class="text-3xl font-extrabold text-slate-900">Dashboard Monitoring Pembelajaran</h1>
                <p class="text-sm text-slate-700">
                    Pantau analitik evaluasi mahasiswa dan kustomisasi materi deskripsi ilmiah spesimen tumbuhan.
                </p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-3 shrink-0">
                <a href="{{ route('manage.plants.create') }}" class="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-slate-900 font-bold text-sm shadow-lg shadow-emerald-900/10 flex items-center gap-2 transition-all">
                    <span>+ Tambah Tumbuhan</span>
                </a>
                <a href="{{ route('manage.modules.index') }}" class="px-5 py-2.5 rounded-2xl bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-100 font-semibold text-sm flex items-center gap-2 transition-all">
                    <span>📚 Modul Pembelajaran</span>
                </a>
                <a href="{{ route('manage.quizzes.index') }}" class="px-5 py-2.5 rounded-2xl bg-white border border-amber-300 text-amber-700 hover:bg-amber-100 font-semibold text-sm flex items-center gap-2 transition-all">
                    <span>🎯 Bank Soal & Kuis</span>
                </a>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
            <div class="p-6 rounded-3xl bg-white border border-emerald-200 shadow-lg">
                <span class="block text-4xl font-extrabold text-slate-900 mb-1">{{ $totalStudents ?? 0 }}</span>
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Total Mahasiswa Terdaftar</span>
            </div>
            <div class="p-6 rounded-3xl bg-white border border-emerald-200 shadow-lg">
                <span class="block text-4xl font-extrabold text-amber-700 mb-1">{{ $totalAttempts ?? 0 }}</span>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Sesi Kuis Dikerjakan</span>
            </div>
            <div class="p-6 rounded-3xl bg-white border border-emerald-200 shadow-lg">
                <span class="block text-4xl font-extrabold text-teal-700 mb-1">{{ number_format($avgScore ?? 0, 1) }} / 100</span>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Rata-rata Nilai Mahasiswa</span>
            </div>
        </div>

        <!-- Recent Student Attempts Analytics Table -->
        <div class="bg-white border border-emerald-200 rounded-3xl p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-emerald-200 pb-4">
                <h2 class="text-xl font-bold text-slate-900">📈 Aktivitas Evaluasi Terakhir Mahasiswa</h2>
                <span class="text-xs text-slate-600">Menampilkan 10 percobaan terakhir</span>
            </div>

            @if($recentAttempts && $recentAttempts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-emerald-200 text-emerald-700 text-xs uppercase tracking-wider">
                                <th class="py-3 px-4 font-semibold">Mahasiswa</th>
                                <th class="py-3 px-4 font-semibold">Modul / Kuis</th>
                                <th class="py-3 px-4 font-semibold">Skor Peroleh</th>
                                <th class="py-3 px-4 font-semibold">Waktu Selesai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-100 text-slate-700">
                            @foreach($recentAttempts as $attempt)
                                <tr class="hover:bg-emerald-100 transition-colors">
                                    <td class="py-3.5 px-4 font-medium text-slate-900">{{ $attempt->user ? $attempt->user->name : 'Mahasiswa' }}</td>
                                    <td class="py-3.5 px-4">{{ $attempt->quiz ? $attempt->quiz->title : '-' }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-lg {{ ($attempt->score ?? 0) >= 60 ? 'bg-emerald-100 text-emerald-700 font-bold' : 'bg-rose-100 text-rose-700 font-bold' }}">
                                            {{ $attempt->score ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-600">{{ $attempt->completed_at ? $attempt->completed_at->format('d M Y, H:i') : 'In progress' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-slate-600 space-y-2">
                    <span class="text-4xl">📊</span>
                    <p class="text-sm">Belum ada sesi kuis yang diselesaikan mahasiswa.</p>
                </div>
            @endif
        </div>
    </div>
</x-manage-layout>
