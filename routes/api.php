<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeamMemberController;
use App\Http\Controllers\DonationController;

Route::get('/test', function () {
    return response()->json([
        'message' => 'Hello from Laravel backend!',
        'status' => 'success'
    ]);
});
Route::apiResource('team-members', TeamMemberController::class);
Route::apiResource('donations', DonationController::class);
