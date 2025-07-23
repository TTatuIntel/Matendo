@php
    $totalApplications = $pendingApplications + $approvedApplications + $rejectedApplications;
@endphp

<h2 class="text-xl font-semibold mb-6">Reports & Analytics</h2>

<!-- Export Controls -->
<div class="mb-6 bg-white rounded-lg shadow p-4 flex flex-wrap justify-between items-center">
    <div>
        <h3 class="font-semibold text-gray-700">Export Reports</h3>
        <p class="text-gray-600 text-sm">Download comprehensive reports in various formats</p>
    </div>
    <div class="flex space-x-3 mt-2 md:mt-0">
        <a href="{{ route('reports.export', ['type' => 'applications', 'format' => 'pdf']) }}"
           class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded flex items-center">
            PDF
        </a>
        <a href="{{ route('reports.export', ['type' => 'applications', 'format' => 'csv']) }}"
           class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded flex items-center">
            CSV
        </a>
        <a href="{{ route('reports.export', ['type' => 'applications', 'format' => 'excel']) }}"
           class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded flex items-center">
            Excel
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Application Overview -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-3">
            <h3 class="font-semibold text-gray-700">Application Status Overview</h3>
            <a href="{{ route('reports.export', ['type' => 'status', 'format' => 'pdf']) }}"
               class="text-xs text-blue-500 hover:text-blue-700">Export</a>
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
            <p class="text-gray-600"><strong>Oldest Pending:</strong> {{ $oldestPending }}</p>
            <p class="text-gray-600"><strong>Avg Review Time:</strong> {{ $avgReviewTime }}</p>
        </div>
    </div>

    <!-- Approval Metrics -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-gray-700 mb-3">Approval Metrics</h3>
        <div class="space-y-4">
            <div>
                <p class="text-gray-600 text-sm">New Approvals This Month</p>
                <p class="text-2xl font-bold text-green-600">{{ $newApproved }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Average Onboarding Time</p>
                <p class="text-xl font-medium text-blue-600">{{ $avgOnboardingTime }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Common Rejection Reasons</p>
                <p class="text-sm text-gray-700">{{ $commonRejectionReasons }}</p>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-gray-700 mb-3">Performance Metrics</h3>
        <div class="space-y-3">
            <div class="flex justify-between text-sm">
                <span>Processing Efficiency</span>
                <span class="text-blue-600">{{ $avgReviewTime }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>Onboarding Speed</span>
                <span class="text-green-600">{{ $avgOnboardingTime }}</span>
            </div>
            <div class="mt-4">
                <p class="text-sm mb-1">Approval Rate</p>
                @php
                    $decisionCount = $approvedApplications + $rejectedApplications;
                    $approvalRate = $decisionCount > 0 ? round(($approvedApplications / $decisionCount) * 100) : 0;
                @endphp
                <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $approvalRate }}%"></div>
                </div>
                <span class="text-xs text-gray-500">{{ $approvalRate }}%</span>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Analytics Section -->
<div class="mt-8 bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-semibold text-gray-700">Detailed Application Analytics</h3>
        <div class="flex space-x-2">
            <a href="{{ route('reports.export', ['type' => 'detailed', 'format' => 'pdf']) }}" class="text-sm text-blue-500">Full Report</a>
            <a href="{{ route('reports.export', ['type' => 'monthly', 'format' => 'excel']) }}" class="text-sm text-blue-500">Monthly Data</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Pie Chart -->
        <div class="text-center">
            <h4 class="text-md font-medium text-gray-700 mb-3">Status Distribution</h4>
            <div class="inline-block relative w-32 h-32 mb-4">
                <div class="absolute top-0 left-0 w-full h-full rounded-full border-4 border-blue-500"
                     style="clip-path: polygon(50% 50%, 50% 0, 100% 0, 100% 100%, 0 100%, 0 0);"></div>
                <div class="absolute top-0 left-0 w-full h-full rounded-full border-4 border-green-500"
                     style="clip-path: polygon(50% 50%, 100% 0, 100% 100%, 0 100%, 0 0); transform: rotate({{ $totalApplications > 0 ? ($pendingApplications / $totalApplications) * 360 : 0 }}deg);"></div>
                <div class="absolute top-0 left-0 w-full h-full rounded-full border-4 border-red-500"
                     style="clip-path: polygon(50% 50%, 100% 100%, 0 100%); transform: rotate({{ $totalApplications > 0 ? (($pendingApplications + $approvedApplications) / $totalApplications) * 360 : 0 }}deg);"></div>
            </div>
            <div class="flex justify-center space-x-4 text-xs">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-500 mr-1"></div>
                    <span>Pending ({{ $pendingApplications }})</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-500 mr-1"></div>
                    <span>Approved ({{ $approvedApplications }})</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-red-500 mr-1"></div>
                    <span>Rejected ({{ $rejectedApplications }})</span>
                </div>
            </div>
        </div>

        <!-- Recent Stats -->
        <div class="space-y-3">
            <div class="flex justify-between bg-gray-50 p-3 rounded">
                <div>
                    <p class="text-sm font-medium">Pending Applications</p>
                    <p class="text-xs text-gray-500">Awaiting review</p>
                </div>
                <span class="text-blue-600">{{ $pendingApplications }}</span>
            </div>
            <div class="flex justify-between bg-gray-50 p-3 rounded">
                <div>
                    <p class="text-sm font-medium">Avg Processing Time</p>
                    <p class="text-xs text-gray-500">Submission to decision</p>
                </div>
                <span class="text-purple-600">{{ $avgReviewTime }}</span>
            </div>
            <div class="flex justify-between bg-gray-50 p-3 rounded">
                <div>
                    <p class="text-sm font-medium">New Approvals</p>
                    <p class="text-xs text-gray-500">This month</p>
                </div>
                <span class="text-green-600">{{ $newApproved }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Archive Table -->
<div class="mt-8 bg-white rounded-lg shadow p-6">
    <h3 class="font-semibold text-gray-700 mb-4">Report Archive</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-6 py-3 text-left font-medium">Report Name</th>
                    <th class="px-6 py-3 text-left font-medium">Date Generated</th>
                    <th class="px-6 py-3 text-left font-medium">Format</th>
                    <th class="px-6 py-3 text-left font-medium">Size</th>
                    <th class="px-6 py-3 text-left font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 font-medium text-gray-900">Monthly Applications Summary</td>
                    <td class="px-6 py-4">{{ now()->subDays(5)->format('M d, Y') }}</td>
                    <td class="px-6 py-4">PDF</td>
                    <td class="px-6 py-4">1.2 MB</td>
                    <td class="px-6 py-4">
                        <a href="#" class="text-blue-600 hover:underline mr-3">Download</a>
                        <a href="#" class="text-red-600 hover:underline">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-medium text-gray-900">Annual Performance Report</td>
                    <td class="px-6 py-4">{{ now()->subDays(15)->format('M d, Y') }}</td>
                    <td class="px-6 py-4">Excel</td>
                    <td class="px-6 py-4">2.8 MB</td>
                    <td class="px-6 py-4">
                        <a href="#" class="text-blue-600 hover:underline mr-3">Download</a>
                        <a href="#" class="text-red-600 hover:underline">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-medium text-gray-900">Rejection Analysis</td>
                    <td class="px-6 py-4">{{ now()->subDays(30)->format('M d, Y') }}</td>
                    <td class="px-6 py-4">CSV</td>
                    <td class="px-6 py-4">0.5 MB</td>
                    <td class="px-6 py-4">
                        <a href="#" class="text-blue-600 hover:underline mr-3">Download</a>
                        <a href="#" class="text-red-600 hover:underline">Delete</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
