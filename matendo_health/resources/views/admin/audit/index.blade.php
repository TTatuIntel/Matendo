@extends('admin.layout')

@section('title', 'Audit Logs')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="medical-card p-4 sm:p-6 fade-in">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">Audit Log Management</h1>
                        <p class="text-sm text-gray-600 font-medium">Monitor system activities and maintain compliance audit trails</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 lg:gap-3">
                    <button onclick="exportLogs()" class="btn-secondary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        Export Logs
                    </button>
                    <button onclick="showCleanupLogsModal()" class="btn-secondary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Cleanup Old Logs
                    </button>
                    <button onclick="refreshLogs()" class="btn-primary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="medical-card p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <input type="text" id="searchFilter" placeholder="Search by description, user, or subject..." class="form-input">
                </div>
                <select id="logTypeFilter" class="form-input">
                    <option value="">All Log Types</option>
                    <option value="auth">Authentication</option>
                    <option value="user">User Actions</option>
                    <option value="doctor">Doctor Activities</option>
                    <option value="patient">Patient Activities</option>
                    <option value="admin">Admin Actions</option>
                    <option value="system">System Events</option>
                    <option value="backup">Backup Operations</option>
                    <option value="security">Security Events</option>
                </select>
                <input type="date" id="dateFromFilter" class="form-input" placeholder="From date">
                <input type="date" id="dateToFilter" class="form-input" placeholder="To date">
            </div>
            <div class="flex justify-between items-center mt-4">
                <div class="flex items-center space-x-4">
                    <button onclick="applyFilters()" class="btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Apply Filters
                    </button>
                    <button onclick="clearFilters()" class="btn-secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Clear
                    </button>
                </div>
                <div class="text-sm text-gray-600">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() ?? 0 }} logs
                </div>
            </div>
        </div>

        <!-- Activity Log Table -->
        <div class="medical-card">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">System Activity Logs</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full" id="auditLogsTable">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Timestamp</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">User</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Action</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Type</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Subject</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">IP Address</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs ?? [] as $log)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">{{ $log->created_at->format('M d, Y H:i:s') }}</div>
                                <div class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @if($log->causer)
                                    <div class="flex items-center space-x-2">
                                        @php
                                            $userRole = $log->causer->role ?? 'user';
                                            $avatarClass = match($userRole) {
                                                'admin' => 'from-red-500 to-pink-600',
                                                'doctor' => 'from-green-500 to-emerald-600',
                                                'patient' => 'from-blue-500 to-indigo-600',
                                                default => 'from-gray-500 to-gray-600'
                                            };
                                        @endphp
                                        <div class="w-6 h-6 bg-gradient-to-r {{ $avatarClass }} rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            {{ substr($log->causer->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $log->causer->name }}</div>
                                            <div class="text-xs text-gray-500">{{ ucfirst($userRole) }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-gray-500 rounded-full flex items-center justify-center text-white text-xs font-bold">S</div>
                                        <div class="text-sm text-gray-900">System</div>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">{{ $log->description ?? 'No description' }}</div>
                                @if($log->properties && count($log->properties) > 0)
                                    <div class="text-xs text-gray-500 mt-1">
                                        <button onclick="showLogDetails('{{ $log->id }}')" class="text-blue-600 hover:text-blue-800">
                                            View Details
                                        </button>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $logType = $log->log_name ?? 'default';
                                    $typeClass = match($logType) {
                                        'auth' => 'bg-blue-100 text-blue-700',
                                        'user' => 'bg-purple-100 text-purple-700',
                                        'doctor' => 'bg-green-100 text-green-700',
                                        'patient' => 'bg-indigo-100 text-indigo-700',
                                        'admin' => 'bg-red-100 text-red-700',
                                        'system' => 'bg-gray-100 text-gray-700',
                                        'backup' => 'bg-yellow-100 text-yellow-700',
                                        'security' => 'bg-orange-100 text-orange-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $typeClass }}">
                                    {{ ucfirst($logType) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($log->subject)
                                    <div class="text-sm text-gray-900">{{ class_basename($log->subject_type) }}</div>
                                    <div class="text-xs text-gray-500">
                                        @if($log->subject_type === 'App\Models\User')
                                            {{ $log->subject->name ?? 'N/A' }}
                                        @else
                                            ID: {{ $log->subject_id }}
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">N/A</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">
                                    {{ $log->properties['ip_address'] ?? request()->ip() ?? 'Unknown' }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-2">
                                    @if($log->causer)
                                        <a href="{{ route('admin.audit.user-logs', $log->causer) }}" class="text-blue-600 hover:text-blue-800" title="View User Logs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    <button onclick="showLogDetails('{{ $log->id }}')" class="text-green-600 hover:text-green-800" title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                No audit logs found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($logs) && $logs->hasPages())
            <div class="p-6 border-t border-gray-200">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<div id="logDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="medical-card p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">Log Entry Details</h3>
            <button onclick="closeLogDetailsModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="logDetailsContent" class="space-y-4">
            <!-- Log details will be loaded here -->
            <div class="text-center py-8">
                <div class="spinner mx-auto mb-4"></div>
                <p class="text-gray-600">Loading log details...</p>
            </div>
        </div>
    </div>
</div>

<!-- Cleanup Logs Modal -->
<div id="cleanupLogsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="medical-card p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Cleanup Old Logs</h3>
        <form id="cleanupLogsForm">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Delete logs older than</label>
                <select name="days" class="form-input" required>
                    <option value="30">30 days</option>
                    <option value="60">60 days</option>
                    <option value="90" selected>90 days</option>
                    <option value="180">180 days</option>
                    <option value="365">1 year</option>
                </select>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0l-7.918 8.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <span class="text-sm font-medium text-yellow-800">Warning: This action cannot be undone!</span>
                </div>
                <p class="text-sm text-yellow-700 mt-1">Consider exporting logs before cleanup for compliance records.</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeCleanupLogsModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">Cleanup Logs</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// CSRF token setup
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupToastNotifications();
    setupRealTimeSearch();
});

// Modal Functions
function showLogDetails(logId) {
    document.getElementById('logDetailsModal').classList.remove('hidden');
    document.getElementById('logDetailsModal').classList.add('flex');
    
    const content = document.getElementById('logDetailsContent');
    content.innerHTML = `
        <div class="text-center py-8">
            <div class="spinner mx-auto mb-4"></div>
            <p class="text-gray-600">Loading log details...</p>
        </div>
    `;
    
    // Fetch log details (this would be implemented with a route that returns specific log details)
    fetch(`/admin/audit/log/${logId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayLogDetails(data.log);
        } else {
            content.innerHTML = `<div class="text-center py-8"><p class="text-red-600">Error loading log details</p></div>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        content.innerHTML = `<div class="text-center py-8"><p class="text-red-600">Error loading log details</p></div>`;
    });
}

function displayLogDetails(log) {
    const content = document.getElementById('logDetailsContent');
    content.innerHTML = `
        <div class="space-y-4">
            <div class="medical-card p-4">
                <h4 class="font-bold text-gray-900 mb-3">Basic Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium">Timestamp:</span> ${log.created_at}</div>
                    <div><span class="font-medium">Log Type:</span> ${log.log_name || 'N/A'}</div>
                    <div><span class="font-medium">User:</span> ${log.causer ? log.causer.name : 'System'}</div>
                    <div><span class="font-medium">IP Address:</span> ${log.properties?.ip_address || 'Unknown'}</div>
                    <div class="col-span-2"><span class="font-medium">Description:</span> ${log.description || 'No description'}</div>
                </div>
            </div>
            
            ${log.subject ? `
            <div class="medical-card p-4">
                <h4 class="font-bold text-gray-900 mb-3">Subject Information</h4>
                <div class="text-sm space-y-2">
                    <div><span class="font-medium">Type:</span> ${log.subject_type}</div>
                    <div><span class="font-medium">ID:</span> ${log.subject_id}</div>
                    ${log.subject.name ? `<div><span class="font-medium">Name:</span> ${log.subject.name}</div>` : ''}
                </div>
            </div>
            ` : ''}
            
            ${log.properties && Object.keys(log.properties).length > 0 ? `
            <div class="medical-card p-4">
                <h4 class="font-bold text-gray-900 mb-3">Additional Properties</h4>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <pre class="text-xs text-gray-700">${JSON.stringify(log.properties, null, 2)}</pre>
                </div>
            </div>
            ` : ''}
        </div>
    `;
}

function closeLogDetailsModal() {
    document.getElementById('logDetailsModal').classList.add('hidden');
    document.getElementById('logDetailsModal').classList.remove('flex');
}

function showCleanupLogsModal() {
    document.getElementById('cleanupLogsModal').classList.remove('hidden');
    document.getElementById('cleanupLogsModal').classList.add('flex');
}

function closeCleanupLogsModal() {
    document.getElementById('cleanupLogsModal').classList.add('hidden');
    document.getElementById('cleanupLogsModal').classList.remove('flex');
    document.getElementById('cleanupLogsForm').reset();
}

// Action Functions
function exportLogs() {
    showToast('Preparing logs export...', 'info');
    
    // Get current filter values to include in export
    const filters = {
        search: document.getElementById('searchFilter')?.value || '',
        log_type: document.getElementById('logTypeFilter')?.value || '',
        date_from: document.getElementById('dateFromFilter')?.value || '',
        date_to: document.getElementById('dateToFilter')?.value || ''
    };
    
    const params = new URLSearchParams();
    Object.keys(filters).forEach(key => {
        if (filters[key]) params.append(key, filters[key]);
    });
    
    const url = `/admin/audit/export${params.toString() ? '?' + params.toString() : ''}`;
    window.location.href = url;
}

function refreshLogs() {
    showToast('Refreshing logs...', 'info');
    setTimeout(() => window.location.reload(), 500);
}

// Form Submissions
document.getElementById('cleanupLogsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitButton = this.querySelector('button[type="submit"]');
    showLoading(submitButton);
    
    const formData = new FormData(this);
    
    fetch('/admin/audit/clear-old-logs', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeCleanupLogsModal();
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast(data.message || 'Error cleaning up logs', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error cleaning up logs', 'error');
    })
    .finally(() => {
        hideLoading(submitButton);
    });
});

// Filter Functions
function applyFilters() {
    const filters = {
        search: document.getElementById('searchFilter')?.value || '',
        log_type: document.getElementById('logTypeFilter')?.value || '',
        date_from: document.getElementById('dateFromFilter')?.value || '',
        date_to: document.getElementById('dateToFilter')?.value || ''
    };
    
    const params = new URLSearchParams();
    Object.keys(filters).forEach(key => {
        if (filters[key]) params.append(key, filters[key]);
    });
    
    const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    window.location.href = url;
}

function clearFilters() {
    document.getElementById('searchFilter').value = '';
    document.getElementById('logTypeFilter').value = '';
    document.getElementById('dateFromFilter').value = '';
    document.getElementById('dateToFilter').value = '';
    
    window.location.href = window.location.pathname;
}

// Real-time search setup
function setupRealTimeSearch() {
    const searchInput = document.getElementById('searchFilter');
    let searchTimeout;
    
    searchInput?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#auditLogsTable tbody tr');
            
            rows.forEach(row => {
                if (row.cells.length === 1) return; // Skip empty state row
                
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }, 300);
    });
}

// Utility Functions
function showLoading(button) {
    if (!button) return;
    button.disabled = true;
    const originalText = button.innerHTML;
    button.setAttribute('data-original-text', originalText);
    button.innerHTML = '<div class="spinner inline-block mr-2"></div>Loading...';
}

function hideLoading(button) {
    if (!button) return;
    button.disabled = false;
    const originalText = button.getAttribute('data-original-text');
    if (originalText) {
        button.innerHTML = originalText;
    }
}

// Toast notification system
function setupToastNotifications() {
    if (!document.getElementById('toast-container')) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
    }
}

function showToast(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toast-container') || document.body;
    const toast = document.createElement('div');
    const toastId = 'toast-' + Date.now();
    
    toast.id = toastId;
    toast.className = `toast show toast-${type} p-4 rounded-lg shadow-lg text-white min-w-80`;
    
    const icon = {
        success: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
        error: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
        info: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
    };
    
    toast.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">${icon[type] || icon.info}</div>
            <div class="ml-3"><p class="text-sm font-medium">${message}</p></div>
            <button onclick="removeToast('${toastId}')" class="ml-4 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    container.appendChild(toast);
    setTimeout(() => removeToast(toastId), duration);
}

function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => toast.remove(), 300);
    }
}

// Modal click outside to close
document.addEventListener('click', function(event) {
    const modals = [
        document.getElementById('logDetailsModal'),
        document.getElementById('cleanupLogsModal')
    ];
    
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeLogDetailsModal();
        closeCleanupLogsModal();
    }
    
    if ((event.ctrlKey || event.metaKey) && event.key === 'e') {
        event.preventDefault();
        exportLogs();
    }
});
</script>
@endpush
