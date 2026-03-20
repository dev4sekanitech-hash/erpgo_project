<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\Starrlight\CaregiverProfileController;
use App\Http\Controllers\Api\Starrlight\JobController;
use App\Http\Controllers\Api\Starrlight\UploadController;
use App\Http\Controllers\Api\Starrlight\StaffRequestController;
use App\Http\Controllers\Api\Starrlight\CareerApplicationController;
use App\Http\Controllers\Api\Starrlight\ContactController;
use App\Http\Controllers\Api\Starrlight\LookupController;

Route::middleware('api.json')->group(function () {

    // ==================== AUTH ====================
    Route::prefix('auth')->group(function () {
        // Public routes
        Route::post('/login', [AuthApiController::class, 'login']);
        Route::post('/register', [AuthApiController::class, 'register']);
        Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthApiController::class, 'resetPassword']);

        // Protected routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/user', function (Request $request) {
                return $request->user();
            });
            Route::get('/me', [AuthApiController::class, 'me']);
            Route::post('/logout', [AuthApiController::class, 'logout']);
            Route::post('/refresh', [AuthApiController::class, 'refresh']);
            Route::post('/change-password', [AuthApiController::class, 'changePassword']);
            Route::post('/edit-profile', [AuthApiController::class, 'editProfile']);
            Route::delete('/delete-account', [AuthApiController::class, 'deleteAccount']);
        });
    });

    // ==================== CAREGIVER PROFILE ====================
    Route::prefix('caregiver/profile')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [CaregiverProfileController::class, 'show']);
        Route::post('/contact', [CaregiverProfileController::class, 'updateContact']);
        Route::post('/languages', [CaregiverProfileController::class, 'updateLanguages']);
        Route::post('/certificates', [CaregiverProfileController::class, 'updateCertificates']);
        Route::post('/work-history', [CaregiverProfileController::class, 'updateWorkHistory']);
        Route::post('/submit', [CaregiverProfileController::class, 'submit']);
    });

    // ==================== UPLOADS ====================
    Route::prefix('uploads')->middleware('auth:sanctum')->group(function () {
        Route::post('/profile-photo', [UploadController::class, 'profilePhoto']);
        Route::post('/certificate', [UploadController::class, 'certificate']);
        Route::post('/resume', [UploadController::class, 'resume']);
    });

    // ==================== JOBS ====================
    Route::prefix('jobs')->group(function () {
        Route::get('/', [JobController::class, 'index']);
        Route::get('/{id}', [JobController::class, 'show']);

        // Protected - requires authentication
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/{id}/apply', [JobController::class, 'apply']);
        });
    });

    // ==================== PUBLIC FORMS ====================
    Route::post('/bookings/staff-request', [StaffRequestController::class, 'store']);
    Route::post('/careers/apply', [CareerApplicationController::class, 'store']);
    Route::post('/contact', [ContactController::class, 'store']);

    // ==================== LOOKUP DATA ====================
    Route::prefix('lookup')->group(function () {
        Route::get('/languages', [LookupController::class, 'languages']);
        Route::get('/provinces', [LookupController::class, 'provinces']);
        Route::get('/job-types', [LookupController::class, 'jobTypes']);
        Route::get('/shift-patterns', [LookupController::class, 'shiftPatterns']);
    });
});
