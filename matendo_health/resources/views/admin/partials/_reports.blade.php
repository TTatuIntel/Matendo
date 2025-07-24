@php
    $totalApplications = $pendingApplications + $approvedApplications + $rejectedApplications;
    $statusTotal = max(1, $totalApplications); // Prevent division by zero
@endphp

<h2 class="text-xl font-semibold mb-6">Reports & Analytics</h2>

<!-- Report Download Controls -->
<div class="mb-6 bg-white rounded-lg shadow p-4 flex flex-wrap justify-between items-center">
    <div>
        <h3 class="font-semibold text-gray-700">Export Reports</h3>
        <p class="text-gray-600 text-sm">Download comprehensive reports in various formats</p>
    </div>
    <div class="flex space-x-3 mt-2 md:mt-0">
        <a href="{{ route('reports.export', ['type' => 'applications', 'format' => 'pdf']) }}"
           class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded">PDF</a>
        <a href="{{ route('reports.export', ['type' => 'applications', 'format' => 'csv']) }}"
           class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded">CSV</a>
        <a href="{{ route('reports.export', ['type' => 'applications', 'format' => 'excel']) }}"
           class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded">Excel</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Applications Overview -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-gray-700 mb-3">Application Status Overview</h3>
        <p class="text-sm text-gray-600 mb-2">Total Applications: {{ $totalApplications }}</p>
        <div class="grid grid-cols-3 gap-2 mb-4">
            <div class="text-center bg-blue-50 p-2 rounded">
                <p class="text-blue-700 font-bold">{{ $pendingApplications }}</p>
                <p class="text-xs text-gray-500">Pending</p>
            </div>
            <div class="text-center bg-green-50 p-2 rounded">
                <p class="text-green-700 font-bold">{{ $approvedApplications }}</p>
                <p class="text-xs text-gray-500">Approved</p>
            </div>
            <div class="text-center bg-red-50 p-2 rounded">
                <p class="text-red-700 font-bold">{{ $rejectedApplications }}</p>
                <p class="text-xs text-gray-500">Rejected</p>
            </div>
        </div>
        <p class="text-sm text-gray-600">Oldest Pending: {{ $oldestPending }}</p>
        <p class="text-sm text-gray-600">Avg Review Time: {{ $avgReviewTime }}</p>
    </div>

    <!-- Approval Metrics -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-gray-700 mb-3">Approval Metrics</h3>
        <p class="text-sm text-gray-600 mb-1">New This Month</p>
        <p class="text-lg font-bold text-green-600">{{ $newApproved }}</p>
        <p class="text-sm text-gray-600 mt-4">Onboarding Time</p>
        <p class="text-base text-blue-600">{{ $avgOnboardingTime }}</p>
        <p class="text-sm text-gray-600 mt-4">Common Rejection Reasons</p>
        <p class="text-sm text-gray-800">{{ $commonRejectionReasons }}</p>
    </div>

    <!-- Pie Chart Placeholder -->
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <h3 class="font-semibold text-gray-700 mb-3">Application Status Chart</h3>
        <div class="inline-block relative w-32 h-32 mb-2">
            <div class="absolute top-0 left-0 w-full h-full rounded-full border-4 border-blue-500"
                 style="clip-path: polygon(50% 50%, 50% 0, 100% 0, 100% 100%, 0 100%, 0 0);"></div>
            <div class="absolute top-0 left-0 w-full h-full rounded-full border-4 border-green-500"
                 style="clip-path: polygon(50% 50%, 100% 0, 100% 100%, 0 100%, 0 0); transform: rotate({{ ($pendingApplications / $statusTotal) * 360 }}deg);"></div>
            <div class="absolute top-0 left-0 w-full h-full rounded-full border-4 border-red-500"
                 style="clip-path: polygon(50% 50%, 100% 100%, 0 100%); transform: rotate({{ (($pendingApplications + $approvedApplications) / $statusTotal) * 360 }}deg);"></div>
        </div>
        <div class="flex justify-center space-x-4 text-xs text-gray-700">
            <span class="flex items-center"><div class="w-3 h-3 bg-blue-500 mr-1"></div>Pending</span>
            <span class="flex items-center"><div class="w-3 h-3 bg-green-500 mr-1"></div>Approved</span>
            <span class="flex items-center"><div class="w-3 h-3 bg-red-500 mr-1"></div>Rejected</span>
        </div>
    </div>
</div>
