<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ProfileController;
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
        // Fix: Use paginate() instead of get() for applications that need pagination
        $applications = \App\Models\Application::latest()->paginate(10);
        
        // Use get() for recent applications (no pagination needed)
        $recentApplications = \App\Models\Application::latest()->take(5)->get();
        
        // Use count() for statistics (more efficient)
        $applicationCount = \App\Models\Application::count();
        $pendingCount = \App\Models\Application::where('status', 'pending')->count();
        $approvedCount = \App\Models\Application::where('status', 'approved')->count();
        $rejectedCount = \App\Models\Application::where('status', 'rejected')->count();
        
        // Get pending approvals for dashboard
        $pendingApprovals = \App\Models\Application::where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();
        
        // Sample data for other counts (replace with actual models when available)
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
            'facilityCount',
            'individualCount',
            'taskCount'
        ));
    })->name('dashboard');
    
    Route::resource('applications', ApplicationController::class);
});

require __DIR__.'/auth.php';