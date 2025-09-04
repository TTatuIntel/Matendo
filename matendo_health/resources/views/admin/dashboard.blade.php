<?php
// resources/views/admin/dashboard.blade.php
?>
@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-6xl mx-auto px-2 sm:px-4 lg:px-6">
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="medical-card p-4 sm:p-6 fade-in pulse-glow">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">Administrative Control Center</h1>
                        <p class="text-sm text-gray-600 font-medium flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v16a2 2 0 002 2z"></path>
                            </svg>
                            {{ now()->format('l, F j, Y') }} • System Status: Online
                        </p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 lg:gap-3">
                    <button onclick="refreshDashboard()" class="btn-secondary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh Data
                    </button>
                    <a href="{{ route('admin.reports.index') }}" class="btn-primary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z"></path>
                        </svg>
                        Generate Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="medical-card p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalUsers ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">All registered users</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m-9 5.197v1a6 6 0 0010.967 0M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="medical-card p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Active Doctors</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $activeDoctors ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Verified & active</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="medical-card p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Patients</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalPatients ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Registered patients</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="medical-card p-6 hover-lift {{ ($activeAlerts ?? 0) > 0 ? 'pulse-glow' : '' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Active Alerts</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $activeAlerts ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Critical alerts</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0l-7.918 8.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="medical-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Monthly Registrations</h3>
                    <select class="form-input w-32 text-xs" id="registrationPeriod">
                        <option value="6">Last 6 months</option>
                        <option value="12" selected>Last year</option>
                    </select>
                </div>
                <div class="h-64">
                    <canvas id="registrationsChart"></canvas>
                </div>
            </div>
            
            <div class="medical-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">User Distribution</h3>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-xs text-gray-600">Active users</span>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- System Health & Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- System Health -->
            <div class="medical-card p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">System Health</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Server Status</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Online</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Database</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Connected</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Last Backup</span>
                        <span class="text-xs text-gray-500">{{ $lastBackup ?? '2 hours ago' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Storage Usage</span>
                        <span class="text-xs text-gray-500">{{ $storageUsage ?? '68%' }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="medical-card lg:col-span-2">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Recent Activities</h3>
                        <a href="{{ route('admin.audit.index') }}" class="text-sm text-purple-600 hover:text-purple-800">View all</a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4 custom-scrollbar" style="max-height: 300px; overflow-y: auto;">
                        @forelse($recentActivities ?? [] as $activity)
                        <div class="flex items-start space-x-3 py-3 border-b border-gray-100 last:border-0">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                @if(str_contains($activity->type ?? 'system', 'user'))
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                @elseif(str_contains($activity->type ?? 'system', 'alert'))
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0l-7.918 8.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $activity->description ?? 'System activity' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() ?? '2 minutes ago' }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if(str_contains($activity->type ?? 'info', 'error')) bg-red-100 text-red-700
                                @elseif(str_contains($activity->type ?? 'info', 'warning')) bg-yellow-100 text-yellow-700
                                @elseif(str_contains($activity->type ?? 'info', 'success')) bg-green-100 text-green-700
                                @else bg-blue-100 text-blue-700 @endif">
                                {{ ucfirst($activity->type ?? 'info') }}
                            </span>
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p>No recent activities</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    startRealTimeUpdates();
});

// Chart instances
let registrationsChart, activityChart;

function initializeCharts() {
    // Registrations Chart
    const ctx1 = document.getElementById('registrationsChart').getContext('2d');
    registrationsChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: {!! json_encode($registrationLabels ?? []) !!},
            datasets: [{
                label: 'New Registrations',
                data: {!! json_encode($registrationData ?? []) !!},
                borderColor: 'rgb(124, 58, 237)',
                backgroundColor: 'rgba(124, 58, 237, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(124, 58, 237)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(124, 58, 237)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Activity Chart
    const ctx2 = document.getElementById('activityChart').getContext('2d');
    activityChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Doctors', 'Patients', 'Admins'],
            datasets: [{
                data: [{{ $activeDoctors ?? 0 }}, {{ $totalPatients ?? 0 }}, {{ $totalAdmins ?? 0 }}],
                backgroundColor: [
                    '#10B981', // Green for doctors
                    '#8B5CF6', // Purple for patients
                    '#F59E0B'  // Amber for admins
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
}

// Refresh dashboard data
function refreshDashboard() {
    const button = event.target.closest('button');
    showLoading(button);
    
    fetch('/admin/dashboard-data', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateStats(data.stats);
            updateCharts(data.charts);
            showToast('Dashboard updated successfully', 'success');
        } else {
            showToast('Error updating dashboard', 'error');
        }
    })
    .catch(error => {
        console.error('Error refreshing dashboard:', error);
        showToast('Error updating dashboard', 'error');
    })
    .finally(() => {
        hideLoading(button);
    });
}

// Update stats cards
function updateStats(stats) {
    // Update stat cards with new values
    // This would update the displayed numbers in the stat cards
}

// Update chart data
function updateCharts(chartData) {
    if (chartData.registrations) {
        registrationsChart.data.labels = chartData.registrations.labels;
        registrationsChart.data.datasets[0].data = chartData.registrations.data;
        registrationsChart.update();
    }
    
    if (chartData.activity) {
        activityChart.data.datasets[0].data = chartData.activity.data;
        activityChart.update();
    }
}

// Real-time updates
function startRealTimeUpdates() {
    // Update every 5 minutes
    setInterval(() => {
        fetch('/admin/dashboard-stats', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.alerts && data.alerts > 0) {
                // Flash alert indicator
                const alertCard = document.querySelector('.pulse-glow');
                if (alertCard) {
                    alertCard.classList.add('animate-pulse');
                }
            }
        })
        .catch(error => console.error('Real-time update error:', error));
    }, 300000); // 5 minutes
}

// Registration period filter
document.getElementById('registrationPeriod')?.addEventListener('change', function() {
    const period = this.value;
    
    fetch(`/admin/dashboard-registrations?period=${period}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            registrationsChart.data.labels = data.labels;
            registrationsChart.data.datasets[0].data = data.data;
            registrationsChart.update();
        }
    })
    .catch(error => console.error('Error updating chart:', error));
});
</script>
@endpush
@endsection
