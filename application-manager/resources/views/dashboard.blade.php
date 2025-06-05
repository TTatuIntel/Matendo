@extends('layouts.app')

@section('content')

@php
    use App\Models\FacilityRequest;
    use App\Models\IndividualRequest;

    $taskAssignments = auth()->user()->taskAssignments;
@endphp
    <div style="display: flex; gap: 20px;">
        {{-- Sidebar Navigation --}}
        <nav style="width: 200px; background-color: #f3f3f3; padding: 10px;">
            <ul style="list-style: none; padding: 0;">
                <li><a href="?section=profile">👤 Profile</a></li>
                <li><a href="/applications">📄 Applications</a></li>
                <li><a href="/requests">📬 Requests</a></li>
                <li><a href="/assignments">✅ Assignments</a></li>
            </ul>
        </nav>

        {{-- Main Content Area --}}
        <div style="flex: 1; padding: 10px;">
            @php
                $section = request('section', 'dashboard');
            @endphp

            @if($section === 'profile')
                <h2>👤 Profile</h2>
                <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
                <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                <p><strong>Role:</strong> {{ auth()->user()->role }}</p>
                <p><strong>Task 1:</strong> {{ auth()->user()->task1 ?? 'No Task Assigned' }}</p>
                <p><strong>Task 2:</strong> {{ auth()->user()->task2 ?? 'No Task Assigned' }}</p>
            @else
                <h2>Welcome, {{ auth()->user()->name }}</h2>
                <p>This is your user dashboard.</p>
            <h4>Your Assigned Tasks</h4>
            <ul>
                @forelse ($taskAssignments as $assignment)
                    @php
                        if ($assignment->task_type === 'facility') {
                            $task = FacilityRequest::find($assignment->task_id);
                            $title = $task->facility_name ?? 'Unknown Facility';
                        } elseif ($assignment->task_type === 'individual') {
                            $task = IndividualRequest::find($assignment->task_id);
                            $title = $task->individual_name ?? 'Unknown Individual';
                        } else {
                            $title = 'Unknown Task Type';
                        }
                    @endphp

                    <li><strong>{{ ucfirst($assignment->task_type) }} Task:</strong> {{ $title }}</li>
                @empty
                    <li>No tasks assigned</li>
                @endforelse
            </ul>


            @endif
        </div>
    </div>
@endsection
