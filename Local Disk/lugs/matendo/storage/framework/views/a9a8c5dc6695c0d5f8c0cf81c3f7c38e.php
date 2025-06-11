<!-- resources/views/layouts/app.blade.php (or wherever x-app-layout is defined) -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
        .smooth-transition { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.1); }
        .btn-primary {
            background: linear-gradient(135deg, #10b981, #14b8a6);
            border: 1px solid #047857;
            color: white;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #059669, #0d9488);
            box-shadow: 0 2px 8px rgba(5,150,105,0.2);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: 1px solid #1e40af;
            color: white;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .btn-secondary:hover {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            box-shadow: 0 2px 8px rgba(59,130,246,0.2);
        }
        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: 1px solid #b91c1c;
            color: white;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            box-shadow: 0 2px 8px rgba(220,38,38,0.2);
        }
        .modal-backdrop {
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(2px);
        }
        .table-row:hover { background: #f0fdfa; }
        .modal-content {
            max-height: 80vh;
            overflow-y: auto;
            scrollbar-width: thin;
        }
        .modal-content::-webkit-scrollbar {
            width: 6px;
        }
        .modal-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-6">
         <?php $__env->slot('header', null, []); ?> 
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
                        Welcome back, <?php echo e(Auth::user()->name); ?> 👋
                    </h2>
                    <p class="text-sm text-gray-600">
                        Here's what's happening with your admin dashboard today.
                    </p>
                </div>
            </div>
         <?php $__env->endSlot(); ?>

        <div
            x-data="{
                activeTab: (new URLSearchParams(window.location.search)).get('tab') || 'dashboard',
                tabs: ['dashboard', 'applications', 'facility', 'individual', 'healthworkers', 'tasks', 'settings', 'reports'],
                focusNext(index) {
                    let nit = (index + 1) % this.tabs.length;
                    this.$refs['tab-' + this.tabs[next]][0].focus();
                },
                focusPrev(index) {
                    let prev = (index - 1 + this.tabs.length) % this.tabs.length;
                    this.$refs['tab-' + this.tabs[prev]][0].focus();
                }
            }"
            role="region"
            aria-label="Admin Dashboard"
        >
            <!-- Main Tab Navigation -->
            <div role="tablist" class="flex flex-wrap gap-4 border-b border-gray-200 mb-6">
                <template x-for="(tab, index) in tabs" :key="tab">
                    <button
                        role="tab"
                        :aria-selected="activeTab === tab"
                        :tabindex="activeTab === tab ? '0' : '-1'"
                        :id="`tab-${tab}`"
                        :aria-controls="`panel-${tab}`"
                        x-ref="'tab-' + tab"
                        @click="activeTab = tab"
                        @keydown.arrow-right.prevent="focusNext(index)"
                        @keydown.arrow-left.prevent="focusPrev(index)"
                        :class="{
                            'btn-primary font-semibold': activeTab === tab,
                            'text-gray-600 hover:text-gray-900 hover:bg-gray-100': activeTab !== tab
                        }"
                        class="px-4 py-2 text-sm rounded-full smooth-transition focus:outline-none focus:ring-2 focus:ring-green-400"
                    >
                        <span x-text="
                            tab.charAt(0).toUpperCase() +
                            tab.slice(1).replace(/([a-z])([A-Z])/g, '$1 $2').replace(/_/g, ' ')
                        "></span>
                    </button>
                </template>
            </div>

            <!-- Tab Panels -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Dashboard Tab -->
                <div x-show="activeTab === 'dashboard'" role="tabpanel" id="panel-dashboard">
                    <div class="p-6 space-y-8">
                        <!-- Flash Messages -->
                        <?php if(session('success')): ?>
                            <div class="bg-green-50 border-l-4 border-green-400 text-green-800 p-3 rounded-md text-sm">
                                <?php echo e(session('success')); ?>

                            </div>
                        <?php endif; ?>
                        <?php if(session('error')): ?>
                            <div class="bg-red-50 border-l-4 border-red-400 text-red-800 p-3 rounded-md text-sm">
                                <?php echo e(session('error')); ?>

                            </div>
                        <?php endif; ?>

                        <!-- Dashboard Overview Section -->
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                                <span class="w-1.5 h-7 bg-green-600 rounded-full mr-3"></span>
                                Dashboard Overview
                            </h2>
                            
                            <div class="flex space-x-6 overflow-x-auto pb-4 mb-8">
                                <!-- Total Applications Card -->
                                <div class="min-w-[280px] flex-shrink-0 p-6 bg-white rounded-lg shadow-sm card-hover smooth-transition border border-gray-100">
                                    <div class="flex justify-between items-center mb-3">
                                        <h3 class="font-semibold text-blue-700 text-base flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Total Applications
                                        </h3>
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                            <?php echo e($totalApplications ?? 123); ?>

                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">Applications received since platform launch.</p>
                                    <div class="text-xs text-gray-600">
                                        <p>New this month: <span class="font-semibold"><?php echo e($newApplicationsThisMonth ?? 15); ?></span></p>
                                    </div>
                                </div>

                                <!-- Facility Requests Card -->
                                <div class="min-w-[280px] flex-shrink-0 p-6 bg-white rounded-lg shadow-sm card-hover smooth-transition border border-gray-100">
                                    <div class="flex justify-between items-center mb-3">
                                        <h3 class="font-semibold text-indigo-700 text-base flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Facility Requests
                                        </h3>
                                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">
                                            <?php echo e($facilityRequestsCount ?? 45); ?>

                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">Requests from facilities for health staff.</p>
                                    <div class="text-xs text-gray-600">
                                        <p>Open requests: <span class="font-semibold"><?php echo e($openRequests ?? 12); ?></span></p>
                                    </div>
                                </div>

                                <!-- Active Health Workers Card -->
                                <div class="min-w-[280px] flex-shrink-0 p-6 bg-white rounded-lg shadow-sm card-hover smooth-transition border border-gray-100">
                                    <div class="flex justify-between items-center mb-3">
                                        <h3 class="font-semibold text-purple-700 text-base flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Active Health Workers
                                        </h3>
                                        <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                                            <?php echo e($activeHealthWorkers ?? 78); ?>

                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">Verified professionals available on platform.</p>
                                    <div class="text-xs text-gray-600">
                                        <p>New verifications: <span class="font-semibold"><?php echo e($newVerifications ?? 8); ?></span></p>
                                    </div>
                                </div>

                                <!-- Pending Tasks Card -->
                                <div class="min-w-[280px] flex-shrink-0 p-6 bg-white rounded-lg shadow-sm card-hover smooth-transition border border-gray-100">
                                    <div class="flex justify-between items-center mb-3">
                                        <h3 class="font-semibold text-yellow-700 text-base flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Pending Tasks
                                        </h3>
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                            <?php echo e($pendingTasksCount ?? 10); ?>

                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">Tasks awaiting action and completion.</p>
                                    <div class="text-xs text-gray-600">
                                        <p>Oldest task: <span class="font-semibold"><?php echo e($oldestPendingTask ?? '3 days'); ?></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activities Section -->
                        <div>
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-semibold text-gray-900 flex items-center">
                                    <span class="w-1.5 h-7 bg-green-600 rounded-full mr-3"></span>
                                    Recent Activities
                                </h2>
                                <div class="flex space-x-3">
                                    <button class="btn-primary flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Refresh
                                    </button>
                                    <button class="btn-secondary flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Export
                                    </button>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                                <!-- Activities List -->
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-medium text-gray-700">Activity</th>
                                                <th class="px-4 py-3 text-left font-medium text-gray-700">Type</th>
                                                <th class="px-4 py-3 text-left font-medium text-gray-700">Status</th>
                                                <th class="px-4 py-3 text-left font-medium text-gray-700">Time</th>
                                                <th class="px-4 py-3 text-center font-medium text-gray-700">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <!-- Recent Activity Row 1 -->
                                            <tr class="table-row smooth-transition">
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"></path>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="font-medium text-gray-900">New Application from Jane Doe</div>
                                                            <div class="text-gray-600">Registered Nurse application</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                        Application
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                        Pending Review
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-gray-600">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 text-gray-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        2 hours ago
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="flex justify-center space-x-2">
                                                        <button class="btn-primary flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                            View Details
                                                        </button>
                                                        <button class="btn-primary flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            Review
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Recent Activity Row 2 -->
                                            <tr class="table-row smooth-transition">
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd"></path>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="font-medium text-gray-900">Facility Request from Clinic XYZ</div>
                                                            <div class="text-gray-600">Urgent request for 2 nurses</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">
                                                        Facility Request
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                        Open
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-gray-600">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 text-gray-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        4 hours ago
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="flex justify-center space-x-2">
                                                        <button class="btn-primary flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                            View Details
                                                        </button>
                                                        <button class="btn-primary flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                            Manage
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="px-4 py-3 border-t border-gray-200">
                                    <?php echo e($applications->links()); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Applications Tab -->
                <div x-show="activeTab === 'applications'" role="tabpanel" id="panel-applications">
                    <?php echo $__env->make('admin.partials._applications', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <!-- Facility Tab -->
                <div x-show="activeTab === 'facility'" role="tabpanel" id="panel-facility">
                    <?php echo $__env->make('admin.partials._facility', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <!-- Individual Tab -->
                <div x-show="activeTab === 'individual'" role="tabpanel" id="panel-individual">
                    <?php echo $__env->make('admin.partials._individual', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <!-- Health Workers Tab -->
                <div x-show="activeTab === 'healthworkers'" role="tabpanel" id="panel-healthworkers">
                    <?php echo $__env->make('admin.partials._healthworkers', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <!-- Tasks Tab -->
                <div x-show="activeTab === 'tasks'" role="tabpanel" id="panel-tasks">
                    <?php echo $__env->make('admin.partials._tasks', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <!-- Settings Tab -->
                <div x-show="activeTab === 'settings'" role="tabpanel" id="panel-settings">
                    <?php echo $__env->make('admin.partials._settings', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <!-- Reports Tab -->
                <div x-show="activeTab === 'reports'" role="tabpanel" id="panel-reports">
                    <?php echo $__env->make('admin.partials._reports', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH D:\lugs\matendo\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>