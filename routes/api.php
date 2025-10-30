<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; // <-- 1. Tambahkan ini
use App\Http\Controllers\DonationController;
use App\Http\Controllers\Api\TeamMemberController; // (Pastikan path ini benar)

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- RUTE PUBLIK ---
// Rute-rute ini bisa diakses siapa saja tanpa login

// Rute untuk login
Route::post('/login', [AuthController::class, 'login']);

// Rute tes Anda
Route::get('/test', function () {
    return response()->json([
        'message' => 'Hello from Laravel backend!',
        'status' => 'success'
    ]);
});

Route::post('/test-post', function (Request $request) {
    Log::info('Test POST route hit successfully.');
    return response()->json([
        'message' => 'Test POST route is working!',
        'data_received' => $request->all()
    ]);
});


// --- RUTE TERLINDUNGI (WAJIB LOGIN) ---
// Semua rute di dalam grup ini WAJIB menggunakan token Sanctum

Route::middleware('auth:sanctum')->group(function () {

    // Rute untuk cek user & logout
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

    // 2. PINDAHKAN RUTE DONASI & TIM KE DALAM GRUP INI
    Route::apiResource('donations', DonationController::class);
    Route::apiResource('team-members', TeamMemberController::class);

    // (Route::post('/donations', ...) sudah tidak perlu karena ada di apiResource)

});