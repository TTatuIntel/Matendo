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

// Home route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// User dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'user'])->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    // Health Records
    Route::get('/upload', [HealthRecordController::class, 'showUploadForm'])->name('upload');
    Route::get('/display', [HealthRecordController::class, 'display'])->name('display');
    Route::post('/medical-records', [HealthRecordController::class, 'store'])->name('medical-records.store');
    Route::delete('/medical-records/{id}', [HealthRecordController::class, 'destroy'])->name('medical-records.destroy');
    Route::post('/documents/upload', [HealthRecordController::class, 'uploadDocuments'])->name('documents.upload');
    Route::get('/documents/{id}/download', [HealthRecordController::class, 'downloadDocument'])->name('documents.download');
    Route::get('/documents/{id}/view', [HealthRecordController::class, 'viewDocument'])->name('documents.view');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard/{tab?}', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::put('/applications/{application}', [ApplicationController::class, 'update'])->name('applications.update');
    Route::post('/applications/process', [ApplicationController::class, 'process'])->name('applications.process');
    Route::get('/applications/{application}/{type}/download', [ApplicationController::class, 'download'])->name('applications.download');
    Route::post('/applications/{application}/regenerate-password', [ApplicationController::class, 'regeneratePassword'])->name('applications.regenerate-password');
    Route::get('/applications/{application}/redownload-pdf', [ApplicationController::class, 'redownloadPdf'])->name('applications.redownload-pdf');
    Route::get('/applications/{application}/snapshot-pdf', [ApplicationController::class, 'snapshotPdf'])->name('applications.snapshot-pdf');
    Route::get('/applications/{application}/credentials', [ApplicationController::class, 'getCredentials'])->name('applications.credentials');
    Route::post('/applications/{application}/resend-credentials', [ApplicationController::class, 'resendCredentials'])->name('applications.resend-credentials');
    Route::post('/applications/bulk-process', [ApplicationController::class, 'bulkProcess'])->name('applications.bulk-process');
    Route::get('/facility', [AdminController::class, 'facility'])->name('facility');
    Route::get('/healthworkers', [AdminController::class, 'healthworkers'])->name('healthworkers');
    Route::get('/tasks', [AdminController::class, 'tasks'])->name('tasks');
Route::get('/admin/facility', [RequestController::class, 'index'])->name('admin.facility');
Route::get('/admin/facility', [RequestController::class, 'getFacilityRequests'])->name('admin.facility');
Route::get('/admin/facility/{facilityRequest}', [RequestController::class, 'show'])->name('admin.facility.show');
Route::post('/admin/facility/{facilityRequest}/approve', [RequestController::class, 'approve'])->name('admin.facility.approve');
Route::post('/admin/facility/{facilityRequest}/reject', [RequestController::class, 'reject'])->name('admin.facility.reject');

    Route::get('/admin/facility', [RequestController::class, 'getFacilityRequests'])->name('admin.facility');
    Route::get('/admin/facility/{facilityRequest}', [RequestController::class, 'show'])->name('admin.facility.show');
    Route::post('/admin/facility/{facilityRequest}/approve', [RequestController::class, 'approve'])->name('admin.facility.approve');
    Route::post('/admin/facility/{facilityRequest}/reject', [RequestController::class, 'reject'])->name('admin.facility.reject');
    Route::get('/admin/facility/view', [RequestController::class, 'index'])->name('admin.facility.view');
});



Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/facility', [RequestController::class, 'getFacilityRequests'])->name('admin.facility');
    Route::get('/admin/facility/{facilityRequest}', [RequestController::class, 'show'])->name('admin.facility.show');
    Route::post('/admin/facility/{facilityRequest}/approve', [RequestController::class, 'approve'])->name('admin.facility.approve');
    Route::post('/admin/facility/{facilityRequest}/reject', [RequestController::class, 'reject'])->name('admin.facility.reject');
    Route::get('/admin/facility/view', [RequestController::class, 'index'])->name('admin.facility.view');


// Individual Requests
    Route::get('/admin/individual', [IndividualRequestController::class, 'getIndividualRequests'])->name('admin.individual');
    Route::get('/admin/individual/view', [IndividualRequestController::class, 'index'])->name('admin.individual.view');
    Route::get('/admin/individual/{individualRequest}', [IndividualRequestController::class, 'show'])->name('admin.individual.show');
    Route::post('/admin/individual/{individualRequest}/approve', [IndividualRequestController::class, 'approve'])->name('admin.individual.approve');
    Route::post('/admin/individual/{individualRequest}/reject', [IndividualRequestController::class, 'reject'])->name('admin.individual.reject');


    Route::get('/admin/tasks', [TaskController::class, 'getTasks'])->name('admin.tasks');
    Route::get('/admin/tasks/view', [TaskController::class, 'index'])->name('admin.tasks.view');
    Route::get('/admin/tasks/{task}', [TaskController::class, 'show'])->name('admin.tasks.show');


    // Route::resource('tasks', TaskController::class);
    // Route::get('/tasks/get-tasks', [TaskController::class, 'getTasks'])->name('tasks.getTasks');
    // Route::post('/tasks/{id}/approve', [TaskController::class, 'approve'])->name('tasks.approve');
    // Route::post('/tasks/{id}/reject', [TaskController::class, 'reject'])->name('tasks.reject');

 // Tasks routes
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/get-tasks', [TaskController::class, 'getTasks'])->name('tasks.getTasks');
    Route::post('/tasks/{task}/approve', [TaskController::class, 'approve'])->name('tasks.approve');
    Route::post('/tasks/{task}/reject', [TaskController::class, 'reject'])->name('tasks.reject');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');
    Route::patch('/tasks/{task}/complete', [App\Http\Controllers\TaskController::class, 'complete'])->name('tasks.complete');


// Add this route to your routes/web.php file
Route::patch('/tasks/{taskId}/complete', [TaskController::class, 'completeTask'])
    ->name('tasks.complete')
    ->middleware('auth');

Route::resource('health-workers', HealthWorkerController::class);
Route::post('/health-workers/{id}/toggle-verification', [HealthWorkerController::class, 'toggleVerification'])->name('health-workers.toggle-verification');

});


// Healthworker routes
Route::middleware(['auth', 'healthworker'])->prefix('healthworker')->name('healthworker.')->group(function () {
    Route::get('/dashboard', [HealthworkerController::class, 'index'])->name('dashboard');
});

Route::get('/healthworkers/{id}', [HealthWorkerController::class, 'show'])->name('healthworkers.show');


Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])
    ->name('tasks.complete')
    ->middleware('auth');



// ✅ Temporary signed dashboard route (NO login required)
Route::get('/temp-access/{id}', [TempAccessController::class, 'view'])
    ->name('temp.access')
    ->middleware('signed'); // ✅ Only 'signed', NOT 'auth'

// ✅ Route to generate signed link (requires login)
Route::post('/users/{id}/generate-temp-link', [TempAccessController::class, 'generateLink'])
    ->name('users.generate-temp-link')
    ->middleware('auth');


Route::post('/temp-access/upload', [TempAccessController::class, 'tempUpload'])
    ->name('temp.upload');

// In routes/web.php
Route::get('/documents/{document}/view', [DocumentController::class, 'view'])
    ->name('documents.view')
    ->middleware('temp.access');

Route::get('/documents/{document}/download', [DocumentController::class, 'download'])
    ->name('documents.download')
    ->middleware('temp.access');

Route::get('/documents/view/{id}', [HealthRecordController::class, 'viewDocument'])->name('documents.view');

Route::get('/reports/download', [ReportController::class, 'download'])->name('reports.download');
Route::prefix('reports')->group(function() {
    Route::get('/export', [ReportController::class, 'export'])->name('reports.export');
});


// Add this line to your routes/web.php
Route::get('/health-workers', [HealthworkerController::class, 'getHealthWorkers'])->name('health-workers.getHealthWorkers');

Route::middleware(['auth'])->group(function () {
    // Admin routes
    Route::prefix('admin')->group(function () {
        // Settings routes
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
        Route::put('/settings/update', [AdminSettingsController::class, 'updateSettings'])->name('admin.settings.update');

        // User management routes
        Route::prefix('users')->group(function () {
            Route::get('/create', [AdminSettingsController::class, 'createUser'])->name('admin.users.create');
            Route::post('/store', [AdminSettingsController::class, 'storeUser'])->name('admin.users.store');
            Route::get('/{user}/edit', [AdminSettingsController::class, 'editUser'])->name('admin.users.edit');
            Route::put('/{user}/update', [AdminSettingsController::class, 'updateUser'])->name('admin.users.update');
            Route::delete('/{user}/destroy', [AdminSettingsController::class, 'destroyUser'])->name('admin.users.destroy');
        });
    });
});
Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
require __DIR__.'/auth.php';
