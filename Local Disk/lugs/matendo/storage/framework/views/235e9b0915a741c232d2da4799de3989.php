<ul class="space-y-4 max-w-xl">
    <?php $__empty_1 = true; $__currentLoopData = $adminTasks ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <li class="p-4 bg-white rounded-lg shadow hover:shadow-lg transition-shadow flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-700"><?php echo e($task['title']); ?></h3>
                <p class="text-sm text-gray-500">Due: <?php echo e($task['due_date']); ?></p>
            </div>
            <button class="text-blue-600 hover:text-blue-800 font-semibold">Start</button>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <li class="p-4 bg-white rounded-lg shadow hover:shadow-lg transition-shadow flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-700">Review 5 pending applications</h3>
                <p class="text-sm text-gray-500">Due: 2 days from now</p>
            </div>
            <button class="text-blue-600 hover:text-blue-800 font-semibold">Start</button>
        </li>
        <li class="p-4 bg-white rounded-lg shadow hover:shadow-lg transition-shadow flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-700">Verify health worker credentials</h3>
                <p class="text-sm text-gray-500">Due: Tomorrow</p>
            </div>
            <button class="text-blue-600 hover:text-blue-800 font-semibold">Start</button>
        </li>
        <li class="p-4 bg-white rounded-lg shadow hover:shadow-lg transition-shadow flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-700">Respond to facility requests</h3>
                <p class="text-sm text-gray-500">Due: 1 day from now</p>
            </div>
            <button class="text-blue-600 hover:text-blue-800 font-semibold">Start</button>
        </li>
    <?php endif; ?>
</ul><?php /**PATH D:\lugs\matendo\resources\views/admin/partials/_tasks.blade.php ENDPATH**/ ?>