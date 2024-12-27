<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

// Health check
Route::get('/healthcheck', function () {
    return response()->json(['status' => 'ok']);
});

// User routes
Route::prefix('users')->group(function () {
    Route::post('/', [UserController::class, 'store']); // Create user
    Route::get('/{id}', [UserController::class, 'show']); // Read user by ID
    Route::put('/{id}', [UserController::class, 'update']); // Update user
    Route::delete('/{id}', [UserController::class, 'destroy']); // Delete user
    Route::get('/username/{username}', [UserController::class, 'findByUsername']); // Find user by username
    Route::get('/email/{email}', [UserController::class, 'findByEmail']); // Find user by email
});
