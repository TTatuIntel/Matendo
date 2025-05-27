<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Applications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">{{ __('All Applications') }}</h3>
                        <div>
                            <label for="status" class="mr-2">{{ __('Filter by Status:') }}</label>
                            <select id="status" name="status" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value)" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="{{ route('admin.applications.index') }}" {{ $status == 'all' ? 'selected' : '' }}>All</option>
                                <option value="{{ route('admin.applications.index', ['status' => 'pending']) }}" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="{{ route('admin.applications.index', ['status' => 'approved']) }}" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="{{ route('admin.applications.index', ['status' => 'rejected']) }}" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Success!</strong> {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Error!</strong> {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.applications.bulk-action') }}" class="mb-4">
                        @csrf
                        <div class="flex items-center space-x-2">
                            <label for="bulk_action" class="mr-2">{{ __('Bulk Action:') }}</label>
                            <select id="bulk_action" name="action" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">{{ __('Select Action') }}</option>
                                <option value="approve">{{ __('Approve') }}</option>
                                <option value="reject">{{ __('Reject') }}</option>
                            </select>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-400 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-500 focus:bg-indigo-700 dark:focus:bg-indigo-500 active:bg-indigo-900 dark:active:bg-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Apply') }}
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <input type="checkbox" id="select_all" class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Reference') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Name') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Profession') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Status') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Applied Date') }}
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">{{ __('View') }}</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse ($applications as $application)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="applications[]" value="{{ $application->id }}" class="application-checkbox rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $application->reference_code }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $application->first_name }} {{ $application->last_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $application->profession }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $application->status == 'approved' ? 'green' : ($application->status == 'rejected' ? 'red' : 'yellow') }}-100 text-{{ $application->status == 'approved' ? 'green' : ($application->status == 'rejected' ? 'red' : 'yellow') }}-800">
                                                    {{ ucfirst($application->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $application->created_at->format('Y-m-d') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.applications.show', $application) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200">{{ __('View') }}</a>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">
                                                {{ __('No applications found.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <div class="mt-4">
                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('select_all').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.application-checkbox');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = this.checked;
            }, this);
        });
    </script>
</x-app-layout>
