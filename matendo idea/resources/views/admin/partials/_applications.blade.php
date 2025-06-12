<div class="p-8 bg-gray-100 min-h-screen">
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 text-green-800 p-4 rounded-lg text-sm shadow-sm mb-6">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-400 text-red-800 p-4 rounded-lg text-sm shadow-sm mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Applications Table -->
    <div>
        <h2 class="text-3xl font-bold text-gray-800 mb-8 flex items-center">
            <div class="w-2 h-8 bg-gradient-to-b from-green-600 to-blue-600 rounded-full mr-4"></div>
            Application List
        </h2>
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 sticky top-0">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">Reference Code</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">Name</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">Email</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($applications as $app)
                            <tr class="hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-all duration-300">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $app->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800">{{ $app->reference_code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800">{{ $app->first_name }} {{ $app->last_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800">{{ $app->email }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $app->status == 'approved' ? 'bg-green-100 text-green-800' : ($app->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($app->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-blue-600 hover:text-blue-800 font-semibold view-btn"
                                            data-id="{{ $app->id }}"
                                            data-ref="{{ $app->reference_code }}"
                                            data-name="{{ $app->first_name }} {{ $app->last_name }}"
                                            data-email="{{ $app->email }}"
                                            data-phone="{{ $app->phone ?? 'N/A' }}"
                                            data-address="{{ $app->address ?? 'N/A' }}"
                                            data-profession="{{ $app->profession ?? 'N/A' }}"
                                            data-specialization="{{ $app->specialization ?? 'N/A' }}"
                                            data-experience="{{ $app->experience ?? 'N/A' }}"
                                            data-start-date="{{ $app->start_date ?? 'N/A' }}"
                                            data-resume-url="{{ $app->resume_url ?? '#' }}"
                                            data-license-url="{{ $app->license_url ?? '#' }}"
                                            data-certifications-url="{{ $app->certifications_url ?? '#' }}">View</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $applications->links() }}
            </div>
        </div>
    </div>

    <!-- Application Modal -->
    <div id="applicationModal" class="fixed inset-0 bg-gray-600 bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[85vh] overflow-y-auto m-4 transition-all duration-300">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex justify-between items-center rounded-t-2xl">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                    <svg class="w-7 h-7 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Application Details - <span id="modalRefNumber"></span>
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-8">
                <!-- Review Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Personal Information -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200">
                        <h4 class="text-lg font-bold text-blue-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            Personal Information
                        </h4>
                        <div class="space-y-3 text-sm">
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
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200">
                        <h4 class="text-lg font-bold text-green-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Professional Information
                        </h4>
                        <div class="space-y-3 text-sm">
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

                    <!-- Supporting Documents -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200">
                        <h4 class="text-lg font-bold text-amber-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            Supporting Documents
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-600">Resume:</span>
                                <a id="modalResume" href="#" class="text-blue-600 hover:text-blue-800 underline font-medium">View</a>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-600">License:</span>
                                <a id="modalLicense" href="#" class="text-blue-600 hover:text-blue-800 underline font-medium">View</a>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-600">Certifications:</span>
                                <a id="modalCertifications" href="#" class="text-blue-600 hover:text-blue-800 underline font-medium">View</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-center space-x-6 pt-6 border-t border-gray-200">
                    <button id="approveButton" class="btn-primary px-8 py-3 rounded-xl text-base font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Approve Application
                    </button>
                    <button id="rejectButton" class="btn-danger px-8 py-3 rounded-xl text-base font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reject Application
                    </button>
                    <button onclick="closeModal()" class="bg-gray-200 hover:bg-gray-300 border-2 border-gray-400 text-gray-700 px-8 py-3 rounded-xl text-base font-semibold transition-all duration-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Confirmation Modal -->
    <div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transition-all duration-300">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Application Approved
                </h3>
                <button onclick="closeApprovalModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mb-6">
                <p class="text-sm text-gray-600 mb-2">The application has been successfully approved. Below are the user credentials:</p>
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium text-gray-600">Email:</span>
                        <span id="approvalEmail" class="text-gray-800"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Password:</span>
                        <span id="approvalPassword" class="text-gray-800"></span>
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                <button onclick="closeApprovalModal()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-all duration-300">Close</button>
            </div>
        </div>
    </div>

@push('styles')
<style>
    .transition-all {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-primary {
        background: linear-gradient(135deg, #059669, #0d9488);
        border: 2px solid #065f46;
        color: white;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #047857, #0f766e);
        border-color: #064e3b;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
    }
    .btn-danger {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        border: 2px solid #991b1b;
        color: white;
    }
    .btn-danger:hover {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-color: #7f1d1d;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded, initializing view buttons');

    const viewButtons = document.querySelectorAll('.view-btn');
    const modal = document.getElementById('applicationModal');
    const approvalModal = document.getElementById('approvalModal');
    const approveButton = document.getElementById('approveButton');
    const rejectButton = document.getElementById('rejectButton');

    if (!modal) {
        console.error('Application Modal not found!');
        return;
    }
    if (!approvalModal) {
        console.error('Approval Modal not found!');
        return;
    }
    if (!approveButton || !rejectButton) {
        console.error('Approve or Reject button not found!');
        return;
    }

    console.log(`Found ${viewButtons.length} view buttons`);

    viewButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            console.log('View button clicked:', this);

            try {
                const data = {
                    id: this.getAttribute('data-id') || 'N/A',
                    ref: this.getAttribute('data-ref') || 'N/A',
                    name: this.getAttribute('data-name') || 'N/A',
                    email: this.getAttribute('data-email') || 'N/A',
                    phone: this.getAttribute('data-phone') || 'N/A',
                    address: this.getAttribute('data-address') || 'N/A',
                    profession: this.getAttribute('data-profession') || 'N/A',
                    specialization: this.getAttribute('data-specialization') || 'N/A',
                    experience: this.getAttribute('data-experience') || 'N/A',
                    startDate: this.getAttribute('data-start-date') || 'N/A',
                    resumeUrl: this.getAttribute('data-resume-url') || '#',
                    licenseUrl: this.getAttribute('data-license-url') || '#',
                    certificationsUrl: this.getAttribute('data-certifications-url') || '#'
                };

                console.log('Data extracted:', data);

                // Populate application modal
                document.getElementById('modalRefNumber').textContent = data.ref;
                document.getElementById('modalName').textContent = data.name;
                document.getElementById('modalEmail').textContent = data.email;
                document.getElementById('modalPhone').textContent = data.phone;
                document.getElementById('modalAddress').textContent = data.address;
                document.getElementById('modalProfession').textContent = data.profession;
                document.getElementById('modalSpecialization').textContent = data.specialization;
                document.getElementById('modalExperience').textContent = data.experience;
                document.getElementById('modalStartDate').textContent = data.startDate;
                document.getElementById('modalResume').href = data.resumeUrl;
                document.getElementById('modalLicense').href = data.licenseUrl;
                document.getElementById('modalCertifications').href = data.certificationsUrl;

                // Set up action buttons
                approveButton.onclick = () => handleAction(data.id, 'approve', data.email);
                rejectButton.onclick = () => handleAction(data.id, 'reject');

                // Show application modal
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                console.log('Application modal should be visible now');
            } catch (error) {
                console.error('Error opening application modal:', error);
                showToast('Failed to open application details', 'error');
            }
        });
    });

    // Close application modal
    function closeModal() {
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            console.log('Application modal closed');
        }
    }

    // Close approval modal and reload page
    function closeApprovalModal() {
        if (approvalModal) {
            approvalModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            console.log('Approval modal closed');
            location.reload(); // Reload to reflect updated application list
        }
    }

    // Handle approve/reject actions
    async function handleAction(id, action, email = null) {
        console.log(`Handling ${action} for application ID: ${id}`);
        const url = `{{ route('admin.applications.process') }}`;
        const formData = new FormData();
        formData.append('id', id);
        formData.append('action', action);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

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
                if (action === 'approve') {
                    // Show approval modal with credentials
                    document.getElementById('approvalEmail').textContent = email;
                    document.getElementById('approvalPassword').textContent = result.data.password || 'N/A';
                    closeModal();
                    approvalModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                } else {
                    showToast('Application rejected successfully!', 'success');
                    closeModal();
                    location.reload();
                }
            } else {
                throw new Error(result.message || `Failed to ${action} application`);
            }
        } catch (error) {
            console.error(`Error during ${action}:`, error);
            showToast(`Failed to ${action} application: ${error.message}`, 'error');
        }
    }

    // Toast notification
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow-lg text-white text-sm font-medium ${
            type === 'success' ? 'bg-green-600' : 'bg-red-600'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Close modals when clicking outside
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    approvalModal.addEventListener('click', function (e) {
        if (e.target === approvalModal) {
            closeApprovalModal();
        }
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal();
            closeApprovalModal();
        }
    });
});
</script>
@endpush
