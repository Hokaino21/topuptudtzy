<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    // List all users (admin)
    Route::get('/users', [\App\Http\Controllers\Api\UserController::class, 'index']);
    // API endpoint to update profile including multipart file upload
    Route::patch('/profile', [ProfileController::class, 'update']);
});
