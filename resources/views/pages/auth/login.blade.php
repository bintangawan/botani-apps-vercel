<x-app-layout title="Masuk Akun — Botani Phanerogamae">
    <div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
        <div class="max-w-md w-full space-y-8 bg-white border border-emerald-300 p-8 sm:p-10 rounded-3xl shadow-lg relative z-10">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-600 shadow-xl shadow-emerald-900/10 mb-4">
                    <span class="text-3xl">🌿</span>
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Masuk ke <span class="text-emerald-700">Platform</span>
                </h2>
                <p class="mt-2 text-sm text-slate-600">
                    Akses herbarium digital & evaluasi pembelajaran Phanerogamae
                </p>
            </div>

            <!-- Credentials quick hint -->
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-300 text-xs text-slate-700 space-y-1.5 shadow-inner">
                <div class="font-bold text-emerald-700 flex items-center justify-between">
                    <span>💡 Kredensial Demo Akun:</span>
                    <span class="text-[10px] text-slate-600">(Password: password)</span>
                </div>
                <div class="grid grid-cols-1 gap-1 text-[11px] font-mono">
                    <div class="flex justify-between border-b border-emerald-200 pb-1">
                        <span class="text-emerald-700">👨‍💼 Admin:</span> <span>admin@botani.ac.id</span>
                    </div>
                    <div class="flex justify-between border-b border-emerald-200 pb-1">
                        <span class="text-emerald-700">👨‍🏫 Dosen:</span> <span>dosen@botani.ac.id</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-emerald-700">🎓 Mahasiswa:</span> <span>mahasiswa@botani.ac.id</span>
                    </div>
                </div>
            </div>

            <form class="mt-8 space-y-6" action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-emerald-700 mb-2">
                            Alamat Email Universitas / Akun
                        </label>
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                               placeholder="contoh: mahasiswa@botani.ac.id"
                               class="w-full bg-white border border-emerald-300 rounded-2xl px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-700 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-emerald-700">
                                Kata Sandi (Password)
                            </label>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               placeholder="••••••••"
                               class="w-full bg-white border border-emerald-300 rounded-2xl px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 rounded border-emerald-300 bg-white text-emerald-700 focus:ring-emerald-500">
                        <label for="remember" class="ml-2 block text-xs text-slate-700">
                            Ingat sesi saya
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-2xl shadow-lg shadow-emerald-900/10 text-sm font-bold text-slate-900 bg-emerald-600 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all transform ">
                        Masuk Sekarang
                    </button>
                </div>

                <div class="text-center pt-2 border-t border-emerald-200">
                    <p class="text-xs text-slate-600">
                        Belum memiliki akun mahasiswa?
                        <a href="{{ route('register') }}" class="font-bold text-emerald-700 hover:text-emerald-700 transition-colors">
                            Daftar di sini
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
