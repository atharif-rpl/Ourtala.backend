<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Tampilkan semua user beserta role-nya
     */
    public function index()
    {
        $this->authorizeAccess();

        $users = User::with('roles')->get();
        return response()->json($users);
    }

    /**
     * Simpan user baru dan tetapkan role
     */
    public function store(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return response()->json([
            'message' => 'User berhasil dibuat.',
            'user' => $user->load('roles')
        ], 201);
    }

    /**
     * Update role user
     */
    public function updateRole(Request $request, User $user)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $user->syncRoles([$validated['role']]);

        return response()->json([
            'message' => 'Role user berhasil diperbarui.',
            'user' => $user->load('roles')
        ]);
    }

    /**
     * Hapus user (tidak boleh hapus dirinya sendiri)
     */
    public function destroy(User $user)
    {
        $this->authorizeAccess();

        if (Auth::id() === $user->id) {
            return response()->json([
                'message' => 'Anda tidak bisa menghapus akun Anda sendiri.'
            ], 400);
        }


        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus.'], 200);
    }

    /**
     * Ambil daftar semua role
     */
    public function getRoles()
    {
        $this->authorizeAccess();

        $roles = Role::pluck('name');
        return response()->json($roles);
    }

    /**
     * Cek apakah user punya izin 'manage users'
     */
    private function authorizeAccess()
    {
        if (!Auth::check() || !Auth::user()->can('manage users')) {
            abort(403, 'Anda tidak punya izin.');
        }
    }
}