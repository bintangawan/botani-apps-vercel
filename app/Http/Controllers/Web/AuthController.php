<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLoginForm(): View
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        return view('pages.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if ($this->authService->attemptLogin($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda sedang dinonaktifkan.']);
            }

            return $this->redirectBasedOnRole($user->role)->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegisterForm(): View
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        return view('pages.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'institution' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $this->authService->registerMahasiswa($validated);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('mahasiswa.dashboard')->with('success', 'Registrasi berhasil! Selamat belajar di platform Botani Phanerogamae.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    protected function redirectBasedOnRole(string $role): RedirectResponse
    {
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'dosen' => redirect()->route('dosen.dashboard'),
            default => redirect()->route('mahasiswa.dashboard'),
        };
    }
}
