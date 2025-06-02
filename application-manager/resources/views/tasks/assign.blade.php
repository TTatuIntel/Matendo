<h2 style="color: #2c3e50;">Assign {{ ucfirst($type) }} Task</h2>

<form method="POST" action="{{ route('tasks.assign') }}">
    @csrf

    <input type="hidden" name="task_type" value="{{ $type }}">
    <input type="hidden" name="task_id" value="{{ $task->id }}">

    <p><strong>Task Name:</strong> {{ $type === 'facility' ? $task->facility_name : $task->individual_name }}</p>

    <label>Assign To:</label><br>
    <select name="assigned_to" required style="width: 100%; padding: 10px; margin-top: 8px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 4px;">
        @foreach($workers as $worker)
            <option value="{{ $worker->id }}">{{ $worker->first_name }} {{ $worker->last_name }} ({{ $worker->email }})</option>
        @endforeach
    </select>

    <button type="submit" style="background-color: #27ae60; color: white; padding: 10px 20px; border: none; border-radius: 4px;">Assign</button>
</form>
