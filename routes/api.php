<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SafetyCheckController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Http\Request;

// ==================== Public Routes (No Authentication) ====================
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// ==================== Authenticated Routes (Require Token) ====================
Route::middleware('auth:sanctum')->group(function () {
    // User management
    Route::get('/user', [UserController::class, 'getUser']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/logout-all', [UserController::class, 'logoutAll']);
    Route::post('/safety-check', [SafetyCheckController::class, 'check']);

    // Recommendations
    Route::get('/recommendations/usage', [OrderController::class, 'generateUsageRecommendations']);
    Route::get('/recommendations/dosage', [OrderController::class, 'dosageReminder']);
    Route::get('/recommendations', [RecommendationController::class, 'index']);
    Route::post('/recommendations/{id}/read', [RecommendationController::class, 'markAsRead']);

    // Notifications (using NotificationController)
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Consultations
    Route::post('/consultations', [ConsultationController::class, 'createConsultation']);
    Route::get('/consultations', [ConsultationController::class, 'patientConsultations']);

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

    // Medicine show (patient)
    Route::get('/medicines', [MedicineController::class, 'index']);

    // Doctor-only routes
    Route::middleware('role:doctor')->group(function () {
        Route::post('/doctor/prescriptions', [PrescriptionController::class, 'storeDoctorPrescription']);
        Route::get('/doctor/prescriptions', [PrescriptionController::class, 'doctorPrescriptions']);
    });

    // Pharmacist-only routes
    Route::middleware('role:pharmacist')->group(function () {
        Route::put('/pharmacist/orders/{id}/verify', [OrderController::class, 'verify']);
        Route::put('/pharmacist/orders/{id}/reject', [OrderController::class, 'reject']);
        Route::put('/pharmacist/orders/{id}/dispense', [OrderController::class, 'dispense']);
        Route::post('/system/inventory/deduct', [InventoryController::class, 'deductStock']);
        Route::get('/pharmacist/inventory', [InventoryController::class, 'index']);
        Route::get('/pharmacist/consultations', [ConsultationController::class, 'pharmacistConsultations']);
        Route::post('/consultations/{id}/reply', [ConsultationController::class, 'reply']);
        Route::post('/pharmacist/prescriptions/{id}/review', [PrescriptionController::class, 'review']);
        Route::get('/pharmacist/prescriptions', [PrescriptionController::class, 'pharmacistPrescriptions']);
        Route::post('/medicines', [MedicineController::class, 'store']);
        // ✅ إضافة المسار الجديد للأدوية المنتهية صلاحيتها
        Route::get('/pharmacist/inventory/expiring', [InventoryController::class, 'expiringMedicines']);
    });

    // Signature verification for prescriptions (requires pharmacist, separate middleware)
    Route::get('/pharmacist/prescriptions/{id}/verify-signature', [PrescriptionController::class, 'verifyPrescriptionSignature'])
        ->middleware('auth:sanctum', 'role:pharmacist');
});