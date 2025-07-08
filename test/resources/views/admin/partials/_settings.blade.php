<div class="max-w-7xl mx-auto p-6 sm:p-8 bg-gray-100 min-h-screen">
    <!-- Toggle Buttons -->
    <div class="flex space-x-4 mb-6">
        <button id="show-settings" class="toggle-btn bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200 active">Platform Settings</button>
        <button id="show-users" class="toggle-btn bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200">User Management</button>
    </div>

    <!-- Platform Settings Form -->
    <div id="settings-section" class="bg-white rounded-xl shadow-lg p-6 transition-opacity duration-300">
        <h3 class="text-xl font-semibold text-gray-800 mb-6">Platform Settings</h3>
        <form class="space-y-5" method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <div>
                <label for="platform-name" class="block text-sm font-medium text-gray-700 mb-1">Platform Name</label>
                <input type="text" id="platform-name" name="platform_name" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" value="{{ old('platform_name', $settings['platform_name'] ?? 'Matendo Medic') }}">
                @error('platform_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="admin-email" class="block text-sm font-medium text-gray-700 mb-1">Admin Email</label>
                <input type="email" id="admin-email" name="admin_email" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" value="{{ old('admin_email', $settings['admin_email'] ?? 'admin@matendomedic.com') }}">
                @error('admin_email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="default-language" class="block text-sm font-medium text-gray-700 mb-1">Default Language</label>
                <select id="default-language" name="default_language" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="en" {{ (old('default_language', $settings['default_language'] ?? 'en') == 'en') ? 'selected' : '' }}>English</option>
                    <option value="fr" {{ (old('default_language', $settings['default_language'] ?? 'en') == 'fr') ? 'selected' : '' }}>French</option>
                    <option value="sw" {{ (old('default_language', $settings['default_language'] ?? 'en') == 'sw') ? 'selected' : '' }}>Swahili</option>
                    <option value="es" {{ (old('default_language', $settings['default_language'] ?? 'en') == 'es') ? 'selected' : '' }}>Spanish</option>
                </select>
                @error('default_language')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="enable-notifications" class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="enable-notifications" name="enable_notifications" value="1" {{ old('enable_notifications', $settings['enable_notifications'] ?? true) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Enable Email Notifications</span>
                </label>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition duration-200">Save Settings</button>
            </div>
        </form>
    </div>

    <!-- User Management Section -->
    <div id="users-section" class="bg-white rounded-xl shadow-lg p-6 hidden transition-opacity duration-300">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-800">User Management</h3>
            <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">Add New User</a>
        </div>

        @php
            // Load users if not already provided
            if (!isset($users)) {
                $users = \App\Models\User::orderBy('created_at', 'desc')->paginate(10);
            }
        @endphp

        @if($users && $users->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Password</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->id }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->usertype === 'admin' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($user->usertype ?? 'user') }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 truncate max-w-xs">{{ $user->password }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($users, 'links'))
                <div class="mt-6">
                    {{ $users->links('pagination::tailwind') }}
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 text-sm">No users found or unable to load users.</p>
                <a href="{{ route('admin.users.create') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">Create First User</a>
            </div>
        @endif
    </div>
</div>

<script>
    document.getElementById('show-settings').addEventListener('click', function() {
        document.getElementById('settings-section').classList.remove('hidden');
        document.getElementById('users-section').classList.add('hidden');
        this.classList.add('bg-blue-600', 'text-white', 'active');
        this.classList.remove('bg-gray-200', 'text-gray-700');
        document.getElementById('show-users').classList.add('bg-gray-200', 'text-gray-700');
        document.getElementById('show-users').classList.remove('bg-blue-600', 'text-white', 'active');
    });

    document.getElementById('show-users').addEventListener('click', function() {
        document.getElementById('users-section').classList.remove('hidden');
        document.getElementById('settings-section').classList.add('hidden');
        this.classList.add('bg-blue-600', 'text-white', 'active');
        this.classList.remove('bg-gray-200', 'text-gray-700');
        document.getElementById('show-settings').classList.add('bg-gray-200', 'text-gray-700');
        document.getElementById('show-settings').classList.remove('bg-blue-600', 'text-white', 'active');
    });
</script>
