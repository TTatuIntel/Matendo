@extends('admin.layout')
@section('title', 'Care Quality Management')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Care Quality Management</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-sm font-medium text-gray-500">Total Patients</p>
            <p class="text-2xl font-bold text-blue-600">{{ $metrics['total_patients'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-sm font-medium text-gray-500">Monitored Patients</p>
            <p class="text-2xl font-bold text-green-600">{{ $metrics['monitored_patients'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-sm font-medium text-gray-500">Missing Vitals</p>
            <p class="text-2xl font-bold text-red-600">{{ $metrics['missing_vitals'] ?? 0 }}</p>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-600">Care quality management features are being developed.</p>
    </div>
</div>
@endsection
