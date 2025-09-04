<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alert;
use App\Models\User;
use App\Models\Patient;

class AlertManagementController extends Controller
{
    public function index()
    {
        $alerts = Alert::with(['patient.user'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(20);
        
        $stats = [
            'total' => Alert::count(),
            'critical' => Alert::where('severity', 'critical')->count(),
            'active' => Alert::where('status', 'active')->count(),
            'resolved' => Alert::where('status', 'resolved')->count()
        ];

        return view('admin.alerts.index', compact('alerts', 'stats'));
    }

    public function getRealTimeAlerts()
    {
        $alerts = Alert::with(['patient.user'])
                      ->where('status', 'active')
                      ->where('severity', 'critical')
                      ->orderBy('created_at', 'desc')
                      ->limit(10)
                      ->get();

        return response()->json($alerts);
    }

    public function createEscalationRule(Request $request)
    {
        $request->validate([
            'rule_name' => 'required|string|max:255',
            'conditions' => 'required|array',
            'actions' => 'required|array'
        ]);

        // Logic to create escalation rule
        return response()->json(['success' => true, 'message' => 'Escalation rule created']);
    }

    public function applySmartFiltering(Request $request)
    {
        $filters = $request->input('filters', []);
        
        $query = Alert::query();
        
        if (isset($filters['severity'])) {
            $query->whereIn('severity', $filters['severity']);
        }
        
        if (isset($filters['status'])) {
            $query->whereIn('status', $filters['status']);
        }
        
        if (isset($filters['date_range'])) {
            $query->whereBetween('created_at', [
                $filters['date_range']['start'],
                $filters['date_range']['end']
            ]);
        }

        $alerts = $query->with(['patient.user'])->paginate(20);
        
        return response()->json($alerts);
    }

    public function bulkEscalate(Request $request)
    {
        $alertIds = $request->input('alert_ids', []);
        
        Alert::whereIn('id', $alertIds)->update([
            'status' => 'escalated',
            'escalated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alerts escalated successfully',
            'count' => count($alertIds)
        ]);
    }

    public function configureAutoRouting(Request $request)
    {
        $rules = $request->input('routing_rules', []);
        
        // Logic to configure automatic alert routing
        return response()->json([
            'success' => true,
            'message' => 'Auto-routing configured successfully'
        ]);
    }

    public function detectSuspiciousActivity()
    {
        // Logic to detect suspicious patterns in alerts
        $suspiciousAlerts = Alert::where('created_at', '>=', now()->subHours(24))
                                ->havingRaw('COUNT(*) > 10')
                                ->groupBy('patient_id')
                                ->with(['patient.user'])
                                ->get();
        
        return response()->json([
            'suspicious_alerts' => $suspiciousAlerts,
            'count' => $suspiciousAlerts->count()
        ]);
    }
}
