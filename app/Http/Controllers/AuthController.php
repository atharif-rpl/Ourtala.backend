<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Pastikan Anda sudah memiliki model User

class AuthController extends Controller
{
    /**
     * Menangani permintaan login.
     */
    public function login(Request $request)
    {
        // 1. Validasi input (email & password wajib diisi)
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. Coba lakukan autentikasi
        if (Auth::attempt($credentials)) {
            // 3. Jika berhasil, buat token untuk user
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            // 4. Kirim kembali data user dan token
            return response()->json([
                'message' => 'Login berhasil',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 200);
        }

        // 5. Jika gagal, kirim pesan error
        return response()->json([
            'message' => 'Email atau password salah.'
        ], 401);
    }

    /**
     * Menangani permintaan logout.
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ], 200);
    }

    /**
     * Mendapatkan data user yang sedang login.
     */
    public function user(Request $request)
    {
        // Mengembalikan data user yang sudah terotentikasi
        return $request->user();
    }
}