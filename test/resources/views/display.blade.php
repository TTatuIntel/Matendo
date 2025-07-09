
<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Dashboard - Medical Records</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Add this right after the Tailwind script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Medical Records
                    </h2>
                    <div class="flex space-x-4">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">Back to Dashboard</a>
                        <a href="{{ route('upload') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Upload Documents</a>
                        <button id="view-all-data" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">View All Data</button>

                                                    <button id="view-previous" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Previous
                        </button>
    <button id="download-data" class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 transition text-sm flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        JSON
    </button>
    <button id="download-pdf" class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition text-sm flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        PDF
</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 flex items-center">
                            <div class="bg-blue-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">Last Vitals</h3>
                                @php
                                    $lastVitals = $records->where('category', 'vitals')->first();
                                @endphp
                                @if ($lastVitals)
                                    <p class="text-sm text-gray-500">BP: {{ $lastVitals->data['systolic'] ?? 'N/A' }}/{{ $lastVitals->data['diastolic'] ?? 'N/A' }} mmHg</p>
                                    <p class="text-xs text-blue-600">{{ $lastVitals->created_at->format('M d, Y') }}</p>
                                @else
                                    <p class="text-sm text-gray-500">No data</p>
                                    <p class="text-xs text-blue-600">N/A</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 flex items-center">
                            <div class="bg-green-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">Medications</h3>
                                @php
                                    $medCount = $records->where('category', 'treatments')->count();
                                @endphp
                                <p class="text-sm text-gray-500">{{ $medCount }} Active</p>
                                <p class="text-xs text-green-600">Current</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 flex items-center">
                            <div class="bg-purple-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">Lab Results</h3>
                                @php
                                    $labCount = $records->where('category', 'labs')->count();
                                @endphp
                                <p class="text-sm text-gray-500">{{ $labCount }} Recent</p>
                                <p class="text-xs text-purple-600">This Week</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 flex items-center">
                            <div class="bg-yellow-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">Appointments</h3>
                                <p class="text-sm text-gray-500">Next: June 20</p>
                                <p class="text-xs text-yellow-600">Upcoming</p>
                            </div>
                        </div>
                    </div>

<!-- Add this card with the other summary cards -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 flex items-center">
        <div class="bg-indigo-100 p-3 rounded-full mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <h3 class="font-medium">Doctor Documents</h3>
            @php
                $doctorDocCount = $documents->where('user_id', auth()->id())->count();
            @endphp
            <p class="text-sm text-gray-500">{{ $doctorDocCount }} Uploaded</p>
            <button class="text-xs text-indigo-600 hover:text-indigo-800 view-doctor-docs">View All</button>
        </div>
    </div>
</div>
                </div>

                <!-- Recent Overview -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <div class="bg-blue-100 p-2 rounded-full mr-3">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </div>
                                Recent Vitals
                            </h3>
                            <div class="space-y-3">
                                @php
                                    $recentVitals = $records->where('category', 'vitals')->take(3);
                                @endphp
                                @forelse ($recentVitals as $vital)
                                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-blue-800">Blood Pressure</p>
                                            <p class="text-sm text-blue-600">{{ $vital->data['systolic'] ?? 'N/A' }}/{{ $vital->data['diastolic'] ?? 'N/A' }} mmHg</p>
                                        </div>
                                        <span class="text-xs text-blue-500">{{ $vital->created_at->format('M d, Y') }}</span>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No recent vitals</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 shadow-sm rounded-lg">
    <h3 class="text-lg font-semibold mb-4 flex items-center text-gray-800">
        <div class="bg-blue-100 p-2 rounded-full mr-3">
            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
        </h3>
        Blood Pressure Trends
    </h3>
    <div class="relative h-64">
        <canvas id="bp-chart"></canvas>
    </div>
    <p id="bp-no-data" class="text-sm text-gray-500 hidden">No blood pressure data available.</p>
</div>
                </div>

                <!-- Filter and Search -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                            <div class="flex space-x-4">
                                <select id="category-filter" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                                    <option value="">All Categories</option>
                                    <option value="vitals">Vitals</option>
                                    <option value="activity">Activity & Hydration</option>
                                    <option value="pain">Pain & Emotion</option>
                                    <option value="sleep">Sleep & Rest</option>
                                    <option value="wellbeing">General Well-being</option>
                                    <option value="labs">Labs & Biomarkers</option>
                                    <option value="infection">Infection Markers</option>
                                    <option value="treatments">Treatments</option>
                                    <option value="appointments">Appointments</option>
                                </select>
                                <select id="time-filter" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                                    <option value="">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                    <option value="year">This Year</option>
                                </select>
                            </div>
                            <div class="flex w-full md:w-auto">
                                <input type="text" id="search-input" placeholder="Search records..." class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-md text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Records Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">All Medical Records</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="records-table">
                                    @php
                                        // Group records by category to show only the latest per category in the table
                                        $latestRecords = $records->groupBy('category')->map->first();
                                    @endphp
                                    @forelse ($latestRecords as $record)
                                        <tr class="record-row" data-category="{{ $record->category }}" data-id="{{ $record->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ ucfirst($record->category) }}
                                                <button class="ml-2 text-blue-600 hover:text-blue-800 view-history" data-category="{{ $record->category }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    History
                                                </button>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                @php
                                                    $details = [];
                                                    foreach ($record->data as $key => $value) {
                                                        $key = str_replace('_', ' ', ucwords($key, '_'));
                                                        $details[] = "$key: $value";
                                                    }
                                                    echo implode(', ', $details);
                                                @endphp
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->created_at->format('Y-m-d') }}</td>
<!-- In the table actions column, replace the delete form with this -->
<td class="px-6 py-4 whitespace-nowrap text-sm">
    <a href="#" class="text-blue-600 hover:text-blue-800 view-record" data-record='{{ json_encode($record) }}'>View</a>
    <a href="#" class="ml-4 text-purple-600 hover:text-purple-800 graph-record" data-record='{{ json_encode($record) }}'>Graph</a>
</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-sm text-gray-500 text-center">No records found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Record Modal -->
        <div id="view-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <div class="bg-blue-100 p-2 rounded-full mr-3">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    Record Details
                </h3>
                <div id="modal-content" class="space-y-2"></div>
                <button id="close-modal" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition w-full">Close</button>
            </div>
        </div>

        <!-- History Modal -->
        <div id="history-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full max-h-[80vh] overflow-y-auto">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <div class="bg-blue-100 p-2 rounded-full mr-3">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span id="history-category">History</span>
                </h3>
                <div id="history-content" class="space-y-4"></div>
                <button id="close-history" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition w-full">Close</button>
            </div>
        </div>

        <!-- Previous Records Modal -->
        <div id="previous-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full max-h-[80vh] overflow-y-auto">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <div class="bg-blue-100 p-2 rounded-full mr-3">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    Previous Records
                </h3>
                <div id="previous-content" class="space-y-4"></div>
                <button id="close-previous" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition w-full">Close</button>
            </div>
        </div>

        <!-- View All Data Modal -->
          <!-- View All Data Modal -->
        <div id="view-all-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[80vh] overflow-y-auto">
                <h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-2 rounded-full mr-3">
                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </div>
                        All Medical Data
                    </div>
                    <div class="flex space-x-2">
                        <button id="download-all-json" class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 transition text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            JSON
                        </button>
                        <button id="download-all-pdf" class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            PDF
                        </button>
                    </div>
                </h3>
                <div id="all-data-content" class="space-y-6"></div>
                <button id="close-all-data" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition w-full">Close</button>
            </div>
        </div>
<!-- Doctor Documents Modal -->
<div id="doctor-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[80vh] overflow-y-auto">
        <h3 class="text-lg font-semibold mb-4 flex items-center">
            <div class="bg-indigo-100 p-2 rounded-full mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            My Uploaded Documents
        </h3>
        <div id="doctor-content" class="space-y-4">
            <!-- Content will be loaded here -->
        </div>
        <button id="close-doctor" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition w-full">Close</button>
    </div>
</div>


<!-- Graph Modal -->
<div id="graph-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl w-full">
<!-- Update the graph modal header section -->
<h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
    <div class="flex items-center">
        <div class="bg-purple-100 p-2 rounded-full mr-3">
            <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </div>
        <span id="graph-title">Data Visualization</span>
    </div>
    <div class="flex space-x-2">
        <button id="show-history-graph" class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition text-sm flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            History
        </button>
        <button id="download-graph" class="px-3 py-1 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition text-sm flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            PDF
        </button>
    </div>
</h3>
        <div class="w-full h-96">
            <canvas id="data-chart"></canvas>
        </div>
        <button id="close-graph" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition w-full">Close</button>
    </div>
</div>

    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('category-filter');
    const timeFilter = document.getElementById('time-filter');
    const searchInput = document.getElementById('search-input');
    const recordRows = document.querySelectorAll('.record-row');
    const viewModal = document.getElementById('view-modal');
    const modalContent = document.getElementById('modal-content');
    const closeModal = document.getElementById('close-modal');
    const historyModal = document.getElementById('history-modal');
    const historyCategory = document.getElementById('history-category');
    const historyContent = document.getElementById('history-content');
    const closeHistory = document.getElementById('close-history');
    const previousModal = document.getElementById('previous-modal');
    const previousContent = document.getElementById('previous-content');
    const closePrevious = document.getElementById('close-previous');
    const viewPrevious = document.getElementById('view-previous');
    const viewLinks = document.querySelectorAll('.view-record');
    const deleteLinks = document.querySelectorAll('.delete-record');
    const historyButtons = document.querySelectorAll('.view-history');

    // Graph functionality
    const graphModal = document.getElementById('graph-modal');
    const closeGraph = document.getElementById('close-graph');
    const downloadGraphBtn = document.getElementById('download-graph');
    const showHistoryGraphBtn = document.getElementById('show-history-graph');
    const graphTitle = document.getElementById('graph-title');
    let currentChart = null;
    let currentRecord = null;

    // Graph record click handlers
    document.querySelectorAll('.graph-record').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const record = JSON.parse(this.dataset.record);
            showGraphForRecord(record);
            graphModal.classList.remove('hidden');
        });
    });

    closeGraph.addEventListener('click', () => {
        graphModal.classList.add('hidden');
        if (currentChart) {
            currentChart.destroy();
            currentChart = null;
        }
    });

    graphModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            if (currentChart) {
                currentChart.destroy();
                currentChart = null;
            }
        }
    });

    downloadGraphBtn.addEventListener('click', function() {
        if (!currentChart) return;

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');

        // Get chart as image
        const canvas = document.getElementById('data-chart');
        const chartImage = canvas.toDataURL('image/png');

        // Add to PDF
        doc.setFontSize(16);
        doc.text(graphTitle.textContent, 20, 20);
        doc.addImage(chartImage, 'PNG', 15, 30, 260, 150);

        // Save PDF
        const fileName = `medical_graph_${new Date().toISOString().split('T')[0]}.pdf`;
        doc.save(fileName);
    });

    // History button in graph modal
    showHistoryGraphBtn.addEventListener('click', function() {
        if (!currentRecord) return;

        const category = currentRecord.category;
        const categoryRecords = allRecords.filter(record => record.category === category);
        historyCategory.textContent = `${category.charAt(0).toUpperCase() + category.slice(1)} History`;
        historyContent.innerHTML = categoryRecords.length > 0 ? categoryRecords.map(record => `
            <div class="p-3 bg-gray-50 rounded-lg hover:bg-blue-50 cursor-pointer history-record" data-record='${JSON.stringify(record)}'>
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium text-gray-800">
                            ${Object.entries(record.data).slice(0, 2).map(([key, value]) =>
                                `${key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}: ${value}`
                            ).join(', ')}
                        </p>
                        <p class="text-sm text-gray-500">${new Date(record.created_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</p>
                    </div>
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-800 text-sm view-history-record">View</button>
                        <button class="text-purple-600 hover:text-purple-800 text-sm graph-history-record">Graph</button>
                    </div>
                </div>
            </div>
        `).join('') : '<p class="text-sm text-gray-500">No records found for this category.</p>';

        // Add event listeners for view and graph buttons
        document.querySelectorAll('.view-history-record').forEach(btn => {
            btn.addEventListener('click', function() {
                const record = JSON.parse(this.closest('.history-record').dataset.record);
                modalContent.innerHTML = `
                    <p><strong>Category:</strong> ${record.category.charAt(0).toUpperCase() + record.category.slice(1)}</p>
                    <p><strong>Date:</strong> ${new Date(record.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                    <p><strong>Details:</strong></p>
                    <ul class="list-disc pl-5">
                        ${Object.entries(record.data).map(([key, value]) => `<li>${key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}: ${value}</li>`).join('')}
                    </ul>
                `;
                historyModal.classList.add('hidden');
                viewModal.classList.remove('hidden');
            });
        });

        document.querySelectorAll('.graph-history-record').forEach(btn => {
            btn.addEventListener('click', function() {
                const record = JSON.parse(this.closest('.history-record').dataset.record);
                showGraphForRecord(record);
                historyModal.classList.add('hidden');
                graphModal.classList.remove('hidden');
            });
        });

        // Show history modal and hide graph modal
        graphModal.classList.add('hidden');
        if (currentChart) {
            currentChart.destroy();
            currentChart = null;
        }
        historyModal.classList.remove('hidden');
    });

    function showGraphForRecord(record) {
        currentRecord = record; // Store the current record
        if (currentChart) {
            currentChart.destroy();
        }

        const ctx = document.getElementById('data-chart').getContext('2d');
        graphTitle.textContent = `${record.category.charAt(0).toUpperCase() + record.category.slice(1)} Data Visualization`;

        // Determine best chart type based on data
        const dataKeys = Object.keys(record.data);
        const dataValues = Object.values(record.data);

        // Numeric data check
        const isNumeric = dataValues.every(val => !isNaN(parseFloat(val)));

        if (dataKeys.length === 1) {
            // Single value - use gauge chart
            currentChart = createGaugeChart(ctx, record);
        } else if (isNumeric) {
            // Numeric data - use bar or line chart
            currentChart = createNumericChart(ctx, record);
        } else {
            // Mixed data - use pie or doughnut chart
            currentChart = createCategoricalChart(ctx, record);
        }
    }

    function createGaugeChart(ctx, record) {
        const value = parseFloat(Object.values(record.data)[0]);
        const max = value * 1.5; // Adjust based on your data

        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [value, max - value],
                    backgroundColor: ['#4F46E5', '#E5E7EB'],
                    borderWidth: 0
                }]
            },
            options: {
                circumference: Math.PI,
                rotation: Math.PI,
                cutout: '80%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false },
                    title: {
                        display: true,
                        text: `${Object.keys(record.data)[0]}: ${value}`,
                        font: { size: 16 }
                    }
                }
            }
        });
    }

    function createNumericChart(ctx, record) {
        const isTimeSeries = record.category === 'vitals' || record.category === 'activity';

        return new Chart(ctx, {
            type: isTimeSeries ? 'line' : 'bar',
            data: {
                labels: Object.keys(record.data).map(key =>
                    key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
                ),
                datasets: [{
                    label: 'Values',
                    data: Object.values(record.data).map(val => parseFloat(val)),
                    backgroundColor: '#4F46E5',
                    borderColor: '#4F46E5',
                    borderWidth: 2,
                    fill: isTimeSeries
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: false }
                },
                plugins: {
                    title: {
                        display: true,
                        text: `${record.category.charAt(0).toUpperCase() + record.category.slice(1)} Data`,
                        font: { size: 16 }
                    }
                }
            }
        });
    }

    function createCategoricalChart(ctx, record) {
        return new Chart(ctx, {
            type: 'pie',
            data: {
                labels: Object.keys(record.data).map(key =>
                    key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
                ),
                datasets: [{
                    data: Object.values(record.data),
                    backgroundColor: [
                        '#4F46E5', '#10B981', '#F59E0B', '#EF4444',
                        '#8B5CF6', '#EC4899', '#14B8A6', '#F97316'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: `${record.category.charAt(0).toUpperCase() + record.category.slice(1)} Distribution`,
                        font: { size: 16 }
                    }
                }
            }
        });
    }

    // NEW: View All Data Modal elements
    const viewAllBtn = document.getElementById('view-all-data');
    const viewAllModal = document.getElementById('view-all-modal');
    const allDataContent = document.getElementById('all-data-content');
    const closeAllData = document.getElementById('close-all-data');
    const downloadBtn = document.getElementById('download-data');
    const downloadPdfBtn = document.getElementById('download-pdf');

    // Add these variables with your other element selectors
    const doctorModal = document.getElementById('doctor-modal');
    const doctorContent = document.getElementById('doctor-content');
    const closeDoctor = document.getElementById('close-doctor');
    const viewDoctorDocsButtons = document.querySelectorAll('.view-doctor-docs');

    // Add this event listener with your other modal handlers
    viewDoctorDocsButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            showDoctorDocuments();
            doctorModal.classList.remove('hidden');
        });
    });

    closeDoctor.addEventListener('click', () => {
        doctorModal.classList.add('hidden');
    });

    doctorModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    function showDoctorDocuments() {
        // Get documents for the current user (doctor)
        const doctorDocuments = @json($documents->where('user_id', auth()->id())->values());

        if (doctorDocuments.length === 0) {
            doctorContent.innerHTML = '<p class="text-sm text-gray-500">You haven\'t uploaded any documents yet.</p>';
            return;
        }

        doctorContent.innerHTML = doctorDocuments.map(doc => {
            const ext = doc.filename.split('.').pop().toLowerCase();
            const iconPath = ext === 'pdf' ?
                'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z' :
                (['jpg','jpeg','png'].includes(ext) ?
                    'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' :
                    'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z');

            return `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                    <div class="flex items-center">
                        <div class="bg-indigo-100 p-2 rounded-full mr-3">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">${doc.filename}</p>
                            <p class="text-sm text-gray-500">
                                Uploaded ${new Date(doc.created_at).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                })} • ${(doc.size / 1024 / 1024).toFixed(2)} MB
                                ${doc.category ? ' • ' + doc.category.replace('_', ' ').charAt(0).toUpperCase() + doc.category.replace('_', ' ').slice(1) : ''}
                            </p>
                            ${doc.uploader_name || doc.uploader_hospital ? `
                            <p class="text-xs text-gray-400 mt-1">
                                ${doc.uploader_name ? 'Uploaded by: ' + doc.uploader_name : ''}
                                ${doc.uploader_hospital ? (doc.uploader_name ? ' (' + doc.uploader_hospital + ')' : 'Hospital: ' + doc.uploader_hospital) : ''}
                            </p>
                            ` : ''}
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="${doc.view_url}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                        <a href="${doc.download_url}" class="text-green-600 hover:text-green-800 text-sm">Download</a>
                    </div>
                </div>
            `;
        }).join('');
    }

    // All records from PHP, converted to JavaScript array
    const allRecords = @json($records);

    // MOVED: Add error checking for allRecords
    console.log('All records loaded:', allRecords);
    console.log('Number of records:', allRecords ? allRecords.length : 0);

    // PDF Download functionality
    downloadPdfBtn.addEventListener('click', function() {
        // Get the most recent record from each category
        const groupedRecords = {};
        allRecords.forEach(record => {
            if (!groupedRecords[record.category]) {
                groupedRecords[record.category] = record;
            } else {
                // Keep the most recent record
                if (new Date(record.created_at) > new Date(groupedRecords[record.category].created_at)) {
                    groupedRecords[record.category] = record;
                }
            }
        });

        // Initialize jsPDF
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Set up the document
        doc.setFontSize(20);
        doc.text('Medical Records Summary', 20, 20);

        doc.setFontSize(12);
        doc.text(`Generated on: ${new Date().toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        })}`, 20, 35);

        // Add a line separator
        doc.line(20, 45, 190, 45);

        let yPosition = 60;
        const categoryOrder = ['vitals', 'activity', 'pain', 'sleep', 'wellbeing', 'labs', 'infection', 'treatments', 'appointments'];

        categoryOrder.forEach(category => {
            const record = groupedRecords[category];
            if (!record) return;

            // Check if we need a new page
            if (yPosition > 250) {
                doc.addPage();
                yPosition = 20;
            }

            // Category header
            doc.setFontSize(14);
            doc.setFont(undefined, 'bold');
            doc.text(`${category.charAt(0).toUpperCase() + category.slice(1)}`, 20, yPosition);
            yPosition += 10;

            // Date
            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.setTextColor(100, 100, 100);
            doc.text(`Date: ${new Date(record.created_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })}`, 20, yPosition);
            yPosition += 8;

            // Record data
            doc.setFontSize(11);
            doc.setTextColor(0, 0, 0);
            Object.entries(record.data).forEach(([key, value]) => {
                const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                const text = `${label}: ${value}`;

                // Handle long text by splitting into multiple lines
                const splitText = doc.splitTextToSize(text, 170);
                doc.text(splitText, 25, yPosition);
                yPosition += splitText.length * 5;
            });

            yPosition += 10; // Add space between categories

            // Add a subtle line separator between categories
            doc.setDrawColor(200, 200, 200);
            doc.line(20, yPosition - 5, 190, yPosition - 5);
            yPosition += 5;
        });

        // Add footer
        const pageCount = doc.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(150, 150, 150);
            doc.text(`Page ${i} of ${pageCount}`, 170, 285);
            doc.text('Medical Dashboard - Confidential', 20, 285);
        }

        // Save the PDF
        const fileName = `medical_summary_${new Date().toISOString().split('T')[0]}.pdf`;
        doc.save(fileName);
    });

    function filterRecords() {
        const category = categoryFilter.value.toLowerCase();
        const time = timeFilter.value.toLowerCase();
        const search = searchInput.value.toLowerCase();

        recordRows.forEach(row => {
            const rowCategory = row.dataset.category;
            const rowText = row.textContent.toLowerCase();
            const rowDate = new Date(row.querySelector('td:nth-child(3)').textContent);
            const today = new Date('{{ now()->format('Y-m-d') }}');

            const categoryMatch = category === '' || rowCategory === category;
            const textMatch = search === '' || rowText.includes(search);

            let timeMatch = true;
            if (time) {
                if (time === 'today') {
                    timeMatch = rowDate.toDateString() === today.toDateString();
                } else if (time === 'week') {
                    const oneWeekAgo = new Date(today);
                    oneWeekAgo.setDate(today.getDate() - 7);
                    timeMatch = rowDate >= oneWeekAgo;
                } else if (time === 'month') {
                    const oneMonthAgo = new Date(today);
                    oneMonthAgo.setMonth(today.getMonth() - 1);
                    timeMatch = rowDate >= oneMonthAgo;
                } else if (time === 'year') {
                    const oneYearAgo = new Date(today);
                    oneYearAgo.setFullYear(today.getFullYear() - 1);
                    timeMatch = rowDate >= oneYearAgo;
                }
            }

            row.style.display = categoryMatch && textMatch && timeMatch ? '' : 'none';
        });
    }

    categoryFilter.addEventListener('change', filterRecords);
    timeFilter.addEventListener('change', filterRecords);
    searchInput.addEventListener('input', filterRecords);

    viewLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const record = JSON.parse(this.dataset.record);
            modalContent.innerHTML = `
                <p><strong>Category:</strong> ${record.category.charAt(0).toUpperCase() + record.category.slice(1)}</p>
                <p><strong>Date:</strong> ${new Date(record.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                <p><strong>Details:</strong></p>
                <ul class="list-disc pl-5">
                    ${Object.entries(record.data).map(([key, value]) => `<li>${key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}: ${value}</li>`).join('')}
                </ul>
            `;
            viewModal.classList.remove('hidden');
        });
    });

    closeModal.addEventListener('click', () => {
        viewModal.classList.add('hidden');
    });

    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this record?')) {
                this.closest('.delete-form').submit();
            }
        });
    });

    // History modal functionality
    historyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.dataset.category;
            const categoryRecords = allRecords.filter(record => record.category === category);

            historyCategory.textContent = `${category.charAt(0).toUpperCase() + category.slice(1)} History`;
            historyContent.innerHTML = categoryRecords.length > 0 ? categoryRecords.map(record => `
                <div class="p-3 bg-gray-50 rounded-lg hover:bg-blue-50 cursor-pointer history-record" data-record='${JSON.stringify(record)}'>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-800">
                                ${Object.entries(record.data).slice(0, 2).map(([key, value]) =>
                                    `${key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}: ${value}`
                                ).join(', ')}
                            </p>
                            <p class="text-sm text-gray-500">${new Date(record.created_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-800 text-sm view-history-record">View</button>
                            <button class="text-purple-600 hover:text-purple-800 text-sm graph-history-record">Graph</button>
                        </div>
                    </div>
                </div>
            `).join('') : '<p class="text-sm text-gray-500">No records found for this category.</p>';

            // Add event listener for graph buttons in history modal
            document.querySelectorAll('.graph-history-record').forEach(btn => {
                btn.addEventListener('click', function() {
                    const record = JSON.parse(this.closest('.history-record').dataset.record);
                    showGraphForRecord(record);
                    historyModal.classList.add('hidden');
                    graphModal.classList.remove('hidden');
                });
            });

            historyModal.classList.remove('hidden');
        });
    });

    closeHistory.addEventListener('click', () => {
        historyModal.classList.add('hidden');
    });

    // Close history modal when clicking outside
    historyModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    // Previous records modal functionality
    viewPrevious.addEventListener('click', function(e) {
        e.preventDefault();
        const currentTime = new Date('2025-06-16T09:21:00+03:00'); // EAT is UTC+3
        const previousRecords = allRecords
            .filter(record => new Date(record.created_at) < currentTime)
            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

        // Debug: Log filtered records
        console.log('Previous Records:', previousRecords);

        previousContent.innerHTML = previousRecords.length > 0 ? previousRecords.map(record => `
            <div class="p-3 bg-gray-50 rounded-lg hover:bg-blue-50 cursor-pointer previous-record" data-record='${JSON.stringify(record)}'>
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium text-gray-800">
                            ${record.category.charAt(0).toUpperCase() + record.category.slice(1)}:
                            ${Object.entries(record.data).slice(0, 2).map(([key, value]) => `${key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}: ${value}`).join(', ')}
                        </p>
                        <p class="text-sm text-gray-500">${new Date(record.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                    </div>
                    <button class="text-blue-600 hover:text-blue-800 text-sm view-previous-record">View Details</button>
                </div>
            </div>
        `).join('') : '<p class="text-sm text-gray-500">No previous records found.</p>';

        previousModal.classList.remove('hidden');

        // Add event listeners to view details buttons
        document.querySelectorAll('.view-previous-record').forEach(btn => {
            btn.addEventListener('click', function() {
                const record = JSON.parse(this.closest('.previous-record').dataset.record);
                modalContent.innerHTML = `
                    <p><strong>Category:</strong> ${record.category.charAt(0).toUpperCase() + record.category.slice(1)}</p>
                    <p><strong>Date:</strong> ${new Date(record.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                    <p><strong>Details:</strong></p>
                    <ul class="list-disc pl-5">
                        ${Object.entries(record.data).map(([key, value]) => `<li>${key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}: ${value}</li>`).join('')}
                    </ul>
                `;
                previousModal.classList.add('hidden');
                viewModal.classList.remove('hidden');
            });
        });
    });

    closePrevious.addEventListener('click', () => {
        previousModal.classList.add('hidden');
    });

    // Close previous modal when clicking outside
    previousModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    // View All Data functionality
    viewAllBtn.addEventListener('click', function(e) {
        e.preventDefault();
        generateAllDataContent();
        viewAllModal.classList.remove('hidden');
    });

    // Close All Data modal
    closeAllData.addEventListener('click', function() {
        viewAllModal.classList.add('hidden');
    });

    // Close modal when clicking outside
    viewAllModal.addEventListener('click', function(e) {
        if (e.target === this) {
            viewAllModal.classList.add('hidden');
        }
    });

    // Function to generate content for the all data modal
    function generateAllDataContent() {
        // Group records by category
        const groupedRecords = {};
        allRecords.forEach(record => {
            if (!groupedRecords[record.category]) {
                groupedRecords[record.category] = [];
            }
            groupedRecords[record.category].push(record);
        });

        // Sort records within each category by date (newest first)
        Object.keys(groupedRecords).forEach(category => {
            groupedRecords[category].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        });

        // Generate HTML content
        const categoryOrder = ['vitals', 'activity', 'pain', 'sleep', 'wellbeing', 'labs', 'infection', 'treatments', 'appointments'];

        allDataContent.innerHTML = categoryOrder.map(category => {
            const records = groupedRecords[category] || [];
            if (records.length === 0) return '';

            return `
                <div class="category-section" data-category="${category}">
                    <h4 class="text-lg font-semibold mb-3 flex items-center text-gray-800">
                        <div class="bg-blue-100 p-2 rounded-full mr-3">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        ${category.charAt(0).toUpperCase() + category.slice(1)} (${records.length})
                    </h4>
                    <div class="space-y-2 mb-6">
                        ${records.map(record => `
                            <div class="p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-800 mb-1">
                                            ${Object.entries(record.data).map(([key, value]) =>
                                                `${key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}: ${value}`
                                            ).join(', ')}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ${new Date(record.created_at).toLocaleDateString('en-US', {
                                                year: 'numeric',
                                                month: 'short',
                                                day: 'numeric',
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            })}
                                        </div>
                                    </div>
                                    <button class="text-blue-600 hover:text-blue-800 text-xs ml-2 view-all-record" data-record='${JSON.stringify(record)}'>
                                        View
                                    </button>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }).filter(html => html !== '').join('');

        // Add event listeners to view buttons
        document.querySelectorAll('.view-all-record').forEach(btn => {
            btn.addEventListener('click', function() {
                const record = JSON.parse(this.dataset.record);
                modalContent.innerHTML = `
                    <p><strong>Category:</strong> ${record.category.charAt(0).toUpperCase() + record.category.slice(1)}</p>
                    <p><strong>Date:</strong> ${new Date(record.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                    <p><strong>Details:</strong></p>
                    <ul class="list-disc pl-5">
                        ${Object.entries(record.data).map(([key, value]) => `<li>${key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}: ${value}</li>`).join('')}
                    </ul>
                `;
                viewAllModal.classList.add('hidden');
                viewModal.classList.remove('hidden');
            });
        });
    }

    // Download All as JSON
    document.getElementById('download-all-json').addEventListener('click', function() {
        const dataToDownload = {
            exported_at: new Date().toISOString(),
            total_records: allRecords.length,
            records: allRecords.map(record => ({
                id: record.id,
                category: record.category,
                data: record.data,
                created_at: record.created_at,
                updated_at: record.updated_at
            }))
        };

        const dataStr = JSON.stringify(dataToDownload, null, 2);
        const dataBlob = new Blob([dataStr], {type: 'application/json'});
        const url = URL.createObjectURL(dataBlob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `medical_records_${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    });

    // Download All as PDF
    document.getElementById('download-all-pdf').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Set document properties
        doc.setProperties({
            title: 'Medical Records Summary',
            subject: 'Complete medical records export',
            author: 'Medical Dashboard',
            keywords: 'medical, records, health',
            creator: 'Medical Dashboard'
        });

        // Add title and date
        doc.setFontSize(20);
        doc.setTextColor(40, 40, 40);
        doc.text('Medical Records Summary', 20, 20);

        doc.setFontSize(12);
        doc.setTextColor(100, 100, 100);
        doc.text(`Generated on: ${new Date().toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        })}`, 20, 30);

        // Add a line separator
        doc.setDrawColor(200, 200, 200);
        doc.line(20, 35, 190, 35);

        let yPosition = 45;
        const categoryOrder = ['vitals', 'activity', 'pain', 'sleep', 'wellbeing', 'labs', 'infection', 'treatments', 'appointments'];
        const categoryColors = {
            vitals: [65, 105, 225],    // Royal Blue
            activity: [34, 139, 34],    // Forest Green
            pain: [178, 34, 34],        // Firebrick
            sleep: [138, 43, 226],      // Blue Violet
            wellbeing: [255, 140, 0],    // Dark Orange
            labs: [75, 0, 130],         // Indigo
            infection: [220, 20, 60],   // Crimson
            treatments: [0, 139, 139],  // Dark Cyan
            appointments: [218, 165, 32] // Golden Rod
        };

        // Process each category
        categoryOrder.forEach(category => {
            const records = allRecords.filter(r => r.category === category);
            if (records.length === 0) return;

            // Check if we need a new page
            if (yPosition > 250) {
                doc.addPage();
                yPosition = 20;
            }

            // Category header
            doc.setFontSize(14);
            doc.setFont(undefined, 'bold');
            doc.setTextColor(...categoryColors[category] || [0, 0, 0]);
            doc.text(`${category.charAt(0).toUpperCase() + category.slice(1)} (${records.length} records)`, 20, yPosition);
            yPosition += 10;

            // Add records for this category
            records.forEach((record, index) => {
                // Check if we need a new page before adding another record
                if (yPosition > 250) {
                    doc.addPage();
                    yPosition = 20;
                }

                // Record date
                doc.setFontSize(10);
                doc.setFont(undefined, 'normal');
                doc.setTextColor(100, 100, 100);
                doc.text(`Record ${index + 1}: ${new Date(record.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                })}`, 25, yPosition);
                yPosition += 7;

                // Record data
                doc.setFontSize(10);
                doc.setTextColor(0, 0, 0);
                Object.entries(record.data).forEach(([key, value]) => {
                    const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                    const text = `${label}: ${value}`;

                    // Handle long text by splitting into multiple lines
                    const splitText = doc.splitTextToSize(text, 160);
                    doc.text(splitText, 30, yPosition);
                    yPosition += splitText.length * 5;
                });

                yPosition += 5; // Add space between records

                // Add a subtle separator between records
                if (index < records.length - 1) {
                    doc.setDrawColor(220, 220, 220);
                    doc.line(25, yPosition - 2, 185, yPosition - 2);
                    yPosition += 5;
                }
            });

            yPosition += 10; // Add extra space between categories
        });

        // Add page numbers
        const pageCount = doc.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(150, 150, 150);
            doc.text(`Page ${i} of ${pageCount}`, 170, 285);
            doc.text('Medical Dashboard - Confidential', 20, 285);
        }

        // Save the PDF
        const fileName = `medical_records_full_${new Date().toISOString().split('T')[0]}.pdf`;
        doc.save(fileName);
    });
// Blood Pressure Chart Initialization
function createBloodPressureChart() {
    const ctx = document.getElementById('bp-chart').getContext('2d');
    const noDataMessage = document.getElementById('bp-no-data');

    // Filter vitals records and sort by date
    const vitalsRecords = allRecords
        .filter(record => record.category === 'vitals' && record.data.systolic && record.data.diastolic)
        .sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

    if (vitalsRecords.length === 0) {
        document.getElementById('bp-chart').classList.add('hidden');
        noDataMessage.classList.remove('hidden');
        return;
    }

    const dates = vitalsRecords.map(record => new Date(record.created_at).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    }));
    const systolicData = vitalsRecords.map(record => parseFloat(record.data.systolic));
    const diastolicData = vitalsRecords.map(record => parseFloat(record.data.diastolic));

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: 'Systolic (mmHg)',
                    data: systolicData,
                    borderColor: '#3B82F6',
                    backgroundColor: '#3B82F6',
                    fill: false,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Diastolic (mmHg)',
                    data: diastolicData,
                    borderColor: '#10B981',
                    backgroundColor: '#10B981',
                    fill: false,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    },
                    ticks: {
                        maxTicksLimit: 10,
                        autoSkip: true
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Blood Pressure (mmHg)'
                    },
                    suggestedMin: 60,
                    suggestedMax: 200,
                    ticks: {
                        stepSize: 20
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.parsed.y} mmHg`;
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Blood Pressure Trends',
                    font: { size: 16 }
                }
            }
        }
    });
}

// Initialize the blood pressure chart on page load
createBloodPressureChart();
});
</script>

</body>
</html>
</x-app-layout>
