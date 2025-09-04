<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\VitalSign;

class CareQualityController extends Controller
{
    public function index()
    {
        $metrics = [
            'total_patients' => Patient::count(),
            'monitored_patients' => Patient::whereHas('vitalSigns', function($q) {
                $q->where('created_at', '>=', now()->subWeek());
            })->count(),
            'missing_vitals' => $this->getMissingVitalsCount()
        ];
        
        return view('admin.care-quality.index', compact('metrics'));
    }

    public function getRealTimeMetrics()
    {
        return response()->json([
            'care_gaps' => $this->detectMissingData(),
            'irregular_vitals' => $this->detectIrregularVitals()
        ]);
    }

    public function detectMissingData()
    {
        return Patient::whereDoesntHave('vitalSigns', function($q) {
            $q->where('created_at', '>=', now()->subDays(3));
        })->with('user')->get();
    }

    public function detectIrregularVitals()
    {
        return VitalSign::where('created_at', '>=', now()->subDay())
                       ->where(function($q) {
                           $q->where('heart_rate', '>', 120)
                             ->orWhere('heart_rate', '<', 60)
                             ->orWhere('systolic_bp', '>', 140)
                             ->orWhere('systolic_bp', '<', 90);
                       })
                       ->with('patient.user')
                       ->get();
    }

    public function generateQualityReport(Request $request)
    {
        $report = [
            'missing_data' => $this->detectMissingData(),
            'irregular_vitals' => $this->detectIrregularVitals(),
            'generated_at' => now()
        ];
        
        return response()->json($report);
    }
    
    private function getMissingVitalsCount()
    {
        return Patient::whereDoesntHave('vitalSigns', function($q) {
            $q->where('created_at', '>=', now()->subDays(3));
        })->count();
    }
}
