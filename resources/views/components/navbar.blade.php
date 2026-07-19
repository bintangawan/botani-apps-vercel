<header class="sticky top-0 z-40 bg-[#07130c]/80 backdrop-blur-xl border-b border-emerald-900/40 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-800 flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                    <span class="text-2xl">🌿</span>
                </div>
                <div>
                    <span class="font-extrabold text-xl tracking-tight text-white group-hover:text-emerald-400 transition-colors">
                        Botani<span class="text-emerald-400">Phanerogamae</span>
                    </span>
                    <span class="block text-[10px] text-emerald-500/90 tracking-widest uppercase font-semibold">
                        Spermatophyta Sumatera
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-1 lg:gap-2">
                <a href="{{ route('home') }}" 
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('home') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                    Beranda
                </a>
                <a href="{{ route('catalog') }}" 
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('catalog*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                    Galeri Tumbuhan
                </a>
                <a href="{{ route('modules') }}" 
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('modules*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                    Modul Pembelajaran
                </a>
                <a href="{{ route('about') }}" 
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('about') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                    Tentang
                </a>
            </nav>

            <!-- Desktop Auth Actions -->
            <div class="hidden md:flex items-center gap-3">
                @auth
                    @php
                        $dashboardRoute = match(auth()->user()->role) {
                            'admin' => route('admin.dashboard'),
                            'dosen' => route('dosen.dashboard'),
                            default => route('mahasiswa.dashboard'),
                        };
                    @endphp
                    <div class="flex items-center gap-3">
                        <a href="{{ $dashboardRoute }}" 
                           class="flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-950/80 border border-emerald-500/30 text-emerald-300 hover:bg-emerald-900/80 hover:text-white text-sm font-semibold shadow-md transition-all">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Dashboard ({{ ucfirst(auth()->user()->role) }})</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" title="Keluar" 
                                    class="p-2.5 rounded-xl bg-rose-950/40 border border-rose-500/20 text-rose-300 hover:bg-rose-900/60 hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" 
                       class="px-4 py-2 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition-all">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" 
                       class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 text-white text-sm font-semibold shadow-lg shadow-emerald-600/30 hover:from-emerald-500 hover:to-emerald-400 hover:scale-105 transition-all">
                        Daftar Belajar
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Button Trigger (Right Drawer) -->
            <div class="flex md:hidden items-center">
                <button type="button" @click="drawerOpen = true" aria-label="Buka Menu"
                        class="p-2.5 rounded-xl bg-emerald-950/60 border border-emerald-500/30 text-emerald-300 hover:text-white hover:bg-emerald-900/80 focus:outline-none transition-all">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>
