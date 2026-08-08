<header class="sticky top-0 z-40 bg-white  border-b border-emerald-200 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-900/10 group- transition-transform">
                    <span class="text-2xl">🌿</span>
                </div>
                <div>
                    <span class="font-extrabold text-xl tracking-tight text-slate-900 group-hover:text-emerald-700 transition-colors">
                        Botani<span class="text-emerald-700">Phanerogamae</span>
                    </span>
                    <span class="block text-[10px] text-emerald-700 tracking-widest uppercase font-semibold">
                        Spermatophyta Sumatera
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-1 lg:gap-2">
                <a href="{{ route('home') }}" 
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('home') ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'text-slate-700 hover:text-emerald-800 hover:bg-emerald-50' }}">
                    Beranda
                </a>
                <a href="{{ route('catalog') }}" 
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('catalog*') ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'text-slate-700 hover:text-emerald-800 hover:bg-emerald-50' }}">
                    Galeri Tumbuhan
                </a>
                <a href="{{ route('modules') }}" 
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('modules*') ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'text-slate-700 hover:text-emerald-800 hover:bg-emerald-50' }}">
                    Modul Pembelajaran
                </a>
                <a href="{{ route('about') }}" 
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('about') ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'text-slate-700 hover:text-emerald-800 hover:bg-emerald-50' }}">
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
                           class="flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 text-sm font-semibold shadow-md transition-all">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Dashboard ({{ ucfirst(auth()->user()->role) }})</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" title="Keluar" 
                                    class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 hover:text-rose-800 transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" 
                       class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 hover:text-emerald-800 hover:bg-emerald-50 transition-all">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" 
                       class="px-5 py-2 rounded-xl bg-emerald-600 text-slate-900 text-sm font-semibold shadow-lg shadow-emerald-900/10 hover:bg-emerald-500  transition-all">
                        Daftar Belajar
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Button Trigger (Right Drawer) -->
            <div class="flex md:hidden items-center">
                <button type="button" @click="drawerOpen = true" aria-label="Buka Menu"
                        class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-700 hover:text-emerald-800 hover:bg-emerald-100 focus:outline-none transition-all">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>
