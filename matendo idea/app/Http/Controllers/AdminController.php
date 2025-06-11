<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Healthworker;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Fetch all applications with pagination for the dashboard table
        $applications = Application::orderBy('created_at', 'desc')->paginate(10);

        // Fetch latest 5 active healthworkers with their users
        $healthworkers = Healthworker::with('user')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // Application stats for overview cards
        $pending_applications = Application::where('status', 'pending')->count();
        $approved_applications = Application::where('status', 'approved')->count();
        $rejected_applications = Application::where('status', 'rejected')->count();

        // Optional: Filter applications by month (e.g., May)
        $month = 5; // Can be dynamic (e.g., request()->input('month', now()->month))
        $applicationsInMonth = Application::where('status', 'pending')
            ->whereMonth('created_at', $month)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // Pass variables to the view
        return view('admin.dashboard', compact(
            'applications',
            'healthworkers',
            'pending_applications',
            'approved_applications',
            'rejected_applications',
            'applicationsInMonth'
        ));
    }
}