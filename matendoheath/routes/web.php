<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HealthRecordController;

Route::get('/', function () {
    return view('welcome');
});

// Route::middleware(['auth', 'verified'])->group(function () {
//     // Dashboard
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');

//     // Health Records
//     Route::get('/upload', [HealthRecordController::class, 'showUpload'])->name('upload');
//     Route::get('/display', [HealthRecordController::class, 'showDisplay'])->name('display');
//    Route::post('/medical-records', [HealthRecordController::class, 'store'])->name('medical-records.store');

//     // Profile Routes
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->name('dashboard');

// Route::get('/upload', [HealthRecordController::class, 'showUpload'])->name('upload');

// Route::get('/display', [HealthRecordController::class, 'showDisplay'])->name('display');

// Route::post('/medical-records', [HealthRecordController::class, 'store'])->name('medical-records.store');

// Route::delete('/medical-records/{id}', [HealthRecordController::class, 'destroy'])->name('medical-records.destroy');


// Route::middleware(['auth'])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');

//     Route::get('/display', [HealthRecordController::class, 'display'])->name('display');
//     Route::get('/upload', [HealthRecordController::class, 'showUploadForm'])->name('upload');
//     Route::post('/medical-records', [HealthRecordController::class, 'store'])->name('medical-records.store');
//     Route::delete('/medical-records/{id}', [HealthRecordController::class, 'destroy'])->name('medical-records.destroy');
//     Route::post('/documents/upload', [HealthRecordController::class, 'uploadDocuments'])->name('documents.upload');
//     Route::get('/documents/{id}/download', [HealthRecordController::class, 'downloadDocument'])->name('documents.download');
//     Route::get('/documents/{id}/view', [HealthRecordController::class, 'viewDocument'])->name('documents.view');
// });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Health Records
    Route::get('/upload', [HealthRecordController::class, 'showUploadForm'])->name('upload');
    Route::get('/display', [HealthRecordController::class, 'display'])->name('display');
    Route::post('/medical-records', [HealthRecordController::class, 'store'])->name('medical-records.store');
    Route::delete('/medical-records/{id}', [HealthRecordController::class, 'destroy'])->name('medical-records.destroy');
    Route::post('/documents/upload', [HealthRecordController::class, 'uploadDocuments'])->name('documents.upload');
    Route::get('/documents/{id}/download', [HealthRecordController::class, 'downloadDocument'])->name('documents.download');
    Route::get('/documents/{id}/view', [HealthRecordController::class, 'viewDocument'])->name('documents.view');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';



