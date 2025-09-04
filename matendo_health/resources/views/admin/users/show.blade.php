@extends('admin.layout')

@section('title', 'User Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="medical-card p-6 fade-in">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4">
                    @php
                        $avatarClass = match($user->role ?? 'patient') {
                            'admin' => 'from-red-500 to-pink-600',
                            'doctor' => 'from-green-500 to-emerald-600',
                            'patient' => 'from-blue-500 to-indigo-600',
                            default => 'from-gray-500 to-gray-600'
                        };
                    @endphp
                    <div class="w-20 h-20 bg-gradient-to-r {{ $avatarClass }} rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                        {{ substr($user->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <div class="flex items-center space-x-2 mt-1">
                            @php
                                $roleClass = match($user->role ?? 'patient') {
                                    'admin' => 'bg-red-100 text-red-700',
                                    'doctor' => 'bg-green-100 text-green-700',
                                    'patient' => 'bg-blue-100 text-blue-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                                $statusClass = match($user->status ?? 'active') {
                                    'active' => 'bg-green-100 text-green-700',
                                    'inactive' => 'bg-gray-100 text-gray-700',
                                    'suspended' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <span class="px-3 py-1 text-sm font-medium rounded-full {{ $roleClass }}">
                                {{ ucfirst($user->role ?? 'Unknown') }}
                            </span>
                            <span class="px-3 py-1 text-sm font-medium rounded-full {{ $statusClass }}">
                                {{ ucfirst($user->status ?? 'Unknown') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ $user->email }}</p>
                        @if($user->phone)
                            <p class="text-sm text-gray-600">{{ $user->phone }}</p>
                        @endif
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Users
                    </a>
                    <button onclick="editUser('{{ $user->id }}')" class="btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit User
                    </button>
                </div>
            </div>
        </div>

        <!-- User Information Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Basic Information -->
            <div class="medical-card p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Basic Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">User ID</label>
                        <p class="text-sm text-gray-900">{{ $user->id }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Email</label>
                        <p class="text-sm text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Registration Date</label>
                        <p class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Last Activity</label>
                        @if($user->last_activity)
                            <p class="text-sm text-gray-900">{{ $user->last_activity->format('M d, Y H:i') }}</p>
                            <p class="text-xs text-gray-500">{{ $user->last_activity->diffForHumans() }}</p>
                        @else
                            <p class="text-sm text-gray-500">Never</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Role-Specific Statistics -->
            <div class="medical-card p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    {{ ucfirst($user->role) }} Statistics
                </h3>
                <div class="space-y-3">
                    @if($user->role === 'doctor' && isset($stats))
                        <div>
                            <label class="text-sm font-medium text-gray-600">Total Patients</label>
                            <p class="text-2xl font-bold text-green-600">{{ $stats['total_patients'] ?? 0 }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Appointments This Month</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $stats['appointments_this_month'] ?? 0 }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Verification Status</label>
                            @php
                                $verificationClass = match($stats['verification_status'] ?? 'pending') {
                                    'verified' => 'text-green-600',
                                    'rejected' => 'text-red-600',
                                    'pending' => 'text-yellow-600',
                                    default => 'text-gray-600'
                                };
                            @endphp
                            <p class="text-sm font-semibold {{ $verificationClass }}">
                                {{ ucfirst($stats['verification_status'] ?? 'Unknown') }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Specialization</label>
                            <p class="text-sm text-gray-900">{{ $stats['specialization'] ?? 'Not specified' }}</p>
                        </div>
                    @elseif($user->role === 'patient' && isset($stats))
                        <div>
                            <label class="text-sm font-medium text-gray-600">Assigned Doctors</label>
                            <p class="text-2xl font-bold text-blue-600">{{ $stats['assigned_doctors'] ?? 0 }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Recent Vitals (7 days)</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $stats['recent_vitals'] ?? 0 }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Active Alerts</label>
                            <p class="text-lg font-semibold text-red-600">{{ $stats['active_alerts'] ?? 0 }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Medical Record Number</label>
                            <p class="text-sm text-gray-900">{{ $stats['medical_record_number'] ?? 'Not assigned' }}</p>
                        </div>
                    @elseif($user->role === 'admin' && isset($stats))
                        <div>
                            <label class="text-sm font-medium text-gray-600">Actions This Month</label>
                            <p class="text-2xl font-bold text-red-600">{{ $stats['actions_performed'] ?? 0 }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Users Managed This Month</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $stats['users_managed'] ?? 0 }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Last Login</label>
                            @if($stats['last_login'] ?? false)
                                <p class="text-sm text-gray-900">{{ $stats['last_login']->format('M d, Y H:i') }}</p>
                            @else
                                <p class="text-sm text-gray-500">Never</p>
                            @endif
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Account Status</label>
                            <p class="text-sm font-semibold text-gray-900">{{ ucfirst($stats['account_status'] ?? 'Unknown') }}</p>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No role-specific data available</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="medical-card p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button onclick="toggleUserStatus('{{ $user->id }}')" class="w-full btn-secondary text-left">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        {{ $user->status === 'active' ? 'Deactivate User' : 'Activate User' }}
                    </button>
                    
                    <button onclick="resetPassword('{{ $user->id }}')" class="w-full btn-secondary text-left">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v-2L3.257 9.257a6 6 0 018.486-8.486L17 6"></path>
                        </svg>
                        Reset Password
                    </button>
                    
                    @if($user->role === 'doctor')
                        <button onclick="verifyDoctor('{{ $user->id }}', '{{ $stats['verification_status'] ?? 'pending' }}')" class="w-full btn-secondary text-left">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @if(isset($stats['verification_status']) && $stats['verification_status'] === 'verified')
                                Unverify Doctor
                            @else
                                Verify Doctor
                            @endif
                        </button>
                    @endif
                    
                    <button onclick="confirmDeleteUser('{{ $user->id }}')" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium text-left">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete User
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="medical-card">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Recent Activities</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4" style="max-height: 400px; overflow-y: auto;">
                    @forelse($recentActivities ?? [] as $activity)
                        <div class="flex items-start space-x-3 py-3 border-b border-gray-100 last:border-0">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $activity->description ?? 'Activity recorded' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $activity->log_name ?? 'system' }} • {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p>No recent activities found</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Edit User</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <form id="editUserForm" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" id="editName" class="form-input" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="editEmail" class="form-input" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" id="editPhone" class="form-input">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="editRole" class="form-input">
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="editStatus" class="form-input">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeEditModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Global variables
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let currentEditUserId = null;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    setupToastNotifications();
});

// Edit User Functions
function editUser(userId) {
    currentEditUserId = userId;
    
    // Fetch current user data
    fetch(`/admin/users/${userId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(user => {
        // Populate form fields
        document.getElementById('editName').value = user.name || '';
        document.getElementById('editEmail').value = user.email || '';
        document.getElementById('editPhone').value = user.phone || '';
        document.getElementById('editRole').value = user.role || 'patient';
        document.getElementById('editStatus').value = user.status || 'active';
        
        // Show modal
        document.getElementById('editUserModal').classList.remove('hidden');
    })
    .catch(error => {
        console.error('Error fetching user data:', error);
        showToast('Error loading user data', 'error');
    });
}

function closeEditModal() {
    document.getElementById('editUserModal').classList.add('hidden');
    currentEditUserId = null;
}

// Handle edit form submission
document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!currentEditUserId) return;
    
    const formData = {
        name: document.getElementById('editName').value,
        email: document.getElementById('editEmail').value,
        phone: document.getElementById('editPhone').value,
        role: document.getElementById('editRole').value,
        status: document.getElementById('editStatus').value
    };
    
    fetch(`/admin/users/${currentEditUserId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('User updated successfully', 'success');
            closeEditModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Error updating user', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating user', 'error');
    });
});

// Quick Action Functions
function verifyDoctor(userId, currentStatus = 'pending') {
    const action = currentStatus === 'verified' ? 'unverify' : 'verify';
    const message = `Are you sure you want to ${action} this doctor?`;
    
    if (!confirm(message)) return;
    
    fetch(`/admin/users/${userId}/verify-doctor`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`Doctor ${data.new_status} successfully`, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Error verifying doctor', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error verifying doctor', 'error');
    });
}

function toggleUserStatus(userId) {
    if (!confirm('Are you sure you want to change the user status?')) return;
    
    fetch(`/admin/users/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('User status updated successfully', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Error updating status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating user status', 'error');
    });
}

function resetPassword(userId) {
    if (!confirm('Are you sure you want to reset this user\'s password? They will receive a new temporary password.')) return;
    
    fetch(`/admin/users/${userId}/reset-password`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Password reset successfully', 'success');
            if (data.temporary_password) {
                // Show temporary password in a more secure way
                showToast(`Temporary password: ${data.temporary_password}`, 'info', 10000);
            }
        } else {
            showToast(data.message || 'Error resetting password', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error resetting password', 'error');
    });
}

function confirmDeleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;
    
    fetch(`/admin/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('User deleted successfully', 'success');
            setTimeout(() => window.location.href = '/admin/users', 1500);
        } else {
            showToast(data.message || 'Error deleting user', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error deleting user', 'error');
    });
}

// Toast notification utility functions
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
            <div class="flex-shrink-0">
                ${icon[type] || icon.info}
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <button onclick="removeToast('${toastId}')" class="ml-4 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        removeToast(toastId);
    }, duration);
}

function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => toast.remove(), 300);
    }
}
</script>
@endpush
@endsection
