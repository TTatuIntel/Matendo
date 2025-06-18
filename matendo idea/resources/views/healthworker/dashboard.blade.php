<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Tasks Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Welcome Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium">Welcome back, {{ Auth::user()->name }}!</h3>
                            <p class="text-gray-600 dark:text-gray-400">Here are your currently assigned tasks</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Task Cards -->
            @if($assignedTasks->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($assignedTasks as $task)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="p-6">
                                <!-- Task Header -->
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $task->facility_name ?? 'Unnamed Facility' }}
                                        </h3>
                                        <div class="flex items-center mt-1">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($task->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @elseif($task->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                            @if($task->priority)
                                                <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full
                                                    @if($task->priority === 'high') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                    @elseif($task->priority === 'medium') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        #{{ $task->reference_number ?? 'N/A' }}
                                    </span>
                                </div>

                                <!-- Task Details -->
                                <div class="space-y-3 text-sm">
                                    <div class="flex items-start">
                                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span class="ml-2 text-gray-600 dark:text-gray-300">
                                            {{ $task->positions ?? 'Position not specified' }}
                                        </span>
                                    </div>

                                    <div class="flex items-start">
                                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="ml-2 text-gray-600 dark:text-gray-300">
                                            {{ $task->facility_type ?? 'Facility type not specified' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Task Actions -->
                                <div class="mt-6 flex justify-between items-center">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        Assigned: {{ $task->assigned_at ? \Carbon\Carbon::parse($task->assigned_at)->diffForHumans() : 'N/A' }}
                                    </span>
                                    <div class="space-x-2">
                                        @if($task->status !== 'completed')
                                            <form action="{{ route('tasks.complete', $task->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                                                    Mark Complete
                                                </button>
                                            </form>
                                        @endif
                                        <button onclick="openTaskModal({{ json_encode($task) }})"
                                                class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">No tasks assigned</h3>
                        <p class="mt-1 text-gray-600 dark:text-gray-400">You don't have any tasks assigned to you yet.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Task Details Modal -->
    <div id="taskModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>

            <!-- Modal container -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 id="modalTitle" class="text-lg leading-6 font-medium text-gray-900 dark:text-white"></h3>
                            <div class="mt-4 space-y-4 text-sm">
                                <div id="modalContent" class="space-y-4">
                                    <!-- Content will be inserted here by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeTaskModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openTaskModal(task) {
            // Format dates
            const formatDate = (dateString) => {
                if (!dateString) return 'Not set';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            };

            // Set modal title
            document.getElementById('modalTitle').textContent = task.facility_name || 'Task Details';

            // Build modal content
            let content = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Reference Number:</p>
                        <p class="text-gray-600 dark:text-gray-400">${task.reference_number || 'N/A'}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Status:</p>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            ${task.status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                            task.status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                            task.status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'}"}>
                            ${task.status ? task.status.replace('_', ' ') : 'Not specified'}
                        </span>
                    </div>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Contact Person:</p>
                        <p class="text-gray-600 dark:text-gray-400">${task.contact_person || 'Not specified'}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Contact Email:</p>
                        <p class="text-gray-600 dark:text-gray-400">${task.email || 'Not specified'}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Contact Phone:</p>
                        <p class="text-gray-600 dark:text-gray-400">${task.phone || 'Not specified'}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Facility Type:</p>
                        <p class="text-gray-600 dark:text-gray-400">${task.facility_type || 'Not specified'}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Positions:</p>
                        <p class="text-gray-600 dark:text-gray-400">${task.positions || 'Not specified'}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Employment Type:</p>
                        <p class="text-gray-600 dark:text-gray-400">${task.employment_type || 'Not specified'}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Shift Type:</p>
                        <p class="text-gray-600 dark:text-gray-400">${task.shift_type || 'Not specified'}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Start Date:</p>
                        <p class="text-gray-600 dark:text-gray-400">${formatDate(task.start_date)}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Priority:</p>
                        <p class="text-gray-600 dark:text-gray-400">${task.priority || 'Not specified'}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Assigned At:</p>
                        <p class="text-gray-600 dark:text-gray-400">${formatDate(task.assigned_at)}</p>
                    </div>
                </div>
                <div class="mt-4 space-y-2">
                    <p class="font-medium text-gray-700 dark:text-gray-300">Job Description:</p>
                    <p class="text-gray-600 dark:text-gray-400">${task.job_description || 'Not specified'}</p>
                </div>
                <div class="mt-4 space-y-2">
                    <p class="font-medium text-gray-700 dark:text-gray-300">Qualifications:</p>
                    <p class="text-gray-600 dark:text-gray-400">${task.qualifications || 'Not specified'}</p>
                </div>
                <div class="mt-4 space-y-2">
                    <p class="font-medium text-gray-700 dark:text-gray-300">Experience:</p>
                    <p class="text-gray-600 dark:text-gray-400">${task.experience || 'Not specified'}</p>
                </div>
            `;

            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('taskModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeTaskModal() {
            document.getElementById('taskModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Close modal when clicking outside content
        document.getElementById('taskModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTaskModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('taskModal').classList.contains('hidden')) {
                closeTaskModal();
            }
        });
    </script>
</x-app-layout>
