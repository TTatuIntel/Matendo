@extends('admin.layout')

@section('title', 'Backup Management')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="medical-card p-4 sm:p-6 fade-in">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">Backup Management Center</h1>
                        <p class="text-sm text-gray-600 font-medium">Create, manage, and restore system backups for data protection</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 lg:gap-3">
                    <button onclick="showCreateBackupModal()" class="btn-primary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Backup
                    </button>
                    <a href="{{ route('admin.backup.schedule') }}" class="btn-secondary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Schedule Backups
                    </a>
                    <button onclick="showCleanupModal()" class="btn-secondary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Cleanup Old
                    </button>
                </div>
            </div>
        </div>

        <!-- Backup Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M13 13h3l-3 3-3-3h3V9z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $backupStats['total_backups'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Backups</div>
            </div>
            
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $backupStats['total_size'] ?? '0 MB' }}</div>
                <div class="text-sm text-gray-600">Total Size</div>
            </div>
            
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $backupStats['last_backup'] ?? 'Never' }}</div>
                <div class="text-sm text-gray-600">Last Backup</div>
            </div>
            
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="text-lg font-bold text-gray-900">{{ $backupStats['disk_space']['free'] ?? 'Unknown' }}</div>
                <div class="text-sm text-gray-600">Free Space</div>
            </div>
        </div>

        <!-- Backup List -->
        <div class="medical-card">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Available Backups</h3>
                    <div class="flex items-center space-x-3">
                        <select id="backupTypeFilter" class="form-input !py-1 !px-3 text-sm">
                            <option value="">All Types</option>
                            <option value="database">Database</option>
                            <option value="files">Files</option>
                            <option value="full">Full System</option>
                        </select>
                        <button onclick="refreshBackupList()" class="btn-secondary !py-1 !px-3 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full" id="backupsTable">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">File Name</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Type</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Size</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Created</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups ?? [] as $backup)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center">
                                        @if($backup['type'] === 'database')
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                            </svg>
                                        @elseif($backup['type'] === 'files')
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M13 13h3l-3 3-3-3h3V9z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $backup['file'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $backup['created_at']->format('M d, Y H:i:s') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $typeClass = match($backup['type']) {
                                        'database' => 'bg-blue-100 text-blue-700',
                                        'files' => 'bg-green-100 text-green-700',
                                        'full' => 'bg-purple-100 text-purple-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $typeClass }}">
                                    {{ ucfirst($backup['type']) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">{{ number_format($backup['size'] / 1024 / 1024, 2) }} MB</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">{{ $backup['created_at']->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $backup['created_at']->diffForHumans() }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-2">
                                    <button onclick="downloadBackup('{{ $backup['file'] }}')" class="text-blue-600 hover:text-blue-800" title="Download">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="showRestoreModal('{{ $backup['file'] }}', '{{ $backup['type'] }}')" class="text-green-600 hover:text-green-800" title="Restore">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                    <button onclick="confirmDeleteBackup('{{ $backup['file'] }}')" class="text-red-600 hover:text-red-800" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                                No backups found. Create your first backup above.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Backup Modal -->
<div id="createBackupModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="medical-card p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Create New Backup</h3>
        <form id="createBackupForm">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Backup Type *</label>
                <select id="backupType" name="backup_type" class="form-input" required>
                    <option value="database">Database Only</option>
                    <option value="files">Files Only</option>
                    <option value="full">Full System (Database + Files)</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <input type="text" id="backupDescription" name="description" class="form-input" placeholder="Optional description for this backup">
            </div>
            <div class="mb-6">
                <div class="flex items-center">
                    <input type="checkbox" id="compressBackup" name="compress" class="form-checkbox text-indigo-600" checked>
                    <label for="compressBackup" class="ml-2 text-sm text-gray-700">Compress backup to save space</label>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeCreateBackupModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Create Backup</button>
            </div>
        </form>
    </div>
</div>

<!-- Restore Backup Modal -->
<div id="restoreBackupModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="medical-card p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Restore from Backup</h3>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0l-7.918 8.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-sm font-medium text-yellow-800">Warning: This will overwrite current data!</span>
            </div>
            <p class="text-sm text-yellow-700 mt-1">A safety backup will be created before restoration.</p>
        </div>
        <form id="restoreBackupForm">
            @csrf
            <input type="hidden" id="restoreBackupFile" name="backup_file">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Restore Type</label>
                <select id="restoreType" name="restore_type" class="form-input" required>
                    <option value="database">Database Only</option>
                    <option value="files">Files Only</option>
                    <option value="full">Full System</option>
                </select>
            </div>
            <div class="mb-6">
                <div class="flex items-center">
                    <input type="checkbox" id="confirmRestore" name="confirmation" class="form-checkbox text-red-600" required>
                    <label for="confirmRestore" class="ml-2 text-sm text-gray-700">I understand this will overwrite current data</label>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeRestoreModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">Restore Now</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteBackupModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="medical-card p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Delete Backup</h3>
        <p class="text-gray-600 mb-6">Are you sure you want to delete this backup? This action cannot be undone.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteBackupModal()" class="btn-secondary">Cancel</button>
            <button onclick="deleteBackup()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">Delete Backup</button>
        </div>
    </div>
</div>

<!-- Cleanup Modal -->
<div id="cleanupModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="medical-card p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Cleanup Old Backups</h3>
        <form id="cleanupForm">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keep backups newer than</label>
                <select name="retention_days" class="form-input" required>
                    <option value="7">7 days</option>
                    <option value="30" selected>30 days</option>
                    <option value="60">60 days</option>
                    <option value="90">90 days</option>
                    <option value="180">180 days</option>
                    <option value="365">1 year</option>
                </select>
            </div>
            <p class="text-sm text-gray-600 mb-6">This will permanently delete backup files older than the selected period.</p>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeCleanupModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Start Cleanup</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// CSRF token setup
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupToastNotifications();
    setupBackupTypeFilter();
});

let deleteBackupFile = null;

// Create Backup Modal Functions
function showCreateBackupModal() {
    document.getElementById('createBackupModal').classList.remove('hidden');
    document.getElementById('createBackupModal').classList.add('flex');
}

function closeCreateBackupModal() {
    document.getElementById('createBackupModal').classList.add('hidden');
    document.getElementById('createBackupModal').classList.remove('flex');
    document.getElementById('createBackupForm').reset();
}

// Restore Modal Functions
function showRestoreModal(backupFile, backupType) {
    document.getElementById('restoreBackupFile').value = backupFile;
    document.getElementById('restoreType').value = backupType;
    document.getElementById('restoreBackupModal').classList.remove('hidden');
    document.getElementById('restoreBackupModal').classList.add('flex');
}

function closeRestoreModal() {
    document.getElementById('restoreBackupModal').classList.add('hidden');
    document.getElementById('restoreBackupModal').classList.remove('flex');
    document.getElementById('restoreBackupForm').reset();
}

// Delete Modal Functions
function confirmDeleteBackup(backupFile) {
    deleteBackupFile = backupFile;
    document.getElementById('deleteBackupModal').classList.remove('hidden');
    document.getElementById('deleteBackupModal').classList.add('flex');
}

function closeDeleteBackupModal() {
    document.getElementById('deleteBackupModal').classList.add('hidden');
    document.getElementById('deleteBackupModal').classList.remove('flex');
    deleteBackupFile = null;
}

// Cleanup Modal Functions
function showCleanupModal() {
    document.getElementById('cleanupModal').classList.remove('hidden');
    document.getElementById('cleanupModal').classList.add('flex');
}

function closeCleanupModal() {
    document.getElementById('cleanupModal').classList.add('hidden');
    document.getElementById('cleanupModal').classList.remove('flex');
    document.getElementById('cleanupForm').reset();
}

// Form Submissions
document.getElementById('createBackupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitButton = this.querySelector('button[type="submit"]');
    showLoading(submitButton);
    
    const formData = new FormData(this);
    
    fetch('/admin/backup/create', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Backup created successfully', 'success');
            closeCreateBackupModal();
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast(data.message || 'Error creating backup', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error creating backup', 'error');
    })
    .finally(() => {
        hideLoading(submitButton);
    });
});

document.getElementById('restoreBackupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitButton = this.querySelector('button[type="submit"]');
    showLoading(submitButton);
    
    const formData = new FormData(this);
    const backupFile = document.getElementById('restoreBackupFile').value;
    
    fetch(`/admin/backup/restore/${backupFile}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('System restored successfully', 'success');
            closeRestoreModal();
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showToast(data.message || 'Error restoring backup', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error restoring backup', 'error');
    })
    .finally(() => {
        hideLoading(submitButton);
    });
});

document.getElementById('cleanupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitButton = this.querySelector('button[type="submit"]');
    showLoading(submitButton);
    
    const formData = new FormData(this);
    
    fetch('/admin/backup/cleanup', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`Cleanup completed: ${data.deleted_count} backups deleted, ${data.freed_space} freed`, 'success');
            closeCleanupModal();
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast(data.message || 'Error during cleanup', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error during cleanup', 'error');
    })
    .finally(() => {
        hideLoading(submitButton);
    });
});

// Action Functions
function downloadBackup(backupFile) {
    showToast('Downloading backup...', 'info');
    window.location.href = `/admin/backup/download/${backupFile}`;
}

function deleteBackup() {
    if (!deleteBackupFile) return;
    
    fetch(`/admin/backup/destroy/${deleteBackupFile}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Backup deleted successfully', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Error deleting backup', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error deleting backup', 'error');
    });
    
    closeDeleteBackupModal();
}

function refreshBackupList() {
    showToast('Refreshing backup list...', 'info');
    setTimeout(() => window.location.reload(), 500);
}

// Filter functionality
function setupBackupTypeFilter() {
    const filter = document.getElementById('backupTypeFilter');
    filter?.addEventListener('change', function() {
        const filterValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#backupsTable tbody tr');
        
        rows.forEach(row => {
            if (row.cells.length === 1) return; // Skip empty state row
            
            const typeCell = row.cells[1];
            const typeText = typeCell.textContent.toLowerCase();
            
            if (!filterValue || typeText.includes(filterValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

// Utility Functions
function showLoading(button) {
    if (!button) return;
    button.disabled = true;
    const originalText = button.innerHTML;
    button.setAttribute('data-original-text', originalText);
    button.innerHTML = '<div class="spinner inline-block mr-2"></div>Loading...';
}

function hideLoading(button) {
    if (!button) return;
    button.disabled = false;
    const originalText = button.getAttribute('data-original-text');
    if (originalText) {
        button.innerHTML = originalText;
    }
}

// Toast notification system
function setupToastNotifications() {
    if (!document.getElementById('toast-container')) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
    }
}

function showToast(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toast-container') || document.body;
    const toast = document.createElement('div');
    const toastId = 'toast-' + Date.now();
    
    toast.id = toastId;
    toast.className = `toast show toast-${type} p-4 rounded-lg shadow-lg text-white min-w-80`;
    
    const icon = {
        success: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
        error: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
        info: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
    };
    
    toast.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">${icon[type] || icon.info}</div>
            <div class="ml-3"><p class="text-sm font-medium">${message}</p></div>
            <button onclick="removeToast('${toastId}')" class="ml-4 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    container.appendChild(toast);
    setTimeout(() => removeToast(toastId), duration);
}

function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => toast.remove(), 300);
    }
}

// Modal click outside to close
document.addEventListener('click', function(event) {
    const modals = [
        document.getElementById('createBackupModal'),
        document.getElementById('restoreBackupModal'),
        document.getElementById('deleteBackupModal'),
        document.getElementById('cleanupModal')
    ];
    
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeCreateBackupModal();
        closeRestoreModal();
        closeDeleteBackupModal();
        closeCleanupModal();
    }
    
    if ((event.ctrlKey || event.metaKey) && event.key === 'n') {
        event.preventDefault();
        showCreateBackupModal();
    }
});
</script>
@endpush
