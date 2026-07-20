<?php

use App\Http\Controllers\Api\AtsClientsSysConfigApiController;
use App\Http\Controllers\Api\GetAppTenantApiController;
use App\Http\Controllers\Api\GetToolsAccessTokenApiController;
use App\Http\Controllers\Api\LmsClientsSysConfigApiController;
use App\Http\Controllers\Api\ToolsMasterApiController;
use App\Http\Controllers\Api\UnioneClientsSysConfigApiController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// 🔐 PROTECTED ROUTES 

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/users', [UserController::class, 'index']);

    // App APIs
    Route::get('/atsClientsSysConfigApi/{client_code}', [AtsClientsSysConfigApiController::class, 'getAtsClientsSysConfigApi']);

    Route::get('/lmsClientsSysConfigApi/{client_code}', [LmsClientsSysConfigApiController::class, 'getLmsClientsSysConfigApi']);

    Route::get('/unioneClientsSysConfigApi/{client_code}', [UnioneClientsSysConfigApiController::class, 'getUnioneClientsSysConfigApi']);

    Route::get('/getAppTenantsApi/{app_name}', [GetAppTenantApiController::class, 'getAppTenantsApi']);
});


Route::get('/toolsMasterSettings', [ToolsMasterApiController::class, 'index']);

Route::post('/getToolsAccessTokenApi', [GetToolsAccessTokenApiController::class, 'getToolsAccessTokenApi']);
