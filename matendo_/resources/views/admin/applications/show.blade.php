<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Application Review') }}: {{ $application->reference_code }}
            </h2>
            <div>
                <a href="{{ route('admin.applications.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            @if (session('temporary_credentials'))
                <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Temporary Credentials Generated!</strong>
                    <div class="block sm:inline mt-2">
                        <p>Email: {{ session('temporary_credentials')['email'] }}</p>
                        <p>Password: {{ session('temporary_credentials')['password'] }}</p>
                        <p class="mt-2 text-sm">Please make sure to securely send these credentials to the applicant.</p>
                    </div>
                </div>
            @endif
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">
                            Application Status: 
                            <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full 
                                @if($application->status === 'approved') bg-green-100 text-green-800 
                                @elseif($application->status === 'rejected') bg-red-100 text-red-800 
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($application->status) }}
                            </span>
                        </h3>
                        <div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                Applied: {{ $application->created_at->format('M d, Y H:i') }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-semibold text-lg mb-4 border-b pb-2">Personal Information</h4>
                            <div class="space-y-2">
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Name:</span>
                                    <span>{{ $application->first_name }} {{ $application->last_name }}</span>
                                </div>
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Email:</span>
                                    <span>{{ $application->email }}</span>
                                </div>
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Phone:</span>
                                    <span>{{ $application->phone }}</span>
                                </div>
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Address:</span>
                                    <span>{{ $application->address }}</span>
                                </div>
                                @if($application->location)
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Location:</span>
                                    <span>{{ $application->location }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Professional Information -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-semibold text-lg mb-4 border-b pb-2">Professional Information</h4>
                            <div class="space-y-2">
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Profession:</span>
                                    <span>{{ $application->profession }}</span>
                                </div>
                                @if($application->other_profession)
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Other Profession:</span>
                                    <span>{{ $application->other_profession }}</span>
                                </div>
                                @endif
                                @if($application->specialization)
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Specialization:</span>
                                    <span>{{ $application->specialization }}</span>
                                </div>
                                @endif
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Experience:</span>
                                    <span>{{ $application->years_experience }} years</span>
                                </div>
                                @if($application->license_number)
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">License Number:</span>
                                    <span>{{ $application->license_number }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Work Preferences -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-semibold text-lg mb-4 border-b pb-2">Work Preferences</h4>
                            <div class="space-y-2">
                                @if($application->work_type)
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Work Type:</span>
                                    <span>{{ implode(', ', $application->work_type) }}</span>
                                </div>
                                @endif
                                @if($application->shift_type)
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Shift Type:</span>
                                    <span>{{ implode(', ', $application->shift_type) }}</span>
                                </div>
                                @endif
                                @if($application->preferred_location)
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Preferred Location:</span>
                                    <span>{{ $application->preferred_location }}</span>
                                </div>
                                @endif
                                @if($application->start_date)
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Available From:</span>
                                    <span>{{ $application->start_date->format('M d, Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Documents -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-semibold text-lg mb-4 border-b pb-2">Documents</h4>
                            <div class="space-y-2">
                                @if($application->resume)
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Resume:</span>
                                    <a href="{{ Storage::url($application->resume) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        View Document
                                    </a>
                                </div>
                                @endif
                                @if($application->license_doc)
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">License Document:</span>
                                    <a href="{{ Storage::url($application->license_doc) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        View Document
                                    </a>
                                </div>
                                @endif
                                @if($application->certifications)
                                <div class="grid grid-cols-2">
                                    <span class="text-gray-600 dark:text-gray-400">Certifications:</span>
                                    <a href="{{ Storage::url($application->certifications) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        View Document
                                    </a>
                                </div>
                                @endif
                                @if(!$application->resume && !$application->license_doc && !$application->certifications)
                                <div class="text-center py-4 text-gray-500">
                                    No documents uploaded
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Section -->
                    <div class="mt-6 border-t pt-6">
                        <h4 class="font-semibold text-lg mb-4">Take Action</h4>
                        
                        <form action="{{ route('admin.applications.update-status', $application) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Update Status</label>
                                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                        <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $application->status === 'approved' ? 'selected' : '' }}>Approve</option>
                                        <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="feedback" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Feedback (Optional)</label>
                                    <textarea id="feedback" name="feedback" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"></textarea>
                                </div>
                            </div>
                            
                            <!-- User Account Section (shows when status is 'approved') -->
                            <div id="account-section" class="mt-4 p-4 border rounded-lg" style="display: none;">
                                <h4 class="font-semibold text-md mb-4 text-green-600">Account Settings</h4>
                                <p class="text-sm text-gray-600 mb-3">When approved, an account will be created for this applicant. Please select their user type:</p>
                                
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="usertype" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign User Type</label>
                                        <select id="usertype" name="usertype" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                            <option value="healthworker">Health Worker</option>
                                            <option value="user">Regular User</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input id="send_credentials" name="send_credentials" type="checkbox" checked class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="send_credentials" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                            Generate and send temporary login credentials to the applicant
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Update Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Show/hide account section based on status selection
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const accountSection = document.getElementById('account-section');
            
            // Initial check
            toggleAccountSection();
            
            // Add event listener for changes
            statusSelect.addEventListener('change', toggleAccountSection);
            
            function toggleAccountSection() {
                if (statusSelect.value === 'approved') {
                    accountSection.style.display = 'block';
                } else {
                    accountSection.style.display = 'none';
                }
            }
        });
    </script>
</x-app-layout>