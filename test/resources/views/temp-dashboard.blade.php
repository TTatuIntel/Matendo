<x-app-layout>

@if(session('temp_link'))
    <div class="bg-green-100 text-green-800 p-4 rounded my-4">
        Temporary Link:
        <a href="{{ session('temp_link') }}" class="underline text-blue-600" target="_blank">Open Dashboard</a>
    </div>
@endif
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temporary Medical Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Temporary Medical Dashboard
                        @if(isset($user))
                            <span class="text-sm font-normal text-gray-500"> - Viewing {{ $user->name }}'s Records</span>
                        @endif
                    </h2>
                    <div class="flex space-x-4">
                        <button id="toggle-upload" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Upload Documents</button>
                        <button id="view-all-data" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">View All Data</button>
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
                <!-- Success/Error Messages -->
                @if (session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                <div id="alert" class="hidden mb-6 p-4 rounded-lg">
                    <span id="alert-message"></span>
                </div>

                <!-- Upload Section (Hidden by Default) -->
                <div id="upload-section" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8 hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Upload Medical Documents</h3>
                        <!-- Upload Area -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors cursor-pointer" id="upload-area">
                            <div class="bg-blue-100 p-4 rounded-full mx-auto w-16 h-16 flex items-center justify-center mb-4">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Upload medical documents</h3>
                            <p class="text-sm text-gray-500 mb-4">Drag and drop files here, or click to browse</p>
                            <div class="mb-4">
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Document Category (Optional)</label>
                                <select id="category" name="category" class="mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm w-full max-w-xs mx-auto">
                                    <option value="">Select Category</option>
                                    <option value="lab_results">Lab Results</option>
                                    <option value="prescriptions">Prescriptions</option>
                                    <option value="medical_reports">Medical Reports</option>
                                </select>
                            </div>
                            <input type="file" multiple class="hidden" id="file-upload" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <button type="button" id="select-files-btn" class="bg-blue-500 text-white px-6 py-3 rounded-md hover:bg-blue-600 transition inline-flex items-center space-x-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Select Files</span>
                            </button>
                            <p class="mt-4 text-xs text-gray-500">
                                Supported formats: PDF, JPG, PNG, DOC, DOCX (Max: 10MB per file)
                            </p>
                        </div>
                        <!-- Selected Files Display -->
                        <div id="selected-files" class="mt-6 hidden">
                            <h4 class="text-md font-medium text-gray-900 mb-3">Selected Files</h4>
                            <div id="files-list" class="space-y-2"></div>
                            <div class="mt-4 flex space-x-3">
                                <button type="button" id="upload-btn" class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">
                                    <span id="upload-text">Upload Files</span>
                                    <span id="upload-spinner" class="hidden ml-2">
                                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                                <button type="button" id="clear-btn" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                                    Clear All
                                </button>
                            </div>
                        </div>
                        <!-- Upload Progress -->
                        <div id="upload-progress" class="mt-4 hidden">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p id="progress-text" class="text-sm text-gray-600 mt-2">Uploading...</p>
                        </div>
                    </div>
                </div>

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
    <h3 class="text-lg font-semibold mb-4 text-gray-800" id="graph-title">Blood Pressure Trends</h3>
    <div class="grid grid-cols-3 gap-2 mb-4 sm:grid-cols-5">
        <button class="graph-icon p-2 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200" data-type="blood_pressure" title="Blood Pressure">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
        </button>
        <button class="graph-icon p-2 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200" data-type="heart_rate" title="Heart Rate">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
        </button>
        <button class="graph-icon p-2 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200" data-type="temperature" title="Temperature">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
            </svg>
        </button>
        <button class="graph-icon p-2 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200" data-type="weight" title="Weight">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-4m6 2l3 1m-3-1l-3 9a5.002 5.002 0 006.001 0M18 7l3 1m0 0L18 16"></path>
            </svg>
        </button>
        <button class="graph-icon p-2 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200" data-type="cd4_count" title="CD4 Count">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </button>
        <button class="graph-icon p-2 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200" data-type="viral_load" title="Viral Load">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </button>
        <button class="graph-icon p-2 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200" data-type="hba1c" title="HbA1c">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 13v-7m8 7v-7m-4 7V6m-7 7h18"></path>
            </svg>
        </button>
        <button class="graph-icon p-2 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200" data-type="total_cholesterol" title="Total Cholesterol">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
            </svg>
        </button>
        <button class="graph-icon p-2 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200" data-type="blood_sugar" title="Blood Sugar">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
        </button>
    </div>
    <div class="relative h-64">
        <canvas id="bp-chart"></canvas>
    </div>
    <p id="bp-no-data" class="text-sm text-gray-500 hidden">No data available for this metric.</p>
    <div class="mt-4 flex justify-end">
        <button id="download-bp-pdf" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download PDF
        </button>
    </div>
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
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

                <!-- Recent Uploads -->
<!-- Recent Uploads -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold mb-4">Recent Uploads</h3>
        <div id="recent-uploads">
            @forelse($documents as $document)
                @php
                    // Determine icon color based on category
                    $iconColor = 'purple';
                    if ($document->category === 'lab_results') {
                        $iconColor = 'blue';
                    } elseif ($document->category === 'prescriptions') {
                        $iconColor = 'green';
                    }

                    // Determine icon path based on file extension
                    $ext = pathinfo($document->filename, PATHINFO_EXTENSION);
                    $iconPath = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'; // Default doc icon
                    if ($ext === 'pdf') {
                        $iconPath = 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z';
                    } elseif (in_array($ext, ['jpg','jpeg','png'])) {
                        $iconPath = 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
                    }
                @endphp

                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border mb-2">
                    <div class="flex items-center w-full">
                        <div class="bg-{{ $iconColor }}-100 p-2 rounded-full mr-3">
                            <svg class="h-5 w-5 text-{{ $iconColor }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path>
                            </svg>
                        </div>
                        <div class="flex-grow">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $document->filename }}</p>
                                    <p class="text-sm text-gray-500">
                                        Uploaded {{ $document->created_at->format('M d, Y') }} •
                                        {{ round($document->size / 1024 / 1024, 2) }} MB
                                        @if($document->category)
                                             • {{ ucfirst(str_replace('_', ' ', $document->category)) }}
                                        @endif
                                    </p>
                                    @if($document->uploader_name || $document->uploader_hospital)
                                        <p class="text-xs text-gray-400 mt-1">
                                            @if($document->uploader_name && $document->uploader_hospital)
                                                Uploaded by: {{ $document->uploader_name }} ({{ $document->uploader_hospital }})
                                            @elseif($document->uploader_name)
                                                Uploaded by: {{ $document->uploader_name }}
                                            @elseif($document->uploader_hospital)
                                                Hospital: {{ $document->uploader_hospital }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <div class="flex space-x-2 ml-4">
                                    <a href="{{ route('documents.view', $document->id) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                    <a href="{{ route('documents.download', $document->id) }}" class="text-green-600 hover:text-green-800 text-sm">Download</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No documents uploaded yet.</p>
            @endforelse
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
                            <button id="download-pdf-modal" class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition text-sm flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                PDF
                            </button>
                        </h3>
                        <div id="all-data-content" class="space-y-6"></div>
                        <button id="close-all-data" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition w-full">Close</button>
                    </div>
                </div>

                <!-- Graph Modal -->
<div id="graph-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl w-full">
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

                <!-- Uploader Info Modal -->
                <div id="uploader-info-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
                        <h3 class="text-lg font-semibold mb-4">Uploader Information</h3>
                        <form id="uploader-info-form">
                            @csrf
                            <div class="mb-4">
                                <label for="uploader-name" class="block text-sm font-medium text-gray-700">Your Full Name</label>
                                <input type="text" id="uploader-name" name="uploader_name" required
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="mb-4">
                                <label for="uploader-hospital" class="block text-sm font-medium text-gray-700">Hospital/Institution</label>
                                <input type="text" id="uploader-hospital" name="uploader_hospital" required
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" id="cancel-uploader-info" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                                    Continue Upload
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements for Records Display
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
    const viewAllBtn = document.getElementById('view-all-data');
    const viewAllModal = document.getElementById('view-all-modal');
    const allDataContent = document.getElementById('all-data-content');
    const closeAllData = document.getElementById('close-all-data');
    const downloadBtn = document.getElementById('download-data');
    const downloadPdfBtn = document.getElementById('download-pdf');
    const downloadPdfModalBtn = document.getElementById('download-pdf-modal');
    const viewLinks = document.querySelectorAll('.view-record');
    const historyButtons = document.querySelectorAll('.view-history');
    const allRecords = @json($records);

    // Graph functionality
    const graphModal = document.getElementById('graph-modal');
    const closeGraph = document.getElementById('close-graph');
    const downloadGraphBtn = document.getElementById('download-graph');
    const showHistoryGraphBtn = document.getElementById('show-history-graph');
    const graphTitle = document.getElementById('graph-title');
    let currentChart = null;
    let currentRecord = null;

    // Elements for File Upload
    const toggleUploadBtn = document.getElementById('toggle-upload');
    const uploadSection = document.getElementById('upload-section');
    const fileInput = document.getElementById('file-upload');
    const uploadArea = document.getElementById('upload-area');
    const selectedFilesDiv = document.getElementById('selected-files');
    const filesList = document.getElementById('files-list');
    const uploadBtn = document.getElementById('upload-btn');
    const uploadText = document.getElementById('upload-text');
    const uploadSpinner = document.getElementById('upload-spinner');
    const clearBtn = document.getElementById('clear-btn');
    const categorySelect = document.getElementById('category');
    const alertDiv = document.getElementById('alert');
    const alertMessage = document.getElementById('alert-message');
    const selectFilesBtn = document.getElementById('select-files-btn');
    const uploadProgress = document.getElementById('upload-progress');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const uploaderInfoModal = document.getElementById('uploader-info-modal');
    const uploaderInfoForm = document.getElementById('uploader-info-form');
    const cancelUploaderInfo = document.getElementById('cancel-uploader-info');

    let selectedFiles = [];
    let pendingFiles = [];

    // Toggle Upload Section
    toggleUploadBtn.addEventListener('click', () => {
        uploadSection.classList.toggle('hidden');
        toggleUploadBtn.textContent = uploadSection.classList.contains('hidden') ? 'Upload Documents' : 'Hide Upload';
    });

    // Records Display Functionality
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

    // Blood Pressure Chart Initialization
    // Multi-Graph Initialization
    function createMultiGraph() {
        const ctx = document.getElementById('bp-chart').getContext('2d');
        const noDataMessage = document.getElementById('bp-no-data');
        const graphTitle = document.getElementById('graph-title');
        let currentChart = null;

        // Data types configuration
        const dataTypes = {
            blood_pressure: {
                category: 'vitals',
                fields: ['systolic', 'diastolic'],
                labels: ['Systolic (mmHg)', 'Diastolic (mmHg)'],
                colors: ['#3B82F6', '#10B981'],
                yTitle: 'Blood Pressure (mmHg)',
                yMin: 60,
                yMax: 200,
                yStep: 20
            },
            heart_rate: {
                category: 'vitals',
                fields: ['heart_rate'],
                labels: ['Heart Rate (bpm)'],
                colors: ['#EF4444'],
                yTitle: 'Heart Rate (bpm)',
                yMin: 40,
                yMax: 120,
                yStep: 10
            },
            temperature: {
                category: 'vitals',
                fields: ['temperature'],
                labels: ['Temperature (°C)'],
                colors: ['#F59E0B'],
                yTitle: 'Temperature (°C)',
                yMin: 35,
                yMax: 42,
                yStep: 0.5
            },
            weight: {
                category: 'vitals',
                fields: ['weight'],
                labels: ['Weight (kg)'],
                colors: ['#8B5CF6'],
                yTitle: 'Weight (kg)',
                yMin: 40,
                yMax: 120,
                yStep: 10
            },
            cd4_count: {
                category: 'infection',
                fields: ['cd4_count'],
                labels: ['CD4 Count (cells/µL)'],
                colors: ['#14B8A6'],
                yTitle: 'CD4 Count (cells/µL)',
                yMin: 0,
                yMax: 1500,
                yStep: 100
            },
            viral_load: {
                category: 'infection',
                fields: ['viral_load'],
                labels: ['Viral Load (copies/mL)'],
                colors: ['#EC4899'],
                yTitle: 'Viral Load (copies/mL)',
                yMin: 0,
                yMax: 100000,
                yStep: 10000
            },
            hba1c: {
                category: 'labs',
                fields: ['hba1c'],
                labels: ['HbA1c (%)'],
                colors: ['#6B7280'],
                yTitle: 'HbA1c (%)',
                yMin: 4,
                yMax: 10,
                yStep: 0.5
            },
            total_cholesterol: {
                category: 'labs',
                fields: ['total_cholesterol'],
                labels: ['Total Cholesterol (mg/dL)'],
                colors: ['#F97316'],
                yTitle: 'Total Cholesterol (mg/dL)',
                yMin: 100,
                yMax: 300,
                yStep: 20
            },
            blood_sugar: {
                category: 'labs',
                fields: ['blood_sugar'],
                labels: ['Blood Sugar (mg/dL)'],
                colors: ['#22C55E'],
                yTitle: 'Blood Sugar (mg/dL)',
                yMin: 50,
                yMax: 200,
                yStep: 10
            }
        };

        function renderGraph(dataType) {
            if (currentChart) {
                currentChart.destroy();
            }

            const config = dataTypes[dataType];
            const records = allRecords
                .filter(record => record.category === config.category && config.fields.every(field => record.data[field] && !isNaN(parseFloat(record.data[field]))))
                .sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

            if (records.length === 0) {
                document.getElementById('bp-chart').classList.add('hidden');
                noDataMessage.classList.remove('hidden');
                graphTitle.textContent = `${dataType.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())} Trends`;
                return;
            }

            document.getElementById('bp-chart').classList.remove('hidden');
            noDataMessage.classList.add('hidden');
            graphTitle.textContent = `${dataType.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())} Trends`;

            const dates = records.map(record => new Date(record.created_at).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            }));

            const datasets = config.fields.map((field, index) => ({
                label: config.labels[index],
                data: records.map(record => parseFloat(record.data[field])),
                borderColor: config.colors[index],
                backgroundColor: config.colors[index],
                fill: false,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6
            }));

            currentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: datasets
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
                                text: config.yTitle
                            },
                            suggestedMin: config.yMin,
                            suggestedMax: config.yMax,
                            ticks: {
                                stepSize: config.yStep
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
                                    const units = context.dataset.label.includes('mmHg') ? 'mmHg' :
                                                context.dataset.label.includes('bpm') ? 'bpm' :
                                                context.dataset.label.includes('°C') ? '°C' :
                                                context.dataset.label.includes('kg') ? 'kg' :
                                                context.dataset.label.includes('cells/µL') ? 'cells/µL' :
                                                context.dataset.label.includes('copies/mL') ? 'copies/mL' :
                                                context.dataset.label.includes('HbA1c') ? '%' :
                                                context.dataset.label.includes('mg/dL') ? 'mg/dL' : '';
                                    return `${context.dataset.label}: ${context.parsed.y} ${units}`;
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: `${dataType.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())} Trends`,
                            font: { size: 16 }
                        }
                    }
                }
            });

            // Update download button
            document.getElementById('download-bp-pdf').removeEventListener('click', downloadHandler);
            downloadHandler = function() {
                if (!currentChart) return;

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('landscape');

                // Get chart as image
                const canvas = document.getElementById('bp-chart');
                const chartImage = canvas.toDataURL('image/png');

                // Add to PDF
                doc.setFontSize(16);
                doc.text(`${dataType.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())} Trends`, 20, 20);
                doc.setFontSize(12);
                doc.setTextColor(100, 100, 100);
                doc.text(`Generated on: ${new Date().toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                })}`, 20, 30);
                doc.addImage(chartImage, 'PNG', 15, 40, 260, 150);

                // Save PDF
                const fileName = `${dataType}_trends_${new Date().toISOString().split('T')[0]}.pdf`;
                doc.save(fileName);
            };
            document.getElementById('download-bp-pdf').addEventListener('click', downloadHandler);
        }

        let downloadHandler = () => {};

        // Icon click handlers
        document.querySelectorAll('.graph-icon').forEach(icon => {
            icon.addEventListener('click', function() {
                const dataType = this.dataset.type;
                renderGraph(dataType);

                // Update active state
                document.querySelectorAll('.graph-icon').forEach(i => i.classList.remove('ring-2', 'ring-blue-500'));
                this.classList.add('ring-2', 'ring-blue-500');
            });
        });

        // Initialize with blood pressure
        renderGraph('blood_pressure');
        document.querySelector('.graph-icon[data-type="blood_pressure"]').classList.add('ring-2', 'ring-blue-500');
    }

    // Initialize the multi-graph on page load
    createMultiGraph();

    // File Upload Functionality
    uploadArea.addEventListener('click', function(e) {
        if (e.target !== categorySelect && !categorySelect.contains(e.target)) {
            fileInput.click();
        }
    });

    selectFilesBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        fileInput.click();
    });

    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.add('border-blue-400', 'bg-blue-50');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('border-blue-400', 'bg-blue-50');

        const files = Array.from(e.dataTransfer.files);
        if (files.length === 0) {
            showAlert('error', 'No files were dropped.');
            return;
        }

        handleFiles(files);
    });

    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        if (files.length === 0) return;
        handleFiles(files);
    });

    function handleFiles(files) {
        if (!files || files.length === 0) {
            showAlert('error', 'No files provided.');
            return;
        }

        const validExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        const maxSize = 10 * 1024 * 1024; // 10MB
        let validFiles = [];
        let invalidFiles = [];

        files.forEach(file => {
            const ext = file.name.split('.').pop().toLowerCase();

            if (!validExtensions.includes(ext)) {
                invalidFiles.push(`${file.name} (invalid format)`);
                return;
            }

            if (file.size > maxSize) {
                invalidFiles.push(`${file.name} (too large: ${(file.size / 1024 / 1024).toFixed(2)}MB)`);
                return;
            }

            const alreadySelected = selectedFiles.some(f => f.name === file.name && f.size === file.size);
            if (alreadySelected) {
                invalidFiles.push(`${file.name} (already selected)`);
                return;
            }

            validFiles.push(file);
        });

        if (invalidFiles.length > 0) {
            showAlert('error', `Invalid files: ${invalidFiles.join(', ')}`);
        }

        if (validFiles.length > 0) {
            selectedFiles = selectedFiles.concat(validFiles);
            showAlert('success', `Added ${validFiles.length} file(s) for upload.`);
            displaySelectedFiles();
        }
    }

    function displaySelectedFiles() {
        if (selectedFiles.length > 0) {
            selectedFilesDiv.classList.remove('hidden');
            filesList.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const fileDiv = document.createElement('div');
                fileDiv.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg border';

                const ext = file.name.split('.').pop().toLowerCase();
                const iconPath = getFileIcon(ext);

                fileDiv.innerHTML = `
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-2 rounded-full mr-3">
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">${file.name}</p>
                            <p class="text-sm text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB • ${ext.toUpperCase()}</p>
                        </div>
                    </div>
                    <button class="text-red-600 hover:text-red-800 text-sm font-medium px-2 py-1 rounded hover:bg-red-50" onclick="removeFile(${index})">
                        Remove
                    </button>
                `;
                filesList.appendChild(fileDiv);
            });
        } else {
            selectedFilesDiv.classList.add('hidden');
        }
    }

    function getFileIcon(ext) {
        switch(ext) {
            case 'pdf':
                return 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z';
            case 'jpg':
            case 'jpeg':
            case 'png':
                return 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
            default:
                return 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
        }
    }

    window.removeFile = function(index) {
        if (index >= 0 && index < selectedFiles.length) {
            const removedFile = selectedFiles.splice(index, 1)[0];
            displaySelectedFiles();
            fileInput.value = '';
        }
    };

    clearBtn.addEventListener('click', function() {
        selectedFiles = [];
        fileInput.value = '';
        displaySelectedFiles();
        showAlert('success', 'All files cleared.');
    });

    function showAlert(type, message) {
        alertDiv.classList.remove('hidden', 'bg-green-100', 'bg-red-100', 'text-green-800', 'text-red-800');

        if (type === 'success') {
            alertDiv.classList.add('bg-green-100', 'text-green-800');
        } else {
            alertDiv.classList.add('bg-red-100', 'text-red-800');
        }

        alertMessage.textContent = message;

        setTimeout(() => {
            alertDiv.classList.add('hidden');
        }, 5000);
    }

    uploadBtn.addEventListener('click', function(e) {
        if (selectedFiles.length === 0) {
            showAlert('error', 'No files selected for upload.');
            return;
        }

        // Store files temporarily and show info modal
        pendingFiles = [...selectedFiles];
        uploaderInfoModal.classList.remove('hidden');
    });

    // Handle form submission
    uploaderInfoForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('uploader_name', document.getElementById('uploader-name').value);
        formData.append('uploader_hospital', document.getElementById('uploader-hospital').value);

        // Add your files
        selectedFiles.forEach(file => {
            formData.append('files[]', file);
        });

        // Add category if selected
        if (categorySelect.value) {
            formData.append('category', categorySelect.value);
        }

        console.log('Submitting form with:', {
            name: formData.get('uploader_name'),
            hospital: formData.get('uploader_hospital'),
            fileCount: selectedFiles.length
        });

        uploaderInfoModal.classList.add('hidden');
        performUpload(formData);
    });

    // Handle cancel button
    cancelUploaderInfo.addEventListener('click', function() {
        uploaderInfoModal.classList.add('hidden');
        pendingFiles = [];
    });

    // Perform the actual upload
    async function performUpload(formData) {
        uploadBtn.disabled = true;
        uploadText.classList.add('hidden');
        uploadSpinner.classList.remove('hidden');
        uploadProgress.classList.remove('hidden');

        try {
            const response = await fetch('{{ route("temp.upload") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || data.message || `Upload failed with status ${response.status}`);
            }

            showAlert('success', data.message || `Successfully uploaded ${data.files.length} file(s).`);
            setTimeout(() => window.location.reload(), 1500);

        } catch (error) {
            console.error('Upload error:', error.message);
            showAlert('error', `Upload failed: ${error.message}`);
        } finally {
            uploadBtn.disabled = false;
            uploadText.classList.remove('hidden');
            uploadSpinner.classList.add('hidden');
            uploadProgress.classList.add('hidden');
            progressBar.style.width = '0%';
            progressText.textContent = 'Uploading...';
        }
    }

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

// Add PDF download functionality for the modal
       // Add PDF download functionality for the modal
    document.getElementById('download-pdf-modal').addEventListener('click', function() {
        // Initialize jsPDF
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Set up the document
        doc.setFontSize(20);
        doc.text('Complete Medical Records', 20, 20);

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

        // Group records by category while maintaining order
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

        // Generate PDF content
        categoryOrder.forEach(category => {
            const records = groupedRecords[category] || [];
            if (records.length === 0) return;

            // Check if we need a new page
            if (yPosition > 250) {
                doc.addPage();
                yPosition = 20;
            }

            // Category header
            doc.setFontSize(14);
            doc.setFont(undefined, 'bold');
            doc.text(`${category.charAt(0).toUpperCase() + category.slice(1)} (${records.length} records)`, 20, yPosition);
            yPosition += 10;

            // Add all records for this category
            records.forEach(record => {
                // Check if we need a new page before adding a new record
                if (yPosition > 250) {
                    doc.addPage();
                    yPosition = 20;
                }

                // Record date
                doc.setFontSize(10);
                doc.setFont(undefined, 'normal');
                doc.setTextColor(100, 100, 100);
                doc.text(`Recorded: ${new Date(record.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                })}`, 25, yPosition);
                yPosition += 8;

                // Record data
                doc.setFontSize(11);
                doc.setTextColor(0, 0, 0);
                Object.entries(record.data).forEach(([key, value]) => {
                    const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                    const text = `${label}: ${value}`;

                    // Handle long text by splitting into multiple lines
                    const splitText = doc.splitTextToSize(text, 170);
                    doc.text(splitText, 30, yPosition);
                    yPosition += splitText.length * 5;
                });

                yPosition += 8; // Add space between records

                // Add a subtle separator between records
                doc.setDrawColor(220, 220, 220);
                doc.line(25, yPosition - 4, 185, yPosition - 4);
                yPosition += 4;
            });

            yPosition += 10; // Add extra space between categories
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
        const fileName = `complete_medical_records_${new Date().toISOString().split('T')[0]}.pdf`;
        doc.save(fileName);
    });
    }

    // Download All as JSON
    downloadBtn.addEventListener('click', function() {
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
});
        </script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    </div>
</body>
</html>
</x-app-layout>
