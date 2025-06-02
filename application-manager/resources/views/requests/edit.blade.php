@extends('layouts.app')

@section('content')
    <h2>Edit Status – {{ ucfirst($type) }} Request</h2>
    <form method="POST" action="{{ $type == 'facility' ? route('requests.facility.update', $request->id) : route('requests.individual.update', $request->id) }}">
        @csrf
        @method('PUT')

        <label>Status:</label>
        <select name="status">
            <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ $request->status == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        <br><br>
        <button type="submit">Save</button>
    </form>
@endsection
