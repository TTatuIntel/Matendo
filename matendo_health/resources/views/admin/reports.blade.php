@extends('admin.layout')

@section('title', 'Reports & Analytics')

@section('content')
<div class="max-w-6xl mx-auto px-2 sm:px-4 lg:px-6">
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="medical-card p-4 sm:p-6 fade-in pulse-glow">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">Reports & Analytics Center</h1>
                        <p class="text-sm text-gray-600 font-medium flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v16a2 2 0 002 2z"></path>
                            </svg>
                            {{ now()->format('l, F j, Y') }} • Data Status: Real-time
                        </p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 lg:gap-3">
                    <select class="form-input !py-2 !px-4 text-sm" id="dateRange">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 3 months</option>
                        <option value="365">Last year</option>
                    </select>
                    <button onclick="refreshReports()" class="btn-secondary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh Data
                    </button>
                    <button onclick="showReportModal()" class="btn-primary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        Generate Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="medical-card p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Reports Generated</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalReports ?? 127 }}</p>
                        <p class="text-xs text-gray-500 mt-1">This month</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="medical-card p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Vital Signs</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $vitalSignsToday ?? 456 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Recorded today</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="medical-card p-6 hover-lift {{ ($alertsToday ?? 0) > 0 ? 'pulse-glow' : '' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Active Alerts</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $alertsToday ?? 23 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Requiring attention</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0l-7.918 8.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="medical-card p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Data Exports</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $appointmentsToday ?? 78 }}</p>
                        <p class="text-xs text-gray-500 mt-1">This week</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

         <!-- Advanced Report Generation -->
        <div class="medical-card p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Advanced Report Generation</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <!-- User Reports -->
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow hover-lift">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m-9 5.197v1a6 6 0 0010.967 0M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900">User Analytics</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">User registration trends, activity patterns, and role distribution</p>
                    <div class="flex gap-2">
                        <button onclick="exportReport('users')" class="btn-primary flex-1 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                            </svg>
                            CSV
                        </button>
                        <button onclick="viewReport('users')" class="btn-secondary flex-1 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            View
                        </button>
                    </div>
                </div>

                <!-- Health Metrics -->
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow hover-lift">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900">Health Metrics</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Vital signs trends, patient outcomes, and health indicators</p>
                    <div class="flex gap-2">
                        <button onclick="exportReport('health-metrics')" class="btn-primary flex-1 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                            </svg>
                            CSV
                        </button>
                        <button onclick="viewReport('health-metrics')" class="btn-secondary flex-1 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            View
                        </button>
                    </div>
                </div>

                <!-- Activity Reports -->
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow hover-lift">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900">System Activity</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">User actions, system events, and performance metrics</p>
                    <div class="flex gap-2">
                        <button onclick="exportReport('activity')" class="btn-primary flex-1 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                            </svg>
                            CSV
                        </button>
                        <button onclick="viewReport('activity')" class="btn-secondary flex-1 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            View
                        </button>
                    </div>
                </div>

                <!-- Alert Reports -->
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow hover-lift">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0l-7.918 8.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900">Alert Analysis</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Alert patterns, response times, and severity distribution</p>
                    <div class="flex gap-2">
                        <button onclick="exportReport('alerts')" class="btn-primary flex-1 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                            </svg>
                            CSV
                        </button>
                        <button onclick="viewReport('alerts')" class="btn-secondary flex-1 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            View
                        </button>
                    </div>
                </div>

                <!-- Doctor Workload -->
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow hover-lift">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900">Doctor Workload</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Patient assignments, consultation hours, and workload distribution</p>
                    <div class="flex gap-2">
                        <button onclick="exportReport('doctor-workload')" class="btn-primary flex-1 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                            </svg>
                            CSV
                        </button>
                        <button onclick="viewReport('doctor-workload')" class="btn-secondary flex-1 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"></path>
                            </svg>
                            View
                        </button>
                    </div>
                </div>

                <!-- Service Quality -->
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow hover-lift">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900">Service Quality</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Response times, satisfaction scores, and service metrics</p>
                    <div class="flex gap-2">
                        <button onclick="exportReport('service-quality')" class="btn-primary flex-1 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                            </svg>
                            CSV
                        </button>
                        <button onclick="viewReport('service-quality')" class="btn-secondary flex-1 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"></path>
                            </svg>
                            View
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Report Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- System Activity Report -->
            <div class="medical-card p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">System Activity</h3>
                <canvas id="activityChart" width="400" height="200"></canvas>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Peak Activity</span>
                        <span class="text-sm font-medium text-green-600">{{ $peakActivityTime ?? '2:00 PM' }}</span>
                    </div>
                </div>
            </div>

            <!-- User Registration Trends -->
            <div class="medical-card p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Registration Trends</h3>
                <canvas id="registrationChart" width="400" height="200"></canvas>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Growth Rate</span>
                        <span class="text-sm font-medium text-blue-600">+{{ $growthRate ?? '12.5' }}%</span>
                    </div>
                </div>
            </div>
        </div>       
        <!-- Recent Activity Log -->
        <div class="medical-card">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Recent System Activity</h3>
                <button onclick="clearActivityLog()" class="btn-secondary text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear Old Logs
                </button>
            </div>
            
            <div class="overflow-x-auto max-h-96">
                <table class="w-full">
                    <thead class="sticky top-0 bg-gray-50">
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Timestamp</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">User</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Action</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Type</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivities ?? [] as $activity)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">{{ $activity->created_at->format('M d, H:i') }}</div>
                                <div class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @if($activity->causer)
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            {{ substr($activity->causer->name ?? 'U', 0, 1) }}
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $activity->causer->name }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">System</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">{{ $activity->description }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $type = $activity->log_name ?? 'default';
                                    $typeClass = match($type) {
                                        'auth' => 'bg-blue-100 text-blue-700',
                                        'patient' => 'bg-purple-100 text-purple-700',
                                        'doctor' => 'bg-green-100 text-green-700',
                                        'admin' => 'bg-red-100 text-red-700',
                                        'system' => 'bg-gray-100 text-gray-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $typeClass }}">
                                    {{ ucfirst($type) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-xs text-gray-600">
                                    {{ json_encode($activity->properties ?? []) }}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">
                                No activity logs found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Charts initialization
document.addEventListener('DOMContentLoaded', function() {
    // Activity Chart
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Daily Activity',
                data: {{ json_encode($weeklyActivity ?? [65, 59, 80, 81, 56, 55, 40]) }},
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Registration Chart
    const registrationCtx = document.getElementById('registrationChart').getContext('2d');
    new Chart(registrationCtx, {
        type: 'line',
        data: {
            labels: {{ json_encode($registrationLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) }},
            datasets: [{
                label: 'New Registrations',
                data: {{ json_encode($registrationData ?? [28, 48, 40, 19, 86, 27]) }},
                borderColor: 'rgba(16, 185, 129, 1)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});

function exportReport(type) {
    const dateRange = document.getElementById('dateRange').value;
    window.location.href = `/admin/reports/export/${type}?days=${dateRange}`;
    showToast(`Exporting ${type} report...`, 'info');
}

function refreshReports() {
    showToast('Refreshing reports...', 'info');
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Advanced report generation functions
function showReportModal() {
    // This would show a comprehensive report generation modal
    // For now, we'll show a selection of available reports
    const reportTypes = [
        { value: 'users', label: 'User Analytics Report' },
        { value: 'health-metrics', label: 'Health Metrics Report' },
        { value: 'activity', label: 'System Activity Report' },
        { value: 'alerts', label: 'Alert Analysis Report' },
        { value: 'doctor-workload', label: 'Doctor Workload Report' },
        { value: 'service-quality', label: 'Service Quality Report' }
    ];
    
    const reportType = prompt('Select report type:\n' + 
        reportTypes.map((type, index) => `${index + 1}. ${type.label}`).join('\n') +
        '\n\nEnter number (1-6):');
    
    if (reportType && reportType >= 1 && reportType <= 6) {
        const selectedReport = reportTypes[reportType - 1];
        exportReport(selectedReport.value);
    }
}

function viewReport(type) {
    showToast(`Loading ${type} report...`, 'info');
    
    // Redirect to specific report view or open in modal
    const reportRoutes = {
        'users': '/admin/reports/users',
        'health-metrics': '/admin/reports/health-metrics',
        'activity': '/admin/reports/activity',
        'alerts': '/admin/reports/alerts',
        'doctor-workload': '/admin/reports/doctor-workload',
        'service-quality': '/admin/reports/service-quality'
    };
    
    if (reportRoutes[type]) {
        window.open(reportRoutes[type], '_blank');
    } else {
        showToast('Report view not available', 'error');
    }
}

function generateCustomReport() {
    const dateRange = document.getElementById('dateRange').value;
    const reportData = {
        date_from: new Date(Date.now() - (dateRange * 24 * 60 * 60 * 1000)).toISOString().split('T')[0],
        date_to: new Date().toISOString().split('T')[0],
        format: 'json'
    };
    
    showToast('Generating comprehensive report...', 'info');
    
    fetch('/admin/reports/generate', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            report_type: 'comprehensive',
            parameters: reportData,
            format: 'json'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Report generated successfully', 'success');
            // Handle the report data display
            console.log('Report Data:', data);
        } else {
            showToast('Error generating report', 'error');
        }
    })
    .catch(() => showToast('Error generating report', 'error'));
}

function clearActivityLog() {
    if (!confirm('Are you sure you want to clear old activity logs? This action cannot be undone.')) {
        return;
    }
    
    fetch('/admin/reports/clear-logs', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Activity logs cleared successfully', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast('Error clearing logs', 'error');
        }
    })
    .catch(() => showToast('Error clearing logs', 'error'));
}

// Enhanced chart interactions
function updateCharts(dateRange) {
    showToast('Updating charts...', 'info');
    
    fetch(`/admin/reports?days=${dateRange}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update charts with new data
        if (data.weeklyActivity && window.activityChart) {
            window.activityChart.data.datasets[0].data = data.weeklyActivity;
            window.activityChart.update();
        }
        
        if (data.registrationData && window.registrationChart) {
            window.registrationChart.data.datasets[0].data = data.registrationData;
            window.registrationChart.update();
        }
        
        showToast('Charts updated successfully', 'success');
    })
    .catch(() => showToast('Error updating charts', 'error'));
}

// Date range change handler
document.addEventListener('DOMContentLoaded', function() {
    const dateRangeSelect = document.getElementById('dateRange');
    if (dateRangeSelect) {
        dateRangeSelect.addEventListener('change', function() {
            updateCharts(this.value);
        });
    }
});

// Export with advanced options
function exportReportAdvanced(type, format = 'csv') {
    const dateRange = document.getElementById('dateRange').value;
    const params = new URLSearchParams({
        days: dateRange,
        format: format,
        timestamp: new Date().getTime()
    });
    
    const url = `/admin/reports/export/${type}?${params.toString()}`;
    
    showToast(`Preparing ${format.toUpperCase()} export...`, 'info');
    
    // Create invisible link and trigger download
    const link = document.createElement('a');
    link.href = url;
    link.download = `${type}-report-${new Date().toISOString().split('T')[0]}.${format}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    setTimeout(() => {
        showToast(`${format.toUpperCase()} export completed`, 'success');
    }, 2000);
}

// Real-time data refresh
function startRealTimeUpdates() {
    setInterval(() => {
        fetch('/admin/reports/live-data', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update live statistics
            if (data.stats) {
                Object.keys(data.stats).forEach(key => {
                    const element = document.querySelector(`[data-stat="${key}"]`);
                    if (element) {
                        element.textContent = data.stats[key];
                    }
                });
            }
        })
        .catch(console.error);
    }, 30000); // Update every 30 seconds
}
</script>
@endpush
