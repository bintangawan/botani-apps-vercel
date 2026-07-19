<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda sedang dinonaktifkan.');
        }

        if (empty($roles) || in_array($user->role, $roles)) {
            return $next($request);
        }

        // If not allowed role, redirect to their appropriate dashboard
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard')->with('error', 'Akses ditolak untuk halaman tersebut.'),
            'dosen' => redirect()->route('dosen.dashboard')->with('error', 'Akses ditolak untuk halaman tersebut.'),
            default => redirect()->route('mahasiswa.dashboard')->with('error', 'Akses ditolak untuk halaman tersebut.'),
        };
    }
}
