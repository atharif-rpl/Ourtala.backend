<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Log request untuk debugging
            Log::info('Login attempt', [
                'email' => $request->email,
                'origin' => $request->header('Origin'),
            ]);

            // Validasi input
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            // Coba autentikasi
            if (!Auth::attempt($credentials)) {
                Log::warning('Login failed: Invalid credentials', ['email' => $request->email]);

                return response()->json([
                    'message' => 'Email atau password salah.'
                ], 401);
            }

            // Ambil user
            $user = Auth::user();

            // Buat token
            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('Login successful', ['user_id' => $user->id]);

            return response()->json([
                'message' => 'Login berhasil',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat login',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout berhasil'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Logout error', ['message' => $e->getMessage()]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat logout'
            ], 500);
        }
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}