
@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 text-white flex flex-col">
        <div class="p-4">
            <h1 class="text-2xl font-bold">Application Dashboard</h1>
        </div>
        <nav class="flex-1 p-4">
            <ul>
                <li class="mb-2">
                    <a href="{{ route('applications.index') }}" class="block p-2 rounded hover:bg-gray-700">Application List</a>
                </li>
                <!-- Add more menu items as needed -->
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <h2 class="text-3xl font-semibold mb-4">Application List</h2>
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($applications as $app)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $app->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $app->reference_code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $app->first_name }} {{ $app->last_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $app->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ ($app->status == 'approved' ? 'bg-green-100 text-green-800' : ($app->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button class="text-blue-600 hover:text-blue-900 view-btn" 
                                        data-id="{{ $app->id }}" 
                                        data-status="{{ $app->status }}"
                                        data-ref="{{ $app->reference_code }}">View</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden" id="editModal">
        <div class="p-5 border shadow-lg rounded-md bg-white" style="width: 400px; height: 400px; display: flex; flex-direction: column;">
            <h2 class="text-2xl font-semibold mb-4" id="modalTitle">Edit Status</h2>
            <form method="POST" id="editForm" class="flex-1 flex flex-col justify-between">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status:</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="mr-2 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400" id="cancelBtn">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded hover:bg-gray-900">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const viewButtons = document.querySelectorAll('.view-btn');
    const modal = document.getElementById('editModal');
    const form = document.getElementById('editForm');
    const modalTitle = document.getElementById('modalTitle');
    const statusSelect = document.getElementById('status');
    const cancelBtn = document.getElementById('cancelBtn');

    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const status = this.getAttribute('data-status');
            const refCode = this.getAttribute('data-ref');

            // Set form action dynamically
            form.action = `/applications/${id}`;
            modalTitle.textContent = `Edit Status for ${refCode}`;
            statusSelect.value = status;

            modal.classList.remove('hidden');
        });
    });

    cancelBtn.addEventListener('click', function () {
        modal.classList.add('hidden');
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>
@endsection
