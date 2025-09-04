@extends('admin.layout')

@section('title', 'User Management')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
            <p class="text-gray-600 mt-2">Manage system users, roles, and permissions</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="exportUsers()" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                </svg>
                Export Users
            </button>
            <button onclick="showCreateModal()" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add New User
            </button>
        </div>
    </div>

        <!-- User Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m-9 5.197v1a6 6 0 0010.967 0M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $users->total() ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Users</div>
            </div>
            
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $doctorsCount ?? 0 }}</div>
                <div class="text-sm text-gray-600">Doctors</div>
            </div>
            
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $patientsCount ?? 0 }}</div>
                <div class="text-sm text-gray-600">Patients</div>
            </div>
            
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $adminsCount ?? 0 }}</div>
                <div class="text-sm text-gray-600">Admins</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="medical-card p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <input type="text" id="searchFilter" placeholder="Search users by name or email..." class="form-input">
                </div>
                <select id="roleFilter" class="form-input">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="doctor">Doctor</option>
                    <option value="patient">Patient</option>
                </select>
                <select id="statusFilter" class="form-input">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <button onclick="filterUsers()" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filter
                </button>
            </div>
        </div>

        <!-- Users Table -->
        <div class="medical-card">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">All Users</h3>
                    <div class="text-sm text-gray-600">
                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() ?? 0 }} users
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full" id="usersTable">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">User</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Role</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Status</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Last Login</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Created</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users ?? [] as $user)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-3">
                                    @php
                                        $avatarClass = match($user->role ?? 'patient') {
                                            'admin' => 'from-red-500 to-pink-600',
                                            'doctor' => 'from-green-500 to-emerald-600',
                                            'patient' => 'from-blue-500 to-indigo-600',
                                            default => 'from-gray-500 to-gray-600'
                                        };
                                    @endphp
                                    <div class="w-10 h-10 bg-gradient-to-r {{ $avatarClass }} rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $user->name ?? 'Unknown User' }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email ?? 'No email' }}</div>
                                        @if($user->phone)
                                            <div class="text-xs text-gray-400">{{ $user->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $roleClass = match($user->role ?? 'patient') {
                                        'admin' => 'bg-red-100 text-red-700',
                                        'doctor' => 'bg-green-100 text-green-700',
                                        'patient' => 'bg-blue-100 text-blue-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $roleClass }}">
                                    {{ ucfirst($user->role ?? 'patient') }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $status = $user->status ?? 'active';
                                    $statusClass = match($status) {
                                        'active' => 'bg-green-100 text-green-700',
                                        'inactive' => 'bg-gray-100 text-gray-700',
                                        'suspended' => 'bg-red-100 text-red-700',
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($user->last_activity)
                                    <div class="text-sm text-gray-900">{{ $user->last_activity->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->last_activity->diffForHumans() }}</div>
                                @else
                                    <div class="text-sm text-gray-500">Never</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-2">
                                    <button onclick="viewUser('{{ $user->id }}')" class="text-blue-600 hover:text-blue-800" title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="editUser('{{ $user->id }}')" class="text-green-600 hover:text-green-800" title="Edit User">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="toggleUserStatus('{{ $user->id }}')" class="text-yellow-600 hover:text-yellow-800" title="Toggle Status">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="confirmDeleteUser('{{ $user->id }}')" class="text-red-600 hover:text-red-800" title="Delete User">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m-9 5.197v1a6 6 0 0010.967 0M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                No users found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(isset($users) && $users->hasPages())
            <div class="p-6 border-t border-gray-200">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Create/Edit User Modal -->
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="medical-card p-6 max-w-lg w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-6" id="modalTitle">Add New User</h3>
        <form id="userForm">
            @csrf
            <input type="hidden" id="userId" name="user_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="userName" name="name" class="form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="userEmail" name="email" class="form-input" required>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select id="userRole" name="role" class="form-input" required>
                        <option value="patient">Patient</option>
                        <option value="doctor">Doctor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="userStatus" name="status" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number (Optional)</label>
                <input type="tel" id="userPhone" name="phone" class="form-input">
            </div>
            
            <!-- Role-specific fields -->
            <div id="doctorFields" class="role-specific-fields hidden mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
                        <input type="text" id="doctorSpecialization" name="specialization" class="form-input" placeholder="e.g., Cardiology, Pediatrics">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Years of Experience</label>
                        <input type="number" id="doctorExperience" name="years_experience" class="form-input" min="0" max="50">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">License Number</label>
                    <input type="text" id="doctorLicense" name="license_number" class="form-input" placeholder="Medical license number">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Qualifications</label>
                    <textarea id="doctorQualifications" name="qualifications" class="form-input" rows="2" placeholder="Educational background and certifications"></textarea>
                </div>
            </div>
            
            <div id="patientFields" class="role-specific-fields hidden mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" id="patientDOB" name="date_of_birth" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select id="patientGender" name="gender" class="form-input">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Blood Type</label>
                        <select id="patientBloodType" name="blood_type" class="form-input">
                            <option value="">Select Blood Type</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact</label>
                        <input type="text" id="patientEmergencyContact" name="emergency_contact" class="form-input" placeholder="Emergency contact name">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Phone</label>
                    <input type="tel" id="patientEmergencyPhone" name="emergency_phone" class="form-input" placeholder="Emergency contact phone">
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" id="userPassword" name="password" class="form-input">
                <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password (for editing)</p>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save User</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="medical-card p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Confirm Delete</h3>
        <p class="text-gray-600 mb-6">Are you sure you want to delete this user? This action cannot be undone and will remove all associated data.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" class="btn-secondary">Cancel</button>
            <button onclick="deleteUser()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">Delete</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let deleteId = null;

// Set up CSRF token
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupFilters();
    setupToastNotifications();
    setupRoleSpecificFields();
});

// Show create user modal
function showCreateModal() {
    document.getElementById('modalTitle').textContent = 'Add New User';
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    clearRoleSpecificFields();
    showRoleSpecificFields('patient'); // Default to patient role
    document.getElementById('userModal').classList.remove('hidden');
    document.getElementById('userModal').classList.add('flex');
}

// Edit user
function editUser(userId) {
    const button = event.target.closest('button');
    showLoading(button);
    
    fetch(`/admin/users/${userId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(user => {
        if (user.error) {
            showToast('Error loading user data', 'error');
            return;
        }
        
        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('userId').value = user.id;
        document.getElementById('userName').value = user.name || '';
        document.getElementById('userEmail').value = user.email || '';
        document.getElementById('userRole').value = user.role || 'patient';
        document.getElementById('userStatus').value = user.status || 'active';
        document.getElementById('userPhone').value = user.phone || '';
        document.getElementById('userModal').classList.remove('hidden');
        document.getElementById('userModal').classList.add('flex');
    })
    .catch(error => {
        console.error('Error loading user:', error);
        showToast('Error loading user data', 'error');
    })
    .finally(() => {
        hideLoading(button);
    });
}

// View user details
function viewUser(userId) {
    // Redirect to user detail page or show detailed modal
    window.location.href = `/admin/users/${userId}`;
}

// Close modal
function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
    document.getElementById('userModal').classList.remove('flex');
}

// Confirm delete user
function confirmDeleteUser(userId) {
    deleteId = userId;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

// Close delete modal
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
    deleteId = null;
}

// Delete user
function deleteUser() {
    if (!deleteId) return;
    
    fetch(`/admin/users/${deleteId}`, {
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
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Error deleting user', 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting user:', error);
        showToast('Error deleting user', 'error');
    });
    
    closeDeleteModal();
}

// Toggle user status
function toggleUserStatus(userId) {
    const button = event.target.closest('button');
    showLoading(button);
    
    fetch(`/admin/users/${userId}/toggle-status`, {
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
            showToast('User status updated successfully', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Error updating status', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating status:', error);
        showToast('Error updating user status', 'error');
    })
    .finally(() => {
        hideLoading(button);
    });
}

// Export users
function exportUsers() {
    showToast('Preparing export...', 'info');
    
    // Get current filters to apply to export
    const filters = new URLSearchParams();
    
    const search = document.getElementById('searchFilter')?.value;
    const role = document.getElementById('roleFilter')?.value;
    const status = document.getElementById('statusFilter')?.value;
    
    if (search) filters.append('search', search);
    if (role) filters.append('role', role);
    if (status) filters.append('status', status);
    
    const url = `/admin/users/export${filters.toString() ? '?' + filters.toString() : ''}`;
    window.location.href = url;
}

// Filter users
function filterUsers() {
    const searchTerm = document.getElementById('searchFilter').value.toLowerCase();
    const roleValue = document.getElementById('roleFilter').value;
    const statusValue = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('#usersTable tbody tr');
    
    let visibleCount = 0;
    
    rows.forEach(row => {
        if (row.cells.length === 1) return; // Skip empty state row
        
        const name = row.cells[0].textContent.toLowerCase();
        const email = row.cells[0].textContent.toLowerCase();
        const role = row.cells[1].textContent.toLowerCase().trim();
        const status = row.cells[2].textContent.toLowerCase().trim();
        
        const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
        const matchesRole = !roleValue || role.includes(roleValue);
        const matchesStatus = !statusValue || status.includes(statusValue);
        
        if (matchesSearch && matchesRole && matchesStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show message if no results
    if (visibleCount === 0) {
        showToast(`No users found matching your criteria`, 'info');
    }
}

// Setup live filters
function setupFilters() {
    const searchInput = document.getElementById('searchFilter');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    // Live search
    let searchTimeout;
    searchInput?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterUsers, 300);
    });
    
    // Filter changes
    roleFilter?.addEventListener('change', filterUsers);
    statusFilter?.addEventListener('change', filterUsers);
}

// Form submission
document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitButton = this.querySelector('button[type="submit"]');
    showLoading(submitButton);
    
    const formData = new FormData(this);
    const userId = document.getElementById('userId').value;
    const isEdit = userId !== '';
    const url = isEdit ? `/admin/users/${userId}` : '/admin/users';
    const method = isEdit ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`User ${isEdit ? 'updated' : 'created'} successfully`, 'success');
            closeModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || `Error ${isEdit ? 'updating' : 'creating'} user`, 'error');
        }
    })
    .catch(error => {
        console.error('Error saving user:', error);
        showToast(`Error ${isEdit ? 'updating' : 'creating'} user`, 'error');
    })
    .finally(() => {
        hideLoading(submitButton);
    });
});

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const userModal = document.getElementById('userModal');
    const deleteModal = document.getElementById('deleteModal');
    
    if (event.target === userModal) {
        closeModal();
    }
    
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
});

// Utility Functions
function showLoading(button) {
    if (!button) return;
    button.disabled = true;
    const originalText = button.innerHTML;
    button.setAttribute('data-original-text', originalText);
    button.innerHTML = `
        <div class="spinner inline-block mr-2"></div>
        Loading...
    `;
}

function hideLoading(button) {
    if (!button) return;
    button.disabled = false;
    const originalText = button.getAttribute('data-original-text');
    if (originalText) {
        button.innerHTML = originalText;
    }
}

function setupToastNotifications() {
    // Create toast container if it doesn't exist
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
    
    // Auto remove after duration
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

// Setup role-specific fields
function setupRoleSpecificFields() {
    const roleSelect = document.getElementById('userRole');
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            showRoleSpecificFields(this.value);
        });
        
        // Initialize with current selection
        showRoleSpecificFields(roleSelect.value);
    }
}

// Show/hide role-specific fields based on selected role
function showRoleSpecificFields(role) {
    const doctorFields = document.getElementById('doctorFields');
    const patientFields = document.getElementById('patientFields');
    
    // Hide all role-specific fields first
    document.querySelectorAll('.role-specific-fields').forEach(field => {
        field.classList.add('hidden');
    });
    
    // Show appropriate fields based on role
    switch(role) {
        case 'doctor':
            if (doctorFields) {
                doctorFields.classList.remove('hidden');
            }
            break;
        case 'patient':
            if (patientFields) {
                patientFields.classList.remove('hidden');
            }
            break;
        case 'admin':
            // No specific fields for admin role
            break;
    }
}

// Clear role-specific fields
function clearRoleSpecificFields() {
    // Clear doctor fields
    const doctorFields = ['doctorSpecialization', 'doctorExperience', 'doctorLicense', 'doctorQualifications'];
    doctorFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) field.value = '';
    });
    
    // Clear patient fields
    const patientFields = ['patientDOB', 'patientGender', 'patientBloodType', 'patientEmergencyContact', 'patientEmergencyPhone'];
    patientFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) field.value = '';
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(event) {
    // ESC key closes modals
    if (event.key === 'Escape') {
        closeModal();
        closeDeleteModal();
    }
    
    // Ctrl/Cmd + N opens create modal
    if ((event.ctrlKey || event.metaKey) && event.key === 'n') {
        event.preventDefault();
        showCreateModal();
    }
});
</script>
@endpush
@endsection
