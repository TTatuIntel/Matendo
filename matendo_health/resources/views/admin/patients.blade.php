@extends('admin.layout')

@section('title', 'Patient Management')

@section('content')
<div class="max-w-6xl mx-auto px-2 sm:px-4 lg:px-6">
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="medical-card p-4 sm:p-6 fade-in">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">Patient Management System</h1>
                        <p class="text-sm text-gray-600 font-medium">Monitor patient accounts, health records, and doctor assignments across the platform</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 lg:gap-3">
                    <button onclick="exportPatients()" class="btn-secondary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        Export Patients
                    </button>
                    <button onclick="showCreatePatientModal()" class="btn-primary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New Patient
                    </button>
                </div>
            </div>
        </div>

        <!-- Patient Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $totalPatients ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Patients</div>
            </div>
            
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $activePatients ?? 0 }}</div>
                <div class="text-sm text-gray-600">Active</div>
            </div>
            
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0l-7.918 8.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $criticalPatients ?? 0 }}</div>
                <div class="text-sm text-gray-600">Critical</div>
            </div>
            
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $vitalRecordsToday ?? 0 }}</div>
                <div class="text-sm text-gray-600">Vitals Today</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="medical-card p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <input type="text" id="searchFilter" placeholder="Search patients by name or email..." class="form-input">
                </div>
                <select id="statusFilter" class="form-input">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="critical">Critical</option>
                    <option value="high">High Risk</option>
                    <option value="moderate">Moderate Risk</option>
                    <option value="normal">Normal</option>
                </select>
                <select id="doctorFilter" class="form-input">
                    <option value="">All Doctors</option>
                    @if(isset($doctors))
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">Dr. {{ $doctor->user->name }}</option>
                        @endforeach
                    @endif
                </select>
                <button onclick="clearFilters()" class="btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Clear
                </button>
            </div>
        </div>

        <!-- Patients Table -->
        <div class="medical-card">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">All Patients</h3>
                    <div class="text-sm text-gray-600">
                        Showing {{ $patients->firstItem() ?? 0 }} to {{ $patients->lastItem() ?? 0 }} of {{ $patients->total() ?? 0 }} patients
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full" id="patientsTable">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Patient</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Email</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Doctor</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Latest Vitals</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Risk Level</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Last Activity</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients ?? [] as $patient)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($patient->user->name ?? 'P', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $patient->user->name ?? 'Unknown Patient' }}</div>
                                        <div class="text-xs text-gray-500">ID: #{{ $patient->id }} • {{ $patient->date_of_birth ? now()->diffInYears($patient->date_of_birth) : 'N/A' }}y {{ $patient->gender ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">{{ $patient->user->email ?? 'No email' }}</div>
                                <div class="text-xs text-gray-500">{{ $patient->phone ?? 'No phone' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @if($patient->doctors->count() > 0)
                                    <div class="text-sm text-gray-900">Dr. {{ $patient->doctors->first()->user->name }}</div>
                                    @if($patient->doctors->count() > 1)
                                        <div class="text-xs text-gray-500">+{{ $patient->doctors->count() - 1 }} more</div>
                                    @endif
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">
                                        Unassigned
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($patient->latestVitals)
                                    <div class="text-sm text-gray-900">
                                        {{ $patient->latestVitals->blood_pressure ?? '--' }} | 
                                        {{ $patient->latestVitals->heart_rate ?? '--' }}bpm
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        @if($patient->latestVitals->measured_at instanceof \Carbon\Carbon)
                                            {{ $patient->latestVitals->measured_at->diffForHumans() }}
                                        @else
                                            {{ $patient->latestVitals->measured_at ?? 'Unknown' }}
                                        @endif
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">No data</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $riskLevel = $patient->risk_level ?? 'normal';
                                    $riskClass = match($riskLevel) {
                                        'critical' => 'bg-red-100 text-red-700',
                                        'high' => 'bg-orange-100 text-orange-700',
                                        'moderate' => 'bg-yellow-100 text-yellow-700',
                                        'normal' => 'bg-green-100 text-green-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $riskClass }}">
                                    {{ ucfirst($riskLevel) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $lastActivity = $patient->last_activity ?? $patient->updated_at ?? null;
                                @endphp
                                <div class="text-sm text-gray-900">
                                    @if($lastActivity instanceof \Carbon\Carbon)
                                        {{ $lastActivity->diffForHumans() }}
                                    @elseif($lastActivity)
                                        {{ $lastActivity }}
                                    @else
                                        Never
                                    @endif
                                </div>
                                @if($patient->user && $patient->user->last_login_at)
                                    <div class="text-xs text-gray-500">
                                        Login: 
                                        @if($patient->user->last_login_at instanceof \Carbon\Carbon)
                                            {{ $patient->user->last_login_at->diffForHumans() }}
                                        @else
                                            {{ $patient->user->last_login_at }}
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-2">
                                    <button onclick="showPatientDetails('{{ $patient->id }}')" class="text-blue-600 hover:text-blue-800" title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="assignDoctor('{{ $patient->id }}')" class="text-green-600 hover:text-green-800" title="Assign Doctor">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                    <button onclick="editPatient('{{ $patient->id }}')" class="text-purple-600 hover:text-purple-800" title="Edit Patient">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="togglePatientStatus('{{ $patient->user->id }}')" class="text-yellow-600 hover:text-yellow-800" title="Toggle Status">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="confirmDeletePatient('{{ $patient->id }}')" class="text-red-600 hover:text-red-800" title="Delete Patient">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                No patients found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($patients) && $patients->hasPages())
            <div class="p-6 border-t border-gray-200">
                {{ $patients->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Patient Details Modal -->
<div id="patientDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="medical-card p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">Patient Details</h3>
            <button onclick="closePatientDetailsModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="patientDetailsContent" class="space-y-6">
            <!-- Patient details will be loaded here -->
            <div class="text-center py-8">
                <div class="spinner mx-auto mb-4"></div>
                <p class="text-gray-600">Loading patient details...</p>
            </div>
        </div>
    </div>
</div>

<!-- Assign Doctor Modal -->
<div id="assignDoctorModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="medical-card p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Assign Doctor</h3>
        <form id="assignDoctorForm">
            @csrf
            <input type="hidden" id="patientId">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Doctor</label>
                <select id="doctorSelect" class="form-input" required>
                    <option value="">Choose a doctor...</option>
                    @if(isset($doctors))
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">Dr. {{ $doctor->user->name }} - {{ $doctor->specialization }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Relationship Type</label>
                <select id="relationshipType" name="relationship_type" class="form-input" required>
                    <option value="primary">Primary Doctor</option>
                    <option value="secondary">Secondary Doctor</option>
                    <option value="consulting">Consulting Doctor</option>
                    <option value="specialist">Specialist</option>
                    <option value="referral">Referral</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea id="assignmentNotes" name="notes" class="form-input" rows="3" placeholder="Any additional notes about this assignment..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeAssignModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Assign Doctor</button>
            </div>
        </form>
    </div>
</div>

<!-- Create/Edit Patient Modal -->
<div id="patientModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="medical-card p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-900" id="patientModalTitle">Add New Patient</h3>
            <button onclick="closePatientModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="patientForm">
            @csrf
            <input type="hidden" id="patientEditId" name="patient_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                    <input type="text" id="patientName" name="name" class="form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" id="patientEmail" name="email" class="form-input" required>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" id="patientPhone" name="phone" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                    <input type="date" id="patientDOB" name="date_of_birth" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select id="patientGender" name="gender" class="form-input">
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea id="patientAddress" name="address" class="form-input" rows="2" placeholder="Patient's address..."></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact</label>
                    <input type="text" id="emergencyContact" name="emergency_contact" class="form-input" placeholder="Contact person name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Phone</label>
                    <input type="tel" id="emergencyPhone" name="emergency_phone" class="form-input" placeholder="Emergency contact phone">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Blood Type</label>
                    <select id="bloodType" name="blood_type" class="form-input">
                        <option value="">Select blood type</option>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Insurance Number</label>
                    <input type="text" id="insuranceNumber" name="insurance_number" class="form-input" placeholder="Insurance policy number">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Known Allergies</label>
                <textarea id="allergies" name="allergies" class="form-input" rows="2" placeholder="List any known allergies..."></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Chronic Conditions</label>
                <textarea id="chronicConditions" name="chronic_conditions" class="form-input" rows="2" placeholder="List any chronic medical conditions..."></textarea>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" id="patientPassword" name="password" class="form-input" placeholder="Leave blank to keep current password (for editing)">
                <p class="text-xs text-gray-500 mt-1">Minimum 8 characters for new patients</p>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closePatientModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save Patient</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deletePatientModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="medical-card p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Confirm Delete</h3>
        <p class="text-gray-600 mb-6">Are you sure you want to delete this patient? This action cannot be undone and will remove all associated medical records and data.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeletePatientModal()" class="btn-secondary">Cancel</button>
            <button onclick="deletePatient()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">Delete Patient</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPatientId = null;
let deletePatientId = null;

// Set up CSRF token and initialize on page load
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.addEventListener('DOMContentLoaded', function() {
    setupFilters();
    setupToastNotifications();
});

// Show patient details modal
function showPatientDetails(patientId) {
    document.getElementById('patientDetailsModal').classList.remove('hidden');
    document.getElementById('patientDetailsModal').classList.add('flex');
    
    const content = document.getElementById('patientDetailsContent');
    content.innerHTML = `
        <div class="text-center py-8">
            <div class="spinner mx-auto mb-4"></div>
            <p class="text-gray-600">Loading patient details...</p>
        </div>
    `;
    
    fetch(`/admin/patients/${patientId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayPatientDetails(data.patient);
        } else {
            content.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-600">Error loading patient details</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        content.innerHTML = `
            <div class="text-center py-8">
                <p class="text-red-600">Error loading patient details</p>
            </div>
        `;
    });
}

function displayPatientDetails(patient) {
    const content = document.getElementById('patientDetailsContent');
    content.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="medical-card p-4">
                <h4 class="font-bold text-gray-900 mb-3">Patient Information</h4>
                <div class="space-y-2 text-sm">
                    <div><span class="font-medium">Name:</span> ${patient.user?.name || 'N/A'}</div>
                    <div><span class="font-medium">Email:</span> ${patient.user?.email || 'N/A'}</div>
                    <div><span class="font-medium">Phone:</span> ${patient.phone || 'N/A'}</div>
                    <div><span class="font-medium">Age:</span> ${patient.age || 'N/A'}</div>
                    <div><span class="font-medium">Gender:</span> ${patient.gender ? patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1) : 'N/A'}</div>
                    <div><span class="font-medium">Blood Type:</span> ${patient.blood_type || 'N/A'}</div>
                    <div><span class="font-medium">Status:</span> 
                        <span class="px-2 py-1 text-xs rounded-full ${patient.user?.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'}">
                            ${patient.user?.status ? patient.user.status.charAt(0).toUpperCase() + patient.user.status.slice(1) : 'N/A'}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="medical-card p-4">
                <h4 class="font-bold text-gray-900 mb-3">Medical Information</h4>
                <div class="space-y-2 text-sm">
                    <div><span class="font-medium">Medical Record #:</span> ${patient.medical_record_number || 'N/A'}</div>
                    <div><span class="font-medium">Emergency Contact:</span> ${patient.emergency_contact || 'N/A'}</div>
                    <div><span class="font-medium">Emergency Phone:</span> ${patient.emergency_phone || 'N/A'}</div>
                    <div><span class="font-medium">Insurance:</span> ${patient.insurance_number || 'N/A'}</div>
                    <div><span class="font-medium">Allergies:</span> ${patient.allergies || 'None listed'}</div>
                    <div><span class="font-medium">Chronic Conditions:</span> ${patient.chronic_conditions || 'None listed'}</div>
                </div>
            </div>
        </div>
        
        <div class="medical-card p-4">
            <h4 class="font-bold text-gray-900 mb-3">Assigned Doctors</h4>
            ${patient.doctors && patient.doctors.length > 0 ? 
                `<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    ${patient.doctors.map(doctor => `
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold">
                                ${doctor.user?.name?.charAt(0) || 'D'}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Dr. ${doctor.user?.name || 'Unknown'}</div>
                                <div class="text-sm text-gray-600">${doctor.specialization || 'General'}</div>
                            </div>
                        </div>
                    `).join('')}
                </div>` : 
                '<p class="text-gray-600">No doctors assigned</p>'
            }
        </div>
        
        ${patient.vital_signs && patient.vital_signs.length > 0 ? `
            <div class="medical-card p-4">
                <h4 class="font-bold text-gray-900 mb-3">Recent Vital Signs</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Date</th>
                                <th class="text-left py-2">Blood Pressure</th>
                                <th class="text-left py-2">Heart Rate</th>
                                <th class="text-left py-2">Temperature</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${patient.vital_signs.slice(0, 5).map(vital => `
                                <tr class="border-b">
                                    <td class="py-2">${new Date(vital.measured_at).toLocaleDateString()}</td>
                                    <td class="py-2">${vital.blood_pressure || '--'}</td>
                                    <td class="py-2">${vital.heart_rate || '--'} bpm</td>
                                    <td class="py-2">${vital.temperature || '--'}°C</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        ` : ''}
    `;
}

function closePatientDetailsModal() {
    document.getElementById('patientDetailsModal').classList.add('hidden');
    document.getElementById('patientDetailsModal').classList.remove('flex');
}

// Show create patient modal
function showCreatePatientModal() {
    document.getElementById('patientModalTitle').textContent = 'Add New Patient';
    document.getElementById('patientForm').reset();
    document.getElementById('patientEditId').value = '';
    document.getElementById('patientModal').classList.remove('hidden');
    document.getElementById('patientModal').classList.add('flex');
}

// Edit patient
function editPatient(patientId) {
    fetch(`/admin/patients/${patientId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const patient = data.patient;
            document.getElementById('patientModalTitle').textContent = 'Edit Patient';
            document.getElementById('patientEditId').value = patient.id;
            document.getElementById('patientName').value = patient.user?.name || '';
            document.getElementById('patientEmail').value = patient.user?.email || '';
            document.getElementById('patientPhone').value = patient.phone || '';
            document.getElementById('patientDOB').value = patient.date_of_birth || '';
            document.getElementById('patientGender').value = patient.gender || '';
            document.getElementById('patientAddress').value = patient.address || '';
            document.getElementById('emergencyContact').value = patient.emergency_contact || '';
            document.getElementById('emergencyPhone').value = patient.emergency_phone || '';
            document.getElementById('bloodType').value = patient.blood_type || '';
            document.getElementById('insuranceNumber').value = patient.insurance_number || '';
            document.getElementById('allergies').value = patient.allergies || '';
            document.getElementById('chronicConditions').value = patient.chronic_conditions || '';
            document.getElementById('patientModal').classList.remove('hidden');
            document.getElementById('patientModal').classList.add('flex');
        } else {
            showToast('Error loading patient data', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error loading patient data', 'error');
    });
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
    document.getElementById('patientModal').classList.remove('flex');
}

// Assign doctor functions
function assignDoctor(patientId) {
    currentPatientId = patientId;
    document.getElementById('patientId').value = patientId;
    document.getElementById('assignDoctorModal').classList.remove('hidden');
    document.getElementById('assignDoctorModal').classList.add('flex');
}

function closeAssignModal() {
    document.getElementById('assignDoctorModal').classList.add('hidden');
    document.getElementById('assignDoctorModal').classList.remove('flex');
    currentPatientId = null;
    document.getElementById('assignDoctorForm').reset();
}

document.getElementById('assignDoctorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const doctorId = document.getElementById('doctorSelect').value;
    const relationshipType = document.getElementById('relationshipType').value;
    const notes = document.getElementById('assignmentNotes').value;
    
    if (!doctorId || !currentPatientId || !relationshipType) {
        showToast('Please fill in all required fields', 'error');
        return;
    }
    
    fetch(`/admin/patients/${currentPatientId}/assign-doctor`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            doctor_id: doctorId, 
            relationship_type: relationshipType,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Doctor assigned successfully', 'success');
            closeAssignModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Error assigning doctor', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error assigning doctor', 'error');
    });
});

// Patient form submission
document.getElementById('patientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitButton = this.querySelector('button[type="submit"]');
    showLoading(submitButton);
    
    const formData = new FormData(this);
    const patientId = document.getElementById('patientEditId').value;
    const isEdit = patientId !== '';
    const url = isEdit ? `/admin/patients/${patientId}/update-profile` : '/admin/users';
    const method = 'POST';
    
    if (!isEdit) {
        formData.append('role', 'patient');
    }
    
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
            showToast(`Patient ${isEdit ? 'updated' : 'created'} successfully`, 'success');
            closePatientModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || `Error ${isEdit ? 'updating' : 'creating'} patient`, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast(`Error ${isEdit ? 'updating' : 'creating'} patient`, 'error');
    })
    .finally(() => {
        hideLoading(submitButton);
    });
});

// Delete functions
function confirmDeletePatient(patientId) {
    deletePatientId = patientId;
    document.getElementById('deletePatientModal').classList.remove('hidden');
    document.getElementById('deletePatientModal').classList.add('flex');
}

function closeDeletePatientModal() {
    document.getElementById('deletePatientModal').classList.add('hidden');
    document.getElementById('deletePatientModal').classList.remove('flex');
    deletePatientId = null;
}

function deletePatient() {
    if (!deletePatientId) return;
    
    fetch(`/admin/patients/${deletePatientId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Patient deleted successfully', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Error deleting patient', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error deleting patient', 'error');
    });
    
    closeDeletePatientModal();
}

// Toggle patient status
function togglePatientStatus(userId) {
    const button = event.target.closest('button');
    showLoading(button);
    
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
            showToast('Patient status updated successfully', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Error updating status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating patient status', 'error');
    })
    .finally(() => {
        hideLoading(button);
    });
}

// Export patients
function exportPatients() {
    showToast('Preparing export...', 'info');
    
    const filters = new URLSearchParams();
    const search = document.getElementById('searchFilter')?.value;
    const status = document.getElementById('statusFilter')?.value;
    const doctor = document.getElementById('doctorFilter')?.value;
    
    if (search) filters.append('search', search);
    if (status) filters.append('status', status);
    if (doctor) filters.append('doctor_id', doctor);
    
    const url = `/admin/patients/export${filters.toString() ? '?' + filters.toString() : ''}`;
    window.location.href = url;
}

// Filter functionality
function setupFilters() {
    const searchInput = document.getElementById('searchFilter');
    const statusFilter = document.getElementById('statusFilter');
    const doctorFilter = document.getElementById('doctorFilter');
    
    // Live search with debounce
    let searchTimeout;
    searchInput?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterTable, 300);
    });
    
    statusFilter?.addEventListener('change', filterTable);
    doctorFilter?.addEventListener('change', filterTable);
}

function filterTable() {
    const searchTerm = document.getElementById('searchFilter').value.toLowerCase();
    const statusValue = document.getElementById('statusFilter').value;
    const doctorValue = document.getElementById('doctorFilter').value;
    const rows = document.querySelectorAll('#patientsTable tbody tr');
    
    let visibleCount = 0;
    
    rows.forEach(row => {
        if (row.cells.length === 1) return; // Skip empty state row
        
        const name = row.cells[0].textContent.toLowerCase();
        const email = row.cells[1].textContent.toLowerCase();
        const doctor = row.cells[2].textContent.toLowerCase();
        const riskLevel = row.cells[4].textContent.toLowerCase().trim();
        
        const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
        const matchesStatus = !statusValue || riskLevel.includes(statusValue);
        const matchesDoctor = !doctorValue || doctor.includes('dr.');
        
        if (matchesSearch && matchesStatus && matchesDoctor) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    if (visibleCount === 0) {
        showToast('No patients found matching your criteria', 'info');
    }
}

function clearFilters() {
    document.getElementById('searchFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('doctorFilter').value = '';
    filterTable();
    showToast('Filters cleared', 'info');
}

// Utility Functions
function showLoading(button) {
    if (!button) return;
    button.disabled = true;
    const originalText = button.innerHTML;
    button.setAttribute('data-original-text', originalText);
    button.innerHTML = `<div class="spinner inline-block mr-2"></div>Loading...`;
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
        document.getElementById('patientDetailsModal'),
        document.getElementById('assignDoctorModal'),
        document.getElementById('patientModal'),
        document.getElementById('deletePatientModal')
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
        closePatientDetailsModal();
        closeAssignModal();
        closePatientModal();
        closeDeletePatientModal();
    }
    
    if ((event.ctrlKey || event.metaKey) && event.key === 'n') {
        event.preventDefault();
        showCreatePatientModal();
    }
});
</script>
@endpush
