<?php

use App\Http\Controllers\Api\AtsClientsSysConfigApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CallReviewSettingApiController;
use App\Http\Controllers\Api\GetAppTenantApiController;
use App\Http\Controllers\Api\GetToolsAccessTokenApiController;
use App\Http\Controllers\Api\LmsClientsSysConfigApiController;
use App\Http\Controllers\Api\ReviewApiController;
use App\Http\Controllers\Api\ToolsMasterApiController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AttendanceAPIs\AttendanceController;
use App\Http\Controllers\AttendanceAPIs\AttendancePunchAuditController;
use App\Http\Controllers\AttendanceAPIs\HolidayController;
use App\Http\Controllers\AttendanceAPIs\MobileAppController;
use Illuminate\Support\Facades\Route;

// 🔐 PROTECTED ROUTES
Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me', [AuthApiController::class, 'me']);

    Route::get('/reviews', [ReviewApiController::class, 'apiIndex']);
    Route::post('/reviews/saveNote', [ReviewApiController::class, 'saveNote']);
    Route::get('/reviews/{id}/history', [ReviewApiController::class, 'history']);
    Route::post('/reviews/saveCallLog', [ReviewApiController::class, 'saveCallLog']);
    Route::post('/reviews/upsertNote', [ReviewApiController::class, 'upsertNote']);
    Route::post('/reviews/saveMultipleCallLogs', [ReviewApiController::class, 'saveMultipleCallLogs']);

    Route::get('/users', [UserController::class, 'index']);

    Route::get(
        '/user-locations/{user_id}',
        [AttendanceController::class, 'userLocations']
    );

    Route::post(
        '/user-punch',
        [AttendanceController::class, 'userPunch']
    );

    Route::get(
        '/user-punches',
        [AttendanceController::class, 'getUserPunches']
    );

    Route::get(
        '/mobile-app/all/{application}',
        [MobileAppController::class, 'latest']
    );

    Route::post(
        '/mobile-app/uploadMobileApp',
        [MobileAppController::class, 'uploadMobileApp']
    )->name('mobile-app.uploadMobileApp');

    Route::get('/attendance-punch-audit', [
        AttendancePunchAuditController::class,
        'index'
    ]);

    Route::post('/attendance-punch-audit', [
        AttendancePunchAuditController::class,
        'store'
    ]);

    Route::get('/holidays', [HolidayController::class, 'index']);


    // App APIs
    Route::get('/atsClientsSysConfigApi/{client_code}', [AtsClientsSysConfigApiController::class, 'getAtsClientsSysConfigApi']);

    Route::get('/lmsClientsSysConfigApi/{client_code}', [LmsClientsSysConfigApiController::class, 'getLmsClientsSysConfigApi']);

    Route::get('/getAppTenantsApi/{app_name}', [GetAppTenantApiController::class, 'getAppTenantsApi']);
});


Route::get('/toolsMasterSettings', [ToolsMasterApiController::class, 'index']);

Route::get('/callReviewSettings', [CallReviewSettingApiController::class, 'index']);

Route::post('/getToolsAccessTokenApi', [GetToolsAccessTokenApiController::class, 'getToolsAccessTokenApi']);
