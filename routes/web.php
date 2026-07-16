<?php

use App\Http\Controllers\AccountAdvanceController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdvanceRequestController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\Ats\AtsClientController;
use App\Http\Controllers\Ats\AtsClientSysConfigController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceLocationController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CompOffController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerJobController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DayShiftScheduleController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\Lms\LmsClientController;
use App\Http\Controllers\Lms\LmsClientSysConfigController;
use App\Http\Controllers\ManagerAdvanceController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\OnDutyController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PunchAuditReportController;
use App\Http\Controllers\RecruiterAssignmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShiftScheduleController;
use App\Http\Controllers\SourcingReportController;
use App\Http\Controllers\SubActivityController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\TimesheetApprovalController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\ToolsMasterController;
use App\Http\Controllers\UploadMobileAppController;
use App\Http\Controllers\UserAttendanceLocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkFromHomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AssetAllocationController;
use App\Http\Controllers\AssetRepairController;
use App\Http\Controllers\SIMRechargeController;
use App\Http\Controllers\AssetDocumentController;

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
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard, Profile, Logout
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    Route::get('/profile', [DashboardController::class, 'index'])
        ->name('profile');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');


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
    | Attendance Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth')->group(function () {

        Route::post('/attendance/punch', [AttendanceController::class, 'punch'])->name('attendance.punch');

        Route::get('/manager/attendance/requests', [AttendanceController::class, 'managerRequests'])->name('manager.attendance.requests');

        Route::post('/manager/attendance/{id}/process', [AttendanceController::class, 'managerProcess'])->name('manager.attendance.process');

        Route::resource('attendance', AttendanceController::class);

        Route::resource('holidays', HolidayController::class);

        Route::prefix('attendance-reports/punch-report')->name('attendance-reports.punch-report.')->group(function () {

            Route::get('/', [PunchAuditReportController::class, 'index'])
                ->name('index');

            Route::get('/export', [PunchAuditReportController::class, 'export'])
                ->name('export');
        });
    });

    Route::middleware(['auth', 'role:admin'])->group(function () {

        Route::resource('attendance-locations', AttendanceLocationController::class);

        Route::resource('user-attendance-locations', UserAttendanceLocationController::class);

        Route::resource('settings/shift-schedule', ShiftScheduleController::class);
        Route::resource('settings/day-shift-schedule', DayShiftScheduleController::class);

        Route::post('/leave-requests/{id}/approved', [LeaveRequestController::class, 'approved'])
            ->name('leave-requests.approved');

        Route::post('/leave-requests/{id}/rejected', [LeaveRequestController::class, 'rejected'])
            ->name('leave-requests.rejected');

        Route::post('/leave-requests/{id}/cancel', [LeaveRequestController::class, 'cancel'])
            ->name('leave-requests.cancel');

        Route::get('/leave-requests/manager', [LeaveRequestController::class, 'managerIndex'])
            ->name('leave-requests.manager');

        Route::resource('leave-types', LeaveTypeController::class);
        Route::resource('leave-requests', LeaveRequestController::class);
    });

    Route::middleware('auth')->group(function () {

        /* on duty routes */
        Route::get(
            '/on-duty/',
            [OnDutyController::class, 'index']
        )->name('onduty.index');

        Route::get(
            '/on-duty/create',
            [OnDutyController::class, 'create']
        )->name('onduty.create');

        Route::post(
            '/on-duty',
            [OnDutyController::class, 'store']
        )->name('onduty.store');

        Route::get(
            '/on-duty/{id}/edit',
            [OnDutyController::class, 'edit']
        )->name('onduty.edit');

        Route::put(
            '/on-duty/{id}',
            [OnDutyController::class, 'update']
        )->name('onduty.update');


        /* compoff routes */
        Route::get(
            '/comp-off/',
            [CompOffController::class, 'index']
        )->name('compoff.index');

        Route::get(
            '/comp-off/create',
            [CompOffController::class, 'create']
        )->name('compoff.create');

        Route::post(
            '/comp-off',
            [CompOffController::class, 'store']
        )->name('compoff.store');

        Route::get(
            '/comp-off/{id}/edit',
            [CompOffController::class, 'edit']
        )->name('compoff.edit');

        Route::put(
            '/comp-off/{id}',
            [CompOffController::class, 'update']
        )->name('compoff.update');


        /* work from home routes */
        Route::get(
            '/wfh/',
            [WorkFromHomeController::class, 'index']
        )->name('wfh.index');

        Route::get(
            '/wfh/create',
            [WorkFromHomeController::class, 'create']
        )->name('wfh.create');

        Route::post(
            '/wfh',
            [WorkFromHomeController::class, 'store']
        )->name('wfh.store');

        Route::get(
            '/wfh/{id}/edit',
            [WorkFromHomeController::class, 'edit']
        )->name('wfh.edit');

        Route::put(
            '/wfh/{id}',
            [WorkFromHomeController::class, 'update']
        )->name('wfh.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Asset Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,accounts, manager')->group(function () {
        Route::resource('asset', AssetController::class);
        Route::resource('asset_categories', AssetCategoryController::class)->parameters(['asset_categories' => 'assetcategory']);
        Route::resource('vendors', VendorController::class);
        Route::resource('asset-allocations', AssetAllocationController::class)->parameters(['asset-allocations' => 'allocation']);
        Route::resource('asset-repairs', AssetRepairController::class)->parameters(['asset-repairs' => 'repair']);
        Route::get('asset-repairs/history/{asset_id}', [AssetRepairController::class, 'history'])->name('asset-repairs.history');
        Route::resource('sim-recharges', SIMRechargeController::class);
        Route::get('asset-documents/download/{document}', [AssetDocumentController::class, 'download'])->name('asset-documents.download');
        Route::get('asset-documents/view/{document}', [AssetDocumentController::class, 'view'])->name('asset-documents.view');
        Route::resource('asset-documents', AssetDocumentController::class)->parameters(['asset-documents' => 'document']);
    });

    /*
    |--------------------------------------------------------------------------
    | Timesheets -> My Timesheets Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,manager,office admin,it admin,hr,accounts,sourcing,sales,contractor,employee')->group(function () {

        Route::get('/timesheets/previous-week', [TimesheetController::class, 'previousWeek'])->name('timesheets.previous-week');

        Route::post('/timesheets/{timesheet}/submit', [TimesheetController::class, 'submit'])->name('timesheets.submit');

        Route::resource('timesheets', TimesheetController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Timesheets -> Manager Approvals Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,manager')->group(function () {

        // Route::get('/approvals', [TimesheetApprovalController::class, 'index'])
        //     ->name('approvals.index');

        // Route::post('/approvals/{timesheet}/approve', [TimesheetApprovalController::class, 'approve'])
        //     ->name('approvals.approve');

        // Route::post('/approvals/{timesheet}/reject', [TimesheetApprovalController::class, 'reject'])
        //     ->name('approvals.reject');

        Route::prefix('timesheet-approvals')
            ->name('timesheet-approvals.')
            ->group(function () {

                Route::get('/', [TimesheetApprovalController::class, 'index'])
                    ->name('index');

                Route::get('/{timesheet}', [TimesheetApprovalController::class, 'show'])
                    ->name('show');

                Route::post('/{timesheet}/approve', [TimesheetApprovalController::class, 'approve'])
                    ->name('approve');

                Route::post('/{timesheet}/reject', [TimesheetApprovalController::class, 'reject'])
                    ->name('reject');
            });
    });

    /*
    |--------------------------------------------------------------------------
    | Timesheets -> Reports Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    /*
    |--------------------------------------------------------------------------
    | Timesheets -> Setting Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        // Project
        Route::resource('projects', ProjectController::class)
            ->except(['create', 'edit', 'show', 'destroy']);

        // Activity
        Route::resource('activities', ActivityController::class)
            ->except(['create', 'edit', 'show', 'destroy']);

        // Sub Activity
        Route::resource('sub-activities', SubActivityController::class)
            ->except(['create', 'edit', 'show', 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Call Review Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {

        // Call Review
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

        Route::get('/reviews/export-excel', [ReviewController::class, 'exportExcel'])->name('reviews.export.excel');

        Route::get('/reviews/export-pdf', [ReviewController::class, 'exportPdf'])->name('reviews.export.pdf');

        Route::get('/reviews/history/{id}', [ReviewController::class, 'history'])
            ->name('reviews.history');

        Route::post('/reviews/save-note', [ReviewController::class, 'saveNote'])->name('reviews.saveNote');

        // Settings 
        Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {

            Route::get('/', [SettingsController::class, 'index'])->name('index');

            Route::resource('system-settings', SystemSettingController::class)->except(['create', 'edit', 'show']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Tools Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {

        /* ---------- LMS Clients ---------- */
        Route::prefix('lms')->name('lms.')->group(function () {

            Route::resource('clients', LmsClientController::class);

            Route::get(
                'clients/{client}/sys-config/create',
                [LmsClientSysConfigController::class, 'create']
            )->name('clientsSysConfigs.create');

            Route::post(
                'clients/{client}/sys-config',
                [LmsClientSysConfigController::class, 'store']
            )->name('clientsSysConfigs.store');

            Route::get(
                'clients/{client}/sys-config/edit',
                [LmsClientSysConfigController::class, 'edit']
            )->name('clientsSysConfigs.edit');

            Route::put(
                'clients/{client}/sys-config',
                [LmsClientSysConfigController::class, 'update']
            )->name('clientsSysConfigs.update');

            Route::delete(
                'clients/{client}/sys-config',
                [LmsClientSysConfigController::class, 'destroy']
            )->name('clientsSysConfigs.destroy');
        });

        /* ---------- ATS Clients ---------- */
        Route::prefix('ats')->name('ats.')->group(function () {

            Route::resource('clients', AtsClientController::class);

            Route::get(
                'clients/{client}/sys-config/create',
                [AtsClientSysConfigController::class, 'create']
            )->name('clientsSysConfigs.create');

            Route::post(
                'clients/{client}/sys-config',
                [AtsClientSysConfigController::class, 'store']
            )->name('clientsSysConfigs.store');

            Route::get(
                'clients/{client}/sys-config/edit',
                [AtsClientSysConfigController::class, 'edit']
            )->name('clientsSysConfigs.edit');

            Route::put(
                'clients/{client}/sys-config',
                [AtsClientSysConfigController::class, 'update']
            )->name('clientsSysConfigs.update');

            Route::delete(
                'clients/{client}/sys-config',
                [AtsClientSysConfigController::class, 'destroy']
            )->name('clientsSysConfigs.destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Sourcing Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,sourcing')->group(function () {

        // Customer Route
        Route::post('customers/filter', [CustomerController::class, 'filter'])
            ->name('customers.filter');

        Route::get('customers-reset', function () {

            session()->forget('customer_filters');

            return redirect()->route('customers.index');
        })->name('customers.reset');

        Route::get('customers-export', [CustomerController::class, 'export'])
            ->name('customers.export');

        Route::resource('customers', CustomerController::class);


        // Customer Job Route
        Route::post('customer-jobs/filter', [CustomerJobController::class, 'filter'])
            ->name('customer-jobs.filter');

        Route::get('customer-jobs-reset', function () {

            session()->forget('customer_job_filters');

            return redirect()->route('customer-jobs.index');
        })->name('customer-jobs.reset');

        Route::get('customer-jobs-export', [CustomerJobController::class, 'export'])
            ->name('customer-jobs.export');

        Route::resource('customer-jobs', CustomerJobController::class);


        // Candidates Route
        Route::post('candidates/filter', [CandidateController::class, 'filter'])
            ->name('candidates.filter');

        Route::get('candidates-reset', function () {

            session()->forget('candidate_filters');

            return redirect()->route('candidates.index');
        })->name('candidates.reset');

        Route::resource('candidates', CandidateController::class);

        Route::get('candidates-export', [CandidateController::class, 'export'])
            ->name('candidates.export');


        // Sourcing Reports Routes
        Route::get('/reports/sourcing', [SourcingReportController::class, 'index'])
            ->name('reports.sourcing');

        Route::get(
            '/reports/sourcing/export',
            [SourcingReportController::class, 'export']
        )->name('reports.sourcing.export');

        // Job Assignment 
        Route::get(
            '/recruiter-assignments',
            [RecruiterAssignmentController::class, 'index']
        )->name('recruiter-assignments.index');

        Route::post(
            '/recruiter-assignments/toggle',
            [RecruiterAssignmentController::class, 'toggle']
        )->name('recruiter-assignments.toggle');

        Route::get(
            '/my-assignments',
            [RecruiterAssignmentController::class, 'myAssignments']
        )->name('my-assignments.index');


        // Audit Routes
        Route::get('audit', [AuditController::class, 'index'])->name('audit.index');

        Route::post('audit/filter', [AuditController::class, 'filter'])->name('audit.filter');

        Route::get('audit-reset', function () {

            session()->forget('audit_filters');

            return redirect()->route('audit.index');
        })->name('audit.reset');

        Route::get('audit-export', [AuditController::class, 'export'])
            ->name('audit.export');

        Route::get('/audit/data', [AuditController::class, 'getData']);
    });

    /*
    |--------------------------------------------------------------------------
    | Sales Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,sales,tender executive')->group(function () {

        Route::post('sales/filter', [SaleController::class, 'filter'])
            ->name('sales.filter');

        Route::get('sales', [SaleController::class, 'index'])
            ->name('sales.index');

        Route::get('sales/reset', function () {

            session()->forget('sale_filters');

            return redirect()->route('sales.index');
        })->name('sales.reset');

        Route::resource('sales', SaleController::class)
            ->except('index');

        Route::get(
            'sales-export',
            [SaleController::class, 'export']
        )->name('sales.export');


        // Tender Route
        Route::post('tenders/filter', [TenderController::class, 'filter'])
            ->name('tenders.filter');

        Route::get('tenders/reset', [TenderController::class, 'reset'])
            ->name('tenders.reset');

        Route::get('tenders/reset', function () {

            session()->forget('tender_filters');

            return redirect()->route('tenders.index');
        })->name('tenders.reset');

        Route::get('/tenders/export', [TenderController::class, 'export'])
            ->name('tenders.export');

        Route::resource('tenders', TenderController::class);


        // Sales Report
        Route::get(
            '/reports/sales',
            [SalesReportController::class, 'index']
        )->name('reports.sales');

        Route::get(
            '/reports/sales/export',
            [SalesReportController::class, 'export']
        )->name('reports.sales.export');
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
    });
});

/*
|--------------------------------------------------------------------------
| EXPENSE MODULE ROUTES
|--------------------------------------------------------------------------
*/
// 1. Employee Expense Requests
Route::middleware(['auth'])->group(function () {

    Route::resource('expenses', ExpenseController::class);

    Route::get('/expenses/{expense}/items', [ExpenseController::class, 'itemsIndex'])->name('expenses.items.index');
    Route::get('/expenses/{expense}/items/{item}/edit', [ExpenseController::class, 'itemsIndex'])->name('expenses.items.edit');
    Route::post('/expenses/{expense}/items', [ExpenseController::class, 'storeItem'])->name('expenses.items.store');
    Route::put('/expenses/{expense}/items/{item}', [ExpenseController::class, 'updateItem'])->name('expenses.items.update');
    Route::delete('/expenses/{expense}/items/{item}', [ExpenseController::class, 'deleteItem'])->name('expenses.items.destroy');

    Route::post('/expenses/{expense}/submit', [ExpenseController::class, 'submit'])->name('expenses.submit');
    Route::get('/expenses/form')->name('expenses.form');
});

// 2. Manager Expense Approvals
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('/manager/requests', [ManagerController::class, 'requests'])->name('manager.requests');
    Route::get('/manager/request/{id}', [ManagerController::class, 'show'])->name('manager.show');
    Route::post('/manager/approve/{id}', [ManagerController::class, 'approve'])->name('manager.approve');
    Route::post('/manager/reject/{id}', [ManagerController::class, 'reject'])->name('manager.reject');
});

// 3. Accounts Expense Processing
Route::middleware(['auth', 'role:admin,accounts'])->group(function () {
    Route::get('/accounts/requests', [AccountController::class, 'requests'])->name('accounts.requests');
    Route::get('/account/requests', [AccountController::class, 'requests'])->name('account.requests');
    Route::get('/account/process/{id}', [AccountController::class, 'showProcess'])->name('account.showProcess');
    Route::post('/account/process/{id}', [AccountController::class, 'process'])->name('account.process');
});

// 4. Expense Category
Route::middleware(['auth', 'role:admin,accounts,manager'])->group(function () {
    Route::resource('expense-categories', ExpenseCategoryController::class);
});

/*
|--------------------------------------------------------------------------
| ADVANCE MODULE ROUTES
|--------------------------------------------------------------------------
*/
// 1. Employee Advance Requests & Items
Route::middleware(['auth'])->group(function () {
    Route::resource('advances', AdvanceRequestController::class);
    Route::get('/advances/{advance}/items', [AdvanceRequestController::class, 'itemsIndex'])->name('advances.items.index');
    Route::get('/advances/{advance}/items/{item}/edit', [AdvanceRequestController::class, 'itemsIndex'])->name('advances.items.edit');
    Route::post('/advances/{advance}/items', [AdvanceRequestController::class, 'storeItem'])->name('advances.items.store');
    Route::put('/advances/{advance}/items/{item}', [AdvanceRequestController::class, 'updateItem'])->name('advances.items.update');
    Route::delete('/advances/{advance}/items/{item}', [AdvanceRequestController::class, 'deleteItem'])->name('advances.items.destroy');
    Route::post('/advances/{advance}/submit', [AdvanceRequestController::class, 'submit'])->name('advances.submit');
});

// 2. Manager Advance Approvals
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('/manager/advances', [ManagerAdvanceController::class, 'index'])->name('manager.advances.index');
    Route::get('/manager/advances/{advance}', [ManagerAdvanceController::class, 'show'])->name('manager.advances.show');
    Route::post('/manager/advances/approve/{advance}', [ManagerAdvanceController::class, 'approve'])->name('manager.advances.approve');
    Route::post('/manager/advances/reject/{advance}', [ManagerAdvanceController::class, 'reject'])->name('manager.advances.reject');
});

// 3. Accounts Advance Processing
Route::middleware(['auth', 'role:admin,accounts'])->group(function () {
    Route::get('/accounts/advances', [AccountAdvanceController::class, 'requests'])->name('accounts.advances.requests');
    Route::get('/account/advances', [AccountAdvanceController::class, 'requests'])->name('account.advances.requests');
    Route::get('/accounts/advances/process/{advance}', [AccountAdvanceController::class, 'showProcess'])->name('accounts.advances.showProcess');
    Route::post('/accounts/advances/process/{advance}', [AccountAdvanceController::class, 'process'])->name('accounts.advances.process');
});

/*
|--------------------------------------------------------------------------
| MANAGER ATTENDANCE APPROVALS (COMP OFF, WFH, ON DUTY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    /* Manager Comp Off Routes */
    Route::get('/manager/compoff/requests', [CompOffController::class, 'managerRequests'])->name('manager.compoff.requests');
    Route::post('/manager/compoff/{id}/process', [CompOffController::class, 'managerProcess'])->name('manager.compoff.process');

    /* Manager Work From Home Routes */
    Route::get('/manager/wfh/requests', [WorkFromHomeController::class, 'managerRequests'])->name('manager.wfh.requests');
    Route::post('/manager/wfh/{id}/process', [WorkFromHomeController::class, 'managerProcess'])->name('manager.wfh.process');

    /* Manager On Duty Routes */
    Route::get('/manager/onduty/requests', [OnDutyController::class, 'managerRequests'])->name('manager.onduty.requests');
    Route::post('/manager/onduty/{id}/process', [OnDutyController::class, 'managerProcess'])->name('manager.onduty.process');
});
