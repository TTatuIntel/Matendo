<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Healthworker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Fetch only pending applications for the table
        $applications = Application::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Fetch latest 5 active healthworkers with their users (for dashboard, if needed)
        $healthworkers = Healthworker::with('user')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // Application stats for overview cards
        $pendingApplications = Application::where('status', 'pending')->count();
        $approvedApplications = Application::where('status', 'approved')->count();
        $rejectedApplications = Application::where('status', 'rejected')->count();
        $oldestPending = Application::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->first()?->created_at?->diffForHumans() ?? 'N/A';
        $avgReviewTime = Application::whereIn('status', ['approved', 'rejected'])
            ->avg(DB::raw('DATEDIFF(updated_at, created_at)')) ?? 0;
        $avgReviewTime = round($avgReviewTime, 1) . ' days';
        $newApproved = Application::where('status', 'approved')
            ->whereMonth('updated_at', now()->month)
            ->count();
        $avgOnboardingTime = Healthworker::whereNotNull('verified_at')
            ->avg(DB::raw('DATEDIFF(verified_at, created_at)')) ?? 0;
        $avgOnboardingTime = round($avgOnboardingTime, 1) . ' days';
        $commonRejectionReasons = 'Missing documents, expired licenses'; // Placeholder; customize as needed

        // Optional: Filter applications by month (dynamic or default to current month)
        $month = $request->input('month', now()->month);
        $applicationsInMonth = Application::where('status', 'pending')
            ->whereMonth('created_at', $month)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // Pass variables to the view
        return view('admin.dashboard', compact(
            'applications',
            'healthworkers',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications',
            'oldestPending',
            'avgReviewTime',
            'newApproved',
            'avgOnboardingTime',
            'commonRejectionReasons',
            'applicationsInMonth'
        ));
    }
}