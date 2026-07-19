<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator Botani',
            'email' => 'admin@botani.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'institution' => 'Universitas Negeri Medan',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Dr. Budi Santoso, M.Si.',
            'email' => 'dosen@botani.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'institution' => 'Universitas Negeri Medan',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Siti Aminah (Mahasiswa Biologi)',
            'email' => 'mahasiswa@botani.ac.id',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'institution' => 'Universitas Negeri Medan',
            'status' => 'active',
        ]);
    }
}
