resources/views/admin/dashboard.blade.php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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
        .table-row:hover { background: #f0fdfa; }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-6">
        <header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
                        Welcome back, {{ Auth::user()->name }} 👋
                    </h2>
                    <p class="text-sm text-gray-600">
                        Here's what's happening with your admin dashboard today.
                    </p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-danger flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <main x-data="{
            activeTab: (new URLSearchParams(window.location.search)).get('tab') || 'dashboard',
            tabs: ['dashboard', 'applications', 'facility', 'individual', 'healthworkers', 'tasks', 'settings', 'reports'],
            focusNext(index) {
                let next = (index + 1) % this.tabs.length;
                this.$refs['tab-' + this.tabs[next]].focus();
            },
            focusPrev(index) {
                let prev = (index - 1 + this.tabs.length) % this.tabs.length;
                this.$refs['tab-' + this.tabs[prev]].focus();
            },
            async approveApplication(id, tab) {
                if (confirm('Approve this application?')) {
                    try {
                        const response = await fetch(`/admin/applications/${id}/approve`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Content-Type': 'application/json',
                            },
                        });
                        const data = await response.json();
                        if (data.success) {
                            alert(data.message);
                            window.location.href = `/admin/dashboard?tab=${tab}`;
                        } else {
                            alert(data.message);
                        }
                    } catch (error) {
                        alert('Error approving application.');
                    }
                }
            },
            async rejectApplication(id, tab) {
                if (confirm('Reject this application?')) {
                    try {
                        const response = await fetch(`/admin/applications/${id}/reject`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Content-Type': 'application/json',
                            },
                        });
                        const data = await response.json();
                        if (data.success) {
                            alert(data.message);
                            window.location.href = `/admin/dashboard?tab=${tab}`;
                        } else {
                            alert(data.message);
                        }
                    } catch (error) {
                        alert('Error rejecting application.');
                    }
                }
            },
            exportCsv() {
                window.location.href = '/admin/applications/export';
            }
        }" role="region" aria-label="Admin Dashboard">

            <!-- Tab Navigation -->
            <nav role="tablist" class="flex flex-wrap gap-4 border-b border-gray-200 mb-6">
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
            </nav>

            <!-- Tab Panels -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Dashboard Tab -->
                <section x-show="activeTab === 'dashboard'" x-cloak role="tabpanel" id="panel-dashboard" class="p-6 space-y-8">
                    @if (session('success'))
                        <div class="bg-green-50 border-l-4 border-green-400 text-green-800 p-3 rounded-md text-sm">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-50 border-l-4 border-red-400 text-red-800 p-3 rounded-md text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Dashboard Overview -->
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                            <span class="w-1.5 h-7 bg-green-600 rounded-full mr-3"></span>
                            Dashboard Overview
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="p-6 bg-white rounded-lg shadow-sm card-hover smooth-transition border border-gray-100">
                                <h3 class="font-semibold text-blue-700 text-base">Total Applications</h3>
                                <p class="text-2xl font-bold">{{ $totalApplications ?? 123 }}</p>
                                <p class="text-xs text-gray-600">New this month: {{ $newApplicationsThisMonth ?? 15 }}</p>
                            </div>
                            <div class="p-6 bg-white rounded-lg shadow-sm card-hover smooth-transition border border-gray-100">
                                <h3 class="font-semibold text-indigo-700 text-base">Facility Requests</h3>
                                <p class="text-2xl font-bold">{{ $facilityRequestsCount ?? 45 }}</p>
                                <p class="text-xs text-gray-600">Open requests: {{ $openRequests ?? 12 }}</p>
                            </div>
                            <div class="p-6 bg-white rounded-lg shadow-sm card-hover smooth-transition border border-gray-100">
                                <h3 class="font-semibold text-purple-700 text-base">Active Health Workers</h3>
                                <p class="text-2xl font-bold">{{ $activeHealthWorkers ?? 78 }}</p>
                                <p class="text-xs text-gray-600">New verifications: {{ $newVerifications ?? 8 }}</p>
                            </div>
                            <div class="p-6 bg-white rounded-lg shadow-sm card-hover smooth-transition border border-gray-100">
                                <h3 class="font-semibold text-yellow-700 text-base">Pending Tasks</h3>
                                <p class="text-2xl font-bold">{{ $pendingTasksCount ?? 10 }}</p>
                                <p class="text-xs text-gray-600">Oldest task: {{ $oldestPendingTask ?? '3 days' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div>
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-gray-900 flex items-center">
                                <span class="w-1.5 h-7 bg-green-600 rounded-full mr-3"></span>
                                Recent Activities
                            </h2>
                            <div class="flex space-x-3">
                                <button @click="window.location.reload()" class="btn-primary flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Refresh
                                </button>
                                <button @click="exportCsv()" class="btn-secondary flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export
                                </button>
                            </div>
                        </div>
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
                                    @foreach ($applications as $application)
                                        <tr class="table-row smooth-transition">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="font-medium text-gray-900">{{ $application->name }}</div>
                                                        <div class="text-gray-600">{{ $application->description }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                    {{ $application->type }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full"
                                                    :class="{
                                                        'bg-yellow-100 text-yellow-800': '{{ $application->status }}' === 'pending',
                                                        'bg-green-100 text-green-800': '{{ $application->status }}' === 'approved',
                                                        'bg-red-100 text-red-800': '{{ $application->status }}' === 'rejected'
                                                    }">
                                                    {{ ucfirst($application->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-600">
                                                {{ $application->created_at->diffForHumans() }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex justify-center space-x-2">
                                                    <button @click="approveApplication({{ $application->id }}, activeTab)" class="btn-primary">Approve</button>
                                                    <button @click="rejectApplication({{ $application->id }}, activeTab)" class="btn-danger">Reject</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 py-3 border-t border-gray-200">
                            {{ $applications->links() }}
                        </div>
                    </div>
                </section>

                <!-- Applications Tab -->
                <section x-show="activeTab === 'applications'" x-cloak role="tabpanel" id="panel-applications" class="p-6">
                    @include('admin.partials._applications')
                </section>

                <!-- Facility Tab -->
                <section x-show="activeTab === 'facility'" x-cloak role="tabpanel" id="panel-facility" class="p-6">
                    @include('admin.partials._facility')
                </section>

                <!-- Individual Tab -->
                <section x-show="activeTab === 'individual'" x-cloak role="tabpanel" id="panel-individual" class="p-6">
                    @include('admin.partials._individual')
                </section>

                <!-- Health Workers Tab -->
                <section x-show="activeTab === 'healthworkers'" x-cloak role="tabpanel" id="panel-healthworkers" class="p-6">
                    @include('admin.partials._healthworkers')
                </section>

                <!-- Tasks Tab -->
                <section x-show="activeTab === 'tasks'" x-cloak role="tabpanel" id="panel-tasks" class="p-6">
                    @include('admin.partials._tasks')
                </section>

                <!-- Settings Tab -->
                <section x-show="activeTab === 'settings'" x-cloak role="tabpanel" id="panel-settings" class="p-6">
                    @include('admin.partials._settings')
                </section>

                <!-- Reports Tab -->
                <section x-show="activeTab === 'reports'" x-cloak role="tabpanel" id="panel-reports" class="p-6">
                    @include('admin.partials._reports')
                </section>
            </div>
        </main>
    </div>
</body>
</html>
