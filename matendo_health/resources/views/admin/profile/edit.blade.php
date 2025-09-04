@extends('admin.layout')

@section('title', 'Admin Profile')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="medical-card p-6 fade-in">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div class="w-20 h-20 bg-gradient-to-r from-purple-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg" id="avatarPreview">
                            @if($admin->avatar)
                                <img src="{{ asset('storage/' . $admin->avatar) }}" alt="Admin Avatar" class="w-20 h-20 rounded-full object-cover">
                            @else
                                {{ substr($admin->name, 0, 1) }}
                            @endif
                        </div>
                        <button onclick="document.getElementById('avatarInput').click()" class="absolute bottom-0 right-0 bg-blue-500 text-white rounded-full p-2 shadow-lg hover:bg-blue-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                        <input type="file" id="avatarInput" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $admin->name }}</h1>
                        <p class="text-blue-600 font-semibold">System Administrator</p>
                        <p class="text-sm text-gray-600">{{ $admin->email }}</p>
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                Active
                            </span>
                            @if($admin->two_factor_enabled ?? false)
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                    2FA Enabled
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0">
                    <button onclick="exportActivity()" class="btn-secondary mr-3">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        Export Activity
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $activityStats['actions_performed_today'] }}</div>
                <div class="text-sm text-gray-600">Actions Today</div>
            </div>

            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $activityStats['users_managed_this_month'] }}</div>
                <div class="text-sm text-gray-600">Users Managed</div>
            </div>

            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $activityStats['security_actions_this_week'] }}</div>
                <div class="text-sm text-gray-600">Security Actions</div>
            </div>

            <div class="medical-card p-6 text-center hover-lift">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $activityStats['login_sessions_this_month'] }}</div>
                <div class="text-sm text-gray-600">Login Sessions</div>
            </div>
        </div>

        <!-- Profile Management Tabs -->
        <div class="medical-card">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6 py-4" aria-label="Tabs">
                    <button onclick="showTab('profile')" class="tab-btn active" id="profile-tab">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profile Information
                    </button>
                    <button onclick="showTab('security')" class="tab-btn" id="security-tab">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Security
                    </button>
                    <button onclick="showTab('notifications')" class="tab-btn" id="notifications-tab">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.07 13H2.05L2 12l.05-1H4.07a8.003 8.003 0 010 2z"></path>
                        </svg>
                        Notifications
                    </button>
                    <button onclick="showTab('activity')" class="tab-btn" id="activity-tab">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z"></path>
                        </svg>
                        Activity Log
                    </button>
                </nav>
            </div>

            <!-- Profile Information Tab -->
            <div id="profile-content" class="tab-content p-6">
                <form id="profileForm" onsubmit="updateProfile(event)">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="name" value="{{ $admin->name }}" class="form-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="email" value="{{ $admin->email }}" class="form-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="text" name="phone" value="{{ $admin->phone }}" class="form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                            <select name="timezone" class="form-input">
                                <option value="UTC" {{ ($admin->timezone ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ ($admin->timezone ?? '') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                <option value="America/Chicago" {{ ($admin->timezone ?? '') == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                <option value="America/Denver" {{ ($admin->timezone ?? '') == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                <option value="America/Los_Angeles" {{ ($admin->timezone ?? '') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                            <textarea name="bio" rows="4" class="form-input" placeholder="Tell us about yourself...">{{ $admin->bio ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="btn-primary">
                            <span class="spinner" id="profileSpinner" style="display: none;"></span>
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Tab -->
            <div id="security-content" class="tab-content p-6" style="display: none;">
                <div class="space-y-8">
                    <!-- Password Change -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h3>
                        <form id="passwordForm" onsubmit="updatePassword(event)">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                    <input type="password" name="current_password" class="form-input" required>
                                </div>
                                <div></div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                    <input type="password" name="password" class="form-input" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-input" required>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn-primary">
                                    <span class="spinner" id="passwordSpinner" style="display: none;"></span>
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Two-Factor Authentication -->
                    <div class="border-t border-gray-200 pt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Two-Factor Authentication</h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-700">Add an extra layer of security to your account</p>
                                <p class="text-sm text-gray-500 mt-1">Status: 
                                    <span class="font-semibold {{ ($admin->two_factor_enabled ?? false) ? 'text-green-600' : 'text-red-600' }}">
                                        {{ ($admin->two_factor_enabled ?? false) ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </p>
                            </div>
                            <button onclick="toggleTwoFactor()" class="btn-{{ ($admin->two_factor_enabled ?? false) ? 'secondary' : 'primary' }}" id="twoFactorBtn">
                                {{ ($admin->two_factor_enabled ?? false) ? 'Disable' : 'Enable' }} 2FA
                            </button>
                        </div>
                    </div>

                    <!-- Security Summary -->
                    <div class="border-t border-gray-200 pt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Security Summary</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600">Last Login</div>
                                <div class="font-semibold">
                                    {{ $securitySummary['last_login'] ? $securitySummary['last_login']->format('M d, Y H:i') : 'Never' }}
                                </div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600">Password Last Changed</div>
                                <div class="font-semibold">
                                    {{ $securitySummary['password_last_changed']->format('M d, Y') }}
                                </div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600">Failed Login Attempts (30 days)</div>
                                <div class="font-semibold text-red-600">{{ $securitySummary['failed_login_attempts'] }}</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600">Account Created</div>
                                <div class="font-semibold">
                                    {{ $securitySummary['account_created']->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div id="notifications-content" class="tab-content p-6" style="display: none;">
                <form id="notificationForm" onsubmit="updateNotificationPreferences(event)">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Notification Preferences</h3>
                    
                    <div class="space-y-6">
                        <!-- Notification Types -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-3">Notification Methods</h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <input type="checkbox" name="email_notifications" id="email_notifications" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    <label for="email_notifications" class="ml-2 text-sm text-gray-700">Email Notifications</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="push_notifications" id="push_notifications" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="push_notifications" class="ml-2 text-sm text-gray-700">Push Notifications</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="sms_notifications" id="sms_notifications" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="sms_notifications" class="ml-2 text-sm text-gray-700">SMS Notifications</label>
                                </div>
                            </div>
                        </div>

                        <!-- Alert Types -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-3">Alert Types</h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <input type="checkbox" name="alert_types[]" value="security" id="alert_security" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    <label for="alert_security" class="ml-2 text-sm text-gray-700">Security Alerts</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="alert_types[]" value="user_activity" id="alert_user" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    <label for="alert_user" class="ml-2 text-sm text-gray-700">User Activity</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="alert_types[]" value="system" id="alert_system" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    <label for="alert_system" class="ml-2 text-sm text-gray-700">System Alerts</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="alert_types[]" value="critical_alerts" id="alert_critical" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    <label for="alert_critical" class="ml-2 text-sm text-gray-700">Critical Alerts</label>
                                </div>
                            </div>
                        </div>

                        <!-- Frequency -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-3">Notification Frequency</h4>
                            <select name="notification_frequency" class="form-input">
                                <option value="immediate">Immediate</option>
                                <option value="daily">Daily Summary</option>
                                <option value="weekly">Weekly Summary</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="btn-primary">
                            <span class="spinner" id="notificationSpinner" style="display: none;"></span>
                            Update Preferences
                        </button>
                    </div>
                </form>
            </div>

            <!-- Activity Log Tab -->
            <div id="activity-content" class="tab-content p-6" style="display: none;">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                <div class="space-y-4" id="activityList">
                    @forelse($recentActivities as $activity)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-900">{{ $activity['description'] }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $activity['log_name'] }} • {{ $activity['created_at']->diffForHumans() }}
                                </div>
                            </div>
                            <div class="text-xs text-gray-400">{{ $activity['ip_address'] }}</div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            No recent activity
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.csrfToken = '{{ csrf_token() }}';

function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-content').style.display = 'block';
    document.getElementById(tabName + '-tab').classList.add('active');
}

function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').innerHTML = 
                `<img src="${e.target.result}" alt="Avatar Preview" class="w-20 h-20 rounded-full object-cover">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function updateProfile(event) {
    event.preventDefault();
    const spinner = document.getElementById('profileSpinner');
    const formData = new FormData(event.target);
    
    // Add avatar file if selected
    const avatarInput = document.getElementById('avatarInput');
    if (avatarInput.files[0]) {
        formData.append('avatar', avatarInput.files[0]);
    }
    
    spinner.style.display = 'inline-block';
    
    fetch('{{ route("admin.profile.update") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Profile updated successfully', 'success');
            if (data.avatar_url) {
                document.getElementById('avatarPreview').innerHTML = 
                    `<img src="${data.avatar_url}" alt="Admin Avatar" class="w-20 h-20 rounded-full object-cover">`;
            }
        } else {
            showToast(data.message || 'Failed to update profile', 'error');
        }
    })
    .catch(error => {
        showToast('An error occurred', 'error');
    })
    .finally(() => {
        spinner.style.display = 'none';
    });
}

function updatePassword(event) {
    event.preventDefault();
    const spinner = document.getElementById('passwordSpinner');
    const formData = new FormData(event.target);
    
    spinner.style.display = 'inline-block';
    
    fetch('{{ route("admin.profile.password") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Password updated successfully', 'success');
            event.target.reset();
        } else {
            showToast(data.message || 'Failed to update password', 'error');
        }
    })
    .catch(error => {
        showToast('An error occurred', 'error');
    })
    .finally(() => {
        spinner.style.display = 'none';
    });
}

function toggleTwoFactor() {
    fetch('{{ route("admin.profile.two-factor") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            const btn = document.getElementById('twoFactorBtn');
            if (data.two_factor_enabled) {
                btn.textContent = 'Disable 2FA';
                btn.className = 'btn-secondary';
            } else {
                btn.textContent = 'Enable 2FA';
                btn.className = 'btn-primary';
            }
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'Failed to toggle 2FA', 'error');
        }
    })
    .catch(error => {
        showToast('An error occurred', 'error');
    });
}

function updateNotificationPreferences(event) {
    event.preventDefault();
    const spinner = document.getElementById('notificationSpinner');
    const formData = new FormData(event.target);
    
    spinner.style.display = 'inline-block';
    
    fetch('{{ route("admin.profile.notifications") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Notification preferences updated', 'success');
        } else {
            showToast(data.message || 'Failed to update preferences', 'error');
        }
    })
    .catch(error => {
        showToast('An error occurred', 'error');
    })
    .finally(() => {
        spinner.style.display = 'none';
    });
}

function exportActivity() {
    window.location.href = '{{ route("admin.profile.export-activity") }}';
}

// Tab styles
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .tab-btn {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            font-weight: 500;
            color: #6b7280;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .tab-btn:hover {
            color: #3b82f6;
        }
        .tab-btn.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
        }
    `;
    document.head.appendChild(style);
});
</script>
@endpush
