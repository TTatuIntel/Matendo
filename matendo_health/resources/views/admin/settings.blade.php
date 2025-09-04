@extends('admin.layout')

@section('title', 'System Settings')

@section('content')
<div class="max-w-6xl mx-auto px-2 sm:px-4 lg:px-6">
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="medical-card p-4 sm:p-6 fade-in">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">System Settings Management</h1>
                        <p class="text-sm text-gray-600 font-medium">Configure system-wide settings and preferences across the platform</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 lg:gap-3">
                    <button onclick="backupSettings()" class="btn-secondary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Backup Settings
                    </button>
                    <button onclick="saveAllSettings()" class="btn-primary !py-2 !px-4 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save All Changes
                    </button>
                </div>
            </div>
        </div>

        <!-- Settings Tabs -->
        <div class="medical-card">
            <div class="border-b border-gray-200 bg-gray-50">
                <nav class="flex overflow-x-auto px-6" aria-label="Tabs">
                    <button type="button" class="settings-tab active py-3 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent hover:border-gray-300" data-tab="general">
                        General Settings
                    </button>
                    <button type="button" class="settings-tab py-3 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent hover:border-gray-300" data-tab="security">
                        Security
                    </button>
                    <button type="button" class="settings-tab py-3 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent hover:border-gray-300" data-tab="notifications">
                        Notifications
                    </button>
                    <button type="button" class="settings-tab py-3 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent hover:border-gray-300" data-tab="maintenance">
                        Maintenance
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- General Settings Tab -->
                <div class="tab-content active" id="general-tab">
                    <form id="generalSettingsForm">
                        <div class="space-y-6">
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-2xl border-l-4 border-blue-500">
                                <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                    Application Settings
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label">Application Name</label>
                                        <input type="text" name="app_name" value="{{ config('app.name') }}" class="form-input">
                                        <small class="text-xs text-gray-500">Display name for your application</small>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Time Zone</label>
                                        <select name="app_timezone" class="form-input">
                                            <option value="UTC">UTC</option>
                                            <option value="America/New_York">Eastern Time</option>
                                            <option value="America/Chicago">Central Time</option>
                                            <option value="America/Denver">Mountain Time</option>
                                            <option value="America/Los_Angeles">Pacific Time</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Default Language</label>
                                        <select name="app_locale" class="form-input">
                                            <option value="en">English</option>
                                            <option value="es">Spanish</option>
                                            <option value="fr">French</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Date Format</label>
                                        <select name="date_format" class="form-input">
                                            <option value="M d, Y">Dec 25, 2024</option>
                                            <option value="d/m/Y">25/12/2024</option>
                                            <option value="Y-m-d">2024-12-25</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-2xl border-l-4 border-green-500">
                                <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                    Patient Settings
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Auto-assign Doctors</label>
                                            <p class="text-xs text-gray-500">Automatically assign new patients to available doctors</p>
                                        </div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="auto_assign_doctors" class="form-checkbox text-green-600">
                                            <span class="ml-2"></span>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Require Email Verification</label>
                                            <p class="text-xs text-gray-500">New patients must verify their email address</p>
                                        </div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="require_email_verification" class="form-checkbox text-green-600" checked>
                                            <span class="ml-2"></span>
                                        </label>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="form-group">
                                            <label class="form-label">Maximum Patients per Doctor</label>
                                            <input type="number" name="max_patients_per_doctor" value="50" min="1" max="500" class="form-input">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Vital Signs Reminder (Hours)</label>
                                            <input type="number" name="vitals_reminder_hours" value="24" min="1" max="168" class="form-input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Security Settings Tab -->
                <div class="tab-content hidden" id="security-tab">
                    <form id="securitySettingsForm">
                        <div class="space-y-6">
                            <div class="bg-gradient-to-br from-red-50 to-pink-50 p-6 rounded-2xl border-l-4 border-red-500">
                                <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                                    Password & Authentication
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label">Minimum Password Length</label>
                                        <input type="number" name="min_password_length" value="8" min="6" max="50" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Password Expiry (Days)</label>
                                        <input type="number" name="password_expiry_days" value="90" min="0" max="365" class="form-input">
                                        <small class="text-xs text-gray-500">0 = Never expires</small>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Max Login Attempts</label>
                                        <input type="number" name="max_login_attempts" value="5" min="3" max="10" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Lockout Duration (Minutes)</label>
                                        <input type="number" name="lockout_duration" value="15" min="5" max="60" class="form-input">
                                    </div>
                                </div>
                                <div class="space-y-4 mt-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Require Complex Passwords</label>
                                            <p class="text-xs text-gray-500">Must include uppercase, lowercase, numbers, and symbols</p>
                                        </div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="require_complex_passwords" class="form-checkbox text-red-600" checked>
                                            <span class="ml-2"></span>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Two-Factor Authentication</label>
                                            <p class="text-xs text-gray-500">Require 2FA for admin accounts</p>
                                        </div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="require_2fa_admin" class="form-checkbox text-red-600">
                                            <span class="ml-2"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-orange-50 to-yellow-50 p-6 rounded-2xl border-l-4 border-orange-500">
                                <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                                    <div class="w-3 h-3 bg-orange-500 rounded-full mr-3"></div>
                                    Data Protection
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Encrypt Patient Data</label>
                                            <p class="text-xs text-gray-500">Encrypt sensitive patient information at rest</p>
                                        </div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="encrypt_patient_data" class="form-checkbox text-orange-600" checked>
                                            <span class="ml-2"></span>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Log Access Attempts</label>
                                            <p class="text-xs text-gray-500">Log all data access and modification attempts</p>
                                        </div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="log_access_attempts" class="form-checkbox text-orange-600" checked>
                                            <span class="ml-2"></span>
                                        </label>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="form-group">
                                            <label class="form-label">Data Retention Period (Days)</label>
                                            <input type="number" name="data_retention_days" value="2555" min="365" max="3650" class="form-input">
                                            <small class="text-xs text-gray-500">How long to keep patient data</small>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Backup Frequency</label>
                                            <select name="backup_frequency" class="form-input">
                                                <option value="daily">Daily</option>
                                                <option value="weekly">Weekly</option>
                                                <option value="monthly">Monthly</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Notifications Settings Tab -->
                <div class="tab-content hidden" id="notifications-tab">
                    <form id="notificationSettingsForm">
                        <div class="space-y-6">
                            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 p-6 rounded-2xl border-l-4 border-purple-500">
                                <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                    Email Notifications
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Critical Alert Emails</label>
                                            <p class="text-xs text-gray-500">Send email alerts for critical patient conditions</p>
                                        </div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="email_critical_alerts" class="form-checkbox text-purple-600" checked>
                                            <span class="ml-2"></span>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Daily Summary Reports</label>
                                            <p class="text-xs text-gray-500">Send daily summary to administrators</p>
                                        </div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="email_daily_summary" class="form-checkbox text-purple-600">
                                            <span class="ml-2"></span>
                                        </label>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="form-group">
                                            <label class="form-label">SMTP Server</label>
                                            <input type="text" name="smtp_host" placeholder="smtp.gmail.com" class="form-input">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">SMTP Port</label>
                                            <input type="number" name="smtp_port" value="587" min="25" max="65535" class="form-input">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">From Email</label>
                                            <input type="email" name="mail_from_address" placeholder="noreply@example.com" class="form-input">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">From Name</label>
                                            <input type="text" name="mail_from_name" value="Medical Monitor" class="form-input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Maintenance Settings Tab -->
                <div class="tab-content hidden" id="maintenance-tab">
                    <div class="space-y-6">
                        <div class="bg-gradient-to-br from-gray-50 to-slate-50 p-6 rounded-2xl border-l-4 border-gray-500">
                            <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                                <div class="w-3 h-3 bg-gray-500 rounded-full mr-3"></div>
                                System Maintenance
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <button onclick="clearCache()" class="btn-primary w-full">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Clear System Cache
                                    </button>
                                    <button onclick="optimizeDatabase()" class="btn-primary w-full">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                        </svg>
                                        Optimize Database
                                    </button>
                                    <button onclick="generateReport()" class="btn-secondary w-full">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        System Health Report
                                    </button>
                                </div>
                                <div class="space-y-4">
                                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                                        <h5 class="font-semibold text-gray-900 mb-2">System Status</h5>
                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Database</span>
                                                <span class="status-normal">Connected</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Cache</span>
                                                <span class="status-normal">Active</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Storage</span>
                                                <span class="status-normal">Available</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                                        <h5 class="font-semibold text-gray-900 mb-2">Storage Usage</h5>
                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Database Size</span>
                                                <span class="text-sm font-medium">{{ $dbSize ?? '125 MB' }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Files</span>
                                                <span class="text-sm font-medium">{{ $filesSize ?? '890 MB' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.settings-tab');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.dataset.tab;

            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active', 'text-blue-600', 'border-blue-500'));
            tabContents.forEach(content => {
                content.classList.remove('active');
                content.classList.add('hidden');
            });

            // Add active class to clicked tab
            this.classList.add('active', 'text-blue-600', 'border-blue-500');

            // Show target tab content
            const targetContent = document.getElementById(targetTab + '-tab');
            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('active');
            }
        });
    });
});

function saveAllSettings() {
    const forms = ['generalSettingsForm', 'securitySettingsForm', 'notificationSettingsForm'];
    const formData = new FormData();

    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            const formInputs = new FormData(form);
            for (let [key, value] of formInputs.entries()) {
                formData.append(key, value);
            }
        }
    });

    fetch('/admin/settings', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Settings saved successfully', 'success');
        } else {
            showToast('Error saving settings', 'error');
        }
    })
    .catch(() => showToast('Error saving settings', 'error'));
}

function backupSettings() {
    fetch('/admin/settings/backup', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'system-settings-backup.json';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        showToast('Settings backup downloaded', 'success');
    })
    .catch(() => showToast('Error creating backup', 'error'));
}

function clearCache() {
    fetch('/admin/settings/clear-cache', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Cache cleared successfully', 'success');
        } else {
            showToast('Error clearing cache', 'error');
        }
    })
    .catch(() => showToast('Error clearing cache', 'error'));
}

function optimizeDatabase() {
    if (!confirm('This may take several minutes. Continue?')) return;
    
    showToast('Optimizing database...', 'info');
    
    fetch('/admin/settings/optimize-database', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Database optimized successfully', 'success');
        } else {
            showToast('Error optimizing database', 'error');
        }
    })
    .catch(() => showToast('Error optimizing database', 'error'));
}

function generateReport() {
    window.open('/admin/settings/system-report', '_blank');
}
</script>
@endpush
