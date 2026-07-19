<!-- Mobile Right Drawer & Backdrop -->
<div x-cloak x-show="drawerOpen" class="fixed inset-0 z-50 md:hidden overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div x-show="drawerOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="drawerOpen = false"
         class="fixed inset-0 bg-black/75 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <!-- Right Drawer Panel -->
        <div x-show="drawerOpen"
             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-screen max-w-xs bg-[#091b11] border-l border-emerald-500/20 shadow-2xl flex flex-col justify-between">

            <!-- Drawer Header & Nav -->
            <div class="px-6 pt-6 pb-4 overflow-y-auto">
                <div class="flex items-center justify-between pb-6 border-b border-emerald-900/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-800 flex items-center justify-center text-lg shadow-md shadow-emerald-500/20">🌿</span>
                        <div>
                            <span class="font-bold text-base text-white">Botani<span class="text-emerald-400">Phanerogamae</span></span>
                            <span class="block text-[9px] text-emerald-500 font-semibold uppercase tracking-wider">Navigasi Utama</span>
                        </div>
                    </div>
                    <button type="button" @click="drawerOpen = false" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-all">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="mt-8 space-y-2">
                    <a href="{{ route('home') }}" @click="drawerOpen = false"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('home') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shadow-md' : 'text-slate-300 hover:bg-emerald-950/40 hover:text-white' }}">
                        <span class="text-lg">🏠</span>
                        <span>Beranda</span>
                    </a>
                    <a href="{{ route('catalog') }}" @click="drawerOpen = false"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('catalog*') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shadow-md' : 'text-slate-300 hover:bg-emerald-950/40 hover:text-white' }}">
                        <span class="text-lg">🍃</span>
                        <span>Galeri Tumbuhan</span>
                    </a>
                    <a href="{{ route('modules') }}" @click="drawerOpen = false"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('modules*') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shadow-md' : 'text-slate-300 hover:bg-emerald-950/40 hover:text-white' }}">
                        <span class="text-lg">📚</span>
                        <span>Modul Pembelajaran</span>
                    </a>
                    <a href="{{ route('about') }}" @click="drawerOpen = false"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('about') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shadow-md' : 'text-slate-300 hover:bg-emerald-950/40 hover:text-white' }}">
                        <span class="text-lg">ℹ️</span>
                        <span>Tentang Platform</span>
                    </a>
                </nav>
            </div>

            <!-- Drawer Footer / Auth section -->
            <div class="p-6 border-t border-emerald-900/50 bg-[#06140b]">
                @auth
                    @php
                        $dashboardRoute = match(auth()->user()->role) {
                            'admin' => route('admin.dashboard'),
                            'dosen' => route('dosen.dashboard'),
                            default => route('mahasiswa.dashboard'),
                        };
                    @endphp
                    <div class="mb-4 p-3 rounded-xl bg-emerald-950/60 border border-emerald-500/20">
                        <span class="text-xs text-slate-400 block">Masuk sebagai:</span>
                        <span class="font-bold text-sm text-white block truncate">{{ auth()->user()->name }}</span>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            Role: {{ auth()->user()->role }}
                        </span>
                    </div>

                    <a href="{{ $dashboardRoute }}" @click="drawerOpen = false"
                       class="w-full mb-2 flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-emerald-600 text-white text-sm font-semibold shadow-lg shadow-emerald-600/20 hover:bg-emerald-500 transition-all">
                        <span>Buka Dashboard</span>
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-300 hover:bg-rose-900/60 text-sm font-medium transition-all">
                            <span>Keluar dari Akun</span>
                        </button>
                    </form>
                @else
                    <div class="space-y-2.5">
                        <a href="{{ route('login') }}" @click="drawerOpen = false"
                           class="w-full flex items-center justify-center px-4 py-3 rounded-xl bg-emerald-950 border border-emerald-500/30 text-emerald-300 font-semibold text-sm hover:bg-emerald-900 transition-all">
                            Masuk Akun
                        </a>
                        <a href="{{ route('register') }}" @click="drawerOpen = false"
                           class="w-full flex items-center justify-center px-4 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-semibold text-sm shadow-lg shadow-emerald-600/30 hover:from-emerald-500 hover:to-emerald-400 transition-all">
                            Daftar Mahasiswa
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>
