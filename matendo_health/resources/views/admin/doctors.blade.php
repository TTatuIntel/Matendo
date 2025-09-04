@extends('admin.layout')

@section('title', 'Doctor Management')

@section('content')
<div class="max-w-6xl mx-auto px-2 sm:px-4 lg:px-6">
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="medical-card p-4 sm:p-6 fade-in">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">Doctor Management System</h1>
                        <p class="text-sm text-gray-600 font-medium">Manage doctor accounts, verifications, and specializations across the platform</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 lg:gap-3">
                    <button onclick="exportDoctors()" class="btn-secondary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        Export Doctors
                    </button>
                    <button onclick="showCreateDoctorModal()" class="btn-primary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New Doctor
                    </button>
                </div>
            </div>
        </div>

        <!-- Doctor Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $totalDoctors ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Doctors</div>
            </div>
            
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $activeDoctors ?? 0 }}</div>
                <div class="text-sm text-gray-600">Active</div>
            </div>
            
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $pendingVerifications ?? 0 }}</div>
                <div class="text-sm text-gray-600">Pending</div>
            </div>
            
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $totalPatients ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Patients</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="medical-card p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <input type="text" id="searchFilter" placeholder="Search doctors by name or email..." class="form-input">
                </div>
                <select id="statusFilter" class="form-input">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                    <option value="suspended">Suspended</option>
                </select>
                <select id="specializationFilter" class="form-input">
                    <option value="">All Specializations</option>
                    <option value="cardiology">Cardiology</option>
                    <option value="neurology">Neurology</option>
                    <option value="orthopedics">Orthopedics</option>
                    <option value="general">General Practice</option>
                </select>
                <button onclick="clearFilters()" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Clear
                </button>
            </div>
        </div>

        <!-- Doctors Table -->
        <div class="medical-card">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">All Doctors</h3>
                    <div class="text-sm text-gray-600">
                        Showing {{ $doctors->firstItem() ?? 0 }} to {{ $doctors->lastItem() ?? 0 }} of {{ $doctors->total() ?? 0 }} doctors
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full" id="doctorsTable">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Doctor</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Email</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Specialization</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Patients</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Status</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Verification</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Joined</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($doctors ?? [] as $doctor)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($doctor->user->name ?? 'D', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $doctor->user->name ?? 'Dr. Unknown' }}</div>
                                        <div class="text-xs text-gray-500">ID: {{ Str::limit($doctor->id, 8, '...') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">{{ $doctor->user->email ?? 'No email' }}</div>
                                <div class="text-xs text-gray-500">{{ $doctor->license_number ?? 'No license' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                    {{ ucfirst($doctor->specialization ?? 'General') }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">{{ $doctor->patients_count ?? 0 }}</div>
                                <div class="text-xs text-gray-500">patients</div>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $status = $doctor->user->status ?? 'active';
                                    $statusClass = match($status) {
                                        'active' => 'bg-green-100 text-green-700',
                                        'inactive' => 'bg-gray-100 text-gray-700',
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'suspended' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $verificationStatus = $doctor->verification_status ?? 'pending';
                                    $verificationClass = match($verificationStatus) {
                                        'verified' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $verificationClass }}">
                                    {{ ucfirst($verificationStatus) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">
                                    {{ $doctor->created_at instanceof \Carbon\Carbon ? $doctor->created_at->format('M d, Y') : ($doctor->created_at ?? 'Unknown') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $doctor->created_at instanceof \Carbon\Carbon ? $doctor->created_at->diffForHumans() : 'Unknown' }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-2">
                                    <button onclick="viewDoctor('{{ $doctor->id }}')" class="text-blue-600 hover:text-blue-800" title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="editDoctor('{{ $doctor->id }}')" class="text-green-600 hover:text-green-800" title="Edit Doctor">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    @if($doctor->verification_status !== 'verified')
                                        <button onclick="verifyDoctor('{{ $doctor->id }}')" class="text-indigo-600 hover:text-indigo-800" title="Verify Doctor">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    <button onclick="toggleDoctorStatus('{{ $doctor->id }}')" class="text-yellow-600 hover:text-yellow-800" title="Toggle Status">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="confirmDelete('{{ $doctor->id }}')" class="text-red-600 hover:text-red-800" title="Delete Doctor">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                No doctors found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($doctors) && $doctors->hasPages())
            <div class="p-6 border-t border-gray-200">
                {{ $doctors->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Create/Edit Doctor Modal -->
<div id="doctorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900" id="doctorModalTitle">Add New Doctor</h3>
                <button onclick="closeDoctorModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <form id="doctorForm" class="p-6">
            <input type="hidden" id="doctorId" name="doctor_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="doctorName" name="name" class="form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="doctorEmail" name="email" class="form-input" required>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
                    <select id="doctorSpecialization" name="specialization" class="form-input" required>
                        <option value="">Select Specialization</option>
                        <option value="General Medicine">General Medicine</option>
                        <option value="Cardiology">Cardiology</option>
                        <option value="Neurology">Neurology</option>
                        <option value="Orthopedics">Orthopedics</option>
                        <option value="Pediatrics">Pediatrics</option>
                        <option value="Dermatology">Dermatology</option>
                        <option value="Emergency Medicine">Emergency Medicine</option>
                        <option value="Internal Medicine">Internal Medicine</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">License Number</label>
                    <input type="text" id="doctorLicense" name="license_number" class="form-input" placeholder="MD-123456">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" id="doctorPhone" name="phone" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Years of Experience</label>
                    <input type="number" id="doctorExperience" name="years_experience" class="form-input" min="0" max="50">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Qualifications</label>
                <textarea id="doctorQualifications" name="qualifications" class="form-input" rows="2" placeholder="e.g., MD, PhD, Fellowship in Cardiology"></textarea>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" id="doctorPassword" name="password" class="form-input" minlength="8">
                <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password (for editing)</p>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDoctorModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save Doctor</button>
            </div>
        </form>
    </div>
</div>

<!-- Doctor Details Modal -->
<div id="doctorDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Doctor Details</h3>
                <button onclick="closeDoctorDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div id="doctorDetailsContent" class="p-6">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Confirm Delete</h3>
                    <p class="text-sm text-gray-600">This action cannot be undone.</p>
                </div>
            </div>
            <p class="text-gray-600 mb-6">Are you sure you want to delete this doctor? All associated data will be removed.</p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()" class="btn-secondary">Cancel</button>
                <button onclick="deleteDoctor()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">Delete</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Global variables
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let deleteId = null;
let editingDoctorId = null;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    setupToastNotifications();
    setupRealTimeFilters();
});

// Modal Management
function showCreateDoctorModal() {
    document.getElementById('doctorModalTitle').textContent = 'Add New Doctor';
    document.getElementById('doctorForm').reset();
    document.getElementById('doctorId').value = '';
    editingDoctorId = null;
    document.getElementById('doctorModal').classList.remove('hidden');
}

function closeDoctorModal() {
    document.getElementById('doctorModal').classList.add('hidden');
    editingDoctorId = null;
}

function closeDoctorDetailsModal() {
    document.getElementById('doctorDetailsModal').classList.add('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    deleteId = null;
}

// Doctor CRUD Operations
function editDoctor(doctorId) {
    // Fetch doctor data
    fetch(`/admin/doctors/${doctorId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const doctor = data.doctor;
            
            // Set editing to the USER id for update endpoint
            editingDoctorId = doctor.user?.id || null;
            
            // Populate form
            document.getElementById('doctorModalTitle').textContent = 'Edit Doctor';
            document.getElementById('doctorId').value = doctor.id;
            document.getElementById('doctorName').value = doctor.user.name || '';
            document.getElementById('doctorEmail').value = doctor.user.email || '';
            document.getElementById('doctorSpecialization').value = doctor.specialization || '';
            document.getElementById('doctorLicense').value = doctor.license_number || '';
            document.getElementById('doctorPhone').value = doctor.user.phone || '';
            document.getElementById('doctorExperience').value = doctor.years_experience || '';
            document.getElementById('doctorQualifications').value = doctor.qualifications || '';
            
            // Show modal
            document.getElementById('doctorModal').classList.remove('hidden');
        } else {
            showToast('Error loading doctor data', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error loading doctor data', 'error');
    });
}

function viewDoctor(doctorId) {
    fetch(`/admin/doctors/${doctorId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const doctor = data.doctor;
            const stats = data.stats;
            
            const content = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                ${doctor.user.name.charAt(0)}
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900">${doctor.user.name}</h4>
                                <p class="text-gray-600">${doctor.specialization}</p>
                                <p class="text-sm text-gray-500">${doctor.license_number || 'No license'}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Email</label>
                                <p class="text-gray-900">${doctor.user.email}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Phone</label>
                                <p class="text-gray-900">${doctor.user.phone || 'Not provided'}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Experience</label>
                                <p class="text-gray-900">${doctor.years_experience || 0} years</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Verification Status</label>
                                <span class="px-2 py-1 text-xs font-medium rounded-full ${doctor.verification_status === 'verified' ? 'bg-green-100 text-green-700' : doctor.verification_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'}">
                                    ${doctor.verification_status}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <h5 class="font-bold text-gray-900">Statistics</h5>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">${stats.total_patients}</div>
                                <div class="text-sm text-gray-600">Total Patients</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">${stats.active_patients}</div>
                                <div class="text-sm text-gray-600">Active Patients</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">${stats.appointments_this_month}</div>
                                <div class="text-sm text-gray-600">Appointments</div>
                            </div>
                            <div class="text-center p-4 bg-orange-50 rounded-lg">
                                <div class="text-2xl font-bold text-orange-600">${stats.medical_records_created}</div>
                                <div class="text-sm text-gray-600">Records</div>
                            </div>
                        </div>
                        
                        ${doctor.qualifications ? `
                        <div>
                            <label class="text-sm font-medium text-gray-600">Qualifications</label>
                            <p class="text-gray-900">${doctor.qualifications}</p>
                        </div>
                        ` : ''}
                        
                        <div>
                            <label class="text-sm font-medium text-gray-600">Joined</label>
                            <p class="text-gray-900">${new Date(doctor.created_at).toLocaleDateString()}</p>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('doctorDetailsContent').innerHTML = content;
            document.getElementById('doctorDetailsModal').classList.remove('hidden');
        } else {
            showToast('Error loading doctor details', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error loading doctor details', 'error');
    });
}

// Handle form submission
document.getElementById('doctorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const isEditing = editingDoctorId !== null;
    const url = isEditing ? `/admin/users/${editingDoctorId}` : '/admin/users';
    const method = isEditing ? 'PUT' : 'POST';
    
    // Convert FormData to JSON for editing
    if (isEditing) {
        const jsonData = {};
        for (let [key, value] of formData.entries()) {
            if (key !== 'doctor_id' && value !== '') {
                jsonData[key] = value;
            }
        }
        jsonData.role = 'doctor'; // Ensure role is set
        
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Doctor updated successfully', 'success');
                closeDoctorModal();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || 'Error updating doctor', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error updating doctor', 'error');
        });
    } else {
        // For creating new doctor, we need to use the users endpoint
        const jsonData = {};
        for (let [key, value] of formData.entries()) {
            if (key !== 'doctor_id' && value !== '') {
                jsonData[key] = value;
            }
        }
        jsonData.role = 'doctor'; // Ensure role is set
        
        fetch('/admin/users', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Doctor created successfully', 'success');
                closeDoctorModal();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || 'Error creating doctor', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error creating doctor', 'error');
        });
    }
});

// Action Functions
function verifyDoctor(doctorId) {
    if (!confirm('Are you sure you want to verify this doctor?')) return;
    
    fetch(`/admin/doctors/${doctorId}/verify`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Doctor verified successfully', 'success');
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

function toggleDoctorStatus(doctorId) {
    if (!confirm('Are you sure you want to change the doctor status?')) return;
    
    fetch(`/admin/doctors/${doctorId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Status updated successfully', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Error updating status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating status', 'error');
    });
}

function confirmDelete(doctorId) {
    deleteId = doctorId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function deleteDoctor() {
    if (!deleteId) return;
    
    fetch(`/admin/doctors/${deleteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Doctor deleted successfully', 'success');
            closeDeleteModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Error deleting doctor', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error deleting doctor', 'error');
    });
}

function exportDoctors() {
    showToast('Exporting doctors...', 'info');
    window.location.href = '/admin/doctors/export';
}

// Filter Functions
function setupRealTimeFilters() {
    const searchInput = document.getElementById('searchFilter');
    const statusFilter = document.getElementById('statusFilter');
    const specializationFilter = document.getElementById('specializationFilter');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const specializationValue = specializationFilter.value;
        const rows = document.querySelectorAll('#doctorsTable tbody tr');
        
        let visibleCount = 0;
        
        rows.forEach(row => {
            if (row.cells.length === 1) return; // Skip empty state row
            
            const name = row.cells[0].textContent.toLowerCase();
            const email = row.cells[1].textContent.toLowerCase();
            const specialization = row.cells[2].textContent.toLowerCase();
            const status = row.cells[4].textContent.toLowerCase().trim();
            
            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesStatus = !statusValue || status.includes(statusValue);
            const matchesSpecialization = !specializationValue || specialization.includes(specializationValue);
            
            if (matchesSearch && matchesStatus && matchesSpecialization) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    specializationFilter.addEventListener('change', filterTable);
}

function clearFilters() {
    document.getElementById('searchFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('specializationFilter').value = '';
    
    // Show all rows
    const rows = document.querySelectorAll('#doctorsTable tbody tr');
    rows.forEach(row => row.style.display = '');
    
    showToast('Filters cleared', 'info');
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
