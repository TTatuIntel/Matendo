@extends('layouts.admin')

@section('title', 'Overall System Monitor')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h2 mb-0">Overall System Monitor</h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" onclick="refreshData()">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                    <button class="btn btn-outline-secondary" onclick="toggleAutoRefresh()" id="autoRefreshBtn">
                        <i class="fas fa-play"></i> Auto Refresh
                    </button>
                    <button class="btn btn-success" onclick="exportData()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            <p class="text-muted">Real-time monitoring of system status and patient care metrics</p>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Patients</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="activePatients">{{ $activePatients }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Available Doctors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="availableDoctors">{{ $availableDoctors }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-md fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Active External Access</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeExternalAccess">{{ $activeExternalAccess }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-external-link-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Critical Alerts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="criticalAlerts">{{ $criticalAlerts }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Patient Assignments Overview -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Patient Assignments per Doctor</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Actions:</div>
                            <a class="dropdown-item" href="#" onclick="exportPatientAssignments()">Export Data</a>
                            <a class="dropdown-item" href="{{ route('admin.doctors.index') }}">Manage Doctors</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="patientAssignmentsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctor Availability Status -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Doctor Availability Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="doctorAvailabilityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- External Access Activity -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">External Access Activity</h6>
                    <span class="badge badge-info" id="liveIndicator">LIVE</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="externalAccessTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>External Doctor</th>
                                    <th>Status</th>
                                    <th>Started</th>
                                    <th>Last Activity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($externalAccessData as $access)
                                <tr data-access-id="{{ $access['id'] }}">
                                    <td>{{ $access['patient_name'] }}</td>
                                    <td>{{ $access['doctor_name'] ?? 'Anonymous' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $access['status'] === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($access['status']) }}
                                        </span>
                                    </td>
                                    <td>{{ $access['started_at'] }}</td>
                                    <td>{{ $access['last_activity'] }}</td>
                                    <td>
                                        @if($access['status'] === 'active')
                                            <button class="btn btn-sm btn-warning" onclick="terminateAccess({{ $access['id'] }})">
                                                <i class="fas fa-stop"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-sm btn-info" onclick="viewAccessLogs({{ $access['id'] }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No active external access sessions</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Critical Alerts -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Critical Alerts</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="alertsDropdown"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <a class="dropdown-item" href="#" onclick="acknowledgeAllAlerts()">Acknowledge All</a>
                            <a class="dropdown-item" href="{{ route('admin.alerts.index') }}">View All Alerts</a>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <div id="criticalAlertsList">
                        @forelse($criticalAlertsData as $alert)
                        <div class="alert alert-{{ $alert['level'] === 'critical' ? 'danger' : 'warning' }} alert-dismissible fade show mb-2" 
                             data-alert-id="{{ $alert['id'] }}">
                            <small class="text-muted d-block">{{ $alert['created_at'] }}</small>
                            <strong>{{ $alert['patient_name'] }}</strong><br>
                            {{ $alert['message'] }}
                            <button type="button" class="close" onclick="acknowledgeAlert({{ $alert['id'] }})" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @empty
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <p>No critical alerts</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Vitals Data -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Critical Vitals</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 300px;">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Vital</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody id="recentVitalsTable">
                                @forelse($recentVitals as $vital)
                                <tr class="{{ $vital['risk_level'] === 'high' ? 'table-danger' : ($vital['risk_level'] === 'medium' ? 'table-warning' : '') }}">
                                    <td>{{ $vital['patient_name'] }}</td>
                                    <td>{{ $vital['type'] }}</td>
                                    <td>{{ $vital['value'] }} {{ $vital['unit'] }}</td>
                                    <td>
                                        <span class="badge badge-{{ $vital['risk_level'] === 'high' ? 'danger' : ($vital['risk_level'] === 'medium' ? 'warning' : 'success') }}">
                                            {{ ucfirst($vital['risk_level']) }}
                                        </span>
                                    </td>
                                    <td>{{ $vital['recorded_at'] }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No critical vitals recorded</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Performance Metrics -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Performance</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="small font-weight-bold">Database Response Time</span>
                            <span class="small">{{ $systemMetrics['db_response_time'] }}ms</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $systemMetrics['db_response_time'] < 100 ? 'success' : ($systemMetrics['db_response_time'] < 500 ? 'warning' : 'danger') }}" 
                                 role="progressbar" style="width: {{ min(($systemMetrics['db_response_time'] / 1000) * 100, 100) }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="small font-weight-bold">Memory Usage</span>
                            <span class="small">{{ $systemMetrics['memory_usage'] }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $systemMetrics['memory_usage'] < 70 ? 'success' : ($systemMetrics['memory_usage'] < 85 ? 'warning' : 'danger') }}" 
                                 role="progressbar" style="width: {{ $systemMetrics['memory_usage'] }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="small font-weight-bold">Active Sessions</span>
                            <span class="small">{{ $systemMetrics['active_sessions'] }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="small font-weight-bold">Data Sync Status</span>
                            <span class="badge badge-{{ $systemMetrics['data_sync_status'] === 'synchronized' ? 'success' : 'warning' }}">
                                {{ ucfirst($systemMetrics['data_sync_status']) }}
                            </span>
                        </div>
                    </div>

                    <div class="text-center">
                        <small class="text-muted">Last updated: <span id="lastUpdated">{{ now()->format('H:i:s') }}</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Access Logs Modal -->
<div class="modal fade" id="accessLogsModal" tabindex="-1" role="dialog" aria-labelledby="accessLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accessLogsModalLabel">External Access Logs</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="accessLogsContent">
                    <!-- Access logs content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
#liveIndicator {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
    100% {
        opacity: 1;
    }
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let autoRefreshInterval = null;
let patientAssignmentsChart = null;
let doctorAvailabilityChart = null;

// Initialize charts on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    updateCharts();
});

function initializeCharts() {
    // Patient Assignments Chart
    const assignmentsCtx = document.getElementById('patientAssignmentsChart').getContext('2d');
    patientAssignmentsChart = new Chart(assignmentsCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Assigned Patients',
                data: [],
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Doctor Availability Chart
    const availabilityCtx = document.getElementById('doctorAvailabilityChart').getContext('2d');
    doctorAvailabilityChart = new Chart(availabilityCtx, {
        type: 'doughnut',
        data: {
            labels: ['Available', 'Busy', 'Offline'],
            datasets: [{
                data: [],
                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#17a673', '#f4b619', '#e02d1b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function updateCharts() {
    fetch('{{ route("admin.overall-monitor.realtime") }}')
        .then(response => response.json())
        .then(data => {
            // Update patient assignments chart
            patientAssignmentsChart.data.labels = data.patientAssignments.labels;
            patientAssignmentsChart.data.datasets[0].data = data.patientAssignments.data;
            patientAssignmentsChart.update();

            // Update doctor availability chart
            doctorAvailabilityChart.data.datasets[0].data = data.doctorAvailability.data;
            doctorAvailabilityChart.update();
        })
        .catch(error => console.error('Error updating charts:', error));
}

function refreshData() {
    fetch('{{ route("admin.overall-monitor.realtime") }}')
        .then(response => response.json())
        .then(data => {
            // Update status cards
            document.getElementById('activePatients').textContent = data.activePatients;
            document.getElementById('availableDoctors').textContent = data.availableDoctors;
            document.getElementById('activeExternalAccess').textContent = data.activeExternalAccess;
            document.getElementById('criticalAlerts').textContent = data.criticalAlerts;

            // Update charts
            updateCharts();

            // Update external access table
            updateExternalAccessTable(data.externalAccessData);

            // Update critical alerts
            updateCriticalAlerts(data.criticalAlertsData);

            // Update recent vitals
            updateRecentVitals(data.recentVitals);

            // Update last updated time
            document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString();

            showToast('Data refreshed successfully', 'success');
        })
        .catch(error => {
            console.error('Error refreshing data:', error);
            showToast('Error refreshing data', 'error');
        });
}

function toggleAutoRefresh() {
    const btn = document.getElementById('autoRefreshBtn');
    
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
        btn.innerHTML = '<i class="fas fa-play"></i> Auto Refresh';
        btn.classList.remove('btn-danger');
        btn.classList.add('btn-outline-secondary');
    } else {
        autoRefreshInterval = setInterval(refreshData, 30000); // Refresh every 30 seconds
        btn.innerHTML = '<i class="fas fa-pause"></i> Stop Refresh';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-danger');
        showToast('Auto refresh enabled (30s)', 'info');
    }
}

function updateExternalAccessTable(data) {
    const tbody = document.querySelector('#externalAccessTable tbody');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No active external access sessions</td></tr>';
        return;
    }

    data.forEach(access => {
        const row = `
            <tr data-access-id="${access.id}">
                <td>${access.patient_name}</td>
                <td>${access.doctor_name || 'Anonymous'}</td>
                <td>
                    <span class="badge badge-${access.status === 'active' ? 'success' : 'secondary'}">
                        ${access.status.charAt(0).toUpperCase() + access.status.slice(1)}
                    </span>
                </td>
                <td>${access.started_at}</td>
                <td>${access.last_activity}</td>
                <td>
                    ${access.status === 'active' ? 
                        `<button class="btn btn-sm btn-warning" onclick="terminateAccess(${access.id})">
                            <i class="fas fa-stop"></i>
                        </button>` : ''
                    }
                    <button class="btn btn-sm btn-info" onclick="viewAccessLogs(${access.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

function updateCriticalAlerts(data) {
    const container = document.getElementById('criticalAlertsList');
    container.innerHTML = '';

    if (data.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <p>No critical alerts</p>
            </div>
        `;
        return;
    }

    data.forEach(alert => {
        const alertDiv = `
            <div class="alert alert-${alert.level === 'critical' ? 'danger' : 'warning'} alert-dismissible fade show mb-2" 
                 data-alert-id="${alert.id}">
                <small class="text-muted d-block">${alert.created_at}</small>
                <strong>${alert.patient_name}</strong><br>
                ${alert.message}
                <button type="button" class="close" onclick="acknowledgeAlert(${alert.id})" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        container.innerHTML += alertDiv;
    });
}

function updateRecentVitals(data) {
    const tbody = document.getElementById('recentVitalsTable');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No critical vitals recorded</td></tr>';
        return;
    }

    data.forEach(vital => {
        const row = `
            <tr class="${vital.risk_level === 'high' ? 'table-danger' : (vital.risk_level === 'medium' ? 'table-warning' : '')}">
                <td>${vital.patient_name}</td>
                <td>${vital.type}</td>
                <td>${vital.value} ${vital.unit}</td>
                <td>
                    <span class="badge badge-${vital.risk_level === 'high' ? 'danger' : (vital.risk_level === 'medium' ? 'warning' : 'success')}">
                        ${vital.risk_level.charAt(0).toUpperCase() + vital.risk_level.slice(1)}
                    </span>
                </td>
                <td>${vital.recorded_at}</td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

function terminateAccess(accessId) {
    if (!confirm('Are you sure you want to terminate this external access session?')) {
        return;
    }

    fetch(`/admin/external-access/${accessId}/revoke`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('External access terminated successfully', 'success');
            refreshData();
        } else {
            showToast('Error terminating access', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error terminating access', 'error');
    });
}

function viewAccessLogs(accessId) {
    // Load access logs into modal
    fetch(`/admin/external-access/${accessId}/logs`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('accessLogsContent').innerHTML = html;
            $('#accessLogsModal').modal('show');
        })
        .catch(error => {
            console.error('Error loading access logs:', error);
            showToast('Error loading access logs', 'error');
        });
}

function acknowledgeAlert(alertId) {
    fetch(`/admin/alerts/${alertId}/acknowledge`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`[data-alert-id="${alertId}"]`).remove();
            showToast('Alert acknowledged', 'success');
        }
    })
    .catch(error => console.error('Error acknowledging alert:', error));
}

function acknowledgeAllAlerts() {
    if (!confirm('Are you sure you want to acknowledge all critical alerts?')) {
        return;
    }

    fetch('/admin/alerts/acknowledge-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('criticalAlertsList').innerHTML = `
                <div class="text-center text-muted py-3">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <p>No critical alerts</p>
                </div>
            `;
            document.getElementById('criticalAlerts').textContent = '0';
            showToast('All alerts acknowledged', 'success');
        }
    })
    .catch(error => console.error('Error acknowledging alerts:', error));
}

function exportData() {
    window.open('/admin/overall-monitor/export', '_blank');
    showToast('Export initiated', 'info');
}

function exportPatientAssignments() {
    window.open('/admin/doctors/export', '_blank');
    showToast('Patient assignments export initiated', 'info');
}

function showToast(message, type = 'info') {
    // Create and show a toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    document.body.appendChild(toast);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Clean up intervals when page is unloaded
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});
</script>
@endpush
