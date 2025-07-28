<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Healthworker;
use App\Models\IndividualRequest;
use App\Models\FacilityRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $applications = Application::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $healthworkers = Healthworker::with('user')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $facilityRequests = FacilityRequest::latest()->paginate(10);
        $facilityStats = [
            'pending' => FacilityRequest::where('status', 'pending')->count(),
            'approved' => FacilityRequest::where('status', 'approved')->count(),
            'rejected' => FacilityRequest::where('status', 'rejected')->count(),
            'total' => FacilityRequest::count(),
        ];

        $individualRequests = IndividualRequest::latest()->paginate(10);
        $individualStats = [
            'pending' => IndividualRequest::where('status', 'pending')->count(),
            'approved' => IndividualRequest::where('status', 'approved')->count(),
            'rejected' => IndividualRequest::where('status', 'rejected')->count(),
            'total' => IndividualRequest::count(),
        ];

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
        $commonRejectionReasons = 'Missing documents, expired licenses';

        $month = $request->input('month', now()->month);
        $applicationsInMonth = Application::where('status', 'pending')
            ->whereMonth('created_at', $month)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $recentApplications = Application::latest('created_at')->get();
        $recentIndividualRequests = IndividualRequest::latest('created_at')->get();
        $recentFacilityRequests = FacilityRequest::latest('created_at')->get();
        $recentHealthworkers = Healthworker::latest('created_at')->get();
        $recentTasks = Task::latest('created_at')->get();

        $activities = collect()
            ->merge($recentApplications->map(function ($item) {
                return [
                    'type' => 'Application',
                    'title' => $item->title ?? 'Application #' . $item->id,
                    'description' => $item->description ?? ($item->user->name ?? 'Unknown') . ' submitted an application',
                    'status' => ucfirst($item->status ?? 'Pending'),
                    'timestamp' => $item->created_at,
                    'icon' => 'M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z',
                    'color' => 'blue',
                    'actions' => [
                        [
                            'label' => 'View Details',
                            'route' => url('/admin/dashboard?tab=applications&item=' . $item->id),
                            'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                        ],
                        [
                            'label' => 'Review',
                            'route' => url('/admin/dashboard?tab=applications&item=' . $item->id . '&action=review'),
                            'icon' => 'M5 13l4 4L19 7',
                        ],
                    ],
                ];
            }))
            ->merge($recentIndividualRequests->map(function ($item) {
                return [
                    'type' => 'Individual Request',
                    'title' => $item->full_name ?? 'Request #' . $item->id,
                    'description' => $item->description ?? ($item->full_name ?? 'Unknown') . ' submitted an individual request',
                    'status' => ucfirst($item->status ?? 'Pending'),
                    'timestamp' => $item->created_at,
                    'icon' => 'M10 18a8 8 0 100-16 8 8 0 000 16z',
                    'color' => 'green',
                    'actions' => [
                        [
                            'label' => 'View Details',
                            'route' => url('/admin/individual?item=' . $item->id),
                            'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                        ],
                        [
                            'label' => 'Manage',
                            'route' => url('/admin/individual?item=' . $item->id . '&action=manage'),
                            'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                        ],
                    ],
                ];
            }))
            ->merge($recentFacilityRequests->map(function ($item) {
                return [
                    'type' => 'Facility Request',
                    'title' => $item->facility_name ?? 'Facility Request #' . $item->id,
                    'description' => $item->description ?? ($item->facility_name ?? 'Unknown') . ' submitted a request',
                    'status' => ucfirst($item->status ?? 'Pending'),
                    'timestamp' => $item->created_at,
                    'icon' => 'M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z',
                    'color' => 'indigo',
                    'actions' => [
                        [
                            'label' => 'View Details',
                            'route' => url('/admin/facility?item=' . $item->id),
                            'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                        ],
                        [
                            'label' => 'Manage',
                            'route' => url('/admin/facility?item=' . $item->id . '&action=manage'),
                            'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                        ],
                    ],
                ];
            }))
            ->merge($recentHealthworkers->map(function ($item) {
                return [
                    'type' => 'Healthworker',
                    'title' => $item->user->name ?? 'Healthworker #' . $item->id,
                    'description' => 'New healthworker registration: ' . ($item->specialization ?? 'Unknown specialization'),
                    'status' => ucfirst($item->status ?? 'Pending'),
                    'timestamp' => $item->created_at,
                    'icon' => 'M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z',
                    'color' => 'purple',
                    'actions' => [
                        [
                            'label' => 'View Details',
                            'route' => url('/admin/dashboard?tab=healthworkers&item=' . $item->id),
                            'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                        ],
                    ],
                ];
            }))
            ->merge($recentTasks->map(function ($item) {
                return [
                    'type' => 'Task',
                    'title' => $item->facility_name ?? 'Task #' . $item->id,
                    'description' => $item->description ?? ($item->facility_name ?? 'Unknown') . ' approved as a task',
                    'status' => ucfirst($item->status ?? 'Approved'),
                    'timestamp' => $item->created_at,
                    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'color' => 'teal',
                    'actions' => [
                        [
                            'label' => 'View Details',
                            'route' => url('/admin/tasks?item=' . $item->id),
                            'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                        ],
                    ],
                ];
            }));

        $activities = $activities->sortByDesc('timestamp');

        $perPage = 10;
        $currentPage = Paginator::resolveCurrentPage('activity_page') ?: 1;
        $pagedActivities = $activities->forPage($currentPage, $perPage);
        $recentActivities = new LengthAwarePaginator(
            $pagedActivities,
            $activities->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'activity_page',
            ]
        );

        return view('admin.dashboard', [
            'applications' => $applications,
            'healthworkers' => $healthworkers,
            'facilityRequests' => $facilityRequests,
            'facilityStats' => $facilityStats,
        'facilityRequestsCount' => $facilityStats['total'], // Add this line
        'openRequests' => $facilityStats['pending'], // Add this line
    'individualRequestsCount' => $individualStats['total'],
    'pendingIndividualRequests' => $individualStats['pending'],
    'individualStats' => $individualStats,
            'individualRequests' => $individualRequests,
            'pendingApplications' => $pendingApplications,
            'approvedApplications' => $approvedApplications,
            'rejectedApplications' => $rejectedApplications,
            'oldestPending' => $oldestPending,
            'avgReviewTime' => $avgReviewTime,
            'newApproved' => $newApproved,
            'avgOnboardingTime' => $avgOnboardingTime,
            'recentActivities' => $recentActivities,
            'commonRejectionReasons' => $commonRejectionReasons,
            'applicationsInMonth' => $applicationsInMonth,
            'activeTab' => $request->input('tab', 'dashboard'),
            'activeItemId' => $request->input('item', null),
            'action' => $request->input('action', null),
        ]);
    }
}
