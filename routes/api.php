<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Standard Controllers
use App\Http\Controllers\UserController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SafetyCheckController;

// API Specific Controllers (Aliased to avoid name collision)
use App\Http\Controllers\Api\MedicineController as ApiMedicineController;
use App\Http\Controllers\MedicineController as WebMedicineController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::patch('/prescriptions/{id}/dispense', [PrescriptionController::class, 'dispense']);
Route::middleware('auth:sanctum')->group(function () {
Route::post('/prescriptions', [PrescriptionController::class, 'storeDoctorPrescription']);
    // other existing routes...
});
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Medicine search and show should generally be public so users can browse
Route::prefix('v1')->group(function () {
    Route::get('/medicines/search', [ApiMedicineController::class, 'search']);
    Route::get('/medicines/{id}', [ApiMedicineController::class, 'show']);
});
Route::get('/medicines', [ApiMedicineController::class, 'requirePrescription']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // V1 Group for Mobile/API specific logic
    Route::prefix('v1')->group(function () {
        Route::get('/patients/search', [UserController::class, 'search']);
    });

    // User management
    Route::get('/user', [UserController::class, 'getUser']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/logout-all', [UserController::class, 'logoutAll']);
    Route::post('/profile/review', [UserController::class, 'reviewChanges']);
    Route::patch('/user/update', [UserController::class, 'updateProfile']);

    //Safety check
    Route::post('/safety-check', [SafetyCheckController::class, 'check']);
    


    // Recommendations
    Route::get('/recommendations/usage', [OrderController::class, 'generateUsageRecommendations']);
    Route::get('/recommendations/dosage', [OrderController::class, 'dosageReminder']);
    Route::get('/recommendations', [RecommendationController::class, 'index']);
    Route::post('/recommendations/{id}/read', [RecommendationController::class, 'markAsRead']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Consultations
    
   Route::post('/consultations', [ConsultationController::class, 'createConsultation'])->middleware('auth:sanctum');
   Route::get('/consultations/patient', [ConsultationController::class, 'patientConsultations'])->middleware('auth:sanctum');
   Route::post('/consultations/{id}/message', [ConsultationController::class, 'sendMessage']);

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

    // General Medicine List (using the web controller version if intended)
    Route::get('/medicines', [WebMedicineController::class, 'index']);

    /* --- Role Based Routes --- */

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
        Route::get('/pharmacist/inventory/expiring', [InventoryController::class, 'expiringMedicines']);
        
        // This likely refers to adding new medicine records
        Route::post('/medicines', [WebMedicineController::class, 'store']);
        
        // Signature verification
        Route::get('/pharmacist/prescriptions/{id}/verify-signature', [PrescriptionController::class, 'verifyPrescriptionSignature']);
    });
});