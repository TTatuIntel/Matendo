@extends('layouts.app')

@section('content')
<style>
    .main-container {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 220px;
        background-color: #2c3e50;
        color: white;
        padding: 20px;
    }

    .sidebar a {
        color: white;
        display: block;
        margin-bottom: 15px;
        text-decoration: none;
        font-weight: bold;
    }

    .content {
        flex: 1;
        padding: 30px;
        background-color: #f9f9f9;
    }

    h2 {
        color: #2c3e50;
        margin-top: 30px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        margin-top: 10px;
        margin-bottom: 30px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: #34495e;
        color: white;
    }

    td button {
        background-color: #3498db;
        border: none;
        padding: 8px 12px;
        color: white;
        border-radius: 4px;
        cursor: pointer;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.6);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: white;
        padding: 30px;
        width: 600px;
        border-radius: 6px;
        position: relative;
    }

    .close {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 20px;
        cursor: pointer;
        color: #aaa;
    }

    .close:hover {
        color: black;
    }
</style>

<div class="main-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Menu</h3>
        <a href="{{ route('dashboard') }}">Back to Dashboard</a>
    </div>

    <!-- Content Area -->
    <div class="content">
        <h2>Facility Requests</h2>
        <table>
            <tr><th>Ref</th><th>Name</th><th>Action</th></tr>
            @foreach($facilities as $req)
                <tr>
                    <td>{{ $req->reference_number }}</td>
                    <td>{{ $req->facility_name }}</td>
                    <td><button onclick="openModal('{{ route('tasks.form', ['type' => 'facility', 'id' => $req->id]) }}')">View</button></td>
                </tr>
            @endforeach
        </table>

        <h2>Individual Requests</h2>
        <table>
            <tr><th>Ref</th><th>Name</th><th>Action</th></tr>
            @foreach($individuals as $req)
                <tr>
                    <td>{{ $req->reference_number }}</td>
                    <td>{{ $req->individual_name }}</td>
                    <td><button onclick="openModal('{{ route('tasks.form', ['type' => 'individual', 'id' => $req->id]) }}')">View</button></td>
                </tr>
            @endforeach
        </table>
    </div>
</div>

<!-- Modal Overlay -->
<div id="formModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="modal-body">Loading...</div>
    </div>
</div>

<script>
    function openModal(url) {
        const modal = document.getElementById('formModal');
        const modalBody = document.getElementById('modal-body');
        modal.style.display = 'flex';
        modalBody.innerHTML = 'Loading...';

        fetch(url)
            .then(response => response.text())
            .then(html => {
                modalBody.innerHTML = html;
            })
            .catch(err => {
                modalBody.innerHTML = '<p>Error loading form.</p>';
            });
    }

    function closeModal() {
        document.getElementById('formModal').style.display = 'none';
    }

    // Close modal on background click
    window.onclick = function(e) {
        const modal = document.getElementById('formModal');
        if (e.target === modal) closeModal();
    }
</script>
@endsection
