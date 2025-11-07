<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\Api\TeamMemberController;
use App\Http\Controllers\Api\AuthController;

// --- RUTE PUBLIK ---
Route::post('/login', [AuthController::class, 'login']);

Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now(),
    ]);
});

// --- RUTE TERLINDUNGI ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::apiResource('donations', DonationController::class);
    Route::apiResource('team-members', TeamMemberController::class);
});