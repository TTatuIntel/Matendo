<!-- resources/views/admin/partials/_healthworkers.blade.php -->

<div class="space-y-8">
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 text-green-800 p-4 rounded-md text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 text-red-800 p-4 rounded-md text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Health Workers Overview Section -->
    <div>
        <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
            <span class="w-1.5 h-7 bg-green-600 rounded-full mr-3"></span>
            Health Workers Overview
        </h2>
        
        <div class="flex space-x-6 overflow-x-auto pb-4 mb-8">
            <!-- Total Verified Card -->
            <div class="min-w-[280px] flex-shrink-0 p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-100">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-purple-700 text-base flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Total Verified
                    </h3>
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                        {{ $totalVerifiedWorkers ?? 78 }}
                    </span>
                </div>
                <p class="text-gray-600 text-sm mb-2">Health workers verified and active on platform.</p>
                <div class="text-xs text-gray-600">
                    <p>New verifications this month: <span class="font-semibold">{{ $newVerificationsThisMonth ?? 8 }}</span></p>
                </div>
            </div>

            <!-- Pending Verification Card -->
            <div class="min-w-[280px] flex-shrink-0 p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-100">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-red-700 text-base flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        Pending Verification
                    </h3>
                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                        {{ $pendingVerificationWorkers ?? 5 }}
                    </span>
                </div>
                <p class="text-gray-600 text-sm mb-2">Profiles awaiting background and credential checks.</p>
                <div class="text-xs text-gray-600">
                    <p>Longest pending: <span class="font-semibold">{{ $longestVerificationPending ?? '4 days' }}</span></p>
                </div>
            </div>

            <!-- Flagged Profiles Card -->
            <div class="min-w-[280px] flex-shrink-0 p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-100">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-yellow-700 text-base flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Flagged Profiles
                    </h3>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                        {{ $flaggedProfiles ?? 2 }}
                    </span>
                </div>
                <p class="text-gray-600 text-sm mb-2">Profiles flagged for suspicious activity or errors.</p>
                <div class="text-xs text-gray-600">
                    <p>Under investigation.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Health Workers Table -->
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-900 flex items-center">
                <span class="w-1.5 h-7 bg-green-600 rounded-full mr-3"></span>
                Health Workers Directory
            </h2>
            <div class="flex space-x-3">
                <button class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Worker
                </button>
                <button class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Search and Filter Bar -->
            <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" placeholder="Search workers by name, specialty..." class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <select class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <select class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">All Specialties</option>
                            <option value="nursing">Nursing</option>
                            <option value="physiotherapy">Physiotherapy</option>
                            <option value="laboratory">Laboratory</option>
                            <option value="pharmacy">Pharmacy</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-gray-700">
                                <input type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            </th>
                            <th class="px-6 py-3 text-left font-medium text-gray-700">Name</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-700">Specialty</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-700">Status</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-700">Verified</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-700">Documents</th>
                            <th class="px-6 py-3 text-center font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        {{-- Dummy Data Row 1 --}}
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <input type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-purple-600">SA</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-900">Sarah Akello</div>
                                        <div class="text-gray-500">sarah.akello@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                    Registered Nurse
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Jan 15, 2025
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">Resume</span>
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">License</span>
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">Certs</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800 p-1 rounded" title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-green-600 hover:text-green-800 p-1 rounded" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800 p-1 rounded" title="Deactivate">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Dummy Data Row 2 --}}
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <input type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-green-600">JM</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-900">James Mukasa</div>
                                        <div class="text-gray-500">james.mukasa@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">
                                    Physiotherapist
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                    Pending
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Pending...
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">Resume</span>
                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded">License Missing</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800 p-1 rounded" title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-green-600 hover:text-green-800 p-1 rounded" title="Verify">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800 p-1 rounded" title="Reject">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">1</span> to <span class="font-medium">2</span> of <span class="font-medium">2</span> results
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 border border-gray-300 rounded text-sm bg-white hover:bg-gray-50 disabled:opacity-50" disabled>
                        Previous
                    </button>
                    <button class="px-3 py-1 bg-green-600 text-white rounded text-sm">1</button>
                    <button class="px-3 py-1 border border-gray-300 rounded text-sm bg-white hover:bg-gray-50 disabled:opacity-50" disabled>
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>