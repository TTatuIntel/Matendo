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
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <div class="bg-green-100 p-2 rounded-full mr-3">
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V0"></path>
                                    </svg>
                                </div>
                                Current Medications
                            </h3>
                            <div class="space-y-3">
                                @php
                                    $medications = $records->where('category', 'treatments')->take(2);
                                @endphp
                                @forelse ($medications as $med)
                                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-green-800">{{ $med->data['medication_name'] ?? 'N/A' }}</p>
                                            <p class="text-sm text-green-600">{{ $med->data['dosage'] ?? 'N/A' }}</p>
                                        </div>
                                        <span class="text-xs text-green-500">{{ $med->created_at->format('M d, Y') }}</span>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No active medications</p>
                                @endforelse
                            </div>
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Recent Uploads</h3>
                        <div id="recent-uploads">
                            @forelse($documents as $document)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border mb-2">
                                    <div class="flex items-center">
                                        <div class="bg-@php echo $document->category === 'lab_results' ? 'blue' : ($document->category === 'prescriptions' ? 'green' : 'purple'); @endphp-100 p-2 rounded-full mr-3">
                                            <svg class="h-5 w-5 text-@php echo $document->category === 'lab_results' ? 'blue' : ($document->category === 'prescriptions' ? 'green' : 'purple'); @endphp-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="@php
                                                    $ext = pathinfo($document->filename, PATHINFO_EXTENSION);
                                                    echo $ext === 'pdf' ? 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z' :
                                                    (in_array($ext, ['jpg','jpeg','png']) ? 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' :
                                                    'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z');
                                                @endphp"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $document->filename }}</p>
                                            <p class="text-sm text-gray-500">
                                                Uploaded {{ $document->created_at->format('M d, Y') }} • {{ round($document->size / 1024 / 1024, 2) }} MB
                                                @if($document->category)
                                                     • {{ ucfirst(str_replace('_', ' ', $document->category)) }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('documents.view', $document->id) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                        <a href="{{ route('documents.download', $document->id) }}" class="text-green-600 hover:text-green-800 text-sm">Download</a>
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
                            <button id="download-data-modal" class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 transition text-sm flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download
                            </button>
                        </h3>
                        <div id="all-data-content" class="space-y-6"></div>
                        <button id="close-all-data" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition w-full">Close</button>
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
                const downloadModalBtn = document.getElementById('download-data-modal');
                const downloadPdfBtn = document.getElementById('download-pdf');
                const viewLinks = document.querySelectorAll('.view-record');
                const historyButtons = document.querySelectorAll('.view-history');
                const allRecords = @json($records);

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

                let selectedFiles = [];

                // Toggle Upload Section
                toggleUploadBtn.addEventListener('click', () => {
                    uploadSection.classList.toggle('hidden');
                    toggleUploadBtn.textContent = uploadSection.classList.contains('hidden') ? 'Upload Documents' : 'Hide Upload';
                });

                // Records Display Functionality
                console.log('All records loaded:', allRecords);
                console.log('Number of records:', allRecords ? allRecords.length : 0);

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
                                            ${Object.entries(record.data).slice(0, 2).map(([key, value]) => `${key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}: ${value}`).join(', ')}
                                        </p>
                                        <p class="text-sm text-gray-500">${new Date(record.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                                    </div>
                                    <button class="text-blue-600 hover:text-blue-800 text-sm view-history-record">View Details</button>
                                </div>
                            </div>
                        `).join('') : '<p class="text-sm text-gray-500">No records found for this category.</p>';

                        historyModal.classList.remove('hidden');

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
                    });
                });

                closeHistory.addEventListener('click', () => {
                    historyModal.classList.add('hidden');
                });

                historyModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.add('hidden');
                    }
                });

                viewAllBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const groupedRecords = {};
                    allRecords.forEach(record => {
                        if (!groupedRecords[record.category]) {
                            groupedRecords[record.category] = [];
                        }
                        groupedRecords[record.category].push(record);
                    });

                    Object.keys(groupedRecords).forEach(category => {
                        groupedRecords[category].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                    });

                    const categoryOrder = ['vitals', 'activity', 'pain', 'sleep', 'wellbeing', 'labs', 'infection', 'treatments', 'appointments'];

                    allDataContent.innerHTML = categoryOrder.map(category => {
                        const records = groupedRecords[category] || [];
                        if (records.length === 0) return '';

                        return `
                            <div class="category-section">
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

                    viewAllModal.classList.remove('hidden');

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
                });

                closeAllData.addEventListener('click', () => {
                    viewAllModal.classList.add('hidden');
                });

                viewAllModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.add('hidden');
                    }
                });

                function downloadJson() {
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
                }

                downloadBtn.addEventListener('click', downloadJson);
                downloadModalBtn.addEventListener('click', downloadJson);

                downloadPdfBtn.addEventListener('click', function() {
                    const groupedRecords = {};
                    allRecords.forEach(record => {
                        if (!groupedRecords[record.category]) {
                            groupedRecords[record.category] = record;
                        } else {
                            if (new Date(record.created_at) > new Date(groupedRecords[record.category].created_at)) {
                                groupedRecords[record.category] = record;
                            }
                        }
                    });

                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();

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

                    doc.line(20, 45, 190, 45);

                    let yPosition = 60;
                    const categoryOrder = ['vitals', 'activity', 'pain', 'sleep', 'wellbeing', 'labs', 'infection', 'treatments', 'appointments'];

                    categoryOrder.forEach(category => {
                        const record = groupedRecords[category];
                        if (!record) return;

                        if (yPosition > 250) {
                            doc.addPage();
                            yPosition = 20;
                        }

                        doc.setFontSize(14);
                        doc.setFont(undefined, 'bold');
                        doc.text(`${category.charAt(0).toUpperCase() + category.slice(1)}`, 20, yPosition);
                        yPosition += 10;

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

                        doc.setFontSize(11);
                        doc.setTextColor(0, 0, 0);
                        Object.entries(record.data).forEach(([key, value]) => {
                            const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                            const text = `${label}: ${value}`;
                            const splitText = doc.splitTextToSize(text, 170);
                            doc.text(splitText, 25, yPosition);
                            yPosition += splitText.length * 5;
                        });

                        yPosition += 10;
                        doc.setDrawColor(200, 200, 200);
                        doc.line(20, yPosition - 5, 190, yPosition - 5);
                        yPosition += 5;
                    });

                    const pageCount = doc.internal.getNumberOfPages();
                    for (let i = 1; i <= pageCount; i++) {
                        doc.setPage(i);
                        doc.setFontSize(8);
                        doc.setTextColor(150, 150, 150);
                        doc.text(`Page ${i} of ${pageCount}`, 170, 285);
                        doc.text('Medical Dashboard - Confidential', 20, 285);
                    }

                    const fileName = `medical_summary_${new Date().toISOString().split('T')[0]}.pdf`;
                    doc.save(fileName);
                });

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


uploadBtn.addEventListener('click', async function() {
    if (selectedFiles.length === 0) {
        showAlert('error', 'No files selected for upload.');
        return;
    }

    uploadBtn.disabled = true;
    uploadText.classList.add('hidden');
    uploadSpinner.classList.remove('hidden');
    uploadProgress.classList.remove('hidden');

    try {
        const formData = new FormData();
        selectedFiles.forEach(file => formData.append('files[]', file));
        if (categorySelect.value) {
            formData.append('category', categorySelect.value);
        }

        // Use the temporary upload endpoint
        const response = await fetch('{{ route("temp.upload") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData,
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || data.message || `Upload failed with status ${response.status}`);
        }

        showAlert('success', data.message || `Successfully uploaded ${data.files.length} file(s).`);

        // Refresh the documents list
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
});

            });
        </script>
    </div>
</body>
</html>
</x-app-layout>
