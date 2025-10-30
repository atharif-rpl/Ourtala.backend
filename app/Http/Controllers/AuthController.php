<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Menangani permintaan login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Coba autentikasi
        if (!Auth::attempt($credentials)) {
            // Jika gagal, kirim error
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Jika berhasil, ambil data user
        $user = User::where('email', $request->email)->firstOrFail();

        // Buat token Sanctum
        $token = $user->createToken('auth-token')->plainTextToken;

        // Kirim balasan sukses
        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token
        ], 200); // 200 OK
    }

    /**
     * Menangani permintaan logout.
     */
    public function logout(Request $request)
    {
        // Hanya user yang terautentikasi (via token) yang bisa logout
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil'], 200);
    }

    /**
     * Mengambil data user yang sedang login.
     */
    public function me(Request $request)
    {
        // Ambil user yang terautentikasi (dari token)
        $user = $request->user();

        // Kembalikan data user + semua role & permission-nya
        return response()->json([
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }
}