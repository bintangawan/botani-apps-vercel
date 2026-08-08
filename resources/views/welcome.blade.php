<x-app-layout title="Selamat Datang — Botani Phanerogamae">
    <section class="min-h-[70vh] flex items-center py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100 border border-emerald-300 px-4 py-2 text-xs font-bold uppercase tracking-wider text-emerald-700">
                <span aria-hidden="true">🌿</span>
                Herbarium Digital Sumatera Utara
            </div>

            <h1 class="mt-6 text-4xl sm:text-6xl font-extrabold tracking-tight text-slate-900">
                Kenali tumbuhan berbiji melalui data lapangan yang terstruktur.
            </h1>

            <p class="mt-6 max-w-2xl mx-auto text-base sm:text-lg leading-relaxed text-slate-600">
                Jelajahi katalog spesimen, klasifikasi taksonomi, dan modul pembelajaran Botani Phanerogamae dalam tampilan yang bersih dan mudah dipelajari.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('catalog') }}" class="w-full sm:w-auto rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-slate-900 hover:bg-emerald-500 transition-colors">
                    Buka Galeri Tumbuhan
                </a>
                <a href="{{ route('modules') }}" class="w-full sm:w-auto rounded-xl bg-white border border-emerald-300 px-6 py-3 text-sm font-bold text-emerald-700 hover:bg-emerald-50 transition-colors">
                    Lihat Modul Pembelajaran
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
