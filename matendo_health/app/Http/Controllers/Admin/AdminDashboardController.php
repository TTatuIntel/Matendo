<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Alert;
use App\Models\Appointment;
use App\Models\Document;
use App\Models\VitalSign;
use App\Models\Notification;
use App\Models\TempAccess;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Use caching for better performance
        $analytics = Cache::remember('admin_dashboard_analytics', 300, function () {
            return [
                'totalUsers' => User::count(),
                'totalPatients' => User::where('role', 'patient')->count(),
                'activeDoctors' => User::where('role', 'doctor')
                    ->where('status', 'active')
                    ->whereHas('doctor', function($query) {
                        $query->where('verification_status', 'verified');
                    })->count(),
                'totalAdmins' => User::where('role', 'admin')->count(),
                'registrationLabels' => $this->getRegistrationLabels(),
                'registrationData' => $this->getRegistrationData(),
            ];
        });

        $realTimeData = [
            'active_doctors' => Doctor::whereHas('user', function($query) {
                $query->where('status', 'active')
                      ->where('last_activity', '>=', now()->subHours(8));
            })->count(),
            'active_patients' => Patient::whereHas('user', function($query) {
                $query->where('last_activity', '>=', now()->subHours(2));
            })->count(),
            'ongoing_appointments' => $this->getAppointmentCount('in_progress', today()),
            'vitals_updated_today' => $this->getVitalSignsCount(today()),
            'documents_uploaded_today' => Document::whereDate('created_at', today())->count(),
            'notifications_sent_today' => Notification::whereDate('created_at', today())->count(),
            'average_response_time' => '250ms', // Placeholder
            'system_load' => [
                'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 1) . 'MB',
                'active_connections' => User::where('last_activity', '>=', now()->subMinutes(5))->count(),
            ]
        ];

        $pendingApprovals = [
            'doctor_registrations' => Doctor::where('verification_status', 'pending')->count(),
            'document_reviews' => Document::where('status', 'pending')->count(),
            'access_requests' => TempAccess::where('status', 'pending')->count(),
            'alert_escalations' => Alert::where('status', 'escalated')->count(),
        ];

        $careQualityMetrics = [
            'total_patients' => Patient::count(),
            'patients_monitored' => Patient::whereHas('vitalSigns', function($query) {
                $query->where('measured_at', '>=', now()->subWeek());
            })->count(),
            'patients_under_care' => Patient::whereHas('doctors', function($query) {
                $query->where('doctor_patients.status', 'active');
            })->count(),
            'overdue_vitals' => Patient::whereDoesntHave('vitalSigns', function($query) {
                $query->where('measured_at', '>=', now()->subDays(3));
            })->whereHas('doctors', function($query) {
                $query->where('doctor_patients.status', 'active');
            })->count(),
            'care_gaps_identified' => Patient::whereDoesntHave('medicalRecords', function($query) {
                $query->where('created_at', '>=', now()->subMonth());
            })->whereHas('doctors')->count(),
        ];

        // Calculate percentages
        $totalPatients = max($careQualityMetrics['total_patients'], 1); // Prevent division by zero
        $careQualityMetrics['patients_monitored_percentage'] = round(($careQualityMetrics['patients_monitored'] / $totalPatients) * 100, 1);
        $careQualityMetrics['patients_under_care_percentage'] = round(($careQualityMetrics['patients_under_care'] / $totalPatients) * 100, 1);

        $externalAccessMetrics = [
            'active_external_access' => TempAccess::where('is_active', true)
                                                  ->where('expires_at', '>', now())->count(),
            'external_access_granted_today' => TempAccess::whereDate('created_at', today())->count(),
            'documents_accessed_externally_today' => Document::whereNotNull('external_access_count')
                                                           ->whereDate('updated_at', today())
                                                           ->sum('external_access_count') ?: 0,
            'expired_access_links' => TempAccess::where('expires_at', '<', now())
                                               ->where('is_active', true)->count(),
            'average_external_session_duration' => '45 minutes', // Placeholder
        ];

        $criticalAlerts = Alert::with(['patient.user'])
                              ->where('severity', 'critical')
                              ->where('status', 'active')
                              ->orderBy('created_at', 'desc')
                              ->limit(5)
                              ->get();

        $systemHealth = [
            'database' => 'connected',
            'cache' => 'active',
            'storage' => 'accessible',
        ];

        $recentActivities = ActivityLog::orderBy('created_at', 'desc')
                                      ->limit(10)
                                      ->get()
                                      ->map(function($log) {
                                          return (object) [
                                              'description' => $log->description ?? 'System activity',
                                              'type' => $log->type ?? 'system',
                                              'created_at' => $log->created_at ?? now(),
                                          ];
                                      });

        // Extract individual variables from analytics for the view
        $totalUsers = $analytics['totalUsers'];
        $totalPatients = $analytics['totalPatients'];
        $activeDoctors = $analytics['activeDoctors'];
        $totalAdmins = $analytics['totalAdmins'];
        $registrationLabels = $analytics['registrationLabels'];
        $registrationData = $analytics['registrationData'];
        
        // Calculate active alerts from critical alerts
        $activeAlerts = $criticalAlerts->count();

        return view('admin.dashboard', compact(
            'analytics',
            'totalUsers',
            'totalPatients', 
            'activeDoctors',
            'totalAdmins',
            'registrationLabels',
            'registrationData',
            'activeAlerts',
            'realTimeData', 
            'pendingApprovals',
            'careQualityMetrics',
            'externalAccessMetrics',
            'criticalAlerts',
            'systemHealth',
            'recentActivities'
        ));
    }

    public function getRealTimeData()
    {
        $data = [
            'analytics' => [
                'totalUsers' => User::count(),
                'activeDoctors' => User::where('role', 'doctor')
                    ->where('status', 'active')
                    ->whereHas('doctor', function($query) {
                        $query->where('verification_status', 'verified');
                    })->count(),
                'totalPatients' => User::where('role', 'patient')->count(),
                'totalAdmins' => User::where('role', 'admin')->count(),
            ],
            'realTimeData' => [
                'active_doctors' => Doctor::whereHas('user', function($query) {
                    $query->where('status', 'active')
                          ->where('last_activity', '>=', now()->subHours(8));
                })->count(),
                'active_patients' => Patient::whereHas('user', function($query) {
                    $query->where('last_activity', '>=', now()->subHours(2));
                })->count(),
            ],
            'criticalAlerts' => Alert::where('severity', 'critical')
                                   ->where('status', 'active')
                                   ->with(['patient.user'])
                                   ->orderBy('created_at', 'desc')
                                   ->limit(5)
                                   ->get(),
        ];

        return response()->json($data);
    }

    private function getRegistrationLabels()
    {
        $labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
        }
        return $labels;
    }

    private function getRegistrationData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = User::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getAppointmentCount($status = null, $date = null)
    {
        // Check if Appointment model exists, otherwise return 0
        if (!class_exists(\App\Models\Appointment::class)) {
            return 0;
        }

        $query = \App\Models\Appointment::query();
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($date) {
            $query->whereDate('scheduled_at', $date);
        }
        
        return $query->count();
    }

    private function getVitalSignsCount($date = null)
    {
        // Check if VitalSign model exists, otherwise return 0
        if (!class_exists(\App\Models\VitalSign::class)) {
            return 0;
        }

        $query = \App\Models\VitalSign::query();
        
        if ($date) {
            $query->whereDate('measured_at', $date);
        }
        
        return $query->count();
    }
}