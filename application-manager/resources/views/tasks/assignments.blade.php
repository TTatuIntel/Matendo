
@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar Menu -->
    <div class="w-64 bg-gray-800 text-white p-6">
        <h1 class="text-2xl font-bold mb-8">Task Manager</h1>
        <nav>
            <ul>
                <li class="mb-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center p-2 rounded hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li class="mb-4">
                    <a href="{{ route('tasks.index') }}" class="flex items-center p-2 rounded hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Assign Tasks
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-8">
        <h2 class="text-2xl font-bold mb-6">Task Assignments</h2>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-6 text-left">ID</th>
                        <th class="py-3 px-6 text-left">Task Type</th>
                        <th class="py-3 px-6 text-left">Task Name</th>
                        <th class="py-3 px-6 text-left">Assigned To</th>
                        <th class="py-3 px-6 text-left">Created At</th>
                        <th class="py-3 px-6 text-left">Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr class="border-b">
                            <td class="py-3 px-6">{{ $assignment->id }}</td>
                            <td class="py-3 px-6">{{ ucfirst($assignment->task_type) }}</td>
                            <td class="py-3 px-6">
                                @if($assignment->task_type === 'facility' && $assignment->facilityRequest)
                                    {{ $assignment->facilityRequest->facility_name }}
                                @elseif($assignment->task_type === 'individual' && $assignment->individualRequest)
                                    {{ $assignment->individualRequest->individual_name }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="py-3 px-6">
                                {{ $assignment->user ? $assignment->user->first_name . ' ' . $assignment->user->last_name . ' (' . $assignment->user->email . ')' : 'N/A' }}
                            </td>
                            <td class="py-3 px-6">{{ $assignment->created_at ? $assignment->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            <td class="py-3 px-6">{{ $assignment->updated_at ? $assignment->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-3 px-6 text-center">No assignments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
@endsection
