<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Ats\AtsClientsController;
use App\Http\Controllers\Ats\AtsClientsSysConfigController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatabaseInspectorController;
use App\Http\Controllers\Lms\LmsClientsController;
use App\Http\Controllers\Lms\LmsClientsSysConfigController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\ToolsMasterController;
use App\Http\Controllers\Unione\UnioneClientsController;
use App\Http\Controllers\Unione\UnioneClientsSysConfigController;
use App\Http\Controllers\UploadMobileAppController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\DatabaseConnectionMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::redirect('/', '/login');

    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');

    Route::get('/auth/microsoft', [AuthController::class, 'redirectToMicrosoft'])->name('microsoft.redirect');
    Route::get('/auth/callback', [AuthController::class, 'handleMicrosoftCallback']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes | auth middleware
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard, Profile, Logout
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/profile', [DashboardController::class, 'index'])->name('profile');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {

        Route::resource('users', UserController::class);

        Route::post('/users/filter', [UserController::class, 'filter'])
            ->name('users.filter');

        Route::get('/users/filter/reset', [UserController::class, 'resetFilter'])
            ->name('users.filter.reset');
    });

    /*
    |--------------------------------------------------------------------------
    | Tools Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {

        /* ---------- LMS Clients ---------- */
        Route::prefix('lms')->name('lms.')->group(function () {

            Route::resource('clients', LmsClientsController::class);

            Route::get(
                'clients/{client}/sys-config/create',
                [LmsClientsSysConfigController::class, 'create']
            )->name('clientsSysConfigs.create');

            Route::post(
                'clients/{client}/sys-config',
                [LmsClientsSysConfigController::class, 'store']
            )->name('clientsSysConfigs.store');

            Route::get(
                'clients/{client}/sys-config/edit',
                [LmsClientsSysConfigController::class, 'edit']
            )->name('clientsSysConfigs.edit');

            Route::put(
                'clients/{client}/sys-config',
                [LmsClientsSysConfigController::class, 'update']
            )->name('clientsSysConfigs.update');

            Route::delete(
                'clients/{client}/sys-config',
                [LmsClientsSysConfigController::class, 'destroy']
            )->name('clientsSysConfigs.destroy');
        });

        /* ---------- ATS Clients ---------- */
        Route::prefix('ats')->name('ats.')->group(function () {

            Route::resource('clients', AtsClientsController::class);

            Route::get(
                'clients/{client}/sys-config/create',
                [AtsClientsSysConfigController::class, 'create']
            )->name('clientsSysConfigs.create');

            Route::post(
                'clients/{client}/sys-config',
                [AtsClientsSysConfigController::class, 'store']
            )->name('clientsSysConfigs.store');

            Route::get(
                'clients/{client}/sys-config/edit',
                [AtsClientsSysConfigController::class, 'edit']
            )->name('clientsSysConfigs.edit');

            Route::put(
                'clients/{client}/sys-config',
                [AtsClientsSysConfigController::class, 'update']
            )->name('clientsSysConfigs.update');

            Route::delete(
                'clients/{client}/sys-config',
                [AtsClientsSysConfigController::class, 'destroy']
            )->name('clientsSysConfigs.destroy');
        });

        /* ---------- UNIONE Clients ---------- */
        Route::prefix('unione')->name('unione.')->group(function () {

            Route::resource('clients', UnioneClientsController::class);

            Route::get(
                'clients/{client}/sys-config/create',
                [UnioneClientsSysConfigController::class, 'create']
            )->name('clientsSysConfigs.create');

            Route::post(
                'clients/{client}/sys-config',
                [UnioneClientsSysConfigController::class, 'store']
            )->name('clientsSysConfigs.store');

            Route::get(
                'clients/{client}/sys-config/edit',
                [UnioneClientsSysConfigController::class, 'edit']
            )->name('clientsSysConfigs.edit');

            Route::put(
                'clients/{client}/sys-config',
                [UnioneClientsSysConfigController::class, 'update']
            )->name('clientsSysConfigs.update');

            Route::delete(
                'clients/{client}/sys-config',
                [UnioneClientsSysConfigController::class, 'destroy']
            )->name('clientsSysConfigs.destroy');
        });
    });


    /*
    |--------------------------------------------------------------------------
    | System Setting Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::resource('upload-mobile-app', UploadMobileAppController::class);
        // Route::resource('tools-master', ToolsMasterController::class);

        Route::get('/tools-master', [ToolsMasterController::class, 'edit'])
            ->name('tools-master.edit');

        Route::put('/tools-master', [ToolsMasterController::class, 'update'])
            ->name('tools-master.update');


        Route::resource('applications', ApplicationController::class);
        Route::resource('modules', ModuleController::class);

        // Settings 
        Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {

            Route::get('/', [SettingsController::class, 'index'])->name('index');

            Route::resource('system-settings', SystemSettingController::class)->except(['create', 'edit', 'show']);
        });
    });

    // db inspector
    Route::middleware('role:admin,db_inspector')->group(function () {
        Route::middleware([DatabaseConnectionMiddleware::class])->group(function () {
            Route::get('/inspect-database', [DatabaseInspectorController::class, 'inspect'])->name('inspect-database');
            Route::get('/resources/views/view.blade.php', [DatabaseInspectorController::class, 'inspect']);
        });

        Route::get('/db-settings', [DatabaseInspectorController::class, 'showSettings'])->name('db-settings');
        Route::post('/db-settings', [DatabaseInspectorController::class, 'connect']);
        Route::post('/db-disconnect', [DatabaseInspectorController::class, 'disconnect'])->name('db-disconnect');
        Route::get('/inspect-database/clients/{app_name}', [DatabaseInspectorController::class, 'getClientsApiProxy'])->name('inspect-database.clients-proxy');
    });
});
