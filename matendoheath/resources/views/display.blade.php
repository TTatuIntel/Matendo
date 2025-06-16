<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Dashboard - Medical Records</title>
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
                        Medical Records
                    </h2>
                    <div class="flex space-x-4">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">Back to Dashboard</a>
                        <a href="{{ route('upload') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Upload Documents</a>
                        <button id="view-previous" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Previous
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
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
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="#" class="text-blue-600 hover:text-blue-800 view-record" data-record='{{ json_encode($record) }}'>View</a>
                                                <form action="{{ route('medical-records.destroy', $record->id) }}" method="POST" class="inline-block delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#" class="ml-4 text-red-600 hover:text-red-800 delete-record">Delete</a>
                                                </form>
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

            // All records from PHP, converted to JavaScript array
            const allRecords = @json($records);

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
                                        ${Object.entries(record.data).slice(0, 2).map(([key, value]) => `${key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}: ${value}`).join(', ')}
                                    </p>
                                    <p class="text-sm text-gray-500">${new Date(record.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                                </div>
                                <button class="text-blue-600 hover:text-blue-800 text-sm view-history-record">View Details</button>
                            </div>
                        </div>
                    `).join('') : '<p class="text-sm text-gray-500">No records found for this category.</p>';

                    historyModal.classList.remove('hidden');

                    // Add event listeners to view details buttons
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
        });
    </script>
</body>
</html>
</x-app-layout>
