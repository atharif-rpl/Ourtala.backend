<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Diperlukan untuk type-hinting dan instance yang dikembalikan Auth

class AuthController extends Controller
{
    /**
     * Menangani permintaan login API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. Coba lakukan autentikasi
        if (Auth::attempt($credentials)) {
            // 3. Jika berhasil, ambil user dan buat token
            // Auth::user() mengembalikan instance App\Models\User
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
     * Menangani permintaan logout API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // $request->user() juga mengembalikan instance App\Models\User
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ], 200);
    }

    /**
     * Mendapatkan data user yang sedang login.
     *
     * @param Request $request
     * @return \App\Models\User
     */
    public function user(Request $request)
    {
        // Mengembalikan data user yang sudah terotentikasi
        // $request->user() mengembalikan instance App\Models\User
        return $request->user();
    }
}