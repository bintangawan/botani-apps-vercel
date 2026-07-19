<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Panel Manajemen — Botani Phanerogamae' }}</title>
    <meta name="description" content="Panel kendali dan monitoring pembelajaran platform Botani Phanerogamae.">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#07130c] text-slate-100 font-sans antialiased selection:bg-emerald-500 selection:text-white min-h-screen flex relative overflow-x-hidden" x-data="{ manageDrawerOpen: false }">

    <!-- Glow background accents -->
    <div class="fixed top-0 left-1/4 w-96 h-96 bg-emerald-600/10 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed bottom-1/3 right-10 w-[30rem] h-[30rem] bg-amber-600/5 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <!-- Desktop Full Vertical Sidebar -->
    <aside class="hidden lg:flex flex-col w-64 bg-[#040b07]/95 border-r border-emerald-900/50 fixed inset-y-0 left-0 z-40 backdrop-blur-xl">
        <div class="p-6 border-b border-emerald-900/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-800 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                <span class="text-xl">🌿</span>
            </div>
            <div>
                <span class="font-extrabold text-lg tracking-tight text-white block">Botani<span class="text-emerald-400">Panel</span></span>
                <span class="text-[10px] text-emerald-500 font-bold uppercase tracking-widest block">
                    {{ auth()->user()->role === 'admin' ? 'Administrator' : 'Dosen Pengampu' }}
                </span>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="flex-grow p-4 space-y-1.5 overflow-y-auto">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-3 py-2">Menu Utama</div>
            
            <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dosen.dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm transition-all {{ request()->routeIs('admin.dashboard', 'dosen.dashboard') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-[#0c2214] hover:text-emerald-400' }}">
                <span class="text-lg">📊</span>
                <span>Dashboard Utama</span>
            </a>

            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-3 pt-6 py-2">Manajemen Konten</div>

            <a href="{{ route('manage.plants.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm transition-all {{ request()->routeIs('manage.plants.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-[#0c2214] hover:text-emerald-400' }}">
                <span class="text-lg">🌱</span>
                <span>Katalog Tumbuhan</span>
            </a>

            <a href="{{ route('manage.modules.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm transition-all {{ request()->routeIs('manage.modules.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-[#0c2214] hover:text-emerald-400' }}">
                <span class="text-lg">📚</span>
                <span>Modul Pembelajaran</span>
            </a>

            <a href="{{ route('manage.quizzes.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm transition-all {{ request()->routeIs('manage.quizzes.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-[#0c2214] hover:text-emerald-400' }}">
                <span class="text-lg">🎯</span>
                <span>Kuis & Bank Soal</span>
            </a>

            @if(auth()->user()->role === 'admin')
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-3 pt-6 py-2">Hak Akses Admin</div>
                
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm transition-all {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-[#0c2214] hover:text-emerald-400' }}">
                    <span class="text-lg">👥</span>
                    <span>Kelola Pengguna & Dosen</span>
                </a>
            @endif

            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-3 pt-6 py-2">Publik & Akun</div>

            <a href="{{ route('home') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm text-slate-400 hover:bg-[#0c2214] hover:text-white transition-all">
                <span class="text-lg">🌐</span>
                <span>Kembali ke Situs Utama</span>
            </a>
        </nav>

        <!-- User Footer & Logout -->
        <div class="p-4 border-t border-emerald-900/50 bg-[#07130c]/80">
            <div class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-[#0c2214] border border-emerald-900/60 mb-2">
                <div class="truncate">
                    <span class="font-bold text-xs text-white block truncate">{{ auth()->user()->name }}</span>
                    <span class="text-[10px] text-emerald-400 block truncate">{{ auth()->user()->email }}</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-rose-950/60 hover:bg-rose-900/80 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all">
                    <span>Keluar dari Sesi</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Mobile Top Header Bar -->
    <div class="lg:hidden fixed top-0 inset-x-0 h-16 bg-[#040b07]/95 border-b border-emerald-900/50 z-30 flex items-center justify-between px-4 backdrop-blur-xl">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-800 flex items-center justify-center">
                <span class="text-base">🌿</span>
            </div>
            <span class="font-bold text-white text-base">Botani<span class="text-emerald-400">Panel</span></span>
        </div>
        <button @click="manageDrawerOpen = true" class="p-2 rounded-xl bg-[#0c2214] border border-emerald-500/30 text-emerald-300 hover:text-white">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
    </div>

    <!-- Mobile Hamburger Right Drawer -->
    <div x-show="manageDrawerOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm lg:hidden"
         @click="manageDrawerOpen = false" style="display: none;">
        
        <div x-show="manageDrawerOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             @click.stop
             class="fixed inset-y-0 right-0 w-80 bg-[#040b07] border-l border-emerald-900/50 flex flex-col justify-between p-6 overflow-y-auto shadow-2xl">
            
            <div class="space-y-6">
                <div class="flex items-center justify-between border-b border-emerald-900/50 pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xl">🌿</span>
                        <span class="font-extrabold text-lg text-white">Botani<span class="text-emerald-400">Panel</span></span>
                    </div>
                    <button @click="manageDrawerOpen = false" class="text-slate-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-3 rounded-2xl bg-[#0c2214] border border-emerald-900/60">
                    <span class="font-bold text-sm text-white block">{{ auth()->user()->name }}</span>
                    <span class="text-xs text-emerald-400 uppercase font-semibold">{{ auth()->user()->role }}</span>
                </div>

                <!-- Mobile Drawer Menu Links -->
                <nav class="space-y-2">
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dosen.dashboard') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm {{ request()->routeIs('admin.dashboard', 'dosen.dashboard') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-[#0c2214] hover:text-emerald-400' }}">
                        <span class="text-lg">📊</span>
                        <span>Dashboard Utama</span>
                    </a>

                    <a href="{{ route('manage.plants.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm {{ request()->routeIs('manage.plants.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-[#0c2214] hover:text-emerald-400' }}">
                        <span class="text-lg">🌱</span>
                        <span>Katalog Tumbuhan</span>
                    </a>

                    <a href="{{ route('manage.modules.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm {{ request()->routeIs('manage.modules.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-[#0c2214] hover:text-emerald-400' }}">
                        <span class="text-lg">📚</span>
                        <span>Modul Pembelajaran</span>
                    </a>

                    <a href="{{ route('manage.quizzes.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm {{ request()->routeIs('manage.quizzes.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-[#0c2214] hover:text-emerald-400' }}">
                        <span class="text-lg">🎯</span>
                        <span>Kuis & Bank Soal</span>
                    </a>

                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-[#0c2214] hover:text-emerald-400' }}">
                            <span class="text-lg">👥</span>
                            <span>Kelola Pengguna & Dosen</span>
                        </a>
                    @endif

                    <a href="{{ route('home') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm text-slate-400 hover:bg-[#0c2214] hover:text-white">
                        <span class="text-lg">🌐</span>
                        <span>Kembali ke Situs Utama</span>
                    </a>
                </nav>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="pt-6 border-t border-emerald-900/50">
                @csrf
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-rose-950/60 text-rose-300 border border-rose-500/30 font-bold text-sm text-center">
                    Keluar dari Sesi
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Wrapper (shifted right on desktop for sidebar) -->
    <main class="flex-grow lg:ml-64 pt-16 lg:pt-0 min-h-screen flex flex-col">
        <div class="flex-grow p-4 sm:p-8">
            {{ $slot }}
        </div>
    </main>

    <!-- SweetAlert Flash Handler -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{!! addslashes(session('success')) !!}",
                    background: '#0c2214',
                    color: '#fff',
                    confirmButtonColor: '#10b981',
                    timer: 3500,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian!',
                    text: "{!! addslashes(session('error')) !!}",
                    background: '#0c2214',
                    color: '#fff',
                    confirmButtonColor: '#f43f5e'
                });
            });
        </script>
    @endif

    <!-- Global SweetAlert Delete Confirmation Helper -->
    <script>
        function confirmDelete(formId, itemLabel = 'data ini') {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan menghapus ${itemLabel}. Tindakan ini tidak dapat dibatalkan!`,
                icon: 'warning',
                showCancelButton: true,
                background: '#0c2214',
                color: '#fff',
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#334155',
                confirmButtonText: 'Ya, Hapus Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</body>
</html>
