@php
    $tasks = [
        (object) [
            'id' => 1,
            'title' => 'Review Patient Records',
            'description' => 'Review and update patient records for the cardiology department.',
            'assigned_to' => (object) ['id' => 1, 'name' => 'Dr. Jane Smith'],
            'status' => 'Pending',
            'due_date' => now()->addDays(3),
        ],
        (object) [
            'id' => 2,
            'title' => 'Staff Training Session',
            'description' => 'Conduct training on new medical equipment.',
            'assigned_to' => null,
            'status' => 'In Progress',
            'due_date' => now()->addDays(7),
        ],
    ];
    $healthworkers = [
        (object) ['id' => 1, 'name' => 'Dr. Jane Smith'],
        (object) ['id' => 2, 'name' => 'Dr. John Doe'],
        (object) ['id' => 3, 'name' => 'Nurse Alice Brown'],
    ];
@endphp

<div class="space-y-8">
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
            <button class="btn-secondary flex items-center" @click="alert('Export functionality not implemented in demo mode')">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export
            </button>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Task</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Assigned To</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Due Date</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($tasks as $task)
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
                                        <div class="font-medium text-gray-900">{{ $task->title }}</div>
                                        <div class="text-gray-600">{{ \Illuminate\Support\Str::limit($task->description, 50) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $task->assigned_to ? $task->assigned_to->name : 'Unassigned' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium bg-{{ $task->status == 'Completed' ? 'green' : ($task->status == 'Pending' ? 'yellow' : 'blue') }}-100 text-{{ $task->status == 'Completed' ? 'green' : ($task->status == 'Pending' ? 'yellow' : 'blue') }}-800 rounded-full">
                                    {{ $task->status ?? 'Pending' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'N/A' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button class="btn-primary flex items-center" @click="$refs.viewTaskModal{{ $task->id }}.showModal()">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <button class="btn-danger flex items-center" @click="alert('Delete functionality not implemented in demo mode')">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-center text-gray-600">
                                No tasks found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Task Modal -->
    <dialog x-ref="addTaskModal" class="modal-backdrop rounded-lg p-0 w-full max-w-lg">
        <div class="modal-content bg-white p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Task</h3>
            <form action="#" onsubmit="alert('Add functionality not implemented in demo mode'); return false;">
                <div class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Task Title</label>
                        <input type="text" name="title" id="title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm"></textarea>
                    </div>
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assign To</label>
                        <select name="assigned_to" id="assigned_to" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                            <option value="">Unassigned</option>
                            @foreach ($healthworkers as $healthworker)
                                <option value="{{ $healthworker->id }}">{{ $healthworker->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                        <input type="date" name="due_date" id="due_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-400 focus:border-green-400 sm:text-sm">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" class="btn-secondary" @click="$refs.addTaskModal.close()">Cancel</button>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- View Task Modals -->
    @foreach ($tasks as $task)
        <dialog x-ref="viewTaskModal{{ $task->id }}" class="modal-backdrop rounded-lg p-0 w-full max-w-lg">
            <div class="modal-content bg-white p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Task Details</h3>
                <div class="space-y-4">
                    <div>
                        <span class="font-medium text-gray-700">Title:</span> {{ $task->title }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Description:</span> {{ $task->description ?? 'N/A' }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Assigned To:</span> {{ $task->assigned_to ? $task->assigned_to->name : 'Unassigned' }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Status:</span>
                        <span class="px-2 py-1 text-xs font-medium bg-{{ $task->status == 'Completed' ? 'green' : ($task->status == 'Pending' ? 'yellow' : 'blue') }}-100 text-{{ $task->status == 'Completed' ? 'green' : ($task->status == 'Pending' ? 'yellow' : 'blue') }}-800 rounded-full">
                            {{ $task->status ?? 'Pending' }}
                        </span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Due Date:</span> {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'N/A' }}
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" class="btn-secondary" @click="$refs.viewTaskModal{{ $task->id }}.close()">Close</button>
                </div>
            </div>
        </dialog>
    @endforeach
</div>
