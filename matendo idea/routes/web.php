<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HealthworkerController;
use App\Http\Controllers\ApplicationController;
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
    Route::post('/applications/process', [ApplicationController::class, 'process'])->name('applications.process');
    Route::get('/applications/{application}/{type}/download', [ApplicationController::class, 'download'])->name('applications.download');
    Route::post('/applications/{application}/regenerate-password', [ApplicationController::class, 'regeneratePassword'])->name('applications.regenerate-password');
    Route::get('/applications/{application}/redownload-pdf', [ApplicationController::class, 'redownloadPdf'])->name('applications.redownload-pdf');
    Route::get('/applications/{application}/snapshot-pdf', [ApplicationController::class, 'snapshotPdf'])->name('applications.snapshot-pdf');
    Route::get('/applications/{application}/credentials', [ApplicationController::class, 'getCredentials'])->name('applications.credentials');
    Route::post('/applications/{application}/resend-credentials', [ApplicationController::class, 'resendCredentials'])->name('applications.resend-credentials');
    Route::post('/applications/bulk-process', [ApplicationController::class, 'bulkProcess'])->name('applications.bulk-process'); // New route for bulk actions
    Route::get('/facility', [AdminController::class, 'facility'])->name('facility');
    Route::get('/healthworkers', [AdminController::class, 'healthworkers'])->name('healthworkers');
    Route::get('/tasks', [AdminController::class, 'tasks'])->name('tasks');
});

// Healthworker routes
Route::middleware(['auth', 'healthworker'])->prefix('healthworker')->name('healthworker.')->group(function () {
    Route::get('/dashboard', [HealthworkerController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';