@extends('layouts.app')

@section('content')
    <h2>Edit Status for {{ $application->reference_code }}</h2>

    <form method="POST" action="{{ route('applications.update', $application->id) }}">
        @csrf
        @method('PUT')

        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ $application->status == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        <button type="submit">Save</button>
    </form>
@endsection
