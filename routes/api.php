<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeamMemberController;
use App\Http\Controllers\DonationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


Route::get('/test', function () {
    return response()->json([
        'message' => 'Hello from Laravel backend!',
        'status' => 'success'
    ]);
});
Route::apiResource('team-members', TeamMemberController::class);
Route::post('/donations', [DonationController::class, 'store']);
Route::apiResource('donations', DonationController::class);

Route::post('/test-post', function (Request $request) {
    Log::info('Test POST route hit successfully.'); // Error akan hilang
    return response()->json([
        'message' => 'Test POST route is working!',
        'data_received' => $request->all()
    ]);
});