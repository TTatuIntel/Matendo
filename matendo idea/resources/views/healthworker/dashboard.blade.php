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
                    {{ __("You're logged in!") }}

                    <h3 class="mt-4 text-lg">Health Worker Stats</h3>
                    <p>Total: {{ $stats['total'] }}</p>
                    <p>Verified: {{ $stats['verified'] }}</p>
                    <p>Unverified: {{ $stats['unverified'] }}</p>

                    <!-- Display Assigned Tasks -->
                    <h3 class="mt-4 text-lg">Your Assigned Tasks</h3>
                    @if($assignedTasks->isNotEmpty())
                        <ul class="list-disc pl-5 mt-2">
                            @foreach($assignedTasks as $task)
                                <li>
                                    <strong>{{ $task->title }}</strong>
                                    <p>Description: {{ $task->description ?? 'No description' }}</p>
                                    <p>Due Date: {{ $task->due_date }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-2">No tasks assigned yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
