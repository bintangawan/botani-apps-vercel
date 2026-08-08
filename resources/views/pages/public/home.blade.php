<x-app-layout title="Botani Phanerogamae — Ensiklopedia Tumbuhan Berbiji Sumatera Utara">
    <!-- Hero Section -->
    <section class="relative pt-12 sm:pt-20 pb-20 sm:pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto space-y-6 sm:space-y-8">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-100 border border-emerald-300 text-emerald-700 text-xs sm:text-sm font-semibold shadow-inner">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Platform Pembelajaran Digital Botani & Observasi Herbarium</span>
                </div>

                <!-- Main Title -->
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold text-slate-900 tracking-tight leading-[1.1]">
                    Eksplorasi Taksonomi <br class="hidden sm:block">
                    <span class="text-emerald-700">
                        Botani Phanerogamae
                    </span>
                </h1>

                <!-- Description -->
                <p class="text-base sm:text-xl text-slate-700 max-w-2xl mx-auto leading-relaxed">
                    Media pengenalan ilmiah & pembelajaran tumbuhan berbiji (<span class="italic font-serif">Spermatophyta</span>) hasil observasi ekstensif di Provinsi Sumatera Utara untuk mahasiswa, dosen, dan peneliti botani.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                    <a href="{{ route('catalog') }}" 
                       class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-slate-900 font-bold text-base shadow-xl shadow-emerald-900/10 transition-all transform  flex items-center justify-center gap-2.5">
                        <span>Jelajahi 133 Spesimen</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                    <a href="{{ route('modules') }}" 
                       class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-white hover:bg-emerald-100 text-emerald-700 border border-emerald-300 font-semibold text-base transition-all flex items-center justify-center gap-2">
                        <span>Modul Pembelajaran</span>
                        <span class="text-sm">📚</span>
                    </a>
                </div>
            </div>
        </div>

    </section>

    <!-- Statistics Counters Section -->
    <section class="py-12 bg-emerald-50 border-y border-emerald-200 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8 text-center">
                <div class="p-6 rounded-3xl bg-white border border-emerald-200  shadow-lg">
                    <span class="block text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight mb-2">
                        {{ $stats['total_species'] ?? 133 }}+
                    </span>
                    <span class="text-xs sm:text-sm font-semibold text-emerald-700 uppercase tracking-wider">
                        Spesimen Terkatalog
                    </span>
                    <span class="block text-[11px] text-slate-600 mt-1">Herbarium Sumatera Utara</span>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-emerald-200  shadow-lg">
                    <span class="block text-3xl sm:text-5xl font-extrabold text-amber-700 tracking-tight mb-2">
                        {{ $stats['total_gymnospermae'] ?? 18 }}
                    </span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-700 uppercase tracking-wider">
                        Gymnospermae
                    </span>
                    <span class="block text-[11px] text-slate-600 mt-1">Tumbuhan Berbiji Terbuka</span>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-emerald-200  shadow-lg">
                    <span class="block text-3xl sm:text-5xl font-extrabold text-emerald-700 tracking-tight mb-2">
                        {{ $stats['total_angiospermae'] ?? 115 }}
                    </span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-700 uppercase tracking-wider">
                        Angiospermae
                    </span>
                    <span class="block text-[11px] text-slate-600 mt-1">Monokotil & Dikotil</span>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-emerald-200  shadow-lg">
                    <span class="block text-3xl sm:text-5xl font-extrabold text-teal-700 tracking-tight mb-2">
                        5 Modul
                    </span>
                    <span class="text-xs sm:text-sm font-semibold text-emerald-700 uppercase tracking-wider">
                        Teori Ilmiah & Kuis
                    </span>
                    <span class="block text-[11px] text-slate-600 mt-1">Berdasarkan Taksonomi</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Flora Grid -->
    <section class="py-20 sm:py-28 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest block mb-2">🍃 Koleksi Pilihan</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Sorotan Spesimen Tumbuhan
                </h2>
                <p class="text-slate-600 text-sm sm:text-base mt-2 max-w-xl">
                    Beragam tumbuhan berbiji unggulan yang diamati dari ekosistem hutan tropis dan kawasan observasi Sumatera Utara.
                </p>
            </div>
            <a href="{{ route('catalog') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 font-semibold text-sm transition-all shadow-md shrink-0 self-start md:self-auto">
                <span>Lihat Semua Katalog (133)</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredPlants as $plant)
                <x-plant-card :plant="$plant" />
            @endforeach
        </div>
    </section>

    <!-- Learning Modules Highlights -->
    <section class="py-20 bg-emerald-50 border-y border-emerald-200 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold text-amber-700 uppercase tracking-widest block mb-2">📚 Modul Akademis</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Kurikulum Botani Phanerogamae
                </h2>
                <p class="text-slate-700 text-sm sm:text-base mt-3">
                    Pelajari landasan teori botani dari Gymnospermae hingga Angiospermae (Monokotil & Dikotil) beserta evaluasi pretest dan posttest interaktif.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($modules as $module)
                    <div class="bg-white border border-emerald-300 rounded-3xl p-8 shadow-xl hover:border-emerald-300 transition-all flex flex-col justify-between group">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-700 font-bold text-lg mb-6 group-hover:scale-110 transition-transform">
                                #{{ $module->module_order }}
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 group-hover:text-emerald-700 transition-colors mb-3">
                                {{ $module->title }}
                            </h3>
                            <p class="text-sm text-slate-600 leading-relaxed mb-6">
                                {{ $module->description }}
                            </p>
                        </div>
                        <a href="{{ route('modules.detail', $module->slug) }}" 
                           class="inline-flex items-center gap-2 font-semibold text-sm text-emerald-700 hover:text-emerald-700 transition-colors pt-4 border-t border-emerald-200">
                            <span>Mulai Belajar Modul Ini</span>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('modules') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl bg-emerald-100 border border-emerald-300 text-emerald-700 hover:bg-emerald-200 hover:text-emerald-800 font-semibold text-sm transition-all shadow-lg">
                    <span>Lihat Seluruh Modul & Kuis Pembelajaran</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Recent Field Observations Section -->
    <section class="py-20 sm:py-28 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest block mb-2">📍 Catatan Lapangan</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Observasi Terbaru di Sumatera Utara
            </h2>
            <p class="text-slate-600 text-sm sm:text-base mt-3">
                Titik sampel pengamatan lapangan yang terdokumentasi dalam basis data herbarium digital.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($recentObservations as $obs)
                <div class="bg-white border border-emerald-200 rounded-2xl p-5 shadow-lg flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs text-emerald-700 mb-2 font-medium">
                            <span>📍 {{ $obs->location ? $obs->location->regency : 'Sumatera Utara' }}</span>
                            <span class="text-slate-500">{{ $obs->observation_date ? \Carbon\Carbon::parse($obs->observation_date)->format('d M Y') : 'Baru' }}</span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-base truncate">{{ $obs->plantSpecies ? $obs->plantSpecies->local_name : 'Spesimen' }}</h4>
                        <p class="text-xs italic text-slate-600 font-serif mb-3 truncate">{{ $obs->plantSpecies ? $obs->plantSpecies->scientific_name : '' }}</p>
                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">{{ $obs->notes }}</p>
                    </div>
                    @if($obs->plantSpecies)
                        <a href="{{ route('catalog.detail', $obs->plantSpecies->slug) }}" class="mt-4 pt-3 border-t border-emerald-200 text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 flex items-center justify-between">
                            <span>Lihat Detail Taksonomi</span>
                            <span>→</span>
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <!-- Call to Action Banner -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="rounded-3xl bg-emerald-100 border border-emerald-300 p-8 sm:p-14 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-8 shadow-lg relative overflow-hidden">
            <div class="space-y-3 max-w-2xl relative z-10">
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 font-semibold text-xs border border-emerald-300">🚀 Bergabung Sebagai Mahasiswa / Dosen</span>
                <h3 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Mulai Eksplorasi Botani Phanerogamae
                </h3>
                <p class="text-sm sm:text-base text-slate-700">
                    Daftarkan akun mahasiswa Anda untuk mengikuti evaluasi pretest & posttest, atau masuk ke dashboard dosen untuk mengelola spesimen herbarium.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 relative z-10 shrink-0 w-full sm:w-auto">
                <a href="{{ route('register') }}" class="px-7 py-3.5 rounded-2xl bg-emerald-600 text-slate-900 font-bold text-sm shadow-xl  transition-all text-center">
                    Daftar Sekarang
                </a>
                <a href="{{ route('login') }}" class="px-7 py-3.5 rounded-2xl bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-50 font-semibold text-sm transition-all text-center">
                    Masuk Akun
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
