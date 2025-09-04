<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\DoctorPatient;
use App\Models\Alert;
use App\Models\TempAccess;
use App\Models\VitalSign;
use App\Models\Document;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class OverallMonitorController extends Controller
{
    public function index()
    {
        // Patient Assignments per Doctor
        $doctorAssignments = Doctor::with(['user', 'doctorPatients.patient.user'])
            ->whereHas('user', function($query) {
                $query->where('status', 'active');
            })
            ->get()
            ->map(function($doctor) {
                $activePatients = $doctor->doctorPatients->where('status', 'active')->count();
                $totalPatients = $doctor->doctorPatients->count();
                
                return [
                    'doctor' => $doctor,
                    'active_patients' => $activePatients,
                    'total_patients' => $totalPatients,
                    'availability_status' => $this->getDoctorAvailability($doctor),
                    'last_activity' => $doctor->user->last_activity ?? $doctor->user->updated_at,
                    'specialization' => $doctor->specialization,
                    'workload_level' => $this->calculateWorkloadLevel($activePatients)
                ];
            })
            ->sortByDesc('active_patients');

        // Doctor Availability and Coverage
        $availabilityMetrics = [
            'total_doctors' => Doctor::whereHas('user', function($q) {
                $q->where('status', 'active');
            })->count(),
            'available_doctors' => Doctor::whereHas('user', function($q) {
                $q->where('status', 'active')
                  ->where('last_activity', '>=', now()->subHours(8));
            })->count(),
            'doctors_with_patients' => DoctorPatient::where('status', 'active')
                ->distinct('doctor_id')->count(),
            'unassigned_patients' => Patient::whereDoesntHave('doctors', function($q) {
                $q->where('doctor_patients.status', 'active');
            })->count(),
            'substitute_coverage_needed' => $this->getSubstituteCoverageNeeded()
        ];

        // Active External Access Links
        $externalAccess = TempAccess::with(['patient.user', 'generatedBy'])
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($access) {
                return [
                    'id' => $access->id,
                    'patient_name' => $access->patient->user->name ?? 'Unknown',
                    'patient_id' => $access->patient->id,
                    'generated_by' => $access->generatedBy->name ?? 'System',
                    'verification_code' => $access->verification_code,
                    'expires_at' => $access->expires_at,
                    'time_remaining' => $access->expires_at->diffForHumans(),
                    'verified_doctor' => $access->verified_doctor_name ?? 'Not verified',
                    'documents_accessed' => $this->getDocumentsAccessedCount($access->id),
                    'risk_level' => $this->assessAccessRiskLevel($access)
                ];
            });

        // Critical Patient Events and Alerts
        $criticalAlerts = Alert::with(['patient.user'])
            ->where('severity', 'critical')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($alert) {
                return [
                    'id' => $alert->id,
                    'patient_name' => $alert->patient->user->name ?? 'Unknown',
                    'patient_id' => $alert->patient->id,
                    'alert_type' => $alert->alert_type ?? 'general',
                    'message' => $alert->message,
                    'severity' => $alert->severity,
                    'created_at' => $alert->created_at,
                    'time_since' => $alert->created_at->diffForHumans(),
                    'assigned_doctor' => $this->getAssignedDoctor($alert->patient),
                    'requires_immediate_action' => $alert->requires_immediate_action ?? false
                ];
            });

        // Recent Patient Vitals Updates
        $recentVitals = VitalSign::with(['patient.user'])
            ->where('measured_at', '>=', now()->subHours(24))
            ->where('status', '!=', 'normal')
            ->orderBy('measured_at', 'desc')
            ->limit(15)
            ->get()
            ->map(function($vital) {
                return [
                    'patient_name' => $vital->patient->user->name ?? 'Unknown',
                    'patient_id' => $vital->patient->id,
                    'status' => $vital->status,
                    'risk_level' => $vital->risk_level ?? 'normal',
                    'measured_at' => $vital->measured_at,
                    'time_ago' => $vital->measured_at->diffForHumans(),
                    'vital_type' => $this->getPrimaryVitalConcern($vital),
                    'assigned_doctor' => $this->getAssignedDoctor($vital->patient)
                ];
            });

        // System Performance Metrics
        $systemMetrics = [
            'total_active_sessions' => User::where('last_activity', '>=', now()->subHours(1))->count(),
            'documents_uploaded_today' => Document::whereDate('created_at', today())->count(),
            'total_temp_access_active' => $externalAccess->count(),
            'patients_monitored_today' => VitalSign::whereDate('measured_at', today())
                ->distinct('patient_id')->count(),
            'average_response_time' => '245ms', // This would come from monitoring system
            'database_connections' => 'Healthy',
            'storage_usage' => '67%'
        ];

        // Real-time Updates Summary
        $realTimeUpdates = [
            'last_vital_update' => VitalSign::latest('measured_at')->first()?->measured_at ?? now(),
            'last_alert_generated' => Alert::latest('created_at')->first()?->created_at ?? now(),
            'last_external_access' => TempAccess::latest('created_at')->first()?->created_at ?? now(),
            'last_patient_assignment' => DoctorPatient::latest('assigned_at')->first()?->assigned_at ?? now()
        ];

        return view('admin.overall-monitor', compact(
            'doctorAssignments',
            'availabilityMetrics', 
            'externalAccess',
            'criticalAlerts',
            'recentVitals',
            'systemMetrics',
            'realTimeUpdates'
        ));
    }

    public function getRealTimeData()
    {
        // This endpoint provides real-time data for AJAX updates
        $data = [
            'critical_alerts_count' => Alert::where('severity', 'critical')
                ->where('status', 'active')->count(),
            'active_external_access' => TempAccess::where('is_active', true)
                ->where('expires_at', '>', now())->count(),
            'recent_vitals_abnormal' => VitalSign::where('measured_at', '>=', now()->subHours(1))
                ->where('status', '!=', 'normal')->count(),
            'unassigned_patients' => Patient::whereDoesntHave('doctors', function($q) {
                $q->where('doctor_patients.status', 'active');
            })->count(),
            'available_doctors' => Doctor::whereHas('user', function($q) {
                $q->where('status', 'active')
                  ->where('last_activity', '>=', now()->subHours(8));
            })->count(),
            'timestamp' => now()->toISOString()
        ];

        return response()->json($data);
    }

    private function getDoctorAvailability($doctor)
    {
        $lastActivity = $doctor->user->last_activity ?? $doctor->user->updated_at;
        
        if (!$lastActivity) {
            return 'unknown';
        }
        
        $hoursAgo = $lastActivity->diffInHours(now());
        
        if ($hoursAgo < 1) {
            return 'active';
        } elseif ($hoursAgo < 8) {
            return 'available';
        } elseif ($hoursAgo < 24) {
            return 'inactive';
        } else {
            return 'offline';
        }
    }

    private function calculateWorkloadLevel($patientCount)
    {
        if ($patientCount >= 20) {
            return 'high';
        } elseif ($patientCount >= 10) {
            return 'medium';
        } elseif ($patientCount > 0) {
            return 'low';
        } else {
            return 'none';
        }
    }

    private function getSubstituteCoverageNeeded()
    {
        // Count patients whose primary doctor is unavailable
        return DoctorPatient::where('status', 'active')
            ->where('relationship_type', 'primary')
            ->whereHas('doctor.user', function($q) {
                $q->where('status', 'inactive')
                  ->orWhere('last_activity', '<', now()->subDays(2));
            })
            ->distinct('patient_id')
            ->count();
    }

    private function getDocumentsAccessedCount($tempAccessId)
    {
        // This would track document access via temp links
        return Document::where('accessed_via_temp_link', $tempAccessId)->count();
    }

    private function assessAccessRiskLevel($access)
    {
        $hoursRemaining = $access->expires_at->diffInHours(now());
        $isVerified = !empty($access->verified_doctor_name);
        
        if (!$isVerified && $hoursRemaining > 24) {
            return 'high';
        } elseif (!$isVerified || $hoursRemaining < 1) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    private function getAssignedDoctor($patient)
    {
        $assignment = $patient->doctors()
            ->wherePivot('status', 'active')
            ->wherePivot('relationship_type', 'primary')
            ->first();
            
        return $assignment ? $assignment->user->name : 'Unassigned';
    }

    private function getPrimaryVitalConcern($vital)
    {
        // Determine which vital sign is the primary concern
        if ($vital->blood_pressure && $vital->systolic_bp > 140) {
            return 'Blood Pressure';
        } elseif ($vital->heart_rate && ($vital->heart_rate > 100 || $vital->heart_rate < 60)) {
            return 'Heart Rate';
        } elseif ($vital->blood_glucose && $vital->blood_glucose > 180) {
            return 'Blood Sugar';
        } elseif ($vital->temperature && $vital->temperature > 38) {
            return 'Temperature';
        } else {
            return 'General';
        }
    }
}
