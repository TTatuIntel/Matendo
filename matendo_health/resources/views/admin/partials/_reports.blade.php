{{-- 
<h2 class="text-xl font-semibold mb-6">Reports & Analytics</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    … old placeholder cards …
</div>
--}}

@php
    // totals & rates
    $totalApplications   = $pendingApplications + $approvedApplications + $rejectedApplications;
    $totalStatusChanges  = $approvedApplications + $rejectedApplications;
    $approvalRateValue   = $totalStatusChanges > 0
        ? round(($approvedApplications / $totalStatusChanges) * 100)
        : 0;
    $approvalRatePercent = $totalStatusChanges > 0
        ? ($approvedApplications / $totalStatusChanges) * 100
        : 0;
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
            <!-- PDF icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" …></svg>
            PDF
        </a>
        <a href="{{ route('reports.export', ['type' => 'applications', 'format' => 'csv']) }}"
           class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded flex items-center">
            <!-- CSV icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" …></svg>
            CSV
        </a>
        <a href="{{ route('reports.export', ['type' => 'applications', 'format' => 'excel']) }}"
           class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded flex items-center">
            <!-- Excel icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" …></svg>
            Excel
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Application Status Overview -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-3">
            <h3 class="font-semibold text-gray-700">Application Status Overview</h3>
            <a href="{{ route('reports.export', ['type' => 'status', 'format' => 'pdf']) }}"
               class="text-xs text-blue-500 hover:text-blue-700 flex items-center">
                <!-- Export icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" …></svg>
                Export
            </a>
        </div>
        <p class="text-gray-600 text-sm mb-4">
            Total Applications: {{ $totalApplications }}
        </p>
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
        <div class="text-sm space-y-1">
            <p class="text-gray-600">
                <span class="font-medium">Oldest Pending:</span>
                {{ $oldestPending }}
            </p>
            <p class="text-gray-600">
                <span class="font-medium">Avg Review Time:</span>
                {{ $avgReviewTime }}
            </p>
        </div>
    </div>

    <!-- Approval Metrics -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-3">
            <h3 class="font-semibold text-gray-700">Approval Metrics</h3>
            <a href="{{ route('reports.export', ['type' => 'metrics', 'format' => 'pdf']) }}"
               class="text-xs text-blue-500 hover:text-blue-700 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" …></svg>
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
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" …></svg>
                Export
            </a>
        </div>

        <!-- Pie Chart / Progress -->
        @if($totalApplications > 0)
            <div class="inline-block relative w-32 h-32 mb-4">
                <!-- Pending slice (blue) -->
                <div class="absolute inset-0 rounded-full border-4 border-blue-500"
                     style="clip-path: polygon(50% 50%, 50% 0, 100% 0, 100% 100%, 0 100%, 0 0);">
                </div>
                <!-- Approved slice (green) -->
                <div class="absolute inset-0 rounded-full border-4 border-green-500"
                     style="clip-path: polygon(50% 50%, 100% 0, 100% 100%, 0 100%, 0 0);
                            transform: rotate({{ ($pendingApplications / $totalApplications) * 360 }}deg);">
                </div>
                <!-- Rejected slice (red) -->
                <div class="absolute inset-0 rounded-full border-4 border-red-500"
                     style="clip-path: polygon(50% 50%, 100% 100%, 0 100%);
                            transform: rotate({{ (($pendingApplications + $approvedApplications) / $totalApplications) * 360 }}deg);">
                </div>
            </div>
        @else
            <div class="h-32 flex items-center justify-center text-gray-400 italic mb-4">
                No application data available
            </div>
        @endif

        <div class="mb-4 text-sm">
            <p class="flex justify-between">
                <span class="text-gray-500">Processing Efficiency</span>
                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                    {{ $avgReviewTime }}
                </span>
            </p>
            <p class="flex justify-between mt-2">
                <span class="text-gray-500">Onboarding Speed</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full">
                    {{ $avgOnboardingTime }}
                </span>
            </p>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1 text-sm">
                <span class="text-gray-500">Approval Rate</span>
                <span class="font-medium">{{ $approvalRateValue }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full bg-green-600"
                     style="width: {{ $approvalRatePercent }}%;">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Detailed Reports Section -->
<div class="mt-8 bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-semibold text-gray-700">Detailed Application Analytics</h3>
        <div class="flex space-x-2">
            <a href="{{ route('reports.export', ['type' => 'detailed', 'format' => 'pdf']) }}"
               class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" …></svg>
                Full Report
            </a>
            <a href="{{ route('reports.export', ['type' => 'monthly', 'format' => 'excel']) }}"
               class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" …></svg>
                Monthly Data
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Status Distribution Pie -->
        <div>
            <h4 class="text-md font-medium text-gray-700 mb-3">Status Distribution</h4>
            @if($totalApplications > 0)
                <div class="h-64 bg-gray-100 rounded flex items-center justify-center">
                    {{-- replicate above pie markup or use a chart library --}}
                </div>
            @else
                <p class="text-center text-gray-400 italic">No data to display.</p>
            @endif
        </div>

        <!-- Recent Activity -->
        <div>
            <h4 class="text-md font-medium text-gray-700 mb-3">Recent Activity</h4>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <div>
                        <p class="text-sm font-medium">Pending Applications</p>
                        <p class="text-xs text-gray-500">Waiting for review</p>
                    </div>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                        {{ $pendingApplications }}
                    </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <div>
                        <p class="text-sm font-medium">Avg Processing Time</p>
                        <p class="text-xs text-gray-500">From submission to decision</p>
                    </div>
                    <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
                        {{ $avgReviewTime }}
                    </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <div>
                        <p class="text-sm font-medium">New Approvals</p>
                        <p class="text-xs text-gray-500">This month</p>
                    </div>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                        {{ $newApproved }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sample Reports Archive -->
<div class="mt-8 bg-white rounded-lg shadow p-6">
    <h3 class="font-semibold text-gray-700 mb-4">Report Archive</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Generated</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Format</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Monthly Applications Summary</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ now()->subDays(5)->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">PDF</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1.2 MB</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Download</a>
                        <a href="#" class="text-red-600 hover:text-red-900">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Annual Performance Report</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ now()->subDays(15)->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Excel</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2.8 MB</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Download</a>
                        <a href="#" class="text-red-600 hover:text-red-900">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Rejection Analysis</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ now()->subDays(30)->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">CSV</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">0.5 MB</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Download</a>
                        <a href="#" class="text-red-600 hover:text-red-900">Delete</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
