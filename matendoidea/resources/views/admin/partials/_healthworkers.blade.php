<div x-data="{
    showModal: false,
    selectedWorker: null,
    health_workers: [],
    stats: {
        total: 0,
        verified: 0,
        unverified: 0
    },
    loading: true,
    init() {
        this.fetchHealthWorkers();
    },
    fetchHealthWorkers() {
        this.loading = true;
        fetch('{{ route('health-workers.index') }}')
            .then(response => response.json())
            .then(data => {
                this.health_workers = data.health_workers;
                this.stats = data.stats;
                this.loading = false;
            })
            .catch(error => {
                console.error('Error fetching health workers:', error);
                this.loading = false;
            });
    },
    viewWorker(worker) {
        fetch(`/health-workers/${worker.id}`)
            .then(response => response.json())
            .then(data => {
                this.selectedWorker = data;
                this.showModal = true;
            });
    },
    toggleVerification(workerId) {
        if (confirm('Are you sure you want to toggle the verification status of this health worker?')) {
            fetch(`/health-workers/${workerId}/toggle-verification`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    this.fetchHealthWorkers();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }
}" class="space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 flex items-center">
                <span class="w-1.5 h-7 bg-teal-600 rounded-full mr-3"></span>
                Health Workers
            </h2>
            <p class="text-gray-600 mt-1">Manage verified healthcare professionals and their assignments</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('health-workers.create') }}" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Worker
            </a>
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
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Verified</p>
                    <p x-text="stats.verified" class="text-2xl font-semibold text-gray-900"></p>
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
                    <p class="text-sm font-medium text-gray-600">Unverified</p>
                    <p x-text="stats.unverified" class="text-2xl font-semibold text-gray-900"></p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total</p>
                    <p x-text="stats.total" class="text-2xl font-semibold text-gray-900"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading State --}}
    <div x-show="loading" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 flex justify-center items-center">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-teal-600"></div>
        <span class="ml-4 text-gray-700">Loading health workers...</span>
    </div>

    {{-- Health Workers Table --}}
    <div x-show="!loading" class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Health Workers</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">ID</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Email</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Joined</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="worker in health_workers" :key="worker.id">
                        <tr class="table-row smooth-transition">
                            <td class="px-4 py-3 text-gray-900" x-text="worker.id"></td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900" x-text="worker.name"></div>
                            </td>
                            <td class="px-4 py-3 text-gray-600" x-text="worker.email"></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full"
                                      :class="{
                                          'bg-green-100 text-green-800': worker.email_verified_at,
                                          'bg-red-100 text-red-800': !worker.email_verified_at
                                      }"
                                      x-text="worker.email_verified_at ? 'Verified' : 'Unverified'">
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600" x-text="new Date(worker.created_at).toLocaleDateString()"></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button @click="viewWorker(worker)" class="btn-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button @click="toggleVerification(worker.id)" class="btn-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal for viewing worker details --}}
    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto modal-backdrop"
         @click.self="showModal = false">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full modal-content">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Health Worker Details</h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="px-6 py-4 space-y-4" x-show="selectedWorker">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ID</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedWorker?.id"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedWorker?.name"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedWorker?.email"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedWorker?.email_verified_at ? 'Verified' : 'Unverified'"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Joined</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedWorker?.created_at ? new Date(selectedWorker.created_at).toLocaleDateString() : 'N/A'"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedWorker?.updated_at ? new Date(selectedWorker.updated_at).toLocaleDateString() : 'N/A'"></p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button @click="showModal = false" class="btn-secondary">Close</button>
                    <button @click="toggleVerification(selectedWorker.id); showModal = false" class="btn-primary">Toggle Verification</button>
                </div>
            </div>
        </div>
    </div>
</div>
