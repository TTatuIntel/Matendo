@php
    $totalApplications = $pendingApplications + $approvedApplications + $rejectedApplications;
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
           class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
            </svg>
            PDF
        </a>
        <a href="{{ route('reports.export', ['type' => 'applications', 'format' => 'csv']) }}"
           class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            CSV
        </a>
        <a href="{{ route('reports.export', ['type' => 'applications', 'format' => 'excel']) }}"
           class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Excel
        </a>
    </div>
</div>

<!-- Grid Layout -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Application Status Overview -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-3">
            <h3 class="font-semibold text-gray-700">Application Status Overview</h3>
            <a href="{{ route('reports.export', ['type' => 'status', 'format' => 'pdf']) }}"
               class="text-xs text-blue-500 hover:text-blue-700 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
            </a>
        </div>

        <p class="text-gray-600 text-sm mb-2">Total Applications: {{ $totalApplications }}</p>

        <div class="grid grid-cols-3 gap-2 mb-4">
            <div class="bg-blue-50 p-3 rounded text-center">
                <p class="text-blue-600 font-bold">{{ $pendingApplications }}</p>
                <p class="text-xs text-gray-500">Pending</p>
            </div>
            <div class="bg-green-50 p-3 rounded text-center">
                <p class="text-green-600 font-bold">{{ $approvedApplications }}</p>
                <p class="text-xs text-gray-500">Approved</p>
            </div>
            <div class="bg-red-50 p-3 rounded text-center">
                <p class="text-red-600 font-bold">{{ $rejectedApplications }}</p>
                <p class="text-xs text-gray-500">Rejected</p>
            </div>
        </div>

        <div class="text-sm">
            <p class="text-gray-600"><span class="font-medium">Oldest Pending:</span> {{ $oldestPending }}</p>
            <p class="text-gray-600"><span class="font-medium">Avg Review Time:</span> {{ $avgReviewTime }}</p>
        </div>
    </div>

    <!-- Approval Metrics -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-3">
            <h3 class="font-semibold text-gray-700">Approval Metrics</h3>
            <a href="{{ route('reports.export', ['type' => 'metrics', 'format' => 'pdf']) }}"
               class="text-xs text-blue-500 hover:text-blue-700 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
            </a>
        </div>
        <div class="space-y-4">
            <div>
                <p class="text-gray-600 text-sm mb-1">New Approvals This Month</p>
                <p class="text-2xl font-bold text-green-600">{{ $newApproved }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm mb-1">Average Onboarding Time</p>
                <p class="text-xl font-medium text-blue-600">{{ $avgOnboardingTime }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm mb-1">Common Rejection Reasons</p>
                <p class="text-sm text-gray-700">{{ $commonRejectionReasons }}</p>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-3">
            <h3 class="font-semibold text-gray-700">Performance Metrics</h3>
            <a href="{{ route('reports.export', ['type' => 'performance', 'format' => 'pdf']) }}"
               class="text-xs text-blue-500 hover:text-blue-700 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
            </a>
        </div>
        <div class="h-40 bg-gray-50 rounded p-4">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-500">Processing Efficiency</span>
                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">{{ $avgReviewTime }}</span>
            </div>
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-500">Onboarding Speed</span>
                <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">{{ $avgOnboardingTime }}</span>
            </div>
            <div class="mt-6">
                <div class="flex items-center justify-between text-sm mb-1">
                    <span class="text-gray-500">Approval Rate</span>
                    <span class="font-medium">
                        @if(($approvedApplications + $rejectedApplications) > 0)
                            {{ round(($approvedApplications / ($approvedApplications + $rejectedApplications)) * 100) }}%
                        @else
                            0%
                        @endif
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full"
                         style="width: @if(($approvedApplications + $rejectedApplications) > 0)
                                      {{ ($approvedApplications / ($approvedApplications + $rejectedApplications)) * 100 }}%
                                   @else
                                      0%
                                   @endif">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Pie Chart -->
<div class="mt-8 bg-white rounded-lg shadow p-6">
    <h3 class="font-semibold text-gray-700 mb-4">Status Distribution</h3>
    <div class="flex flex-col items-center justify-center h-64">
        <div class="inline-block relative w-32 h-32 mb-2">
            <div class="absolute top-0 left-0 w-full h-full rounded-full border-4 border-blue-500"
                 style="clip-path: polygon(50% 50%, 50% 0, 100% 0, 100% 100%, 0 100%, 0 0);"></div>

            @if($totalApplications > 0)
                <div class="absolute top-0 left-0 w-full h-full rounded-full border-4 border-green-500"
                     style="clip-path: polygon(50% 50%, 100% 0, 100% 100%, 0 100%, 0 0); transform: rotate({{ ($pendingApplications / $totalApplications) * 360 }}deg);"></div>

                <div class="absolute top-0 left-0 w-full h-full rounded-full border-4 border-red-500"
                     style="clip-path: polygon(50% 50%, 100% 100%, 0 100%); transform: rotate({{ (($pendingApplications + $approvedApplications) / $totalApplications) * 360 }}deg);"></div>
            @endif
        </div>
        @if($totalApplications === 0)
            <p class="text-sm text-gray-400 italic">No application data available</p>
        @endif
        <div class="flex justify-center space-x-4 mt-2">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-blue-500 mr-1"></div>
                <span class="text-xs">Pending ({{ $pendingApplications }})</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-green-500 mr-1"></div>
                <span class="text-xs">Approved ({{ $approvedApplications }})</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-red-500 mr-1"></div>
                <span class="text-xs">Rejected ({{ $rejectedApplications }})</span>
            </div>
        </div>
    </div>
</div>
