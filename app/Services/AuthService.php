<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Register a new student (mahasiswa).
     */
    public function registerMahasiswa(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'mahasiswa',
            'institution' => $data['institution'] ?? null,
            'status' => 'active',
        ]);
    }

    /**
     * Attempt login with given credentials.
     */
    public function attemptLogin(array $credentials, bool $remember = false): bool
    {
        return Auth::attempt($credentials, $remember);
    }
}
