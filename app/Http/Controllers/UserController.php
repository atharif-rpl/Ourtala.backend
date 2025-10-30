<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    // Method untuk mengambil semua user
    public function index()
    {
        // Hanya user dengan izin 'manage users' yang bisa mengakses ini
        if (!auth()->user()->can('manage users')) {
            abort(403, 'Anda tidak punya izin.');
        }

        // Ambil semua user beserta role mereka
        $users = User::with('roles')->get();
        return response()->json($users);
    }

    // Method untuk membuat user baru
    public function store(Request $request)
    {
        if (!auth()->user()->can('manage users')) {
            abort(403, 'Anda tidak punya izin.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|exists:roles,name', // Pastikan role-nya ada
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Tetapkan role-nya
        $user->assignRole($validated['role']);

        return response()->json($user, 201);
    }

    // Method untuk meng-update role user
    public function updateRole(Request $request, User $user)
    {
        if (!auth()->user()->can('manage users')) {
            abort(403, 'Anda tidak punya izin.');
        }

        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        // 'syncRoles' akan menghapus role lama dan menambah role baru
        $user->syncRoles([$validated['role']]);

        return response()->json($user->load('roles'));
    }

    // Method untuk menghapus user
    public function destroy(User $user)
    {
        if (!auth()->user()->can('manage users')) {
            abort(403, 'Anda tidak punya izin.');
        }

        // Jangan biarkan user menghapus dirinya sendiri
        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'Anda tidak bisa menghapus akun Anda sendiri.'], 400);
        }

        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus'], 200);
    }

    // Method untuk mengambil daftar semua role
    public function getRoles()
    {
         if (!auth()->user()->can('manage users')) {
            abort(403, 'Anda tidak punya izin.');
        }

        $roles = Role::all()->pluck('name');
        return response()->json($roles);
    }
}