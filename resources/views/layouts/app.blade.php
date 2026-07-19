<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Botani Phanerogamae — Ensiklopedia & Media Pembelajaran Tumbuhan Berbiji' }}</title>
    <meta name="description" content="Eksplorasi herbarium digital 133+ spesies tumbuhan berbiji (Gymnospermae & Angiospermae) di Sumatera Utara beserta modul pembelajaran interaktif.">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#07130c] text-slate-100 font-sans antialiased selection:bg-emerald-500 selection:text-white min-h-screen flex flex-col relative overflow-x-hidden" x-data="{ drawerOpen: false }">

    <!-- Glow background accents -->
    <div class="fixed top-0 left-1/4 w-96 h-96 bg-emerald-600/10 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed bottom-1/3 right-10 w-[30rem] h-[30rem] bg-amber-600/5 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <!-- Navigation Bar -->
    <x-navbar />

    <!-- Mobile Right Drawer -->
    <x-mobile-drawer />

    <!-- Flash Alerts -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity
             class="fixed top-24 right-6 z-50 max-w-md bg-emerald-950/90 border border-emerald-500/50 text-emerald-200 px-5 py-4 rounded-2xl shadow-2xl backdrop-blur-md flex items-center gap-3">
            <svg class="w-6 h-6 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
            <button @click="show = false" class="ml-auto text-emerald-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif

    @if(session('error') || (isset($errors) && $errors->any()))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity
             class="fixed top-24 right-6 z-50 max-w-md bg-rose-950/90 border border-rose-500/50 text-rose-200 px-5 py-4 rounded-2xl shadow-2xl backdrop-blur-md flex items-start gap-3">
            <svg class="w-6 h-6 text-rose-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="text-sm font-medium">
                @if(session('error'))
                    <p>{{ session('error') }}</p>
                @endif
                @if(isset($errors) && $errors->any())
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <button @click="show = false" class="ml-auto text-rose-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Luxury Botanical Footer -->
    <footer class="mt-24 border-t border-emerald-900/40 bg-[#040b07] pt-16 pb-12 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-800 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                            <span class="text-xl">🌿</span>
                        </div>
                        <div>
                            <span class="font-bold text-xl tracking-tight text-white">Botani<span class="text-emerald-400">Phanerogamae</span></span>
                            <span class="block text-xs text-emerald-500 font-medium">Herbarium Digital & Pembelajaran</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 max-w-sm leading-relaxed">
                        Platform dokumentasi ilmiah 133+ takson tumbuhan berbiji (Gymnospermae & Angiospermae) hasil eksplorasi lapangan di Provinsi Sumatera Utara.
                    </p>
                    <div class="flex flex-wrap gap-2.5 pt-2">
                        <span class="px-3 py-1 rounded-full bg-emerald-950/60 border border-emerald-500/20 text-xs text-emerald-300 font-medium">Eksplorasi Taksonomi</span>
                        <span class="px-3 py-1 rounded-full bg-emerald-950/60 border border-emerald-500/20 text-xs text-emerald-300 font-medium">Spermatophyta Sumatera</span>
                        <span class="px-3 py-1 rounded-full bg-emerald-950/60 border border-emerald-500/20 text-xs text-emerald-300 font-medium">Kurikulum Biologi</span>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Navigasi Utama</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Beranda</a></li>
                        <li><a href="{{ route('catalog') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Jelajahi Galeri Tumbuhan</a></li>
                        <li><a href="{{ route('modules') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Modul Pembelajaran</a></li>
                        <li><a href="{{ route('about') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Tentang Penelitian</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Akses Pengguna</h4>
                    <ul class="space-y-2.5 text-sm">
                        @auth
                            @if(auth()->user()->role === 'admin')
                                <li><a href="{{ route('admin.dashboard') }}" class="text-emerald-400 hover:text-emerald-300 font-medium transition-colors">Dashboard Admin</a></li>
                            @elseif(auth()->user()->role === 'dosen')
                                <li><a href="{{ route('dosen.dashboard') }}" class="text-emerald-400 hover:text-emerald-300 font-medium transition-colors">Dashboard Dosen</a></li>
                            @else
                                <li><a href="{{ route('mahasiswa.dashboard') }}" class="text-emerald-400 hover:text-emerald-300 font-medium transition-colors">Dashboard Mahasiswa</a></li>
                            @endif
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-rose-400 hover:text-rose-300 transition-colors">Keluar dari Akun</button>
                                </form>
                            </li>
                        @else
                            <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Masuk Akun</a></li>
                            <li><a href="{{ route('register') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Daftar Mahasiswa Baru</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <div class="border-t border-emerald-950 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Botani Phanerogamae Apps. Seluruh Hak Cipta Dilindungi.</p>
                <p>Dikembangkan untuk observasi taksonomi & evaluasi pembelajaran Biologi.</p>
            </div>
        </div>
    </footer>
</body>
</html>
