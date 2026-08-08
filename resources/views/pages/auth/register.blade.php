<x-app-layout title="Daftar Mahasiswa Baru — Botani Phanerogamae">
    <div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
        <div
            class="max-w-md w-full space-y-8 bg-white border border-emerald-300 p-8 sm:p-10 rounded-3xl shadow-lg relative z-10">
            <div class="text-center">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-600 shadow-xl shadow-emerald-900/10 mb-4">
                    <span class="text-3xl">🎓</span>
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Registrasi <span class="text-emerald-700">Mahasiswa</span>
                </h2>
                <p class="mt-2 text-sm text-slate-600">
                    Buat akun untuk mengikuti pretest & posttest pembelajaran Botani Phanerogamae
                </p>
            </div>

            <form class="mt-8 space-y-5" action="{{ route('register.post') }}" method="POST">
                @csrf

                <div>
                    <label for="name"
                        class="block text-xs font-semibold uppercase tracking-wider text-emerald-700 mb-2">
                        Nama Lengkap Mahasiswa
                    </label>
                    <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}"
                        placeholder="Contoh: Siti Aminah, S.Si."
                        class="w-full bg-white border border-emerald-300 rounded-2xl px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-700 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email"
                        class="block text-xs font-semibold uppercase tracking-wider text-emerald-700 mb-2">
                        Alamat Email Aktif
                    </label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                        placeholder="contoh: siti.aminah@botani.ac.id"
                        class="w-full bg-white border border-emerald-300 rounded-2xl px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-700 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="institution"
                        class="block text-xs font-semibold uppercase tracking-wider text-emerald-700 mb-2">
                        Institusi / Universitas / Fakultas
                    </label>
                    <input id="institution" name="institution" type="text"
                        value="{{ old('institution', 'Universitas Negeri Medan') }}"
                        placeholder="contoh: Universitas Negeri Medan"
                        class="w-full bg-white border border-emerald-300 rounded-2xl px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all">
                </div>

                <div>
                    <label for="password"
                        class="block text-xs font-semibold uppercase tracking-wider text-emerald-700 mb-2">
                        Kata Sandi (Min. 8 Karakter)
                    </label>
                    <input id="password" name="password" type="password" required placeholder="••••••••"
                        class="w-full bg-white border border-emerald-300 rounded-2xl px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all">
                </div>

                <div>
                    <label for="password_confirmation"
                        class="block text-xs font-semibold uppercase tracking-wider text-emerald-700 mb-2">
                        Konfirmasi Kata Sandi
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        placeholder="••••••••"
                        class="w-full bg-white border border-emerald-300 rounded-2xl px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all">
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-2xl shadow-lg shadow-emerald-900/10 text-sm font-bold text-slate-900 bg-emerald-600 hover:bg-emerald-500 transition-all transform ">
                        Daftar Akun Mahasiswa
                    </button>
                </div>

                <div class="text-center pt-3 border-t border-emerald-200">
                    <p class="text-xs text-slate-600">
                        Sudah punya akun?
                        <a href="{{ route('login') }}"
                            class="font-bold text-emerald-700 hover:text-emerald-700 transition-colors">
                            Masuk ke akun Anda
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
