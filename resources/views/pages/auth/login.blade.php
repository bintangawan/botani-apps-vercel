<x-app-layout title="Masuk Akun — Botani Phanerogamae">
    <div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
        <!-- Glow circles -->
        <div class="absolute w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl -top-10 -left-10 pointer-events-none"></div>
        <div class="absolute w-80 h-80 bg-amber-500/10 rounded-full blur-3xl -bottom-10 -right-10 pointer-events-none"></div>

        <div class="max-w-md w-full space-y-8 bg-[#0c2214]/90 border border-emerald-500/30 p-8 sm:p-10 rounded-3xl shadow-2xl backdrop-blur-2xl relative z-10">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-800 shadow-xl shadow-emerald-500/20 mb-4">
                    <span class="text-3xl">🌿</span>
                </div>
                <h2 class="text-3xl font-extrabold text-white tracking-tight">
                    Masuk ke <span class="text-emerald-400">Platform</span>
                </h2>
                <p class="mt-2 text-sm text-slate-400">
                    Akses herbarium digital & evaluasi pembelajaran Phanerogamae
                </p>
            </div>

            <!-- Credentials quick hint -->
            <div class="p-3.5 rounded-2xl bg-emerald-950/70 border border-emerald-500/30 text-xs text-slate-300 space-y-1.5 shadow-inner">
                <div class="font-bold text-emerald-400 flex items-center justify-between">
                    <span>💡 Kredensial Demo Akun:</span>
                    <span class="text-[10px] text-slate-400">(Password: password)</span>
                </div>
                <div class="grid grid-cols-1 gap-1 text-[11px] font-mono">
                    <div class="flex justify-between border-b border-emerald-900/50 pb-1">
                        <span class="text-emerald-300">👨‍💼 Admin:</span> <span>admin@botani.ac.id</span>
                    </div>
                    <div class="flex justify-between border-b border-emerald-900/50 pb-1">
                        <span class="text-emerald-300">👨‍🏫 Dosen:</span> <span>dosen@botani.ac.id</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-emerald-300">🎓 Mahasiswa:</span> <span>mahasiswa@botani.ac.id</span>
                    </div>
                </div>
            </div>

            <form class="mt-8 space-y-6" action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-emerald-400 mb-2">
                            Alamat Email Universitas / Akun
                        </label>
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                               placeholder="contoh: mahasiswa@botani.ac.id"
                               class="w-full bg-[#07130c] border border-emerald-800/80 rounded-2xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-emerald-400">
                                Kata Sandi (Password)
                            </label>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               placeholder="••••••••"
                               class="w-full bg-[#07130c] border border-emerald-800/80 rounded-2xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 rounded border-emerald-800 bg-[#07130c] text-emerald-500 focus:ring-emerald-500">
                        <label for="remember" class="ml-2 block text-xs text-slate-300">
                            Ingat sesi saya
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-2xl shadow-lg shadow-emerald-600/30 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all transform hover:scale-[1.02]">
                        Masuk Sekarang
                    </button>
                </div>

                <div class="text-center pt-2 border-t border-emerald-900/50">
                    <p class="text-xs text-slate-400">
                        Belum memiliki akun mahasiswa?
                        <a href="{{ route('register') }}" class="font-bold text-emerald-400 hover:text-emerald-300 transition-colors">
                            Daftar di sini
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
