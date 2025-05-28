<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Health Worker Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Welcome, {{ auth()->user()->name }}!</h3>
                    <p class="mb-4">Your account is now active. You can use this dashboard to manage your health worker profile and view available assignments.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                        <!-- Profile Card -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-600">
                            <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">My Profile</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Update your personal information and credentials</p>
                            <a href="{{ route('profile.edit') }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                                Edit Profile →
                            </a>
                        </div>
                        
                        <!-- Assignments Card (placeholder) -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-600">
                            <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">My Assignments</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">View and manage your current and upcoming work assignments</p>
                            <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">
                                Coming Soon
                            </span>
                        </div>
                        
                        <!-- Documents Card (placeholder) -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-600">
                            <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">My Documents</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Access and manage your credentials and documents</p>
                            <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">
                                Coming Soon
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>