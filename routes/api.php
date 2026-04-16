<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SafetyCheckController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PrescriptionController;


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/prescriptions/upload', [PrescriptionController::class, 'upload']);  
    
});
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);

Route::post('/safety-check', [SafetyCheckController::class, 'check']);