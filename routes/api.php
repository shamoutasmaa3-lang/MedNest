<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SafetyCheckController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;

// ==================== Public Routes (No Authentication) ====================
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/safety-check', [SafetyCheckController::class, 'check']); // Public safety check

// ==================== Authenticated Routes (Require Token) ====================
Route::middleware('auth:sanctum')->group(function () {
    // User management
    Route::get('/user', [UserController::class, 'getUser']);
    Route::post('/logout', [UserController::class, 'logout']);

    // Cart management
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartController::class, 'addItem']);
    Route::delete('/cart/items/{item_id}', [CartController::class, 'removeItem']);

    // Orders
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // Prescriptions (patient)
    Route::get('/patient/prescriptions', [PrescriptionController::class, 'patientPrescriptions']);
    Route::post('/prescriptions/upload', [PrescriptionController::class, 'upload']);

    // Doctor-only routes
    Route::middleware('role:doctor')->group(function () {
        Route::post('/doctor/prescriptions', [PrescriptionController::class, 'storeDoctorPrescription']);
        Route::get('/doctor/prescriptions', [PrescriptionController::class, 'doctorPrescriptions']);
    });

    // Pharmacist-only routes
    Route::middleware('role:pharmacist')->group(function () {
        Route::put('/pharmacist/orders/{id}/verify', [OrderController::class, 'verify']);
        Route::put('/pharmacist/orders/{id}/dispense', [OrderController::class, 'dispense']);
        Route::post('/system/inventory/deduct', [InventoryController::class, 'deductStock']);
        Route::get('/pharmacist/inventory', [InventoryController::class, 'index']);
    });
});