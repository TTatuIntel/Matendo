<div class="flex space-x-8 overflow-x-auto pb-4">
    <div class="min-w-[320px] flex-shrink-0 p-6 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
        <h3 class="font-bold text-blue-600 mb-3">Open Requests</h3>
        <p class="text-4xl font-bold"><?php echo e($openFacilityRequests ?? 12); ?></p>
        <p class="text-gray-600 mb-2">Requests that require health worker allocation.</p>
        <p class="text-sm text-gray-500">Average response time: <span class="font-semibold"><?php echo e($facilityAvgResponseTime ?? '2 days'); ?></span></p>
        <p class="text-sm text-gray-500">Urgent requests: <span class="font-semibold text-red-600"><?php echo e($urgentRequests ?? 3); ?></span></p>
    </div>
    <div class="min-w-[320px] flex-shrink-0 p-6 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
        <h3 class="font-bold text-gray-700 mb-3">Closed Requests</h3>
        <p class="text-4xl font-bold"><?php echo e($closedFacilityRequests ?? 33); ?></p>
        <p class="text-gray-600 mb-2">Requests that have been fulfilled and closed.</p>
        <p class="text-sm text-gray-500">Average closure time: <span class="font-semibold"><?php echo e($avgClosureTime ?? '5 days'); ?></span></p>
    </div>
    <div class="min-w-[320px] flex-shrink-0 p-6 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
        <h3 class="font-bold text-purple-600 mb-3">Pending Approvals</h3>
        <p class="text-4xl font-bold"><?php echo e($pendingApprovals ?? 5); ?></p>
        <p class="text-gray-600 mb-2">Requests awaiting facility or admin approval.</p>
        <p class="text-sm text-gray-500">Longest pending: <span class="font-semibold"><?php echo e($longestPending ?? '4 days'); ?></span></p>
    </div>
</div>
<?php /**PATH D:\lugs\matendo\resources\views/admin/partials/_facility.blade.php ENDPATH**/ ?>