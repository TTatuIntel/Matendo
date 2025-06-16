<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HealthworkerController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\IndividualRequestController;
use App\Http\Controllers\TaskController;

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


// Healthworker routes
Route::middleware(['auth', 'healthworker'])->prefix('healthworker')->name('healthworker.')->group(function () {
    Route::get('/dashboard', [HealthworkerController::class, 'index'])->name('dashboard');
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


Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/tasks', [TaskController::class, 'getTasks'])->name('admin.tasks');
    Route::get('/admin/tasks/view', [TaskController::class, 'index'])->name('admin.tasks.view');
    Route::get('/admin/tasks/{task}', [TaskController::class, 'show'])->name('admin.tasks.show');
});

});

require __DIR__.'/auth.php';
