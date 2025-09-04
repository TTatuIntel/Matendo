@extends('admin.layout')
@section('title', 'User Audit Logs')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">User Audit Logs - {{ $user->name ?? 'Unknown User' }}</h1>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-600">User audit logs feature is being developed.</p>
    </div>
</div>
@endsection
