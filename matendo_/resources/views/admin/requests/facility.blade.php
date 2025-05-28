<!-- FACILITY HIRE TAB CONTENT -->
<div x-show="activeMainTab === 'facility'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
    <!-- Facility Hire Stats Overview -->
    <div class="flex flex-wrap gap-4 mb-6">
        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div class="ml-4">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Bookings</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $facilityBookings->total() }}</p>
            </div>
        </div>

        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 dark:text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-4">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $facilityPendingCount }}</p>
            </div>
        </div>

        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-4">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $facilityApprovedCount }}</p>
            </div>
        </div>

        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div class="ml-4">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejected</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $facilityRejectedCount }}</p>
            </div>
        </div>
    </div>

    <!-- Facility Hire Table -->
    <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Reference</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Contact Person</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Facility Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Requested Date</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($facilityBookings as $booking)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $booking->reference_number }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $booking->contact_person }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $booking->facility_type }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                    @if($booking->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($booking->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $booking->created_at->format('Y-m-d') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <button @click="openOverlay({
                                    id: '{{ $booking->id }}',
                                    reference_number: '{{ $booking->reference_number }}',
                                    contact_person: '{{ $booking->contact_person }}',
                                    facility_type: '{{ $booking->facility_type }}',
                                    status: '{{ $booking->status }}',
                                    created_at: '{{ $booking->created_at->format('Y-m-d') }}'
                                })" type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">No facility bookings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Showing
                            <span class="font-medium">{{ $facilityBookings->first() }}</span>
                            to
                            <span class="font-medium">{{ $facilityBookings->last() }}</span>
                            of
                            <span class="font-medium">{{ $facilityBookings->total() }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        {{ $facilityBookings->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div x-show="showOverlay" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 z-60 flex items-center justify-center" @click.self="closeOverlay">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md" @keydown.escape="closeOverlay">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Facility Booking Details</h2>
            <div class="space-y-4">
                <p><strong>Reference:</strong> <span x-text="selectedApplication.reference_number"></span></p>
                <p><strong>Contact Person:</strong> <span x-text="selectedApplication.contact_person"></span></p>
                <p><strong>Facility Type:</strong> <span x-text="selectedApplication.facility_type"></span></p>
                <p><strong>Status:</strong> <span x-text="selectedApplication.status" class="inline-flex rounded-full px-2 py-1 text-xs font-semibold" :class="selectedApplication.status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : selectedApplication.status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'"></span></p>
                <p><strong>Requested Date:</strong> <span x-text="selectedApplication.created_at"></span></p>
            </div>
            <div class="mt-6 space-x-4">
                <form :id="'status-update-form-' + selectedApplication.id" method="POST" :action="'/facility-requests/' + selectedApplication.id + '/status'" x-ref="statusForm">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="">
                </form>
                <button @click="approveApplication(selectedApplication.id)" class="w-full md:w-auto px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600" :disabled="selectedApplication.status === 'approved'">Approve</button>
                <button @click="rejectApplication(selectedApplication.id)" class="w-full md:w-auto px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600" :disabled="selectedApplication.status === 'rejected'">Reject</button>
                <button @click="closeOverlay" class="w-full md:w-auto mt-2 md:mt-0 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">Close</button>
            </div>
        </div>
    </div>
</div>
