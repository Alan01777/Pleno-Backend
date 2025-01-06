<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\FileController;

// Health check
Route::get('/healthcheck', function () {
    return response()->json(['status' => 'ok']);
})->name('healthcheck');

// Authentication routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [UserController::class, 'store'])->name('register'); // Register user

// Protected user routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('users')->group(function () {
        Route::get('/user', [UserController::class, 'show'])->name('user.show'); // Read authenticated user
        Route::put('/user', [UserController::class, 'update'])->name('user.update'); // Update authenticated user
        Route::delete('/user', [UserController::class, 'destroy'])->name('user.destroy'); // Delete authenticated user
        Route::get('/username/{username}', [UserController::class, 'findByUsername'])->name('user.findByUsername'); // Find user by username
        Route::get('/email/{email}', [UserController::class, 'findByEmail'])->name('user.findByEmail'); // Find user by email
    });
});

// Company routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('companies.index'); // List all companies
        Route::post('/', [CompanyController::class, 'store'])->name('companies.store'); // Create a new company
        Route::get('/{id}', [CompanyController::class, 'show'])->name('companies.show'); // Read company by ID
        Route::put('/{id}', [CompanyController::class, 'update'])->name('companies.update'); // Update company
        Route::delete('/{id}', [CompanyController::class, 'destroy'])->name('companies.destroy'); // Delete company
    });
});

// File Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('files')->group(function () {
        Route::get('/', [FileController::class, 'index'])->name('files.index');
        Route::post('/', [FileController::class, 'store'])->name('files.store');
        Route::get('/{id}', [FileController::class, 'show'])->name('files.show');
        Route::put('/{id}', [FileController::class, 'update'])->name('files.update');
        Route::delete('/{id}', [FileController::class, 'destroy'])->name('files.destroy');
    });
});
