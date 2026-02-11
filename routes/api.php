<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('users', [AuthController::class, 'getUsers']);
    });
});

Route::prefix('property')->middleware('auth:sanctum')->group(function () {
    Route::post('add', [\App\Http\Controllers\API\PropertyController::class, 'store']);
    Route::get('list', [\App\Http\Controllers\API\PropertyController::class, 'index']);
    Route::post('update/{id}', [\App\Http\Controllers\API\PropertyController::class, 'update']);
    Route::delete('delete/{id}', [\App\Http\Controllers\API\PropertyController::class, 'destroy']);
});
