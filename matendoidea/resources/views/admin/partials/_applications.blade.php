<!-- resources/views/admin/partials/_applications.blade.php -->

<div class="p-6 space-y-8">
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 text-green-800 p-3 rounded-md text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 text-red-800 p-3 rounded-md text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Applications Overview Section -->
    <div>
        <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
            <span class="w-1.5 h-7 bg-green-600 rounded-full mr-3"></span>
            Applications Overview
        </h2>
        <div class="flex space-x-6 overflow-x-auto pb-4">
            <!-- Pending Review Card -->
            <div class="min-w-[280px] flex-shrink-0 p-6 bg-white rounded-lg shadow-sm card-hover smooth-transition border border-gray-100">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-yellow-700 text-base flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        Pending Review
                    </h3>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                        {{ $pendingApplications }}
                    </span>
                </div>
                <p class="text-gray-600 text-sm mb-2">Applications waiting for admin review and verification.</p>
                <div class="text-xs text-gray-600">
                    <p>Oldest pending: <span class="font-semibold">{{ $oldestPending }}</span></p>
                    <p>Average review time: <span class="font-semibold">{{ $avgReviewTime }}</span></p>
                </div>
            </div>

            <!-- Approved Card -->
            <div class="min-w-[280px] flex-shrink-0 p-6 bg-white rounded-lg shadow-sm card-hover smooth-transition border border-gray-100">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-green-700 text-base flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Approved
                    </h3>
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                        {{ $approvedApplications }}
                    </span>
                </div>
                <p class="text-gray-600 text-sm mb-2">Applications successfully verified and approved for platform access.</p>
                <div class="text-xs text-gray-600">
                    <p>New approvals this month: <span class="font-semibold">{{ $newApproved }}</span></p>
                    <p>Average onboarding time: <span class="font-semibold">{{ $avgOnboardingTime }}</span></p>
                </div>
            </div>

            <!-- Rejected Card -->
            <div class="min-w-[280px] flex-shrink-0 p-6 bg-white rounded-lg shadow-sm card-hover smooth-transition border border-gray-100">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-red-700 text-base flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Rejected
                    </h3>
                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                        {{ $rejectedApplications }}
                    </span>
                </div>
                <p class="text-gray-600 text-sm mb-2">Applications declined due to incomplete or invalid information.</p>
                <div class="text-xs text-gray-600">
                    <p>Common reasons: <span class="font-semibold">{{ $commonRejectionReasons }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Applications Section -->
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-900 flex items-center">
                <span class="w-1.5 h-7 bg-green-600 rounded-full mr-3"></span>
                Pending Applications
            </h2>
            <div class="flex space-x-3">
                <button class="btn-primary flex items-center" onclick="location.reload()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
                <button class="btn-secondary flex items-center" onclick="exportApplications()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export
                </button>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-700">Ref Number</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700">Name</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700">Profession</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700">Submitted</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($applications as $application)
                            <tr class="table-row smooth-transition cursor-pointer">
                                <td class="px-4 py-3 text-gray-900">{{ $application->reference_number }}</td>
                                <td class="px-4 py-3 text-gray-800">{{ $application->first_name }} {{ $application->last_name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $application->profession }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $application->created_at->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <button onclick="openModal('{{ $application->reference_number }}', '{{ $application->first_name }} {{ $application->last_name }}', '{{ $application->email }}', '{{ $application->phone }}', '{{ $application->address }}', '{{ $application->profession }}', '{{ $application->specialization ?? 'none' }}', '{{ $application->years_experience }} years', '{{ $application->start_date?->format('Y-m-d') ?? 'N/A' }}', '{{ $application->resume ? Storage::url($application->resume) : '#' }}', '{{ $application->license_doc ? Storage::url($application->license_doc) : '#' }}', '{{ $application->certifications ? Storage::url($application->certifications) : '#' }}', '{{ $application->id }}')" class="btn-secondary px-4 py-2 rounded-full text-sm font-semibold flex items-center mx-auto">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-gray-600">No pending applications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $applications->links() }}
            </div>
        </div>
    </div>

    <!-- Application Modal -->
    <div id="applicationModal" class="fixed inset-0 modal-backdrop hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-5xl w-full mx-4 modal-content">
            <div class="sticky top-0 bg-gray-50 border-b border-gray-200 px-6 py-3 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Application Details - <span id="modalRefNumber"></span>
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:bg-gray-100 p-1 rounded-full smooth-transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <!-- Review Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <!-- Personal Information -->
                    <div class="p-4 bg-white border border-gray-200 rounded-lg">
                        <h4 class="text-base font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            Personal Information
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Name:</span>
                                <span id="modalName" class="text-gray-800"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Email:</span>
                                <span id="modalEmail" class="text-gray-800"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Phone:</span>
                                <span id="modalPhone" class="text-gray-800"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Address:</span>
                                <span id="modalAddress" class="text-gray-800 text-right"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information -->
                    <div class="p-4 bg-white border border-gray-200 rounded-lg">
                        <h4 class="text-base font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Professional Information
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Profession:</span>
                                <span id="modalProfession" class="text-gray-800"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Specialization:</span>
                                <span id="modalSpecialization" class="text-gray-800"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Experience:</span>
                                <span id="modalExperience" class="text-gray-800"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Start Date:</span>
                                <span id="modalStartDate" class="text-gray-800"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Work Preferences -->
                    <div class="p-4 bg-white border border-gray-200 rounded-lg">
                        <h4 class="text-base font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            Work Preferences
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Work Type:</span>
                                <span class="text-gray-800">{{ $application->work_type ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Shift Type:</span>
                                <span class="text-gray-800">{{ $application->shift_type ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Location:</span>
                                <span class="text-gray-800">{{ $application->preferred_location ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Supporting Documents -->
                    <div class="p-4 bg-white border border-gray-200 rounded-lg">
                        <h4 class="text-base font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            Supporting Documents
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-600">Resume:</span>
                                <a id="modalResume" href="#" class="text-blue-600 hover:text-blue-800 underline" target="_blank">View</a>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-600">License:</span>
                                <a id="modalLicense" href="#" class="text-blue-600 hover:text-blue-800 underline" target="_blank">View</a>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-600">Certifications:</span>
                                <a id="modalCertifications" href="#" class="text-blue-600 hover:text-blue-800 underline" target="_blank">View</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-center space-x-3 pt-4 border-t border-gray-200">
                    <button id="approveButton" class="btn-primary flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Approve Application
                    </button>
                    <button id="rejectButton" class="btn-danger flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reject Application
                    </button>
                    <button onclick="closeModal()" class="bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium smooth-transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Confirmation Popup -->
    <div id="approvalPopup" class="fixed inset-0 modal-backdrop hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
            <div class="border-b border-gray-200 px-4 py-3 flex justify-between items-center">
                <h3 class="text-base font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Application Approved
                </h3>
                <button onclick="closeApprovalPopup()" class="text-gray-400 hover:bg-gray-100 p-1 rounded-full smooth-transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 space-y-4">
                <div class="bg-gray-50 rounded-md p-3">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        User Credentials
                    </h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-600">Email:</span>
                            <div class="flex items-center space-x-2">
                                <span id="popupEmail"></span>
                                <button onclick="copyToClipboard('popupEmailInput')" class="text-blue-600 hover:text-blue-800" title="Copy Email">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                            <input type="text" id="popupEmailInput" class="hidden" readonly>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-600">Password:</span>
                            <div class="flex items-center space-x-2">
                                <span id="popupPassword" class="font-mono">********</span>
                                <button id="togglePassword" onclick="togglePasswordVisibility()" class="text-blue-600 hover:text-blue-800" title="Show/Hide Password">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542-7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button onclick="copyToClipboard('popupPasswordInput')" class="text-blue-600 hover:text-blue-800" title="Copy Password">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                            <input type="password" id="popupPasswordInput" class="hidden" readonly>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-md p-3">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                        Snapshot
                    </h4>
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-medium text-gray-600">Application Snapshot:</span>
                        <div class="flex space-x-2">
                            <a id="viewSnapshotLink" href="#" class="text-blue-600 hover:text-blue-800 flex items-center" target="_blank">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542-7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View
                            </a>
                            <a id="downloadPdfLink" href="#" class="text-blue-600 hover:text-blue-800 flex items-center" target="_blank">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center">
                    <form id="regeneratePasswordForm" action="#" method="POST" class="action-form" onsubmit="event.preventDefault(); handleRegeneratePassword(this);">
                        @csrf
                        <button type="submit" class="btn-primary flex items-center" data-loading-text="Regenerating...">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Regenerate Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('styles')
<style>
    .modal-content {
        max-height: 80vh;
        overflow-y: auto;
        scrollbar-width: thin;
    }
    .modal-content::-webkit-scrollbar {
        width: 6px;
    }
    .modal-content::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
</style>
@endpush

@push('scripts')
<script>
    let currentApplicationData = {};

    // Modal handling
    function openModal(refNumber, name, email, phone, address, profession, specialization, experience, startDate, resumeUrl, licenseUrl, certificationsUrl, applicationId) {
        if (!refNumber || !name || !email) {
            showToast('Missing application data!', 'error');
            return;
        }

        currentApplicationData = { refNumber, name, email, applicationId };
        document.getElementById('modalRefNumber').textContent = refNumber;
        document.getElementById('modalName').textContent = name;
        document.getElementById('modalEmail').textContent = email;
        document.getElementById('modalPhone').textContent = phone || 'N/A';
        document.getElementById('modalAddress').textContent = address || 'N/A';
        document.getElementById('modalProfession').textContent = profession || 'N/A';
        document.getElementById('modalSpecialization').textContent = specialization || 'N/A';
        document.getElementById('modalExperience').textContent = experience || 'N/A';
        document.getElementById('modalStartDate').textContent = startDate || 'N/A';
        document.getElementById('modalResume').href = resumeUrl !== '#' ? resumeUrl : '#';
        document.getElementById('modalResume').textContent = resumeUrl !== '#' ? 'View' : 'Not Provided';
        document.getElementById('modalLicense').href = licenseUrl !== '#' ? licenseUrl : '#';
        document.getElementById('modalLicense').textContent = licenseUrl !== '#' ? 'View' : 'Not Provided';
        document.getElementById('modalCertifications').href = certificationsUrl !== '#' ? certificationsUrl : '#';
        document.getElementById('modalCertifications').textContent = certificationsUrl !== '#' ? 'View' : 'Not Provided';
        document.getElementById('applicationModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Set up action buttons
        const approveButton = document.getElementById('approveButton');
        const rejectButton = document.getElementById('rejectButton');

        approveButton.onclick = async () => {
            const url = '{{ route("admin.applications.process") }}';
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
            formData.append('id', applicationId);
            formData.append('action', 'approve');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (response.ok) {
                    openApprovalPopup(refNumber, name, result.data.email, result.data.password, result.data.snapshot_url, result.data.pdf_url);
                    showToast('Application approved successfully!', 'success');
                } else {
                    throw new Error(result.message || 'Failed to approve application');
                }
            } catch (error) {
                showToast(`Failed to approve application: ${error.message}`, 'error');
            }
        };

        rejectButton.onclick = async () => {
            const url = '{{ route("admin.applications.process") }}';
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
            formData.append('id', applicationId);
            formData.append('action', 'reject');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (response.ok) {
                    showToast('Application rejected successfully!', 'success');
                    closeModal();
                    location.reload();
                } else {
                    throw new Error(result.message || 'Failed to reject application');
                }
            } catch (error) {
                showToast(`Failed to reject application: ${error.message}`, 'error');
            }
        };
    }

    function closeModal() {
        document.getElementById('applicationModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Approval popup handling
    function openApprovalPopup(refNumber, name, email, password, snapshotUrl, pdfUrl) {
        document.getElementById('popupEmail').textContent = email;
        document.getElementById('popupEmailInput').value = email;
        document.getElementById('popupPassword').textContent = '********';
        document.getElementById('popupPasswordInput').value = password;
        document.getElementById('viewSnapshotLink').href = snapshotUrl || '#';
        document.getElementById('downloadPdfLink').href = pdfUrl || '#';
        document.getElementById('regeneratePasswordForm').action = `/admin/applications/${refNumber}/regenerate-password`;
        document.getElementById('approvalPopup').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        closeModal();
    }

    function closeApprovalPopup() {
        document.getElementById('approvalPopup').classList.add('hidden');
        document.body.style.overflow = 'auto';
        location.reload();
    }

    // Copy to clipboard
    function copyToClipboard(elementId) {
        const input = document.getElementById(elementId);
        input.select();
        document.execCommand('copy');
        showToast('Copied to clipboard!', 'success');
    }

    // Toggle password visibility
    function togglePasswordVisibility() {
        const passwordSpan = document.getElementById('popupPassword');
        const passwordInput = document.getElementById('popupPasswordInput');
        const toggleButton = document.getElementById('togglePassword');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordSpan.textContent = passwordInput.value;
            toggleButton.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                </svg>
            `;
        } else {
            passwordInput.type = 'password';
            passwordSpan.textContent = '********';
            toggleButton.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542-7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            `;
        }
    }

    // Regenerate password
    async function handleRegeneratePassword(form) {
        const url = form.action;
        const formData = new FormData(form);

        const button = form.querySelector('button[type="submit"]');
        button.disabled = true;
        button.innerHTML = `
            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Regenerating...
        `;

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            if (response.ok) {
                document.getElementById('popupPassword').textContent = result.password;
                document.getElementById('popupPasswordInput').value = result.password;
                document.getElementById('popupPasswordInput').type = 'text';
                document.getElementById('togglePassword').innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    </svg>
                `;
                showToast('Password regenerated successfully!', 'success');
            } else {
                throw new Error(result.message || 'Failed to regenerate password');
            }
        } catch (error) {
            showToast(`Failed to regenerate password: ${error.message}`, 'error');
        } finally {
            button.disabled = false;
            button.innerHTML = `
                <svg class="w-4 hproduction-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24                    <path stroke-linecap="round" stroke="none" stroke-linejoin="round" stroke-width="1" d="M4 4v5h.582m15.356 2 A2 2 0 004.582 9m0 0H9m11 11v-5h-.581m0 0 a8.003 a0 0 0 0 0 a0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Regenerate Password
            `;
        }
    }

    // Export applications (placeholder; implement server-side export)
    function exportApplications() {
        showToast('Export functionality not implemented yet.', 'error');
        // Example: window.location.href = '/admin/applications/export';
    }

    // Toast notification
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded-md text-sm font-medium text-white ${
            type === 'success' ? 'bg-green-600' : 'bg-red-600'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Close modals on click outside
    document.getElementById('applicationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    document.getElementById('approvalPopup').addEventListener('click', function(e) {
        if (e.target === this) {
            closeApprovalPopup();
        }
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            closeApprovalPopup();
        }
    });
</script>
@endpush
