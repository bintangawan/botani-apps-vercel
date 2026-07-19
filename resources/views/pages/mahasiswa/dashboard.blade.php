<x-app-layout title="Dashboard Mahasiswa — Botani Phanerogamae">
    <div class="py-12 sm:py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Header -->
        <div class="bg-gradient-to-r from-[#0c2214] to-emerald-950 border border-emerald-500/30 rounded-3xl p-8 sm:p-10 mb-10 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="space-y-2 text-center sm:text-left">
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">🎓 Panel Mahasiswa</span>
                <h1 class="text-3xl font-extrabold text-white">Selamat Datang, {{ $user->name }}!</h1>
                <p class="text-sm text-slate-300">
                    {{ $user->institution ?? 'Universitas Negeri Medan' }} — Ikuti pembelajaran dan evaluasi Botani Phanerogamae.
                </p>
            </div>
            <div class="flex gap-3 shrink-0">
                <a href="{{ route('modules') }}" class="px-6 py-3 rounded-2xl bg-emerald-600 text-white font-bold text-sm shadow-lg hover:bg-emerald-500 transition-all">
                    Buka Modul Teori
                </a>
                <a href="{{ route('catalog') }}" class="px-6 py-3 rounded-2xl bg-[#07130c] border border-emerald-500/30 text-emerald-300 font-semibold text-sm hover:bg-emerald-900/60 transition-all">
                    Jelajahi Galeri
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Modules & Practice Quizzes -->
            <div class="lg:col-span-2 space-y-6">
                <h2 class="text-xl font-bold text-white border-b border-emerald-900/40 pb-3">📚 Modul Pembelajaran & Kuis Evaluasi</h2>
                <div class="space-y-4">
                    @foreach($modules as $module)
                        <div class="p-6 rounded-2xl bg-[#0c2214]/60 border border-emerald-900/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <span class="text-xs font-bold text-emerald-400">Modul #{{ $module->module_order }}</span>
                                <h3 class="font-bold text-white text-lg">{{ $module->title }}</h3>
                                <p class="text-xs text-slate-400 line-clamp-1">{{ $module->description }}</p>
                            </div>
                            <div class="flex gap-2 shrink-0 w-full sm:w-auto">
                                <a href="{{ route('modules.detail', $module->slug) }}" class="flex-1 sm:flex-initial px-4 py-2 rounded-xl bg-[#07130c] border border-emerald-500/30 text-emerald-300 text-xs font-semibold hover:bg-emerald-900/60 text-center transition-all">
                                    Baca Teori
                                </a>
                                <a href="{{ route('mahasiswa.quizzes.index') }}" class="flex-1 sm:flex-initial px-4 py-2 rounded-xl bg-emerald-600/30 text-emerald-300 border border-emerald-500/40 text-xs font-bold hover:bg-emerald-600 hover:text-white text-center transition-all">
                                    Mulai Kuis Latihan
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right Col: Quiz Attempt History & Stats -->
            <div class="space-y-6">
                <h2 class="text-xl font-bold text-white border-b border-emerald-900/40 pb-3">📊 Riwayat Kuis Saya</h2>
                <div class="bg-[#0c2214]/60 border border-emerald-900/50 rounded-2xl p-6 space-y-4">
                    @if($attempts && $attempts->count() > 0)
                        <div class="space-y-3">
                            @foreach($attempts as $att)
                                <a href="{{ route('mahasiswa.quizzes.result', $att) }}" class="p-3.5 rounded-xl bg-[#07130c] hover:bg-emerald-900/40 border border-emerald-900/40 flex items-center justify-between transition-all block">
                                    <div>
                                        <span class="font-bold text-sm text-white block">{{ $att->quiz ? $att->quiz->title : 'Kuis Evaluasi' }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $att->started_at ? $att->started_at->format('d/m/Y H:i') : '' }}</span>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 font-bold text-xs">
                                        Skor: {{ $att->score ?? 0 }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 space-y-2">
                            <span class="text-3xl">📝</span>
                            <p class="text-sm font-semibold text-slate-300">Belum Ada Riwayat Kuis</p>
                            <p class="text-xs text-slate-500">Anda belum mengikuti evaluasi pretest atau practice kuis apapun.</p>
                        </div>
                    @endif
                </div>

                <!-- Quick Help Box -->
                <div class="p-6 rounded-2xl bg-gradient-to-br from-emerald-950 to-[#07130c] border border-emerald-500/30 space-y-2">
                    <span class="text-xs font-bold text-amber-400 uppercase">💡 Panduan Belajar</span>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Pelajari terlebih dahulu karakteristik taksonomi pada setiap kartu di <a href="{{ route('catalog') }}" class="text-emerald-400 underline">Galeri Tumbuhan</a> sebelum mengerjakan posttest akhir.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
