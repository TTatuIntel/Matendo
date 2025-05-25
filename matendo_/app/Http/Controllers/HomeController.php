<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        // Get application stats
        $stats = [
            'total' => Application::count(),
            'pending' => Application::where('status', 'pending')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];
        
        // Get recent applications
        $recentApplications = Application::latest()->take(5)->get();
        
        // Get applications by profession (for chart)
        $applicationsByProfession = Application::select('profession', DB::raw('count(*) as total'))
            ->groupBy('profession')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();
        
        return view('admin.dashboard', compact('stats', 'recentApplications', 'applicationsByProfession'));
    }
}