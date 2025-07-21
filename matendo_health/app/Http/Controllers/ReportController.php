<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Healthworker;
use App\Models\FacilityRequest;
use App\Models\IndividualRequest;
use App\Models\Task;
use App\Models\User;
use App\Exports\ApplicationsExport;
use App\Exports\StatusReportExport;
use App\Exports\MetricsReportExport;
use App\Exports\PerformanceReportExport;
use App\Exports\HealthWorkerExport;
use App\Exports\FacilityRequestExport;
use App\Exports\IndividualRequestExport;
use App\Exports\TaskExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    // public function dashboard()
    // {
    //     // Application Metrics
    //     $applicationStats = $this->getStatusData();

    //     // Health Worker Metrics
    //     $healthWorkerStats = $this->getHealthWorkerData();

    //     // Facility Request Metrics
    //     $facilityRequestStats = $this->getFacilityRequestData();

    //     // Individual Request Metrics
    //     $individualRequestStats = $this->getIndividualRequestData();

    //     // Task Metrics
    //     $taskStats = $this->getTaskData();

    //     return view('reports.dashboard', [
    //         // Application Data
    //         'pendingApplications' => $applicationStats['pending'],
    //         'approvedApplications' => $applicationStats['approved'],
    //         'rejectedApplications' => $applicationStats['rejected'],
    //         'oldestPending' => $applicationStats['oldestPending'],
    //         'avgReviewTime' => $applicationStats['avgReviewTime'],
    //         'newApproved' => $applicationStats['newApproved'],
    //         'commonRejectionReasons' => $applicationStats['commonRejectionReasons'],

    //         // Health Worker Data
    //         'healthWorkerStats' => $healthWorkerStats,
    //         'activeTasksCount' => $taskStats['activeTasksCount'],
    //         'completedTasksCount' => $taskStats['completedTasksCount'],
    //         'totalTasksCount' => $taskStats['total'],
    //         'unassignedTasksCount' => $taskStats['unassigned'],
    //         'avgAssignmentTime' => $taskStats['avgAssignmentTime'],
    //         'recentHealthWorkers' => $healthWorkerStats['recentSignups'],

    //         // Facility Request Data
    //         'facilityRequestStats' => $facilityRequestStats,
    //         'facilityRequestAvgTime' => $facilityRequestStats['avgProcessingTime'],
    //         'commonFacilityType' => $facilityRequestStats['commonFacilityType'],

    //         // Individual Request Data
    //         'individualRequestStats' => $individualRequestStats,
    //         'individualRequestAvgTime' => $individualRequestStats['avgProcessingTime'],
    //         'commonCareType' => $individualRequestStats['commonCareType'],

    //         // Task Data
    //         'taskStats' => $taskStats,
    //         'urgentTasksCount' => $taskStats['urgent'],
    //         'avgTaskCompletionTime' => $taskStats['avgCompletionTime'],
    //         'topFacilityType' => $taskStats['topFacilityType'],
    //         'topShiftType' => $taskStats['topShiftType'],
    //         'topEmploymentType' => $taskStats['topEmploymentType'],
    //         'onTimeCompletionRate' => $taskStats['onTimeCompletionRate'],
    //         'avgTaskDuration' => $taskStats['avgDuration'],
    //         'avgSatisfactionScore' => $taskStats['avgSatisfactionScore'],

    //         // Combined Metrics
    //         'totalRequestsCount' => $facilityRequestStats['total'] + $individualRequestStats['total'],
    //         'convertedRequestsCount' => $facilityRequestStats['converted'] + $individualRequestStats['converted']
    //     ]);
    // }

public function dashboard()
{
    // Application Metrics
    $applicationStats = $this->getStatusData();

    // Health Worker Metrics
    $healthWorkerStats = [
        'total' => User::where('usertype', 'healthworker')->count(),
        'verified' => User::where('usertype', 'healthworker')
            ->whereNotNull('email_verified_at')
            ->count(),
        'unverified' => User::where('usertype', 'healthworker')
            ->whereNull('email_verified_at')
            ->count(),
        'recentSignups' => User::where('usertype', 'healthworker')
            ->where('created_at', '>=', now()->subMonth())
            ->count()
    ];

    // Task Metrics
    $taskStats = [
        'total' => Task::count(),
        'unassigned' => Task::whereNull('assigned_to')->count(),
        'active' => Task::whereIn('status', ['approved', 'in_progress'])->count(),
        'completed' => Task::where('status', 'completed')->count()
    ];

    return view('admin.dashboard', [
        // Application Data
        'pendingApplications' => $applicationStats['pending'],
        'approvedApplications' => $applicationStats['approved'],
        'rejectedApplications' => $applicationStats['rejected'],
        'oldestPending' => $applicationStats['oldestPending'],
        'avgReviewTime' => $applicationStats['avgReviewTime'],
        'newApproved' => $applicationStats['newApproved'],

        // Health Worker Data
        'healthWorkerStats' => $healthWorkerStats,
        'activeTasksCount' => $taskStats['active'],
        'completedTasksCount' => $taskStats['completed'],
        'totalTasksCount' => $taskStats['total'],
        'unassignedTasksCount' => $taskStats['unassigned'],

        // Other necessary variables
        'commonRejectionReasons' => $applicationStats['commonRejectionReasons']
    ]);
}

    public function export(Request $request)
    {
        $type = $request->input('type', 'applications');
        $format = $request->input('format', 'pdf');

        switch ($type) {
            case 'status':
                $data = $this->getStatusData();
                $view = 'reports.status';
                $exportClass = StatusReportExport::class;
                $filename = 'application-status-report';
                break;

            case 'metrics':
                $data = $this->getMetricsData();
                $view = 'reports.metrics';
                $exportClass = MetricsReportExport::class;
                $filename = 'approval-metrics-report';
                break;

            case 'performance':
                $data = $this->getPerformanceData();
                $view = 'reports.performance';
                $exportClass = PerformanceReportExport::class;
                $filename = 'performance-metrics-report';
                break;

            case 'healthworkers':
                $data = $this->getHealthWorkerData();
                $view = 'reports.healthworkers';
                $exportClass = HealthWorkerExport::class;
                $filename = 'healthworker-report';
                break;

            case 'facility-requests':
                $data = $this->getFacilityRequestData();
                $view = 'reports.facility-requests';
                $exportClass = FacilityRequestExport::class;
                $filename = 'facility-request-report';
                break;

            case 'individual-requests':
                $data = $this->getIndividualRequestData();
                $view = 'reports.individual-requests';
                $exportClass = IndividualRequestExport::class;
                $filename = 'individual-request-report';
                break;

            case 'tasks':
                $data = $this->getTaskData();
                $view = 'reports.tasks';
                $exportClass = TaskExport::class;
                $filename = 'task-report';
                break;

            default:
                $data = $this->getApplicationsData();
                $view = 'reports.applications';
                $exportClass = ApplicationsExport::class;
                $filename = 'applications-report';
        }

        if ($format === 'pdf') {
            return Pdf::loadView($view, $data)
                     ->download($filename . '.pdf');
        }

        return Excel::download(new $exportClass($data), $filename . '.' . $format);
    }

    protected function getStatusData()
    {
        return [
            'pending' => Application::where('status', 'pending')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
            'oldestPending' => Application::where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->first()?->created_at?->diffForHumans() ?? 'N/A',
            'avgReviewTime' => round(Application::whereIn('status', ['approved', 'rejected'])
                ->avg(DB::raw('DATEDIFF(updated_at, created_at)')) ?? 0, 1) . ' days',
            'newApproved' => Application::where('status', 'approved')
                ->whereMonth('updated_at', now()->month)
                ->count(),
            'commonRejectionReasons' => 'Missing documents, expired licenses'
        ];
    }

    protected function getMetricsData()
    {
        return [
            'newApproved' => Application::where('status', 'approved')
                ->whereMonth('updated_at', now()->month)
                ->count(),
            'avgOnboardingTime' => round(Healthworker::whereNotNull('verified_at')
                ->avg(DB::raw('DATEDIFF(verified_at, created_at)')) ?? 0, 1) . ' days',
            'commonRejectionReasons' => 'Missing documents, expired licenses'
        ];
    }

    protected function getPerformanceData()
    {
        $approved = Application::where('status', 'approved')->count();
        $rejected = Application::where('status', 'rejected')->count();
        $total = $approved + $rejected;

        return [
            'avgReviewTime' => round(Application::whereIn('status', ['approved', 'rejected'])
                ->avg(DB::raw('DATEDIFF(updated_at, created_at)')) ?? 0, 1) . ' days',
            'avgOnboardingTime' => round(Healthworker::whereNotNull('verified_at')
                ->avg(DB::raw('DATEDIFF(verified_at, created_at)')) ?? 0, 1) . ' days',
            'approvalRate' => $total > 0 ? round(($approved / $total) * 100) : 0
        ];
    }

    protected function getApplicationsData()
    {
        return [
            'applications' => Application::with(['healthworker'])
                ->orderBy('created_at', 'desc')
                ->get(),
            'stats' => $this->getStatusData()
        ];
    }

    protected function getHealthWorkerData()
    {
        $recentSignups = User::where('usertype', 'healthworker')
            ->where('created_at', '>=', now()->subMonth())
            ->count();

        return [
            'total' => User::where('usertype', 'healthworker')->count(),
            'verified' => User::where('usertype', 'healthworker')
                ->whereNotNull('email_verified_at')
                ->count(),
            'unverified' => User::where('usertype', 'healthworker')
                ->whereNull('email_verified_at')
                ->count(),
            'recentSignups' => $recentSignups,
            'avgVerificationTime' => round(User::where('usertype', 'healthworker')
                ->whereNotNull('email_verified_at')
                ->avg(DB::raw('DATEDIFF(email_verified_at, created_at)')) ?? 0, 1) . ' days'
        ];
    }

    protected function getFacilityRequestData()
    {
        $converted = FacilityRequest::onlyTrashed()->count();

        return [
            'pending' => FacilityRequest::where('status', 'pending')->count(),
            'approved' => FacilityRequest::where('status', 'approved')->count(),
            'rejected' => FacilityRequest::where('status', 'rejected')->count(),
            'total' => FacilityRequest::withTrashed()->count(),
            'converted' => $converted,
            'avgProcessingTime' => round(FacilityRequest::whereIn('status', ['approved', 'rejected'])
                ->avg(DB::raw('DATEDIFF(updated_at, created_at)')) ?? 0, 1) . ' days',
            'commonFacilityType' => FacilityRequest::select('facility_type')
                ->groupBy('facility_type')
                ->orderByRaw('COUNT(*) DESC')
                ->first()->facility_type ?? 'N/A'
        ];
    }

    protected function getIndividualRequestData()
    {
        $converted = IndividualRequest::onlyTrashed()->count();

        return [
            'pending' => IndividualRequest::where('status', 'pending')->count(),
            'approved' => IndividualRequest::where('status', 'approved')->count(),
            'rejected' => IndividualRequest::where('status', 'rejected')->count(),
            'total' => IndividualRequest::withTrashed()->count(),
            'converted' => $converted,
            'avgProcessingTime' => round(IndividualRequest::whereIn('status', ['approved', 'rejected'])
                ->avg(DB::raw('DATEDIFF(updated_at, created_at)')) ?? 0, 1) . ' days',
            'commonCareType' => IndividualRequest::select('care_type')
                ->groupBy('care_type')
                ->orderByRaw('COUNT(*) DESC')
                ->first()->care_type ?? 'N/A'
        ];
    }

    protected function getTaskData()
    {
        $urgentCount = Task::where('priority', 'High')->count();
        $completedCount = Task::where('status', 'completed')->count();
        $unassignedCount = Task::whereNull('assigned_to')->count();

        // Calculate average completion time for completed tasks
        $avgCompletionTime = Task::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->avg(DB::raw('DATEDIFF(completed_at, assigned_at)')) ?? 0;

        // Get most common types
        $topFacilityType = Task::select('facility_type')
            ->groupBy('facility_type')
            ->orderByRaw('COUNT(*) DESC')
            ->first()->facility_type ?? 'N/A';

        $topShiftType = Task::select('shift_type')
            ->groupBy('shift_type')
            ->orderByRaw('COUNT(*) DESC')
            ->first()->shift_type ?? 'N/A';

        $topEmploymentType = Task::select('employment_type')
            ->groupBy('employment_type')
            ->orderByRaw('COUNT(*) DESC')
            ->first()->employment_type ?? 'N/A';

        return [
            'pending' => Task::where('status', 'pending')->count(),
            'approved' => Task::where('status', 'approved')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'completed' => $completedCount,
            'total' => Task::count(),
            'unassigned' => $unassignedCount,
            'urgent' => $urgentCount,
            'activeTasksCount' => Task::whereIn('status', ['approved', 'in_progress'])->count(),
            'completedTasksCount' => $completedCount,
            'avgCompletionTime' => round($avgCompletionTime, 1) . ' days',
            'avgAssignmentTime' => round(Task::whereNotNull('assigned_at')
                ->avg(DB::raw('DATEDIFF(assigned_at, created_at)')) ?? 0, 1) . ' days',
            'avgDuration' => round(Task::where('status', 'completed')
                ->whereNotNull('completed_at')
                ->avg(DB::raw('DATEDIFF(completed_at, assigned_at)')) ?? 0, 1) . ' days',
            'onTimeCompletionRate' => Task::where('status', 'completed')
                ->whereNotNull('completed_at')
                ->whereNotNull('due_date')
                ->whereRaw('completed_at <= due_date')
                ->count() / max(1, $completedCount) * 100,
            'topFacilityType' => $topFacilityType,
            'topShiftType' => $topShiftType,
            'topEmploymentType' => $topEmploymentType,
            'avgSatisfactionScore' => round(Task::whereNotNull('satisfaction_score')
                ->avg('satisfaction_score') ?? 0, 1)
        ];
    }
}
