<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\Starrlight\StarrlightAuthController;
use App\Http\Controllers\Api\Starrlight\StarrlightLookupController;
use App\Http\Controllers\Api\Starrlight\StarrlightJobController;
use App\Http\Controllers\Api\Starrlight\StarrlightCaregiverController;
use App\Http\Controllers\Api\Starrlight\StarrlightUploadController;
use App\Http\Controllers\Api\Starrlight\StarrlightBookingController;
use App\Http\Controllers\Api\Starrlight\StarrlightCareerController;
use App\Http\Controllers\Api\Starrlight\StarrlightContactController;

Route::middleware('api.json')->group(function () {

    Route::post('/login', [AuthApiController::class, 'login']);

    // STARRLIGHT API ROUTES

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/login', [StarrlightAuthController::class, 'login']);
        Route::post('/register', [StarrlightAuthController::class, 'register']);
        Route::post('/forgot-password', [StarrlightAuthController::class, 'forgotPassword']);
        Route::post('/logout', [StarrlightAuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('/me', [StarrlightAuthController::class, 'me'])->middleware('auth:sanctum');
    });

    // Lookup Data
    Route::prefix('lookup')->group(function () {
        Route::get('/languages', [StarrlightLookupController::class, 'languages']);
        Route::get('/provinces', [StarrlightLookupController::class, 'provinces']);
        Route::get('/job-types', [StarrlightLookupController::class, 'jobTypes']);
        Route::get('/shift-patterns', [StarrlightLookupController::class, 'shiftPatterns']);
    });

    // Jobs
    Route::prefix('jobs')->group(function () {
        Route::get('/', [StarrlightJobController::class, 'index']);
        Route::get('/{id}', [StarrlightJobController::class, 'show']);
        Route::post('/{id}/apply', [StarrlightJobController::class, 'apply'])->middleware('auth:sanctum');
    });

    // Caregiver Profile (Auth required)
    Route::prefix('caregiver/profile')->middleware('auth:sanctum')->group(function () {
        Route::post('/contact', [StarrlightCaregiverController::class, 'storeContact']);
        Route::post('/languages', [StarrlightCaregiverController::class, 'storeLanguages']);
        Route::post('/certificates', [StarrlightCaregiverController::class, 'storeCertificates']);
        Route::post('/work-history', [StarrlightCaregiverController::class, 'storeWorkHistory']);
        Route::get('/', [StarrlightCaregiverController::class, 'show']);
        Route::post('/submit', [StarrlightCaregiverController::class, 'submit']);
    });

    // File Uploads (Auth required)
    Route::prefix('uploads')->middleware('auth:sanctum')->group(function () {
        Route::post('/profile-photo', [StarrlightUploadController::class, 'profilePhoto']);
        Route::post('/certificate', [StarrlightUploadController::class, 'certificate']);
        Route::post('/resume', [StarrlightUploadController::class, 'resume']);
    });

    // Bookings (Public)
    Route::post('/bookings/staff-request', [StarrlightBookingController::class, 'staffRequest']);

    // Careers (Public)
    Route::post('/careers/apply', [StarrlightCareerController::class, 'apply']);

    // Contact (Public)
    Route::post('/contact', [StarrlightContactController::class, 'submit']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::post('/refresh', [AuthApiController::class, 'refresh']);
        Route::post('/change-password', [AuthApiController::class, 'changePassword']);
        Route::post('/edit-profile', [AuthApiController::class, 'editProfile']);
        Route::delete('/delete-account', [AuthApiController::class, 'deleteAccount']);
    });
});
