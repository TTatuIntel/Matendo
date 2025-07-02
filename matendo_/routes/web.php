<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FacilityRequestController;
use App\Http\Controllers\IndividualRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $applications = \App\Models\Application::latest()->paginate(10);
        $recentApplications = \App\Models\Application::latest()->take(5)->get();
        $applicationCount = \App\Models\Application::count();
        $pendingCount = \App\Models\Application::where('status', 'pending')->count();
        $approvedCount = \App\Models\Application::where('status', 'approved')->count();
        $rejectedCount = \App\Models\Application::where('status', 'rejected')->count();
        $pendingApprovals = \App\Models\Application::where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();
        $facilityBookings = \App\Models\FacilityRequest::latest()->paginate(10);
        $unAssignedTasksCount = \App\Models\FacilityRequest::where('status', 'pending')->count();
        $assignedCount = \App\Models\FacilityRequest::where('status', 'approved')->count();
        $individualRequests = \App\Models\IndividualRequest::latest()->paginate(10);
        $individualPendingCount = \App\Models\IndividualRequest::where('status', 'pending')->count();
        $individualApprovedCount = \App\Models\IndividualRequest::where('status', 'approved')->count();
        $individualRejectedCount = \App\Models\IndividualRequest::where('status', 'rejected')->count();
        $facilityCount = 18;
        $individualCount = 24;
        $taskCount = 36;

        return view('admin.dashboard', compact(
            'applications',
            'recentApplications',
            'applicationCount',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'pendingApprovals',
            'facilityBookings',
            'unAssignedTasksCount',
            'assignedCount',
            'individualRequests',
            'individualPendingCount',
            'individualApprovedCount',
            'individualRejectedCount',
            'facilityCount',
            'individualCount',
            'taskCount'
        ));
    })->name('dashboard');

    Route::resource('applications', ApplicationController::class);

    // Route for updating application status
    Route::patch('/applications/{id}/status', [ApplicationController::class, 'updateStatus'])->name('applications.status');

    // Route for updating individual request status
    Route::patch('/individual-requests/{individual_request}/status', [IndividualRequestController::class, 'updateStatus'])->name('individual-requests.updateStatus');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::patch('/applications/{id}/status', [ApplicationController::class, 'updateStatus'])->name('applications.updatestatus');
});

use App\Http\Controllers\TaskAssignmentController;

Route::middleware(['auth'])->group(function () {
    // Assignment routes
    Route::post('/admin/facility-requests/{id}/assign', [TaskAssignmentController::class, 'assignFacilityRequest']);
    Route::post('/admin/individual-requests/{id}/assign', [TaskAssignmentController::class, 'assignIndividualRequest']);
    Route::get('/assignments/{taskType}/{taskId}', [TaskAssignmentController::class, 'getAssignment']);
    Route::put('/assignments/{id}/status', [TaskAssignmentController::class, 'updateAssignmentStatus']);
});
require __DIR__.'/auth.php';
