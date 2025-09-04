<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Alert;
use App\Models\VitalSign;

class CriticalPatientMonitorController extends Controller
{
    public function index()
    {
        $criticalPatients = Patient::whereHas('alerts', function($q) {
            $q->where('severity', 'critical')
              ->where('status', 'active');
        })->with(['user', 'alerts' => function($q) {
            $q->where('severity', 'critical')
              ->where('status', 'active')
              ->latest();
        }])->get();
        
        return view('admin.critical-monitor.index', compact('criticalPatients'));
    }

    public function getRealTimeData()
    {
        return response()->json([
            'critical_patients' => Patient::whereHas('alerts', function($q) {
                $q->where('severity', 'critical')
                  ->where('status', 'active');
            })->count(),
            'urgent_vitals' => $this->getUrgentVitals()
        ]);
    }

    public function detectCareGaps()
    {
        $careGaps = Patient::whereDoesntHave('vitalSigns', function($q) {
            $q->where('created_at', '>=', now()->subDays(2));
        })->orWhereDoesntHave('appointments', function($q) {
            $q->where('scheduled_at', '>=', now()->subWeek());
        })->with('user')->get();
        
        return response()->json(['care_gaps' => $careGaps]);
    }

    public function escalateIssue($patientId, Request $request)
    {
        $patient = Patient::findOrFail($patientId);
        
        Alert::create([
            'patient_id' => $patientId,
            'alert_type' => 'manual_escalation',
            'severity' => 'critical',
            'title' => 'Manual Escalation',
            'message' => $request->input('reason', 'Manually escalated by admin'),
            'triggered_at' => now(),
            'triggered_by' => auth()->user()->name
        ]);
        
        return response()->json(['success' => true, 'message' => 'Issue escalated']);
    }

    public function resolveCareGap($gapId)
    {
        return response()->json(['success' => true, 'message' => 'Care gap resolved']);
    }
    
    private function getUrgentVitals()
    {
        return VitalSign::where('created_at', '>=', now()->subHour())
                       ->where(function($q) {
                           $q->where('heart_rate', '>', 130)
                             ->orWhere('systolic_bp', '>', 160)
                             ->orWhere('temperature', '>', 101);
                       })
                       ->count();
    }
}
