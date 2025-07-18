{{-- Individual Requests Management --}}
<div x-data="{
    showModal: false,
    selectedRequest: null,
    requests: [],
    stats: {
        pending: 0,
        approved: 0,
        rejected: 0,
        total: 0
    },
    loading: true,
    init() {
        this.fetchRequests();
    },
    fetchRequests() {
        this.loading = true;
        fetch('{{ route('admin.individual') }}')
            .then(response => response.json())
            .then(data => {
                this.requests = data.requests;
                this.stats = data.stats;
                this.loading = false;
            })
            .catch(error => {
                console.error('Error fetching requests:', error);
                this.loading = false;
            });
    },
    viewRequest(request) {
        fetch(`/admin/individual/${request.id}`)
            .then(response => response.json())
            .then(data => {
                this.selectedRequest = data;
                this.showModal = true;
            });
    },
    approveRequest(requestId) {
        if(confirm('Are you sure you want to approve this request?')) {
            fetch(`/admin/individual/${requestId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert(data.message);
                    this.fetchRequests();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    },
    rejectRequest(requestId) {
        if(confirm('Are you sure you want to reject this request?')) {
            fetch(`/admin/individual/${requestId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert(data.message);
                    this.fetchRequests();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    },
    assignHealthWorker(requestId) {
        alert('Assign health worker functionality would be implemented here.');
    }
}" class="space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 flex items-center">
                <span class="w-1.5 h-7 bg-purple-600 rounded-full mr-3"></span>
                Individual Requests
            </h2>
            <p class="text-gray-600 mt-1">Manage requests from individuals for healthcare services</p>
        </div>
        <div class="flex space-x-3">
            <button class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Request
            </button>
            <button class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filter
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p x-text="stats.pending" class="text-2xl font-semibold text-gray-900"></p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Approved</p>
                    <p x-text="stats.approved" class="text-2xl font-semibold text-gray-900"></p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rejected</p>
                    <p x-text="stats.rejected" class="text-2xl font-semibold text-gray-900"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading State --}}
    <div x-show="loading" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 flex justify-center items-center">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-600"></div>
        <span class="ml-4 text-gray-700">Loading individual requests...</span>
    </div>

    {{-- Requests Table --}}
    <div x-show="!loading" class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Individual Requests</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Client</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Service Type</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Schedule</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Urgency</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Submitted</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="request in requests" :key="request.id">
                        <tr class="table-row smooth-transition">
                            <td class="px-4 py-3">
                                <div>
                                    <div class="font-medium text-gray-900" x-text="request.client_name"></div>
                                    <div class="text-gray-600 text-xs" x-text="request.phone"></div>
                                    <div class="text-gray-600 text-xs" x-text="request.location"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-gray-900" x-text="request.service_type"></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-gray-900" x-text="request.care_duration"></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full"
                                      :class="{
                                          'bg-red-100 text-red-800': request.urgency === 'High',
                                          'bg-yellow-100 text-yellow-800': request.urgency === 'Medium',
                                          'bg-green-100 text-green-800': request.urgency === 'Low'
                                      }"
                                      x-text="request.urgency">
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full"
                                      :class="{
                                          'bg-yellow-100 text-yellow-800': request.status === 'Pending',
                                          'bg-green-100 text-green-800': request.status === 'Approved',
                                          'bg-red-100 text-red-800': request.status === 'Rejected'
                                      }"
                                      x-text="request.status">
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600" x-text="request.submitted_at"></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button @click="viewRequest(request)" class="btn-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <template x-if="request.status === 'Pending'">
                                        <div class="flex space-x-1">
                                            <button @click="approveRequest(request.id)" class="btn-primary">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                            <button @click="rejectRequest(request.id)" class="btn-danger">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="request.status === 'Approved'">
                                        <button @click="assignHealthWorker(request.id)" class="btn-secondary">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal for viewing request details --}}
    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto modal-backdrop"
         @click.self="showModal = false">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full modal-content">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Request Details</h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="px-6 py-4 space-y-4" x-show="selectedRequest">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Client Name</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.client_name"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.email"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.phone"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Location</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.location"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Service Type</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.service_type"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Schedule</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.care_duration"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Medical Conditions</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.medical_conditions || 'N/A'"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Medications</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.medications || 'N/A'"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Emergency Contact</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.emergency_contact"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Emergency Phone</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.emergency_phone"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reference Number</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.reference_number"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Urgency</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.urgency"></p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.description || 'Not specified'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Qualifications</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.qualifications || 'Not specified'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Experience</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.experience || 'Not specified'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Job Description</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="selectedRequest?.job_description || 'Not specified'"></p>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button @click="showModal = false" class="btn-secondary">Close</button>
                    <template x-if="selectedRequest?.status === 'Pending'">
                        <div class="flex space-x-2">
                            <button @click="approveRequest(selectedRequest.id); showModal = false" class="btn-primary">Approve</button>
                            <button @click="rejectRequest(selectedRequest.id); showModal = false" class="btn-danger">Reject</button>
                        </div>
                    </template>
                    <template x-if="selectedRequest?.status === 'Approved'">
                        <button @click="assignHealthWorker(selectedRequest.id); showModal = false" class="btn-secondary">Assign Health Worker</button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
