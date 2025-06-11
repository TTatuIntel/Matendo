<div class="flex space-x-8 overflow-x-auto pb-4">
    <div class="min-w-[320px] flex-shrink-0 p-6 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
        <h3 class="font-bold text-green-600 mb-3">Active Requests</h3>
        <p class="text-4xl font-bold">{{ $activeIndividualRequests ?? 20 }}</p>
        <p class="text-gray-600 mb-2">Requests made by patients or individuals.</p>
        <p class="text-sm text-gray-500">Average fulfillment time: <span class="font-semibold">{{ $avgFulfillmentTime ?? '3 days' }}</span></p>
    </div>
    <div class="min-w-[320px] flex-shrink-0 p-6 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
        <h3 class="font-bold text-yellow-600 mb-3">Pending</h3>
        <p class="text-4xl font-bold">{{ $pendingIndividualRequests ?? 7 }}</p>
        <p class="text-gray-600 mb-2">Requests awaiting matching with health workers.</p>
        <p class="text-sm text-gray-500">Oldest pending: <span class="font-semibold">{{ $oldestIndividualPending ?? '6 days' }}</span></p>
    </div>
    <div class="min-w-[320px] flex-shrink-0 p-6 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
        <h3 class="font-bold text-red-600 mb-3">Cancelled</h3>
        <p class="text-4xl font-bold">{{ $cancelledIndividualRequests ?? 3 }}</p>
        <p class="text-gray-600 mb-2">Requests cancelled by users or system.</p>
        <p class="text-sm text-gray-500">Common reasons: <span class="font-semibold">{{ $cancelReasons ?? 'Change of plans, duplicates' }}</span></p>
    </div>
</div>