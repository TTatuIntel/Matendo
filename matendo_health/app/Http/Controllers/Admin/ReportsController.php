<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Alert;
use App\Models\VitalSign;
use App\Models\Appointment;
use App\Models\Document;
use App\Models\ActivityLog;
use App\Models\TempAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display the main reports dashboard
     */
    public function index()
    {
        try {
            $reportStats = [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'total_patients' => Patient::count(),
                'total_doctors' => Doctor::count(),
                'critical_alerts' => Alert::where('severity', 'critical')
                                         ->where('status', 'active')
                                         ->count(),
                'reports_generated_today' => $this->getReportsGeneratedToday(),
                'last_report_generated' => $this->getLastReportTime()
            ];

            $availableReports = [
                'users' => 'User Activity Reports',
                'activity' => 'System Activity Reports',
                'alerts' => 'Alert Reports',
                'health-metrics' => 'Health Metrics Reports',
                'service-quality' => 'Service Quality Reports',
                'doctor-workload' => 'Doctor Workload Reports',
                'patient-outcomes' => 'Patient Outcomes Reports',
                'external-access' => 'External Access Reports'
            ];

            // Data for the dashboard view
            $dashboardData = [
                'totalReports' => $this->getReportsGeneratedToday(),
                'vitalSignsToday' => VitalSign::whereDate('measured_at', today())->count(),
                'alertsToday' => Alert::whereDate('created_at', today())->count(),
                'appointmentsToday' => $this->getAppointmentCount(null, today(), today()),
                'peakActivityTime' => $this->getPeakActivityTime(),
                'growthRate' => $this->getGrowthRate(),
                'weeklyActivity' => $this->getWeeklyActivityData(),
                'registrationLabels' => $this->getRegistrationLabels(),
                'registrationData' => $this->getRegistrationData(),
                'recentActivities' => ActivityLog::with('causer')
                                                ->orderBy('created_at', 'desc')
                                                ->limit(20)
                                                ->get()
            ];

            return view('admin.reports', array_merge(compact('reportStats', 'availableReports'), $dashboardData));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load reports dashboard: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate user activity report
     */
    public function userReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'role' => 'nullable|in:admin,doctor,patient',
                'status' => 'nullable|in:active,inactive'
            ]);

            $query = User::query();

            // Apply filters
            if (!empty($validated['role'])) {
                $query->where('role', $validated['role']);
            }

            if (!empty($validated['status'])) {
                $query->where('status', $validated['status']);
            }

            if (!empty($validated['date_from']) && !empty($validated['date_to'])) {
                $query->whereBetween('created_at', [
                    Carbon::parse($validated['date_from'])->startOfDay(),
                    Carbon::parse($validated['date_to'])->endOfDay()
                ]);
            }

            $users = $query->with(['patient', 'doctor'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(50);

            // Generate statistics
            $stats = [
                'total_users' => $query->count(),
                'active_users' => $query->where('status', 'active')->count(),
                'new_registrations' => $query->whereBetween('created_at', [
                    now()->subMonth(),
                    now()
                ])->count(),
                'last_login_today' => $query->where('last_activity', '>=', now()->startOfDay())->count()
            ];

            return response()->json([
                'success' => true,
                'users' => $users,
                'stats' => $stats,
                'filters_applied' => $validated
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate user report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate system activity report
     */
    public function activityReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'activity_type' => 'nullable|string'
            ]);

            $query = ActivityLog::with(['causer', 'subject']);

            // Apply date filters
            if (!empty($validated['date_from']) && !empty($validated['date_to'])) {
                $query->whereBetween('created_at', [
                    Carbon::parse($validated['date_from'])->startOfDay(),
                    Carbon::parse($validated['date_to'])->endOfDay()
                ]);
            }

            // Apply activity type filter
            if (!empty($validated['activity_type'])) {
                $query->where('log_name', $validated['activity_type']);
            }

            $activities = $query->orderBy('created_at', 'desc')
                              ->paginate(100);

            // Generate activity statistics
            $stats = [
                'total_activities' => $query->count(),
                'unique_users' => $query->distinct('causer_id')->count('causer_id'),
                'most_active_user' => $this->getMostActiveUser(),
                'activity_by_hour' => $this->getActivityByHour()
            ];

            return response()->json([
                'success' => true,
                'activities' => $activities,
                'stats' => $stats,
                'filters_applied' => $validated
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate activity report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate alerts report
     */
    public function alerts(Request $request)
    {
        try {
            $validated = $request->validate([
                'severity' => 'nullable|in:low,medium,high,critical',
                'status' => 'nullable|in:active,resolved,dismissed',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from'
            ]);

            $query = Alert::with(['patient.user']);

            // Apply filters
            if (!empty($validated['severity'])) {
                $query->where('severity', $validated['severity']);
            }

            if (!empty($validated['status'])) {
                $query->where('status', $validated['status']);
            }

            if (!empty($validated['date_from']) && !empty($validated['date_to'])) {
                $query->whereBetween('created_at', [
                    Carbon::parse($validated['date_from'])->startOfDay(),
                    Carbon::parse($validated['date_to'])->endOfDay()
                ]);
            }

            $alerts = $query->orderBy('created_at', 'desc')->paginate(50);

            // Generate alert statistics
            $stats = [
                'total_alerts' => Alert::count(),
                'critical_alerts' => Alert::where('severity', 'critical')->count(),
                'active_alerts' => Alert::where('status', 'active')->count(),
                'resolved_today' => Alert::where('status', 'resolved')
                                       ->whereDate('updated_at', today())
                                       ->count(),
                'avg_resolution_time' => $this->getAverageResolutionTime(),
                'alerts_by_severity' => Alert::select('severity', DB::raw('COUNT(*) as count'))
                                           ->groupBy('severity')
                                           ->pluck('count', 'severity')
                                           ->toArray()
            ];

            return response()->json([
                'success' => true,
                'alerts' => $alerts,
                'stats' => $stats,
                'filters_applied' => $validated
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate alerts report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate health metrics report
     */
    public function healthMetricsReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'metric_type' => 'nullable|in:vitals,appointments,medications'
            ]);

            $dateFrom = !empty($validated['date_from']) ? 
                       Carbon::parse($validated['date_from'])->startOfDay() : 
                       now()->subMonth();
            
            $dateTo = !empty($validated['date_to']) ? 
                     Carbon::parse($validated['date_to'])->endOfDay() : 
                     now();

            $metrics = [
                'vital_signs' => [
                    'total_records' => VitalSign::whereBetween('measured_at', [$dateFrom, $dateTo])->count(),
                    'patients_monitored' => VitalSign::whereBetween('measured_at', [$dateFrom, $dateTo])
                                                   ->distinct('patient_id')
                                                   ->count('patient_id'),
                    'avg_heart_rate' => VitalSign::whereBetween('measured_at', [$dateFrom, $dateTo])
                                               ->avg('heart_rate'),
                    'avg_blood_pressure' => [
                        'systolic' => VitalSign::whereBetween('measured_at', [$dateFrom, $dateTo])
                                             ->avg('systolic_bp'),
                        'diastolic' => VitalSign::whereBetween('measured_at', [$dateFrom, $dateTo])
                                              ->avg('diastolic_bp')
                    ],
                    'abnormal_readings' => VitalSign::whereBetween('measured_at', [$dateFrom, $dateTo])
                                                  ->where(function($q) {
                                                      $q->where('heart_rate', '>', 120)
                                                        ->orWhere('heart_rate', '<', 60)
                                                        ->orWhere('systolic_bp', '>', 140)
                                                        ->orWhere('systolic_bp', '<', 90);
                                                  })->count()
                ],
                'appointments' => [
                    'total_scheduled' => $this->getAppointmentCount('scheduled', $dateFrom, $dateTo),
                    'completed' => $this->getAppointmentCount('completed', $dateFrom, $dateTo),
                    'cancelled' => $this->getAppointmentCount('cancelled', $dateFrom, $dateTo),
                    'no_shows' => $this->getAppointmentCount('no_show', $dateFrom, $dateTo)
                ],
                'documents' => [
                    'uploaded_today' => Document::whereDate('created_at', today())->count(),
                    'total_size' => Document::sum('file_size') / 1024 / 1024, // MB
                    'by_type' => Document::select('document_type', DB::raw('COUNT(*) as count'))
                                       ->groupBy('document_type')
                                       ->pluck('count', 'document_type')
                                       ->toArray()
                ]
            ];

            return response()->json([
                'success' => true,
                'metrics' => $metrics,
                'date_range' => [
                    'from' => $dateFrom->toDateString(),
                    'to' => $dateTo->toDateString()
                ],
                'filters_applied' => $validated
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate health metrics report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate custom report
     */
    public function generateReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'report_type' => 'required|string',
                'parameters' => 'nullable|array',
                'format' => 'required|in:json,csv,pdf'
            ]);

            $reportData = match ($validated['report_type']) {
                'users' => $this->userReport($request)->getData(),
                'activity' => $this->activityReport($request)->getData(),
                'alerts' => $this->alerts($request)->getData(),
                'health-metrics' => $this->healthMetricsReport($request)->getData(),
                'service-quality' => $this->serviceQualityReport($request)->getData(),
                'doctor-workload' => $this->doctorWorkloadReport($request)->getData(),
                'patient-outcomes' => $this->patientOutcomesReport($request)->getData(),
                'external-access' => $this->externalAccessReport($request)->getData(),
                default => throw new \InvalidArgumentException('Invalid report type')
            };

            // Generate report based on format
            return $this->formatReport($reportData, $validated['format'], $validated['report_type']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export report in specified format
     */
    public function export($type, Request $request)
    {
        try {
            return match ($type) {
                'users' => $this->exportUsersReport($request),
                'activity' => $this->exportActivityReport($request),
                'alerts' => $this->exportAlertsReport($request),
                'health-metrics' => $this->exportHealthMetricsReport($request),
                default => response()->json(['error' => 'Invalid export type'], 400)
            };

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Service quality report
     */
    public function serviceQualityReport(Request $request)
    {
        try {
            $metrics = [
                'patient_satisfaction' => [
                    'total_patients' => Patient::count(),
                    'patients_with_recent_vitals' => Patient::whereHas('vitalSigns', function($q) {
                        $q->where('measured_at', '>=', now()->subWeek());
                    })->count(),
                'response_time_avg' => $this->getAverageResponseTime(),
                'care_continuity' => $this->getCareKontinuityMetrics()
            ],
            'system_performance' => [
                'uptime' => $this->getSystemUptime(),
                'avg_response_time' => $this->getAverageResponseTime(),
                'error_rate' => $this->getSystemErrorRate(),
                'user_satisfaction' => $this->getUserSatisfactionScore()
            ]
            ];

            return response()->json([
                'success' => true,
                'metrics' => $metrics,
                'generated_at' => now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate service quality report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Doctor workload report
     */
    public function doctorWorkloadReport(Request $request)
    {
        try {
            $doctors = Doctor::with(['user', 'patients'])
                           ->whereHas('user', function($q) {
                               $q->where('status', 'active');
                           })
                           ->get()
                           ->map(function($doctor) {
                               return [
                                   'id' => $doctor->id,
                                   'name' => $doctor->user->name,
                                   'specialization' => $doctor->specialization,
                                   'patient_count' => $doctor->patients()->count(),
                                   'active_patients' => $doctor->patients()
                                                              ->whereHas('user', function($q) {
                                                                  $q->where('status', 'active');
                                                              })->count(),
                                   'appointments_today' => $this->getDoctorAppointmentsCount($doctor->id, today()),
                                   'avg_consultation_time' => '45 minutes', // Placeholder
                                   'workload_score' => $this->calculateWorkloadScore($doctor)
                               ];
                           });

            return response()->json([
                'success' => true,
                'doctors' => $doctors,
                'summary' => [
                    'total_doctors' => $doctors->count(),
                    'avg_patient_per_doctor' => $doctors->avg('patient_count'),
                    'overloaded_doctors' => $doctors->where('workload_score', '>', 8)->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate doctor workload report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Patient outcomes report
     */
    public function patientOutcomesReport(Request $request)
    {
        try {
            $outcomes = [
                'recovery_metrics' => [
                    'patients_improving' => Patient::whereHas('vitalSigns', function($q) {
                        $q->where('measured_at', '>=', now()->subWeek())
                          ->where('heart_rate', '<', 100);
                    })->count(),
                    'stable_patients' => Patient::whereHas('vitalSigns', function($q) {
                        $q->where('measured_at', '>=', now()->subWeek());
                    })->count(),
                    'readmission_rate' => '12%', // Placeholder
                    'avg_length_of_care' => '21 days' // Placeholder
                ],
                'treatment_effectiveness' => [
                    'medication_adherence' => '85%', // Placeholder
                    'appointment_attendance' => $this->getAppointmentAttendanceRate(),
                    'vital_signs_compliance' => $this->getVitalSignsComplianceRate()
                ]
            ];

            return response()->json([
                'success' => true,
                'outcomes' => $outcomes,
                'generated_at' => now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate patient outcomes report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * External access report
     */
    public function externalAccessReport(Request $request)
    {
        try {
            $accessData = [
                'total_access_grants' => TempAccess::count(),
                'active_sessions' => TempAccess::where('is_active', true)
                                              ->where('expires_at', '>', now())
                                              ->count(),
                'expired_sessions' => TempAccess::where('expires_at', '<', now())->count(),
                'access_by_date' => TempAccess::whereDate('created_at', '>=', now()->subWeek())
                                             ->groupBy(DB::raw('DATE(created_at)'))
                                             ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                             ->pluck('count', 'date'),
                'most_accessed_patients' => TempAccess::select('patient_id', DB::raw('COUNT(*) as access_count'))
                                                     ->with('patient.user')
                                                     ->groupBy('patient_id')
                                                     ->orderByDesc('access_count')
                                                     ->limit(10)
                                                     ->get(),
                'security_events' => [
                    'failed_verifications' => TempAccess::where('failed_verification_attempts', '>', 0)->count(),
                    'suspicious_activity' => TempAccess::where('access_count', '>', 50)->count()
                ]
            ];

            return response()->json([
                'success' => true,
                'access_data' => $accessData,
                'generated_at' => now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate external access report: ' . $e->getMessage()
            ], 500);
        }
    }

    // Private helper methods
    private function getReportsGeneratedToday()
    {
        // This would track report generation in a separate table
        return 15; // Placeholder
    }

    private function getLastReportTime()
    {
        return now()->subHours(2);
    }

    private function getMostActiveUser()
    {
        return ActivityLog::select('causer_id', DB::raw('COUNT(*) as activity_count'))
                         ->groupBy('causer_id')
                         ->orderByDesc('activity_count')
                         ->with('causer')
                         ->first();
    }

    private function getActivityByHour()
    {
        return ActivityLog::whereDate('created_at', today())
                         ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                         ->groupBy('hour')
                         ->pluck('count', 'hour')
                         ->toArray();
    }

    private function getAverageResolutionTime()
    {
        $resolvedAlerts = Alert::where('status', 'resolved')
                              ->whereNotNull('resolved_at')
                              ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) as avg_minutes'))
                              ->first();
        
        return $resolvedAlerts ? round($resolvedAlerts->avg_minutes / 60, 1) . ' hours' : '0 hours';
    }

    private function getAppointmentCount($status = null, $dateFrom = null, $dateTo = null)
    {
        if (!class_exists(\App\Models\Appointment::class)) {
            return 0;
        }

        $query = \App\Models\Appointment::query();
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($dateFrom && $dateTo) {
            $query->whereBetween('scheduled_at', [$dateFrom, $dateTo]);
        }
        
        return $query->count();
    }

    private function getCareKontinuityMetrics()
    {
        return [
            'patients_with_regular_checkups' => Patient::whereHas('vitalSigns', function($q) {
                $q->where('measured_at', '>=', now()->subMonth());
            })->count(),
            'care_gaps_identified' => Patient::whereDoesntHave('vitalSigns', function($q) {
                $q->where('measured_at', '>=', now()->subDays(7));
            })->count()
        ];
    }

    private function getDoctorAppointmentsCount($doctorId, $date)
    {
        if (!class_exists(\App\Models\Appointment::class)) {
            return 0;
        }
        
        return \App\Models\Appointment::where('doctor_id', $doctorId)
                                     ->whereDate('scheduled_at', $date)
                                     ->count();
    }

    private function calculateWorkloadScore($doctor)
    {
        $patientCount = $doctor->patients()->count();
        $baseScore = min($patientCount / 10, 5); // Max 5 points for patient count
        return min($baseScore + rand(3, 5), 10); // Add some variance, max 10
    }

    private function getAppointmentAttendanceRate()
    {
        if (!class_exists(\App\Models\Appointment::class)) {
            return '0%';
        }
        
        $total = \App\Models\Appointment::count();
        $attended = \App\Models\Appointment::where('status', 'completed')->count();
        
        return $total > 0 ? round(($attended / $total) * 100, 1) . '%' : '0%';
    }

    private function getVitalSignsComplianceRate()
    {
        $totalPatients = Patient::count();
        $compliantPatients = Patient::whereHas('vitalSigns', function($q) {
            $q->where('measured_at', '>=', now()->subWeek());
        })->count();
        
        return $totalPatients > 0 ? round(($compliantPatients / $totalPatients) * 100, 1) . '%' : '0%';
    }

    private function formatReport($data, $format, $reportType)
    {
        switch ($format) {
            case 'json':
                return response()->json($data);
            
            case 'csv':
                return $this->generateCSVReport($data, $reportType);
            
            case 'pdf':
                return $this->generatePDFReport($data, $reportType);
            
            default:
                return response()->json($data);
        }
    }

    private function generateCSVReport($data, $reportType)
    {
        return response()->streamDownload(function () use ($data, $reportType) {
            echo "Report: {$reportType}\n";
            echo "Generated: " . now() . "\n\n";
            echo json_encode($data, JSON_PRETTY_PRINT);
        }, "{$reportType}-report-" . now()->format('Y-m-d') . '.csv');
    }

    private function generatePDFReport($data, $reportType)
    {
        // Placeholder - would implement PDF generation
        return response()->json([
            'message' => 'PDF generation not yet implemented',
            'data' => $data
        ]);
    }

    // Export helper methods
    private function exportUsersReport($request)
    {
        $users = User::with(['patient', 'doctor'])->get();
        
        return response()->streamDownload(function () use ($users) {
            echo "Users Report\n";
            echo "Generated: " . now() . "\n\n";
            echo "Name,Email,Role,Status,Created At,Last Activity\n";
            
            foreach ($users as $user) {
                echo implode(',', [
                    '"' . $user->name . '"',
                    $user->email,
                    $user->role,
                    $user->status,
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->last_activity ? $user->last_activity->format('Y-m-d H:i:s') : 'Never'
                ]) . "\n";
            }
        }, 'users-report-' . now()->format('Y-m-d') . '.csv');
    }

    private function exportActivityReport($request)
    {
        $activities = ActivityLog::with(['causer'])->limit(1000)->get();
        
        return response()->streamDownload(function () use ($activities) {
            echo "Activity Report\n";
            echo "Generated: " . now() . "\n\n";
            echo "Timestamp,User,Action,Description\n";
            
            foreach ($activities as $activity) {
                echo implode(',', [
                    $activity->created_at->format('Y-m-d H:i:s'),
                    '"' . ($activity->causer->name ?? 'System') . '"',
                    $activity->log_name ?? 'N/A',
                    '"' . ($activity->description ?? 'No description') . '"'
                ]) . "\n";
            }
        }, 'activity-report-' . now()->format('Y-m-d') . '.csv');
    }

    private function exportAlertsReport($request)
    {
        $alerts = Alert::with(['patient.user'])->get();
        
        return response()->streamDownload(function () use ($alerts) {
            echo "Alerts Report\n";
            echo "Generated: " . now() . "\n\n";
            echo "Alert Type,Severity,Status,Patient,Created At,Message\n";
            
            foreach ($alerts as $alert) {
                echo implode(',', [
                    $alert->alert_type ?? 'N/A',
                    $alert->severity,
                    $alert->status,
                    '"' . ($alert->patient->user->name ?? 'Unknown') . '"',
                    $alert->created_at->format('Y-m-d H:i:s'),
                    '"' . ($alert->message ?? 'No message') . '"'
                ]) . "\n";
            }
        }, 'alerts-report-' . now()->format('Y-m-d') . '.csv');
    }

    private function exportHealthMetricsReport($request)
    {
        $vitals = VitalSign::with(['patient.user'])->limit(1000)->get();
        
        return response()->streamDownload(function () use ($vitals) {
            echo "Health Metrics Report\n";
            echo "Generated: " . now() . "\n\n";
            echo "Patient,Heart Rate,Blood Pressure,Temperature,Measured At\n";
            
            foreach ($vitals as $vital) {
                echo implode(',', [
                    '"' . ($vital->patient->user->name ?? 'Unknown') . '"',
                    $vital->heart_rate ?? 'N/A',
                    ($vital->systolic_bp ?? 'N/A') . '/' . ($vital->diastolic_bp ?? 'N/A'),
                    $vital->temperature ?? 'N/A',
                    $vital->measured_at ? $vital->measured_at->format('Y-m-d H:i:s') : 'N/A'
                ]) . "\n";
            }
        }, 'health-metrics-report-' . now()->format('Y-m-d') . '.csv');
    }

    /**
     * Get average response time for system performance metrics
     */
    private function getAverageResponseTime()
    {
        // Calculate average response time based on activity logs or system metrics
        // This is a placeholder implementation
        $avgMinutes = ActivityLog::whereDate('created_at', today())
                                ->count() > 0 ? rand(15, 45) : 30;
        
        return $avgMinutes . ' minutes';
    }

    /**
     * Get system uptime percentage
     */
    private function getSystemUptime()
    {
        // This would typically be calculated from system monitoring tools
        // Placeholder implementation
        return '99.' . rand(5, 9) . '%';
    }

    /**
     * Get system error rate percentage
     */
    private function getSystemErrorRate()
    {
        // Calculate error rate based on failed requests/activities
        $errorCount = ActivityLog::where('log_name', 'error')
                                ->whereDate('created_at', today())
                                ->count();
        
        $totalActivities = ActivityLog::whereDate('created_at', today())->count();
        
        if ($totalActivities == 0) {
            return '0.0%';
        }
        
        $errorRate = ($errorCount / $totalActivities) * 100;
        return number_format($errorRate, 1) . '%';
    }

    /**
     * Get user satisfaction score
     */
    private function getUserSatisfactionScore()
    {
        // This would typically come from user feedback/surveys
        // Placeholder implementation
        return '4.' . rand(5, 9) . '/5';
    }

    /**
     * Get peak activity time
     */
    private function getPeakActivityTime()
    {
        $peakHour = ActivityLog::whereDate('created_at', today())
                             ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                             ->groupBy('hour')
                             ->orderByDesc('count')
                             ->first();
        
        if ($peakHour) {
            $hour = $peakHour->hour;
            $time = sprintf('%02d:00', $hour);
            return $time;
        }
        
        return '2:00 PM';
    }

    /**
     * Get user growth rate percentage
     */
    private function getGrowthRate()
    {
        $currentMonth = User::whereMonth('created_at', now()->month)
                           ->whereYear('created_at', now()->year)
                           ->count();
        
        $previousMonth = User::whereMonth('created_at', now()->subMonth()->month)
                            ->whereYear('created_at', now()->subMonth()->year)
                            ->count();
        
        if ($previousMonth == 0) {
            return $currentMonth > 0 ? '100.0' : '0.0';
        }
        
        $growthRate = (($currentMonth - $previousMonth) / $previousMonth) * 100;
        return number_format($growthRate, 1);
    }

    /**
     * Get weekly activity data for chart
     */
    private function getWeeklyActivityData()
    {
        $data = [];
        $startOfWeek = now()->startOfWeek();
        
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $count = ActivityLog::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        
        return $data;
    }

    /**
     * Get registration chart labels (last 6 months)
     */
    private function getRegistrationLabels()
    {
        $labels = [];
        $currentDate = now();
        
        for ($i = 5; $i >= 0; $i--) {
            $date = $currentDate->copy()->subMonths($i);
            $labels[] = $date->format('M');
        }
        
        return $labels;
    }

    /**
     * Get registration data for chart (last 6 months)
     */
    private function getRegistrationData()
    {
        $data = [];
        $currentDate = now();
        
        for ($i = 5; $i >= 0; $i--) {
            $date = $currentDate->copy()->subMonths($i);
            $count = User::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count();
            $data[] = $count;
        }
        
        return $data;
    }
}
