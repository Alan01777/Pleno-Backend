<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;

// Health check
Route::get('/healthcheck', function () {
    return response()->json(['status' => 'ok']);
});

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [UserController::class, 'store']); // Register user

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('users')->group(function () {
        Route::get('/{id}', [UserController::class, 'show']); // Read user by ID
        Route::put('/{id}', [UserController::class, 'update']); // Update user
        Route::delete('/{id}', [UserController::class, 'destroy']); // Delete user
        Route::get('/username/{username}', [UserController::class, 'findByUsername']); // Find user by username
        Route::get('/email/{email}', [UserController::class, 'findByEmail']); // Find user by email
    });
});


// // Company routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index']); // List all companies
        Route::post('/', [CompanyController::class, 'store']); // Create a new company
        Route::get('/{id}', [CompanyController::class, 'show']); // Read company by ID
        Route::put('/{id}', [CompanyController::class, 'update']); // Update company
        Route::delete('/{id}', [CompanyController::class, 'destroy']); // Delete company
    });
});
