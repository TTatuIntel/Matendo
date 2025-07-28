<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HealthworkerController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\IndividualRequestController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\TempAccessController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminSettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home route
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Temporary Access Routes (Signed URLs - No Login Required)
|--------------------------------------------------------------------------
*/

// Temporary signed dashboard route (NO login required)
Route::get('/temp-access/{id}', [TempAccessController::class, 'view'])
    ->name('temp.access')
    ->middleware('signed');

Route::post('/temp-access/upload', [TempAccessController::class, 'tempUpload'])
    ->name('temp.upload');

// Document viewing with temporary access
Route::get('/documents/{document}/view', [DocumentController::class, 'view'])
    ->name('documents.view')
    ->middleware('temp.access');

Route::get('/documents/{document}/download', [DocumentController::class, 'download'])
    ->name('documents.download')
    ->middleware('temp.access');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    // User dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['user'])->name('dashboard');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Health Records
    Route::prefix('health-records')->name('health-records.')->group(function () {
        Route::get('/upload', [HealthRecordController::class, 'showUploadForm'])->name('upload');
        Route::get('/display', [HealthRecordController::class, 'display'])->name('display');
        Route::post('/', [HealthRecordController::class, 'store'])->name('store');
        Route::delete('/{id}', [HealthRecordController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/download', [HealthRecordController::class, 'downloadDocument'])->name('download');
        Route::get('/{id}/view', [HealthRecordController::class, 'viewDocument'])->name('view');
    });

    // Legacy health records routes (for backward compatibility)
    Route::get('/upload', [HealthRecordController::class, 'showUploadForm'])->name('upload');
    Route::get('/display', [HealthRecordController::class, 'display'])->name('display');
    Route::post('/medical-records', [HealthRecordController::class, 'store'])->name('medical-records.store');
    Route::delete('/medical-records/{id}', [HealthRecordController::class, 'destroy'])->name('medical-records.destroy');

    // Documents
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::post('/upload', [HealthRecordController::class, 'uploadDocuments'])->name('upload');
        Route::get('/view/{id}', [HealthRecordController::class, 'viewDocument'])->name('view');
        Route::get('/{id}/download', [HealthRecordController::class, 'downloadDocument'])->name('download');
    });

    // Generate temporary access links (requires login)
    Route::post('/users/{id}/generate-temp-link', [TempAccessController::class, 'generateLink'])
        ->name('users.generate-temp-link');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/download', [ReportController::class, 'download'])->name('download');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
    });

    // Task completion routes accessible to different user types
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete-general');
    Route::patch('/tasks/{taskId}/complete', [TaskController::class, 'completeTask'])->name('tasks.complete-by-id');
    Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete-post');

    // Health Worker Resource Routes
    Route::resource('health-workers', HealthworkerController::class);
    Route::get('/health-workers', [HealthworkerController::class, 'getHealthWorkers'])->name('health-workers.getHealthWorkers');
    Route::post('/health-workers/{id}/toggle-verification', [HealthworkerController::class, 'toggleVerification'])->name('health-workers.toggle-verification');
    Route::get('/healthworkers/{id}', [HealthworkerController::class, 'show'])->name('healthworkers.show');

    // Admin Settings & User Management (requires auth but not admin prefix here for compatibility)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
        Route::put('/settings/update', [AdminSettingsController::class, 'updateSettings'])->name('settings.update');
        
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/create', [AdminSettingsController::class, 'createUser'])->name('create');
            Route::post('/store', [AdminSettingsController::class, 'storeUser'])->name('store');
            Route::get('/{user}/edit', [AdminSettingsController::class, 'editUser'])->name('edit');
            Route::put('/{user}/update', [AdminSettingsController::class, 'updateUser'])->name('update');
            Route::delete('/{user}/destroy', [AdminSettingsController::class, 'destroyUser'])->name('destroy');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Task Routes (Global - needed for tasks.getTasks)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Global task routes that need to be accessible
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/get-tasks', [TaskController::class, 'getTasks'])->name('tasks.getTasks');
    Route::post('/tasks/{task}/approve', [TaskController::class, 'approve'])->name('tasks.approve');
    Route::post('/tasks/{task}/reject', [TaskController::class, 'reject'])->name('tasks.reject');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard/{tab?}', [AdminController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Application Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('applications')->name('applications.')->group(function () {
        // Main application management
        Route::get('/', [ApplicationController::class, 'index'])->name('index');
        Route::post('/process', [ApplicationController::class, 'process'])->name('process');
        Route::put('/{application}', [ApplicationController::class, 'update'])->name('update');
        
        // Application details for modal (AJAX endpoint)
        Route::get('/{id}/details', [ApplicationController::class, 'getDetails'])
            ->name('details')
            ->where('id', '[0-9]+');
        
        // Document viewing and downloading
        Route::get('/{id}/document/{documentType}', [ApplicationController::class, 'viewDocument'])
            ->name('document.view')
            ->where(['id' => '[0-9]+', 'documentType' => 'resume|license|certifications']);
            
        Route::get('/{id}/document/{documentType}/download', [ApplicationController::class, 'downloadDocument'])
            ->name('document.download')
            ->where(['id' => '[0-9]+', 'documentType' => 'resume|license|certifications']);
        
        // Export applications to CSV
        Route::get('/export', [ApplicationController::class, 'export'])->name('export');
        
        // Legacy application routes (kept for compatibility)
        Route::get('/{application}/{type}/download', [ApplicationController::class, 'download'])->name('download');
        Route::post('/{application}/regenerate-password', [ApplicationController::class, 'regeneratePassword'])->name('regenerate-password');
        Route::get('/{application}/redownload-pdf', [ApplicationController::class, 'redownloadPdf'])->name('redownload-pdf');
        Route::get('/{application}/snapshot-pdf', [ApplicationController::class, 'snapshotPdf'])->name('snapshot-pdf');
        Route::get('/{application}/credentials', [ApplicationController::class, 'getCredentials'])->name('credentials');
        Route::post('/{application}/resend-credentials', [ApplicationController::class, 'resendCredentials'])->name('resend-credentials');
        Route::post('/bulk-process', [ApplicationController::class, 'bulkProcess'])->name('bulk-process');
        
        // Password regeneration with reference number
        Route::post('/{reference_number}/regenerate-password', [ApplicationController::class, 'regeneratePassword'])
            ->name('regenerate-password-ref')
            ->where('reference_number', '[A-Z0-9-]+');
    });

    /*
    |--------------------------------------------------------------------------
    | Facility Request Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('facility')->name('facility.')->group(function () {
        Route::get('/', [RequestController::class, 'getFacilityRequests'])->name('index');
        Route::get('/view', [RequestController::class, 'index'])->name('view');
        Route::get('/{facilityRequest}', [RequestController::class, 'show'])->name('show');
        Route::post('/{facilityRequest}/approve', [RequestController::class, 'approve'])->name('approve');
        Route::post('/{facilityRequest}/reject', [RequestController::class, 'reject'])->name('reject');
    });

    /*
    |--------------------------------------------------------------------------
    | Individual Request Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('individual')->name('individual.')->group(function () {
        Route::get('/', [IndividualRequestController::class, 'getIndividualRequests'])->name('index');
        Route::get('/view', [IndividualRequestController::class, 'index'])->name('view');
        Route::get('/{individualRequest}', [IndividualRequestController::class, 'show'])->name('show');
        Route::post('/{individualRequest}/approve', [IndividualRequestController::class, 'approve'])->name('approve');
        Route::post('/{individualRequest}/reject', [IndividualRequestController::class, 'reject'])->name('reject');
    });

    /*
    |--------------------------------------------------------------------------
    | Task Management (Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'getTasks'])->name('index');
        Route::get('/view', [TaskController::class, 'index'])->name('view');
        Route::get('/get-tasks', [TaskController::class, 'getTasks'])->name('get-tasks');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::post('/{task}/approve', [TaskController::class, 'approve'])->name('approve');
        Route::post('/{task}/reject', [TaskController::class, 'reject'])->name('reject');
        Route::post('/{task}/assign', [TaskController::class, 'assign'])->name('assign');
        Route::patch('/{task}/complete', [TaskController::class, 'complete'])->name('complete');
        Route::post('/{task}/complete', [TaskController::class, 'complete'])->name('complete-post');
    });

    /*
    |--------------------------------------------------------------------------
    | Health Worker Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('healthworkers')->name('healthworkers.')->group(function () {
        Route::get('/', [AdminController::class, 'healthworkers'])->name('index');
        Route::get('/get-health-workers', [HealthworkerController::class, 'getHealthWorkers'])->name('get-health-workers');
        Route::get('/{id}', [HealthworkerController::class, 'show'])->name('show');
        Route::post('/{id}/toggle-verification', [HealthworkerController::class, 'toggleVerification'])->name('toggle-verification');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Settings & User Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminSettingsController::class, 'index'])->name('index');
        Route::put('/update', [AdminSettingsController::class, 'updateSettings'])->name('update');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/create', [AdminSettingsController::class, 'createUser'])->name('create');
        Route::post('/store', [AdminSettingsController::class, 'storeUser'])->name('store');
        Route::get('/{user}/edit', [AdminSettingsController::class, 'editUser'])->name('edit');
        Route::put('/{user}/update', [AdminSettingsController::class, 'updateUser'])->name('update');
        Route::delete('/{user}/destroy', [AdminSettingsController::class, 'destroyUser'])->name('destroy');
    });

    // Legacy routes (kept for compatibility) - These provide the missing route names
    Route::get('/facility', [RequestController::class, 'getFacilityRequests'])->name('facility');
    Route::get('/individual', [IndividualRequestController::class, 'getIndividualRequests'])->name('individual');
    Route::get('/tasks', [TaskController::class, 'getTasks'])->name('tasks');
    Route::get('/healthworkers', [AdminController::class, 'healthworkers'])->name('healthworkers');
});

/*
|--------------------------------------------------------------------------
| Additional Admin Routes (Outside prefix for compatibility)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {
    // Additional facility routes
    Route::get('/admin/facility', [RequestController::class, 'getFacilityRequests'])->name('admin.facility');
    Route::get('/admin/facility/{facilityRequest}', [RequestController::class, 'show'])->name('admin.facility.show');
    Route::post('/admin/facility/{facilityRequest}/approve', [RequestController::class, 'approve'])->name('admin.facility.approve');
    Route::post('/admin/facility/{facilityRequest}/reject', [RequestController::class, 'reject'])->name('admin.facility.reject');
    Route::get('/admin/facility/view', [RequestController::class, 'index'])->name('admin.facility.view');

    // Additional individual routes
    Route::get('/admin/individual', [IndividualRequestController::class, 'getIndividualRequests'])->name('admin.individual');
    Route::get('/admin/individual/view', [IndividualRequestController::class, 'index'])->name('admin.individual.view');
    Route::get('/admin/individual/{individualRequest}', [IndividualRequestController::class, 'show'])->name('admin.individual.show');
    Route::post('/admin/individual/{individualRequest}/approve', [IndividualRequestController::class, 'approve'])->name('admin.individual.approve');
    Route::post('/admin/individual/{individualRequest}/reject', [IndividualRequestController::class, 'reject'])->name('admin.individual.reject');

    // Additional task routes
    Route::get('/admin/tasks', [TaskController::class, 'getTasks'])->name('admin.tasks');
    Route::get('/admin/tasks/view', [TaskController::class, 'index'])->name('admin.tasks.view');
    Route::get('/admin/tasks/{task}', [TaskController::class, 'show'])->name('admin.tasks.show');
});

/*
|--------------------------------------------------------------------------
| Healthworker Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'healthworker'])->prefix('healthworker')->name('healthworker.')->group(function () {
    Route::get('/dashboard', [HealthworkerController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Include Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';