<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Display listing of users with search and role filter.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('pages.manage.users.index', compact('users'));
    }

    /**
     * Store a newly created user (especially Dosen account by Admin).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,dosen,mahasiswa'],
            'institution' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'email.unique' => 'Email tersebut sudah terdaftar pada sistem.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'institution' => $validated['institution'] ?? 'Universitas Negeri Medan',
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Akun pengguna (' . strtoupper($validated['role']) . ') berhasil dibuat!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun administrator yang sedang Anda gunakan.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Akun pengguna '{$name}' telah berhasil dihapus dari sistem.");
    }
}
