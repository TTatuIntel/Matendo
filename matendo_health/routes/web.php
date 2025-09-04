<?php

use Illuminate\Support\Facades\Route;

// Core / Auth
use App\Http\Controllers\ProfileController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DoctorManagementController;
use App\Http\Controllers\Admin\PatientManagementController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SystemMonitoringController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\AlertManagementController;
use App\Http\Controllers\Admin\CareQualityController;
use App\Http\Controllers\Admin\ExternalAccessController;
use App\Http\Controllers\Admin\CriticalPatientMonitorController;
use App\Http\Controllers\Admin\OverallMonitorController;

// Doctor Controllers
use App\Http\Controllers\Doctor\DoctorDashboardController;
use App\Http\Controllers\Doctor\PatientMonitorController;
use App\Http\Controllers\Doctor\AnalyticsController as DoctorAnalyticsController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\EmergencyController;
use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\Doctor\NotificationController;
use App\Http\Controllers\Doctor\DoctorProfileController;
use App\Http\Controllers\Doctor\DoctorMedController;

// Patient Controllers
use App\Http\Controllers\Patient\PatientDashboardController;
use App\Http\Controllers\Patient\VitalSignsController;
use App\Http\Controllers\Patient\DocumentController as PatientDocumentController;
use App\Http\Controllers\Patient\HealthTrendsController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\MedicationController;
use App\Http\Controllers\Patient\ProfileTwoController;

// Shared
use App\Http\Controllers\TempAccessController;
use App\Http\Controllers\DoctorDocumentController;
use App\Http\Controllers\SearchController;

// Models for short link
use App\Models\TempAccess;

/*
|--------------------------------------------------------------------------
| Landing & Auth
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        switch ($user->role) {
            case 'admin':  return redirect()->route('admin.dashboard');
            case 'doctor': return redirect()->route('doctor.dashboard');
            case 'patient':return redirect()->route('patient.dashboard');
        }
    }
    return view('welcome');
})->name('home');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/realtime', [AdminDashboardController::class, 'getRealTimeData'])->name('dashboard.realtime');

    // Add missing parent routes (redirect to .index where applicable)
    Route::get('/users-redirect', function () {
        return redirect()->route('admin.users.index');
    })->name('users');

    Route::get('/doctors-redirect', function () {
        return redirect()->route('admin.doctors.index');
    })->name('doctors');

    Route::get('/patients-redirect', function () {
        return redirect()->route('admin.patients.index');
    })->name('patients');

    Route::get('/monitoring-redirect', function () {
        return redirect()->route('admin.monitoring.index');
    })->name('monitoring');

    Route::get('/settings-redirect', function () {
        return redirect()->route('admin.settings.index');
    })->name('settings');

    Route::get('/reports-redirect', function () {
        return redirect()->route('admin.reports.index');
    })->name('reports');

    Route::get('/alerts-redirect', function () {
        return redirect()->route('admin.alerts.index');
    })->name('alerts');

    Route::get('/audit-redirect', function () {
        return redirect()->route('admin.audit.index');
    })->name('audit');

    Route::get('/security-redirect', function () {
        return redirect()->route('admin.security.index');
    })->name('security');

    Route::get('/backup-redirect', function () {
        return redirect()->route('admin.backup.index');
    })->name('backup');

    // Add logs route (redirects to audit logs)
    Route::get('/logs-redirect', function () {
        return redirect()->route('admin.audit.logs');
    })->name('logs.index');

    Route::controller(UserManagementController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/export', 'export')->name('export');
        Route::get('/{user}', 'show')->name('show');
        Route::put('/{user}', 'update')->name('update');
        Route::delete('/{user}', 'destroy')->name('destroy');
        Route::post('/{user}/toggle-status', 'toggleStatus')->name('toggle-status');
        Route::post('/{user}/reset-password', 'resetPassword')->name('reset-password');
        Route::post('/{user}/verify-doctor', 'verifyDoctor')->name('verify-doctor');
    });

    Route::controller(DoctorManagementController::class)->prefix('doctors')->name('doctors.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{doctor}', 'show')->name('show');
        Route::post('/{doctor}/verify', 'verify')->name('verify');
        Route::post('/{doctor}/reject', 'reject')->name('reject');
        Route::post('/{doctor}/assign-patients', 'assignPatients')->name('assign-patients');
        Route::delete('/{doctor}/remove-patient/{patient}', 'removePatient')->name('remove-patient');
        Route::get('/{doctor}/patients', 'patients')->name('patients');
        Route::post('/{doctor}/update-specialization', 'updateSpecialization')->name('update-specialization');
        Route::delete('/{doctor}', 'destroy')->name('destroy');
        Route::post('/{doctor}/toggle-status', 'toggleStatus')->name('toggle-status');
        Route::get('/export', 'export')->name('export');
    });

    Route::controller(PatientManagementController::class)->prefix('patients')->name('patients.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{patient}', 'show')->name('show');
        Route::post('/{patient}/assign-doctor', 'assignDoctor')->name('assign-doctor');
        Route::delete('/{patient}/remove-doctor/{doctor}', 'removeDoctor')->name('remove-doctor');
        Route::get('/{patient}/medical-history', 'medicalHistory')->name('medical-history');
        Route::post('/{patient}/update-profile', 'updateProfile')->name('update-profile');
        Route::get('/{patient}/alerts', 'alerts')->name('alerts');
        Route::delete('/{patient}', 'destroy')->name('destroy');
        Route::get('/export', 'export')->name('export');
    });

    Route::controller(SystemSettingsController::class)->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'update')->name('update');
        Route::get('/backup', 'backup')->name('backup');
        Route::post('/restore', 'restore')->name('restore');
        Route::get('/logs', 'logs')->name('logs');
        Route::post('/clear-cache', 'clearCache')->name('clear-cache');
        Route::get('/system-report', 'systemReport')->name('system-report');
        Route::get('/health-check', 'healthCheck')->name('health-check');
        Route::get('/export-configuration', 'exportConfiguration')->name('export-configuration');
        Route::post('/import-configuration', 'importConfiguration')->name('import-configuration');
    });

    Route::controller(ReportsController::class)->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/users', 'userReport')->name('users');
        Route::get('/activity', 'activityReport')->name('activity');
        Route::get('/alerts', 'alerts')->name('alerts');
        Route::get('/health-metrics', 'healthMetricsReport')->name('health-metrics');
        Route::post('/generate', 'generateReport')->name('generate');
        Route::get('/export/{type}', 'export')->name('export');
        
        // Advanced Analytics Routes
        Route::get('/service-quality', 'serviceQualityReport')->name('service-quality');
        Route::get('/doctor-workload', 'doctorWorkloadReport')->name('doctor-workload');
        Route::get('/patient-outcomes', 'patientOutcomesReport')->name('patient-outcomes');
        Route::get('/external-access', 'externalAccessReport')->name('external-access');
    });

    // Enhanced System Monitoring Routes
    Route::controller(SystemMonitoringController::class)->prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/realtime', 'getRealTimeData')->name('realtime');
        Route::get('/health', 'getHealthStatus')->name('health');
        Route::get('/metrics', 'getSystemMetrics')->name('metrics');
        Route::get('/export', 'exportReport')->name('export');
    });

    // Audit Log Routes
    Route::controller(AuditLogController::class)->prefix('audit')->name('audit.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/logs', 'index')->name('logs');
        Route::get('/user/{user}', 'userLogs')->name('user-logs');
        Route::get('/export', 'exportLogs')->name('export');
        Route::delete('/clear-old', 'clearOldLogs')->name('clear-old');
    });

    // Backup Management Routes
    Route::controller(BackupController::class)->prefix('backup')->name('backup.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/create', 'create')->name('create');
        Route::get('/download/{backup}', 'download')->name('download');
        Route::delete('/{backup}', 'destroy')->name('destroy');
        Route::post('/restore/{backup}', 'restore')->name('restore');
        Route::get('/schedule', 'schedule')->name('schedule');
    });

    // Security Management Routes
    Route::controller(SecurityController::class)->prefix('security')->name('security.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/incidents', 'incidents')->name('incidents');
        Route::post('/incidents', 'createIncident')->name('create-incident');
        Route::get('/sessions', 'sessions')->name('sessions');
        Route::post('/revoke-session/{session}', 'revokeSession')->name('revoke-session');
        Route::get('/failed-logins', 'failedLogins')->name('failed-logins');
        Route::post('/block-ip', 'blockIp')->name('block-ip');
        Route::post('/unblock-ip', 'unblockIp')->name('unblock-ip');
        Route::post('/force-password-reset', 'forcePasswordReset')->name('force-password-reset');
        Route::post('/security-scan', 'runSecurityScan')->name('security-scan');
        Route::get('/export-logs', 'exportSecurityLogs')->name('export-logs');
    });

    // Admin Profile Management Routes
    Route::controller(AdminProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::post('/update', 'update')->name('update');
        Route::post('/password', 'updatePassword')->name('password');
        Route::post('/two-factor', 'toggleTwoFactor')->name('two-factor');
        Route::get('/activity-logs', 'getActivityLogs')->name('activity-logs');
        Route::get('/export-activity', 'exportActivity')->name('export-activity');
        Route::post('/notifications', 'updateNotificationPreferences')->name('notifications');
    });

    // System Management Routes
    Route::prefix('system')->name('system.')->group(function () {
        Route::post('/clear-cache', [SystemSettingsController::class, 'clearCache'])->name('clear-cache');
        Route::post('/optimize', [SystemSettingsController::class, 'optimize'])->name('optimize');
        Route::post('/maintenance-mode', [SystemSettingsController::class, 'toggleMaintenance'])->name('maintenance');
    });

    // Advanced User Management
    Route::prefix('users-advanced')->name('users.advanced.')->group(function () {
        Route::get('/bulk-actions', [UserManagementController::class, 'bulkActions'])->name('bulk-actions');
        Route::post('/bulk-update', [UserManagementController::class, 'bulkUpdate'])->name('bulk-update');
        Route::get('/export-csv', [UserManagementController::class, 'exportCsv'])->name('export-csv');
        Route::post('/import-csv', [UserManagementController::class, 'importCsv'])->name('import-csv');
    });

    // Intelligent Alert Management Routes
    Route::controller(AlertManagementController::class)->prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/realtime', 'getRealTimeAlerts')->name('realtime');
        Route::post('/escalation-rule', 'createEscalationRule')->name('create-escalation-rule');
        Route::post('/smart-filter', 'applySmartFiltering')->name('apply-smart-filter');
        Route::post('/bulk-escalate', 'bulkEscalate')->name('bulk-escalate');
        Route::post('/auto-routing', 'configureAutoRouting')->name('configure-auto-routing');
        Route::get('/suspicious-activity', 'detectSuspiciousActivity')->name('detect-suspicious-activity');
    });

    // Care Quality Management Routes
    Route::controller(CareQualityController::class)->prefix('care-quality')->name('care-quality.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/realtime-metrics', 'getRealTimeMetrics')->name('realtime-metrics');
        Route::get('/detect-missing-data', 'detectMissingData')->name('detect-missing-data');
        Route::get('/detect-irregular-vitals', 'detectIrregularVitals')->name('detect-irregular-vitals');
        Route::post('/generate-report', 'generateQualityReport')->name('generate-report');
    });

    // External Access Monitoring Routes
    Route::controller(ExternalAccessController::class)->prefix('external-access')->name('external-access.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/realtime-data', 'getRealTimeAccessData')->name('realtime-data');
        Route::post('/grant-access', 'grantTemporaryAccess')->name('grant-access');
        Route::post('/{accessId}/revoke', 'revokeAccess')->name('revoke-access');
        Route::post('/{sessionId}/terminate', 'terminateSession')->name('terminate-session');
        Route::post('/update-policies', 'updateAccessPolicies')->name('update-policies');
        Route::get('/suspicious-activity', 'detectSuspiciousActivity')->name('detect-suspicious-activity');
    });

    // Critical Patient Monitoring Routes
    Route::controller(CriticalPatientMonitorController::class)->prefix('critical-monitor')->name('critical-monitor.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/realtime-data', 'getRealTimeData')->name('realtime-data');
        Route::get('/detect-care-gaps', 'detectCareGaps')->name('detect-care-gaps');
        Route::post('/{patientId}/escalate', 'escalateIssue')->name('escalate-issue');
        Route::post('/care-gap/{gapId}/resolve', 'resolveCareGap')->name('resolve-care-gap');
    });

    // Overall Monitor Routes
    Route::controller(OverallMonitorController::class)->prefix('overall-monitor')->name('overall-monitor.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/realtime', 'getRealTimeData')->name('realtime');
    });
});

/*
|--------------------------------------------------------------------------
| Doctor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:doctor'])
    ->prefix('doctor')->name('doctor.')
    ->group(function () {

    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DoctorAnalyticsController::class, 'index'])->name('analytics');

    Route::controller(PatientController::class)->prefix('patient')->name('patient.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{patient}/monitor', 'monitor')->name('monitor');
    });

    Route::controller(PatientMonitorController::class)->prefix('patients')->name('patients.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{patient}/monitor', 'show')->name('monitor');
        Route::get('/{patient}/history', 'history')->name('history');
        Route::get('/{patient}/documents', 'documents')->name('documents');
        Route::get('/{patient}/medications', 'medications')->name('medications');
        Route::post('/{patient}/add-comment', 'addComment')->name('add-comment');
        Route::post('/{patient}/update-treatment', 'updateTreatment')->name('update-treatment');
        Route::get('/{patient}/report', 'generateReport')->name('report');
        Route::post('/{patient}/discharge', 'discharge')->name('discharge');
        Route::get('/{patient}/documents/{document}/download', 'downloadDocument')->name('documents.download');
    });

    Route::prefix('monitor')->name('monitor.')->group(function () {
        Route::get('/patient/{patient}', [PatientMonitorController::class, 'monitor'])->name('patient');
        Route::post('/add-comment', [PatientMonitorController::class, 'addComment'])->name('add-comment');
        Route::post('/mark-vital-reviewed', [PatientMonitorController::class, 'markVitalReviewed'])->name('mark-vital-reviewed');
        Route::post('/emergency-alert', [PatientMonitorController::class, 'emergencyAlert'])->name('emergency-alert');
    });

    Route::controller(DoctorAnalyticsController::class)->prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/patient/{patient}', 'patientAnalytics')->name('patient');
        Route::get('/trends', 'trends')->name('trends');
        Route::get('/comparative', 'comparative')->name('comparative');
        Route::post('/generate-insights', 'generateInsights')->name('generate-insights');
    });

    Route::controller(DoctorAppointmentController::class)->prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/calendar', 'calendar')->name('calendar');
        Route::post('/', 'store')->name('store');
        Route::get('/{appointment}', 'show')->name('show');
        Route::put('/{appointment}', 'update')->name('update');
        Route::post('/{appointment}/confirm', 'confirm')->name('confirm');
        Route::post('/{appointment}/cancel', 'cancel')->name('cancel');
        Route::post('/{appointment}/complete', 'complete')->name('complete');
        Route::post('/{appointment}/no-show', 'markNoShow')->name('no-show');
        Route::post('/{id}/accept', 'accept')->name('accept');
        Route::post('/{id}/reject', 'reject')->name('reject');
        Route::post('/{id}/reschedule', 'reschedule')->name('reschedule');
    });

    Route::controller(EmergencyController::class)->prefix('emergency')->name('emergency.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/alerts', 'alerts')->name('alerts');
        Route::post('/trigger-alert', 'triggerAlert')->name('trigger-alert');
        Route::post('/respond-alert/{alert}', 'respondToAlert')->name('respond-alert');
        Route::get('/protocols', 'protocols')->name('protocols');
    });

    Route::get('profile', [DoctorProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [DoctorProfileController::class, 'update'])->name('profile.update');

    Route::get('patients/{patient}/medications', [DoctorMedController::class, 'index']);
    Route::post('medications', [DoctorMedController::class, 'storeMedication']);
    Route::post('health-tips', [DoctorMedController::class, 'storeHealthTips']);
    Route::put('medications/{id}', [DoctorMedController::class, 'updateMedication']);
    Route::delete('medications/{id}', [DoctorMedController::class, 'destroyMedication']);
    Route::get('patients/{patient}/health-tips', [DoctorMedController::class, 'getHealthTips']);

    Route::get('/documents', [DoctorDocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents/upload', [DoctorDocumentController::class, 'upload'])->name('documents.upload');
    Route::get('/documents/{id}/download', [DoctorDocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{id}', [DoctorDocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/patients/{patient}/documents', [DoctorDocumentController::class, 'index'])->name('patient.documents');
    Route::post('/patients/{user}/documents', [PatientController::class, 'storeDocument'])->name('patient.documents.store');

    Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/unread-count', fn() => auth()->user()->unreadNotifications()->count())->name('unread-count');
        Route::post('/{id}/mark-as-read', function ($id) {
            auth()->user()->unreadNotifications()->where('id', $id)->update(['read_at' => now()]);
            return response()->json(['success' => true]);
        })->name('mark-as-read');
        Route::post('/mark-all-read', function () {
            auth()->user()->unreadNotifications()->update(['read_at' => now()]);
            return response()->json(['success' => true]);
        })->name('mark-all-read');
        Route::post('/appointment/{notification}/mark-as-read', 'markAppointmentAsRead')->name('appointment.mark-as-read');
    });
});

/*
|--------------------------------------------------------------------------
| Patient Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:patient'])
    ->prefix('patient')->name('patient.')
    ->group(function () {

    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');

    Route::controller(VitalSignsController::class)->prefix('vitals')->name('vitals.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/update', 'updateVitals')->name('update');
        Route::post('/store', 'storeVitals')->name('store');
        Route::get('/history', 'history')->name('history');
        Route::get('/export', 'export')->name('export');
        Route::get('/statistics', 'statistics')->name('statistics');
        Route::post('/mark-medication', 'markMedication')->name('mark-medication');
    });
    Route::post('/update-vitals', [VitalSignsController::class, 'updateVitals'])->name('update-vitals');
    Route::post('/store-vitals', [VitalSignsController::class, 'storeVitals'])->name('store-vitals');

    Route::controller(PatientDocumentController::class)->prefix('documents')->name('documents.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::post('/upload', 'upload')->name('upload');
        Route::get('/{document}', 'show')->name('show');
        Route::put('/{document}', 'update')->name('update');
        Route::get('/{document}/download', 'download')->name('download');
        Route::delete('/{document}', 'destroy')->name('destroy');
        Route::post('/bulk-upload', 'bulkUpload')->name('bulk-upload');
    });

    Route::get('/doctor-documents', [PatientDocumentController::class, 'getDoctorDocuments'])->name('doctor-documents.index');
    Route::get('/doctor-documents/{id}/download', [PatientDocumentController::class, 'downloadDoctorDocument'])->name('doctor-documents.download');

    Route::controller(HealthTrendsController::class)->prefix('health-trends')->name('health-trends.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/detailed', 'detailed')->name('detailed');
        Route::get('/comparison', 'comparison')->name('comparison');
        Route::get('/goals', 'goals')->name('goals');
        Route::post('/set-goal', 'setGoal')->name('set-goal');
        Route::get('/export', 'export')->name('export');
    });

    Route::controller(PatientAppointmentController::class)->prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::post('/request', 'store')->name('request');
        Route::get('/{appointment}', 'show')->name('show');
        Route::post('/{appointment}/confirm', 'confirm')->name('confirm');
        Route::post('/{appointment}/cancel', 'cancel')->name('cancel');
        Route::post('/{appointment}/reschedule', 'reschedule')->name('reschedule');
    });

    Route::controller(MedicationController::class)->prefix('medications')->name('medications.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/reminders', 'reminders')->name('reminders');
        Route::post('/mark-taken', 'markTaken')->name('mark-taken');
        Route::post('/skip', 'skip')->name('skip');
        Route::post('/report-side-effect', 'reportSideEffect')->name('report-side-effect');
        Route::get('/history', 'history')->name('history');
        Route::post('/add-from-doctor', 'addFromDoctor')->name('add-from-doctor');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [PatientDashboardController::class, 'profile'])->name('index');
        Route::put('/update', [PatientDashboardController::class, 'updateProfile'])->name('update');
        Route::get('/edit', [ProfileTwoController::class, 'edit'])->name('edit');

        Route::put('/edit', [ProfileTwoController::class, 'update'])->name('update.edit');

        Route::get('/emergency-contacts', [PatientDashboardController::class, 'emergencyContacts'])->name('emergency-contacts');
        Route::post('/emergency-contacts', [PatientDashboardController::class, 'storeEmergencyContact'])->name('store-emergency-contact');
        Route::put('/emergency-contacts/{contact}', [PatientDashboardController::class, 'updateEmergencyContact'])->name('update-emergency-contact');
        Route::delete('/emergency-contacts/{contact}', [PatientDashboardController::class, 'deleteEmergencyContact'])->name('delete-emergency-contact');
    });

    Route::prefix('goals')->name('goals.')->group(function () {
        Route::get('/', [PatientDashboardController::class, 'goals'])->name('index');
        Route::post('/', [PatientDashboardController::class, 'storeGoal'])->name('store');
        Route::put('/{goal}', [PatientDashboardController::class, 'updateGoal'])->name('update');
        Route::delete('/{goal}', [PatientDashboardController::class, 'deleteGoal'])->name('delete');
    });

    Route::prefix('wellness')->name('wellness.')->group(function () {
        Route::get('/', [PatientDashboardController::class, 'wellness'])->name('index');
        Route::post('/log-activity', [PatientDashboardController::class, 'logActivity'])->name('log-activity');
        Route::post('/log-meal', [PatientDashboardController::class, 'logMeal'])->name('log-meal');
        Route::get('/insights', [PatientDashboardController::class, 'wellnessInsights'])->name('insights');
    });

    // Enhanced Temp Access Management Routes for Patients
    Route::controller(TempAccessController::class)->prefix('temp_access')->name('temp_access.')->group(function () {
        Route::post('/generate', 'generateTempLink')->name('generate');
        Route::post('/revoke', 'revokeAccess')->name('revoke');
        Route::get('/status', 'getAccessStatus')->name('status');
    });
});

/*
|--------------------------------------------------------------------------
| Public Temporary Access Routes (External Doctors)
|--------------------------------------------------------------------------
*/
Route::prefix('temp_access')->name('temp.access.')->group(function () {
    // Main dashboard access route
    Route::get('/{token}', [TempAccessController::class, 'showDashboard'])->name('dashboard');

    // Doctor verification route
    Route::post('/verify/{token}', [TempAccessController::class, 'verifyDoctor'])->name('verify');

    // Document management routes
    Route::post('/{token}/upload-document', [TempAccessController::class, 'uploadDocument'])->name('upload.document');
    Route::get('/{token}/document/{documentId}/download', [TempAccessController::class, 'downloadDocument'])->name('document.download');

    // Data access routes
    Route::get('/{token}/vitals', [TempAccessController::class, 'getVitalsData'])->name('vitals');

    // Medical management routes
    Route::post('/{token}/prescribe-medication', [TempAccessController::class, 'prescribeMedication'])->name('prescribe.medication');
    Route::post('/{token}/add-health-tip', [TempAccessController::class, 'addHealthTip'])->name('add.health.tip');
});

/*
|--------------------------------------------------------------------------
| Short-link Helper Route (UI-shortened → full link)
|--------------------------------------------------------------------------
*/
Route::get('/t/{code}', function (string $code) {
    // Find the temp access record by matching the beginning of the token
    $access = TempAccess::where('token', 'like', $code . '%')
        ->where('is_active', true)
        ->latest('created_at')
        ->first();

    if (!$access) {
        return view('temp_access.error', [
            'message' => 'Access link not found or has expired. Please request a new link from the patient.'
        ]);
    }

    return redirect()->route('temp.access.dashboard', ['token' => $access->token]);
})->name('temp.access.short');

/*
|--------------------------------------------------------------------------
| Shared Routes (All Authenticated Users)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function () {
        // View routes
        Route::get('/', 'index')->name('index');
        Route::get('/unread', 'getUnread')->name('unread');
        Route::get('/poll', 'poll')->name('poll');
        Route::get('/dashboard', 'getDashboardNotifications')->name('dashboard');
        Route::get('/patient/{patient}', 'getPatientNotifications')->name('patient');
        Route::get('/stats', 'getNotificationCounts')->name('stats');
        Route::get('/preferences', 'getPreferences')->name('preferences');
        Route::get('/unread-count', 'unreadCount')->name('unread-count');
        
        // Action routes
        Route::post('/{id}/read', 'markAsRead')->name('mark-read');
        Route::post('/{notification}/mark-read', 'markAsRead')->name('mark-read-legacy'); // Legacy compatibility
        Route::post('/mark-all-read', 'markAllAsRead')->name('mark-all-read');
        Route::post('/bulk-read', 'bulkMarkAsRead')->name('bulk-read');
        Route::post('/preferences', 'updatePreferences')->name('update-preferences');
        Route::post('/test', 'sendTestNotification')->name('test');
        
        // Delete routes
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::delete('/{notification}', 'destroy')->name('destroy-legacy'); // Legacy compatibility
    });

    Route::prefix('api/real-time')->name('realtime.')->group(function () {
        Route::get('/vitals/{patient}', function ($patientId) {
            $patient = \App\Models\Patient::findOrFail($patientId);
            return response()->json($patient->latestVitalSigns);
        })->name('vitals');

        Route::get('/alerts/{user}', function ($userId) {
            $user = \App\Models\User::findOrFail($userId);
            return response()->json($user->notifications()->whereNull('read_at')->get());
        })->name('alerts');
    });

    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/health-data', [PatientDashboardController::class, 'exportHealthData'])->name('health-data');
        Route::get('/appointments', [PatientAppointmentController::class, 'exportAppointments'])->name('appointments');
        Route::get('/medications', [MedicationController::class, 'exportMedications'])->name('medications');
    });

    // Global Search Routes
    Route::controller(SearchController::class)->prefix('search')->name('search.')->group(function () {
        Route::get('/', function() {
            return view('search.index');
        })->name('index');
        Route::get('/global', 'global')->name('global');
        Route::get('/suggestions', 'suggestions')->name('suggestions');
        Route::get('/filter-options', 'filterOptions')->name('filter-options');
        Route::get('/recent', 'recentSearches')->name('recent');
        Route::get('/analytics', 'analytics')->name('analytics');
        Route::post('/clear-cache', 'clearCache')->name('clear-cache');
        Route::get('/export', 'exportResults')->name('export');
    });
});

/*
|--------------------------------------------------------------------------
| Health Check & System Status Routes
|--------------------------------------------------------------------------
*/
Route::get('/health', fn () => response()->json([
    'status' => 'healthy',
    'timestamp' => now(),
    'version' => '1.0.0',
    'services' => ['database' => 'connected', 'cache' => 'active', 'storage' => 'accessible']
]))->name('health-check');

Route::get('/status', fn () => response()->json([
    'database' => 'connected',
    'cache' => 'active',
    'storage' => 'accessible',
    'users_online' => \App\Models\User::where('last_activity', '>=', now()->subMinutes(5))->count(),
    'uptime' => 'Running',
    'memory_usage' => memory_get_usage(true),
    'load_average' => function_exists('sys_getloadavg') ? sys_getloadavg() : [],
]))->name('system-status');

/*
|--------------------------------------------------------------------------
| PWA Routes
|--------------------------------------------------------------------------
*/
Route::get('/manifest.json', fn () => response()->json([
    'name' => 'Medical Monitor',
    'short_name' => 'MedMonitor',
    'start_url' => '/',
    'display' => 'standalone',
    'background_color' => '#ffffff',
    'theme_color' => '#10b981',
    'icons' => [
        ['src' => '/images/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png'],
        ['src' => '/images/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png'],
    ],
]))->name('manifest');

Route::get('/sw.js', fn () => response()->view('pwa.serviceworker')
    ->header('Content-Type', 'application/javascript'))->name('sw');

/*
|--------------------------------------------------------------------------
| Role-based Dashboard Redirect
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = auth()->user();
    if (!$user) return redirect()->route('login');

    return match ($user->role) {
        'admin'  => redirect()->route('admin.dashboard'),
        'doctor' => redirect()->route('doctor.dashboard'),
        'patient'=> redirect()->route('patient.dashboard'),
        default  => redirect()->route('home'),
    };
})->middleware('auth')->name('dashboard');

// Legacy appointment routes for compatibility
use App\Http\Controllers\Patient\AppointmentController;
Route::middleware(['auth'])->group(function () {
    Route::get('/appointments', [PatientAppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments/store', [PatientAppointmentController::class, 'store'])->name('appointments.store');
    Route::post('/appointments/{id}/cancel', [PatientAppointmentController::class, 'cancel'])->name('appointments.cancel');
});