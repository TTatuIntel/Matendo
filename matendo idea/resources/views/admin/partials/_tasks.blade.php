@php
    $healthworkers = \App\Models\User::where('usertype', 'healthworker')->select('id', 'name')->get();
@endphp

<div x-data="{
    showModal: false,
    selectedTask: null,
    tasks: [],
    stats: {
        pending: 0,
        approved: 0,
        rejected: 0,
        total: 0
    },
    loading: true,
    init() {
        this.fetchTasks();
    },
    fetchTasks() {
        this.loading = true;
        fetch('{{ route('tasks.getTasks') }}')
            .then(response => response.json())
            .then(data => {
                this.tasks = data.tasks;
                this.stats = data.stats;
                this.loading = false;
            })
            .catch(error => {
                console.error('Error fetching tasks:', error);
                this.loading = false;
            });
    },
    viewTask(task) {
        fetch(`/tasks/${task.id}`)
            .then(response => response.json())
            .then(data => {
                this.selectedTask = data;
                this.showModal = true;
            });
    },
    approveTask(taskId) {
        if (confirm('Are you sure you want to approve this task?')) {
            fetch(`/tasks/${taskId}/approve`, {
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
                    this.fetchTasks();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    },
    rejectTask(taskId) {
        if (confirm('Are you sure you want to reject this task?')) {
            fetch(`/tasks/${taskId}/reject`, {
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
                    this.fetchTasks();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    },
    addTask() {
        const form = document.getElementById('add-task-form');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch('{{ route('tasks.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                this.fetchTasks();
                this.$refs.addTaskModal.close();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}" class="space-y-8">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 flex items-center">
            <span class="w-1.5 h-7 bg-green-600 rounded-full mr-3"></span>
            Tasks Management
        </h2>
        <div class="flex space-x-3">
            <button class="btn-primary flex items-center" @click="$refs.addTaskModal.showModal()">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Task
            </button>
            <button class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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

    <!-- Loading State -->
    <div x-show="loading" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 flex justify-center items-center">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-green-600"></div>
        <span class="ml-4 text-gray-700">Loading tasks...</span>
    </div>

    <!-- Tasks Table -->
    <div x-show="!loading" class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Tasks</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Task</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Assigned To</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Due Date</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="task in tasks" :key="task.id">
                        <tr class="table-row smooth-transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-900" x-text="task.facility_name"></div>
                                        <div class="text-gray-600" x-text="task.job_description ? task.job_description.substring(0, 50) + (task.job_description.length > 50 ? '...' : '') : 'N/A'"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600" x-text="task.contact_person || 'Unassigned'"></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full"
                                      :class="{
                                          'bg-yellow-100 text-yellow-800': task.status === 'Pending',
                                          'bg-green-100 text-green-800': task.status === 'Approved',
                                          'bg-red-100 text-red-800': task.status === 'Rejected'
                                      }"
                                      x-text="task.status">
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600" x-text="task.start_date || 'N/A'"></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button @click="viewTask(task)" class="btn-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <template x-if="task.status === 'Pending'">
                                        <div class="flex space-x-1">
                                            <button @click="approveTask(task.id)" class="btn-primary">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                            <button @click="rejectTask(task.id)" class="btn-danger">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Task Modal -->
    <dialog x-ref="addTaskModal" class="modal-backdrop rounded-lg p-0 w-full max-w-lg">
        <div class="modal-content bg-white p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Task</h3>
            <form id="add-task-form" @submit.prevent="addTask">
                <div class="space-y-4">
                    <div>
                        <label for="facility_name" class="block text-sm font-medium text-gray-700">Facility Name</label>
                        <input type="text" name="facility_name" id="facility_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                    </div>
                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person</label>
                        <input type="text" name="contact_person" id="contact_person" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="phone" id="phone" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                    </div>
                    <div>
                        <label for="facility_type" class="block text-sm font-medium text-gray-700">Facility Type</label>
                        <input type="text" name="facility_type" id="facility_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                    </div>
                    <div>
                        <label for="positions" class="block text-sm font-medium text-gray-700">Positions</label>
                        <input type="text" name="positions" id="positions" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                    </div>
                    <div>
                        <label for="employment_type" class="block text-sm font-medium text-gray-700">Employment Type</label>
                        <select name="employment_type" id="employment_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                        </select>
                    </div>
                    <div>
                        <label for="shift_type" class="block text-sm font-medium text-gray-700">Shift Type</label>
                        <select name="shift_type" id="shift_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                            <option value="Day">Day</option>
                            <option value="Night">Night</option>
                            <option value="Rotating">Rotating</option>
                        </select>
                    </div>
                    <div>
                        <label for="staff_number" class="block text-sm font-medium text-gray-700">Staff Number</label>
                        <input type="number" name="staff_number" id="staff_number" required min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" id="start_date" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                    </div>
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                        <select name="priority" id="priority" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                            <option value="Low">Low</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                    <div>
                        <label for="job_description" class="block text-sm font-medium text-gray-700">Job Description</label>
                        <textarea name="job_description" id="job_description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm"></textarea>
                    </div>
                    <div>
                        <label for="qualifications" class="block text-sm font-medium text-gray-700">Qualifications</label>
                        <textarea name="qualifications" id="qualifications" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm"></textarea>
                    </div>
                    <div>
                        <label for="experience" class="block text-sm font-medium text-gray-700">Experience</label>
                        <textarea name="experience" id="experience" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" class="btn-secondary" @click="$refs.addTaskModal.close()">Cancel</button>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- View Task Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto modal-backdrop" @click.self="showModal = false">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full modal-content">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Task Details</h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="px-6 py-4 space-y-4" x-show="selectedTask">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Facility Name</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.facility_name"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Contact Person</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.contact_person"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.email"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.phone"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Coordinates</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.coordinates || 'N/A'"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Facility Type</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.facility_type"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Positions</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.positions"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employment Type</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.employment_type"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Shift Type</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.shift_type"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Staff Number</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.staff_number"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Date</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.start_date"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Priority</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.urgency"></p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Job Description</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.job_description || 'Not specified'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Qualifications</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.qualifications || 'Not specified'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Experience</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.experience || 'Not specified'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Medical Conditions</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.medical_conditions || 'N/A'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Medications</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.medications || 'N/A'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Emergency Contact</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.emergency_contact || 'N/A'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Emergency Phone</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.emergency_phone || 'N/A'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reference Number</label>
                        <p class="mt-1 text-sm text-gray-900" x-text="selectedTask?.reference_number"></p>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button @click="showModal = false" class="btn-secondary">Close</button>
                    <template x-if="selectedTask?.status === 'Pending'">
                        <div class="flex space-x-2">
                            <button @click="approveTask(selectedTask.id); showModal = false" class="btn-primary">Approve</button>
                            <button @click="rejectTask(selectedTask.id); showModal = false" class="btn-danger">Reject</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
