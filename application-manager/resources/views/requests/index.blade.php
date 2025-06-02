@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 text-white p-6">
        <h2 class="text-2xl font-bold mb-6">Menu</h2>
        <ul>
            <li class="mb-4"><a href="{{ route('requests.index') }}" class="text-lg hover:text-gray-300">Requests</a></li>
            <li class="mb-4"><a href="{{ route('dashboard') }}" class="text-lg hover:text-gray-300">Dashboard</a></li>
            <li class="mb-4"><a href="#" class="text-lg hover:text-gray-300">Settings</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-8">
        <!-- Facility Requests -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Facility Requests</h2>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-3 px-6 text-left font-semibold text-gray-700">Ref</th>
                            <th class="py-3 px-6 text-left font-semibold text-gray-700">Facility</th>
                            <th class="py-3 px-6 text-left font-semibold text-gray-700">Contact</th>
                            <th class="py-3 px-6 text-left font-semibold text-gray-700">Status</th>
                            <th class="py-3 px-6 text-left font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($facilities as $req)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-4 px-6">{{ $req->reference_number }}</td>
                                <td class="py-4 px-6">{{ $req->facility_name }}</td>
                                <td class="py-4 px-6">{{ $req->contact_person }}</td>
                                <td class="py-4 px-6">
                                    <span class="px-2 py-1 rounded-full text-sm {{ $req->status == 'approved' ? 'bg-green-100 text-green-800' : ($req->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <button onclick="openOverlay('facility', {{ $req->id }}, '{{ $req->status }}')" class="text-blue-600 hover:text-blue-800">View/Edit</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Individual Requests -->
        <div>
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Individual Requests</h2>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-3 px-6 text-left font-semibold text-gray-700">Ref</th>
                            <th class="py-3 px-6 text-left font-semibold text-gray-700">Individual</th>
                            <th class="py-3 px-6 text-left font-semibold text-gray-700">Contact</th>
                            <th class="py-3 px-6 text-left font-semibold text-gray-700">Status</th>
                            <th class="py-3 px-6 text-left font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($individuals as $req)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-4 px-6">{{ $req->reference_number }}</td>
                                <td class="py-4 px-6">{{ $req->individual_name }}</td>
                                <td class="py-4 px-6">{{ $req->contact_person }}</td>
                                <td class="py-4 px-6">
                                    <span class="px-2 py-1 rounded-full text-sm {{ $req->status == 'approved' ? 'bg-green-100 text-green-800' : ($req->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <button onclick="openOverlay('individual', {{ $req->id }}, '{{ $req->status }}')" class="text-blue-600 hover:text-blue-800">View/Edit</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Overlay -->
<div id="editOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96 h-96 flex flex-col">
        <h2 id="overlayTitle" class="text-xl font-semibold mb-4"></h2>
        <form id="editForm" method="POST" class="flex-1 flex flex-col justify-between">
            @csrf
            @method('PUT')
            <div>
                <label for="status" class="block text-gray-700 mb-2">Status:</label>
                <select name="status" id="status" class="w-full p-2 border rounded mb-4">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeOverlay()" class="mr-4 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openOverlay(type, id, status) {
    const overlay = document.getElementById('editOverlay');
    const form = document.getElementById('editForm');
    const title = document.getElementById('overlayTitle');
    const statusSelect = document.getElementById('status');

    // Set form action based on type
    form.action = type === 'facility' 
        ? "{{ route('requests.facility.update', ':id') }}".replace(':id', id)
        : "{{ route('requests.individual.update', ':id') }}".replace(':id', id);

    // Set title
    title.textContent = `Edit Status – ${type.charAt(0).toUpperCase() + type.slice(1)} Request`;

    // Set selected status
    statusSelect.value = status;

    // Show overlay
    overlay.classList.remove('hidden');
}

function closeOverlay() {
    document.getElementById('editOverlay').classList.add('hidden');
}
</script>

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
@endsection
@endsection