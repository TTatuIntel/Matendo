<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequestController;
// use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard'); // Ensure the dashboard.blade.php view exists
})->name('dashboard');

use App\Http\Controllers\ApplicationController;

Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
Route::get('/applications/{id}/edit', [ApplicationController::class, 'edit'])->name('applications.edit');
Route::put('/applications/{id}', [ApplicationController::class, 'update'])->name('applications.update');


Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
Route::get('/requests/facility/{id}/edit', [RequestController::class, 'editFacility'])->name('requests.facility.edit');
Route::put('/requests/facility/{id}', [RequestController::class, 'updateFacility'])->name('requests.facility.update');
Route::get('/requests/individual/{id}/edit', [RequestController::class, 'editIndividual'])->name('requests.individual.edit');
Route::put('/requests/individual/{id}', [RequestController::class, 'updateIndividual'])->name('requests.individual.update');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


    // Your task assignment routes
    Route::get('/assignments', [TaskController::class, 'showAssignList'])->name('tasks.index');
    Route::get('/assign/{type}/{id}', [TaskController::class, 'assignForm'])->name('tasks.form');
    Route::post('/assign', [TaskController::class, 'assign'])->name('tasks.assign');
    // Route::get('/assignments', [TaskController::class, 'showAssignList'])->name('tasks.index');

    Route::get('/assignments/list', [TaskController::class, 'showAssignments'])->name('tasks.assignments');

require __DIR__.'/auth.php';
