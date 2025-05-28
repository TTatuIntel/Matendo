
<x-app-layout>
    <!-- Alpine.js State Management -->
<div x-data="{
    activeMainTab: localStorage.getItem('activeMainTab') || 'dashboard',
    activeAppTab: localStorage.getItem('activeAppTab') || 'all',
    activeDashboardTab: localStorage.getItem('activeDashboardTab') || 'applications',
    selectedApplication: null,
    showOverlay: false,
    // Methods to handle tab changes
    setActiveMainTab(tab) {
        this.activeMainTab = tab;
        localStorage.setItem('activeMainTab', tab);
    },
    setActiveAppTab(tab) {
        this.activeAppTab = tab;
        localStorage.setItem('activeAppTab', tab);
    },
    setActiveDashboardTab(tab) {
        this.activeDashboardTab = tab;
        localStorage.setItem('activeDashboardTab', tab);
    },
    openOverlay(application) {
        this.selectedApplication = application;
        this.showOverlay = true;
    },
    closeOverlay() {
        this.showOverlay = false;
        this.selectedApplication = null;
    },

}">

        <!-- Static Top Navigation Bar -->
        <header class="bg-white shadow dark:bg-gray-800 sticky top-0 z-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-xl font-bold text-gray-800 dark:text-white">Healthcare Admin</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Notifications Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="relative rounded-full bg-white p-1 text-gray-600 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                <span class="sr-only">View notifications</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">12</span>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-700" role="menu">
                                <!-- Notification Header -->
                                <div class="border-b border-gray-200 px-4 py-2 dark:border-gray-600">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Notifications</h3>
                                </div>
                                <!-- Notification List -->
                                <div class="max-h-60 overflow-y-auto">
                                    <!-- Notification Items -->
                                    @php
                                        // This would normally come from your controller
                                        $notifications = [
                                            [
                                                'title' => 'New application submitted',
                                                'time' => '5 minutes ago',
                                                'icon' => 'document',
                                                'color' => 'indigo',
                                                'url' => '#'
                                            ],
                                            [
                                                'title' => 'Facility hire request',
                                                'time' => '1 hour ago',
                                                'icon' => 'building',
                                                'color' => 'blue',
                                                'url' => '#',
                                                'action' => 'facility'
                                            ],
                                            [
                                                'title' => 'Task completed',
                                                'time' => '2 hours ago',
                                                'icon' => 'check',
                                                'color' => 'green',
                                                'url' => '#',
                                                'action' => 'tasks'
                                            ],
                                            [
                                                'title' => 'New individual care request',
                                                'time' => '3 hours ago',
                                                'icon' => 'user',
                                                'color' => 'purple',
                                                'url' => '#',
                                                'action' => 'individual'
                                            ]
                                        ];
                                    @endphp

                                    @foreach($notifications as $notification)
                                        <a href="{{ $notification['url'] }}"
                                           @if(isset($notification['action']))
                                               @click.prevent="setActiveMainTab('{{ $notification['action'] }}')"
                                           @endif
                                           class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-{{ $notification['color'] }}-100 dark:bg-{{ $notification['color'] }}-900">
                                                        @if($notification['icon'] === 'document')
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-{{ $notification['color'] }}-600 dark:text-{{ $notification['color'] }}-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                        @elseif($notification['icon'] === 'building')
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-{{ $notification['color'] }}-600 dark:text-{{ $notification['color'] }}-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                            </svg>
                                                        @elseif($notification['icon'] === 'check')
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-{{ $notification['color'] }}-600 dark:text-{{ $notification['color'] }}-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        @elseif($notification['icon'] === 'user')
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-{{ $notification['color'] }}-600 dark:text-{{ $notification['color'] }}-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $notification['title'] }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $notification['time'] }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                                <!-- View All Link -->
                                <div class="border-t border-gray-200 px-4 py-2 dark:border-gray-600">
                                    <a href="#" class="block text-center text-sm font-medium text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View all notifications</a>
                                </div>
                            </div>
                        </div>

                        <!-- Dark Mode Toggle -->
                        <button class="rounded-full bg-white p-1 text-gray-600 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => { localStorage.setItem('darkMode', val); document.documentElement.classList.toggle('dark', val); })" @click="darkMode = !darkMode">
                            <span class="sr-only">Toggle dark mode</span>
                            <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </button>

                        <!-- User Menu -->
                        <div class="relative ml-3" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" class="flex items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-gray-800" id="user-menu-button">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                                    <span class="ml-2 hidden text-sm font-medium text-gray-700 dark:text-gray-300 md:block">{{ Auth::user()->name ?? 'Admin User' }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-700" role="menu">
                                <a href="#" @click.prevent="setActiveMainTab('settings')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600" role="menuitem">Your Profile</a>
                                <a href="#" @click.prevent="setActiveMainTab('settings')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600" role="menuitem">Settings</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600" role="menuitem">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Tab Navigation -->
        <div class="border-b border-gray-200 bg-white sticky top-16 z-40 dark:border-gray-700 dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <nav class="-mb-px flex overflow-x-auto" aria-label="Main Navigation">
                    <!-- Dashboard Tab -->
                    <button @click="setActiveMainTab('dashboard')"
                       class="group relative flex items-center whitespace-nowrap border-b-2 px-4 py-4 text-sm font-medium transition-colors duration-200"
                       :class="activeMainTab === 'dashboard' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </button>

                    <!-- Applications Tab -->
                    <button @click="setActiveMainTab('applications')"
                       class="group relative flex items-center whitespace-nowrap border-b-2 px-4 py-4 text-sm font-medium transition-colors duration-200"
                       :class="activeMainTab === 'applications' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Applications
                        <span class="absolute -top-1 right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">8</span>
                    </button>

                    <!-- Facility Hire Tab -->
                    <button @click="setActiveMainTab('facility')"
                       class="group relative flex items-center whitespace-nowrap border-b-2 px-4 py-4 text-sm font-medium transition-colors duration-200"
                       :class="activeMainTab === 'facility' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Facility Hire
                        <span class="absolute -top-1 right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">5</span>
                    </button>

                    <!-- Individual Requests Tab -->
                    <button @click="setActiveMainTab('individual')"
                       class="group relative flex items-center whitespace-nowrap border-b-2 px-4 py-4 text-sm font-medium transition-colors duration-200"
                       :class="activeMainTab === 'individual' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Individual Requests
                        <span class="absolute -top-1 right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">3</span>
                    </button>

                    <!-- Health Workers Tab -->
                    <button @click="setActiveMainTab('workers')"
                       class="group relative flex items-center whitespace-nowrap border-b-2 px-4 py-4 text-sm font-medium transition-colors duration-200"
                       :class="activeMainTab === 'workers' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Health Workers
                    </button>

                    <!-- Tasks Tab -->
                    <button @click="setActiveMainTab('tasks')"
                       class="group relative flex items-center whitespace-nowrap border-b-2 px-4 py-4 text-sm font-medium transition-colors duration-200"
                       :class="activeMainTab === 'tasks' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Tasks
                    </button>

                    <!-- Settings Tab -->
                    <button @click="setActiveMainTab('settings')"
                       class="group relative flex items-center whitespace-nowrap border-b-2 px-4 py-4 text-sm font-medium transition-colors duration-200"
                       :class="activeMainTab === 'settings' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </button>
                </nav>
            </div>
        </div>

        <!-- Page Header with Actions -->
        <div class="bg-white shadow-sm dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col space-y-4 py-4 md:flex-row md:items-center md:justify-between md:space-y-0">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white" x-text="activeMainTab.charAt(0).toUpperCase() + activeMainTab.slice(1)">Dashboard</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="activeMainTab === 'dashboard' ? 'Overview of your healthcare administration system' :
                                                                                      activeMainTab === 'applications' ? 'Manage healthcare worker applications' :
                                                                                      activeMainTab === 'facility' ? 'Manage facility bookings and requests' :
                                                                                      activeMainTab === 'individual' ? 'Manage individual healthcare requests' :
                                                                                      activeMainTab === 'workers' ? 'Manage healthcare professionals' :
                                                                                      activeMainTab === 'tasks' ? 'Manage healthcare tasks and assignments' :
                                                                                      'Manage system settings and preferences'">
                            Overview of your healthcare administration system
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Date Range Selector -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Last 7 days
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-700">
                                <div class="py-1">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">Today</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">Yesterday</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">Last 7 days</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">Last 30 days</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">This month</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">Custom range</a>
                                </div>
                            </div>
                        </div>

                        <!-- Export Button -->
                        <button class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export
                        </button>

                        <!-- New Item Button -->
                        <button class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none dark:bg-indigo-700 dark:hover:bg-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span x-text="activeMainTab === 'applications' ? 'New Application' :
                                         activeMainTab === 'facility' ? 'Book Facility' :
                                         activeMainTab === 'individual' ? 'New Care Request' :
                                         activeMainTab === 'workers' ? 'Add Health Worker' :
                                         activeMainTab === 'tasks' ? 'Create Task' :
                                         'New ' + activeMainTab.charAt(0).toUpperCase() + activeMainTab.slice(1, -1)">
                                New Application
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Content Area -->
        <div class="bg-gray-100 py-6 dark:bg-gray-900">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- DASHBOARD TAB CONTENT -->
                <div x-show="activeMainTab === 'dashboard'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <!-- Stats Overview -->
                    <div class="flex flex-wrap gap-4">
                        <!-- Applications Stat Card -->
                        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Applications</h2>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $applicationCount ?? 42 }}</p>
                                <p class="mt-1 text-xs text-green-600 dark:text-green-400">
                                    <span class="font-medium">↑ 12%</span>
                                    <span class="text-gray-500 dark:text-gray-400">from last month</span>
                                </p>
                            </div>
                        </div>

                        <!-- Facility Hire Stat Card -->
                        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Facility Hire</h2>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $facilityCount ?? 18 }}</p>
                                <p class="mt-1 text-xs text-green-600 dark:text-green-400">
                                    <span class="font-medium">↑ 8%</span>
                                    <span class="text-gray-500 dark:text-gray-400">from last month</span>
                                </p>
                            </div>
                        </div>

                        <!-- Individual Requests Stat Card -->
                        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Individual Requests</h2>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $individualCount ?? 24 }}</p>
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                    <span class="font-medium">↓ 3%</span>
                                    <span class="text-gray-500 dark:text-gray-400">from last month</span>
                                </p>
                            </div>
                        </div>

                        <!-- Assigned Tasks Stat Card -->
                        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned Tasks</h2>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $taskCount ?? 36 }}</p>
                                <p class="mt-1 text-xs text-green-600 dark:text-green-400">
                                    <span class="font-medium">↑ 18%</span>
                                    <span class="text-gray-500 dark:text-gray-400">from last month</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions - Navigation-like Layout -->
                    <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <div class="mx-auto px-4 sm:px-6">
                                <nav class="flex overflow-x-auto" aria-label="Healthcare Services">
                                    <!-- New Application Quick Action -->
                                    <a href="#" @click.prevent="setActiveMainTab('applications')" class="group relative flex items-center whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400">
                                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/40 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-900/60 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium">New Application</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Create a new application for healthcare workers</div>
                                        </div>
                                    </a>

                                    <!-- Book Facility Quick Action -->
                                    <a href="#" @click.prevent="setActiveMainTab('facility')" class="group relative flex items-center whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-teal-100 dark:bg-teal-900/40 group-hover:bg-teal-200 dark:group-hover:bg-teal-900/60 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium">Book Facility</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Schedule a facility for healthcare services</div>
                                        </div>
                                    </a>

                                    <!-- Assign Task Quick Action -->
                                    <a href="#" @click.prevent="setActiveMainTab('tasks')" class="group relative flex items-center whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-violet-100 dark:bg-violet-900/40 group-hover:bg-violet-200 dark:group-hover:bg-violet-900/60 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium">Assign Task</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Assign tasks to approved health workers</div>
                                        </div>
                                    </a>

                                    <!-- Generate Report Quick Action -->
                                    <a href="#" @click.prevent="setActiveMainTab('settings')" class="group relative flex items-center whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/40 group-hover:bg-amber-200 dark:group-hover:bg-amber-900/60 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium">Generate Report</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Create reports for healthcare activities</div>
                                        </div>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <!-- Dashboard Tabbed Summary Section -->
                    <div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <div class="px-4">
                                <nav class="-mb-px flex space-x-8">
                                    <button @click="setActiveDashboardTab('applications')" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors duration-200" :class="activeDashboardTab === 'applications' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                                        Recent Applications
                                    </button>
                                    <button @click="setActiveDashboardTab('approvals')" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors duration-200" :class="activeDashboardTab === 'approvals' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                                        Pending Approvals
                                    </button>
                                    <button @click="setActiveDashboardTab('activity')" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors duration-200" :class="activeDashboardTab === 'activity' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                                        Recent Activity
                                    </button>
                                </nav>
                            </div>
                        </div>

                        <!-- Recent Applications Tab Content -->
                        <div x-show="activeDashboardTab === 'applications'" class="px-4 py-4">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Reference</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Name</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Profession</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Applied</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($recentApplications ?? [] as $application)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium">
                                                    <a href="#" @click.prevent="setActiveMainTab('applications'); setActiveAppTab('all')" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        {{ $application->reference_code }}
                                                    </a>
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $application->first_name }} {{ $application->last_name }}
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $application->profession }}
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm">
                                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                                        @if($application->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                        @elseif($application->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @endif">
                                                        {{ ucfirst($application->status) }}
                                                    </span>
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $application->created_at->diffForHumans() }}
                                                </td>
                                            </tr>
                                        @empty
                                            <!-- Sample Application Data when no real data is available -->
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium">
                                                    <a href="#" @click.prevent="setActiveMainTab('applications'); setActiveAppTab('all')" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        APP-2023-001
                                                    </a>
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    John Smith
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    Registered Nurse
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm">
                                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                        Pending
                                                    </span>
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    5 minutes ago
                                                </td>
                                            </tr>
                                            <!-- More sample data rows -->
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <a href="#" @click.prevent="setActiveMainTab('applications')" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    View all applications
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Pending Approvals Tab Content -->
                        <div x-show="activeDashboardTab === 'approvals'" class="px-4 py-4">
                            <div class="space-y-4">
                                @forelse($pendingApprovals ?? [] as $approval)
                                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $approval->type ?? 'Application' }}: {{ $approval->reference_code }}
                                                </h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $approval->first_name }} {{ $approval->last_name }} - {{ $approval->profession }} with {{ $approval->years_experience }} years experience
                                                </p>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    Submitted {{ $approval->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button class="rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 hover:bg-green-100 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800">
                                                    Approve
                                                </button>
                                                <button class="rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-100 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800">
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <!-- Sample Pending Approvals when no real data is available -->
                                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    Application: APP-2023-001
                                                </h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    John Smith - Registered Nurse with 5 years experience
                                                </p>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    Submitted 5 minutes ago
                                                </p>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button class="rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 hover:bg-green-100 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800">
                                                    Approve
                                                </button>
                                                <button class="rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-100 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800">
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    Facility Hire: FAC-2023-002
                                                </h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    City Medical Center - Requesting 3 nurses for weekend coverage
                                                </p>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    Submitted 2 hours ago
                                                </p>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button class="rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 hover:bg-green-100 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800">
                                                    Approve
                                                </button>
                                                <button class="rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-100 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800">
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    Individual Request: IND-2023-003
                                                </h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    Robert Thompson - Home care assistance for elderly patient
                                                </p>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    Submitted 1 day ago
                                                </p>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button class="rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 hover:bg-green-100 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800">
                                                    Approve
                                                </button>
                                                <button class="rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-100 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800">
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            <div class="mt-4 flex justify-end">
                                <a href="#" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    View all pending approvals
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Recent Activity Tab Content -->
                        <div x-show="activeDashboardTab === 'activity'" class="px-4 py-4">
                            <div class="space-y-4">
                                @forelse($recentActivities ?? [] as $activity)
                                    <div class="flex items-start space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-{{ $activity->color ?? 'green' }}-100 dark:bg-{{ $activity->color ?? 'green' }}-900">
                                            {!! $activity->icon ?? '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' !!}
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                <span class="font-medium">{{ $activity->title ?? 'Activity' }}</span> - {{ $activity->description ?? 'Activity description' }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->time ?? '30 minutes ago' }} {{ $activity->user ? 'by ' . $activity->user->name : '' }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <!-- Sample Activity Items when no real data is available -->
                                    <div class="flex items-start space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                <span class="font-medium">Application Approved</span> - Sarah Johnson's application has been approved
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">30 minutes ago by Admin User</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                <span class="font-medium">New Application</span> - John Smith submitted a new application
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">1 hour ago</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600 dark:text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                <span class="font-medium">Application Rejected</span> - Michael Brown's application has been rejected
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">3 hours ago by Admin User</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-600 dark:text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                <span class="font-medium">New Care Request</span> - Robert Thompson submitted a new individual care request
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">1 day ago</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            <div class="mt-4 flex justify-end">
                                <a href="#" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    View all activity
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- APPLICATIONS TAB CONTENT -->
                <div x-show="activeMainTab === 'applications'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <!-- Applications Stats Overview -->
                    <div class="flex flex-wrap gap-4 mb-6">
                        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Applications</h2>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $applications->total() }}</p>
                            </div>
                        </div>

                        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 dark:text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</h2>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pendingCount }}</p>
                            </div>
                        </div>

                        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved</h2>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $approvedCount }}</p>
                            </div>
                        </div>

                        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejected</h2>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rejectedCount }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Applications Table -->
                    <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Reference</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Profession</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Applied Date</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                    @forelse($applications as $application)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $application->reference_code }}</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $application->first_name }} {{ $application->last_name }}</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $application->profession }}</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                                    @if($application->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                    @elseif($application->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @endif">
                                                    {{ ucfirst($application->status) }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $application->created_at->format('Y-m-d') }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <button @click="openOverlay({
                                            id: '{{ $application->id }}',
                                            reference_code: '{{ $application->reference_code }}',
                                            first_name: '{{ $application->first_name }}',
                                            last_name: '{{ $application->last_name }}',
                                            profession: '{{ $application->profession }}',
                                            status: '{{ $application->status }}',
                                            created_at: '{{ $application->created_at->format('Y-m-d') }}'
                                        })" type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</button>                                        </td>
                                                                                </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">No applications found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            Showing
                                            <span class="font-medium">{{ $applications->firstItem() }}</span>
                                            to
                                            <span class="font-medium">{{ $applications->lastItem() }}</span>
                                            of
                                            <span class="font-medium">{{ $applications->total() }}</span>
                                            results
                                        </p>
                                    </div>
                                    <div>
                                        {{ $applications->links('pagination::tailwind') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div x-show="showOverlay" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 z-60 flex items-center justify-center" @click.self="closeOverlay">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md" @keydown.escape="closeOverlay">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Application Details</h2>
                            <div class="space-y-4">
                                <p><strong>Reference:</strong> <span x-text="selectedApplication.reference_code"></span></p>
                                <p><strong>Name:</strong> <span x-text="selectedApplication.first_name + ' ' + selectedApplication.last_name"></span></p>
                                <p><strong>Profession:</strong> <span x-text="selectedApplication.profession"></span></p>
                                <p><strong>Status:</strong> <span x-text="selectedApplication.status" class="inline-flex rounded-full px-2 py-1 text-xs font-semibold" :class="selectedApplication.status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : selectedApplication.status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'"></span></p>
                                <p><strong>Applied Date:</strong> <span x-text="selectedApplication.created_at"></span></p>
                            </div>
                            <div class="mt-6 space-x-4">
                                <form :id="'status-update-form-' + selectedApplication.id" method="POST" :action="'/applications/' + selectedApplication.id + '/status'" x-ref="statusForm">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="">
                                </form>
                                <button @click="approveApplication(selectedApplication.id)" class="w-full md:w-auto px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600" :disabled="selectedApplication.status === 'approved'">Approve</button>
                                <button @click="rejectApplication(selectedApplication.id)" class="w-full md:w-auto px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600" :disabled="selectedApplication.status === 'rejected'">Reject</button>
                                <button @click="closeOverlay" class="w-full md:w-auto mt-2 md:mt-0 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Other tab content sections would go here -->
<!-- FACILITY HIRE TAB CONTENT -->
<div x-show="activeMainTab === 'facility'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
    <!-- Facility Hire Stats Overview -->
    <div class="flex flex-wrap gap-4 mb-6">
        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div class="ml-4">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">UnAssigned Tasks</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $facilityBookings->where('status', 'pending')->count() }}</p>
            </div>
        </div>

        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 dark:text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-4">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $facilityBookings->where('status', 'approved')->count() }}</p>
            </div>
        </div>

        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div class="ml-4">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejected</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $facilityBookings->where('status', 'rejected')->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Facility Hire Table -->
    <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Reference</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Facility Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Purpose</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Requested Date</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($facilityBookings as $booking)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $booking->reference_number }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $booking->facility_name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $booking->resources_needed }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                    @if($booking->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($booking->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $booking->created_at->format('Y-m-d') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <button @click="openOverlay({
                                    id: '{{ $booking->id }}',
                                    reference_code: '{{ $booking->reference_number }}',
                                    facility_name: '{{ $booking->facility_name }}',
                                    purpose: '{{ $booking->resources_needed }}',
                                    status: '{{ $booking->status }}',
                                    created_at: '{{ $booking->created_at->format('Y-m-d') }}'
                                })" type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">No facility bookings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <!-- Pagination - Updated to handle both paginated and regular collections -->
<div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
    <div class="flex items-center justify-between">
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    @if(method_exists($facilityBookings, 'total'))
                        Showing
                        <span class="font-medium">{{ $facilityBookings->firstItem() ?? 1 }}</span>
                        to
                        <span class="font-medium">{{ $facilityBookings->lastItem() ?? $facilityBookings->count() }}</span>
                        of
                        <span class="font-medium">{{ $facilityBookings->total() }}</span>
                        results
                    @else
                        Showing {{ $facilityBookings->count() }} results
                    @endif
                </p>
            </div>
            <div>
                @if(method_exists($facilityBookings, 'links'))
                    {{ $facilityBookings->links('pagination::tailwind') }}
                @endif
            </div>
        </div>
    </div>
</div>
    </div>
    <div x-show="showOverlay" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 z-60 flex items-center justify-center" @click.self="closeOverlay">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md" @keydown.escape="closeOverlay">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Facility Booking Details</h2>
            <div class="space-y-4">
                <p><strong>Reference:</strong> <span x-text="selectedApplication.reference_code"></span></p>
                <p><strong>Facility Name:</strong> <span x-text="selectedApplication.facility_name"></span></p>
                <p><strong>Purpose:</strong> <span x-text="selectedApplication.purpose"></span></p>
                <p><strong>Status:</strong> <span x-text="selectedApplication.status" class="inline-flex rounded-full px-2 py-1 text-xs font-semibold" :class="selectedApplication.status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : selectedApplication.status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'"></span></p>
                <p><strong>Requested Date:</strong> <span x-text="selectedApplication.created_at"></span></p>
            </div>
            <div class="mt-6 space-x-4">
                <form :id="'status-update-form-' + selectedApplication.id" method="POST" :action="'/facility-bookings/' + selectedApplication.id + '/status'" x-ref="statusForm">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" x-model="selectedApplication.status">
                </form>
                <button @click="approveApplication(selectedApplication.id)" class="w-full md:w-auto px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600" :disabled="selectedApplication.status === 'approved'">Approve</button>
                <button @click="rejectApplication(selectedApplication.id)" class="w-full md:w-auto px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600" :disabled="selectedApplication.status === 'rejected'">Reject</button>
                <button @click="closeOverlay" class="w-full md:w-auto mt-2 md:mt-0 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- INDIVIDUAL REQUESTS TAB CONTENT -->
<div x-show="activeMainTab === 'individual'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
    <!-- Individual Requests Stats Overview -->
    <div class="flex flex-wrap gap-4 mb-6">
        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="ml-4">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Requests</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $individualPendingCount }}</p>
            </div>
        </div>

        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 dark:text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-4">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved Requests</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $individualApprovedCount }}</p>
            </div>
        </div>

        <div class="flex flex-1 min-w-[200px] transform items-center rounded-lg bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md dark:bg-gray-800">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div class="ml-4">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejected Requests</h2>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $individualRejectedCount }}</p>
            </div>
        </div>
    </div>

    <!-- Individual Requests Table -->
    <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Reference</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Individual Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Skills Needed</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Start Date</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($individualRequests as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $request->reference_number }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $request->individual_name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $request->skills_needed }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                    @if($request->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($request->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @endif">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $request->start_date ? $request->start_date->format('Y-m-d') : 'N/A' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <button @click="openOverlay({
                                    id: '{{ $request->id }}',
                                    reference_code: '{{ $request->reference_number }}',
                                    facility_name: '{{ $request->individual_name }}',
                                    purpose: '{{ $request->skills_needed }}',
                                    status: '{{ $request->status }}',
                                    created_at: '{{ $request->start_date ? $request->start_date->format('Y-m-d') : 'N/A' }}'
                                })" type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">No individual requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            @if(method_exists($individualRequests, 'total'))
                                Showing
                                <span class="font-medium">{{ $individualRequests->firstItem() ?? 1 }}</span>
                                to
                                <span class="font-medium">{{ $individualRequests->lastItem() ?? $individualRequests->count() }}</span>
                                of
                                <span class="font-medium">{{ $individualRequests->total() }}</span>
                                results
                            @else
                                Showing {{ $individualRequests->count() }} results
                            @endif
                        </p>
                    </div>
                    <div>
                        @if(method_exists($individualRequests, 'links'))
                            {{ $individualRequests->links('pagination::tailwind') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay Modal for Individual Requests -->
    <div x-show="showOverlay && activeMainTab === 'individual'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 z-60 flex items-center justify-center" @click.self="closeOverlay">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md" @keydown.escape="closeOverlay">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Individual Request Details</h2>
            <div class="space-y-4">
                <p><strong>Reference:</strong> <span x-text="selectedApplication.reference_code"></span></p>
                <p><strong>Individual Name:</strong> <span x-text="selectedApplication.facility_name"></span></p>
                <p><strong>Skills Needed:</strong> <span x-text="selectedApplication.purpose"></span></p>
                <p><strong>Status:</strong> <span x-text="selectedApplication.status" class="inline-flex rounded-full px-2 py-1 text-xs font-semibold" :class="selectedApplication.status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : selectedApplication.status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'"></span></p>
                <p><strong>Start Date:</strong> <span x-text="selectedApplication.created_at"></span></p>
            </div>
            <div class="mt-6 space-x-4">
                <form :id="'status-update-form-' + selectedApplication.id" method="POST" :action="'/admin/individual-requests/' + selectedApplication.id + '/status'" x-ref="statusForm">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" x-model="selectedApplication.status">
                </form>
                <button @click="approveApplication(selectedApplication.id)" class="w-full md:w-auto px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600" :disabled="selectedApplication.status === 'approved'">Approve</button>
                <button @click="rejectApplication(selectedApplication.id)" class="w-full md:w-auto px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600" :disabled="selectedApplication.status === 'rejected'">Reject</button>
                <button @click="closeOverlay" class="w-full md:w-auto mt-2 md:mt-0 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">Close</button>
            </div>
        </div>
    </div>
</div>

                </div>

            </div>
        </div>
    </div>


</x-app-layout>

<script>
    // Add any additional JavaScript needed for the dashboard
    document.addEventListener('alpine:init', () => {
        // Listen for custom events to handle tab changes
        window.addEventListener('set-active-main-tab', (event) => {
            const tab = event.detail;
            // This will be handled by Alpine.js x-data
        });
    });
</script>
<script defer src="https://unpkg.com/alpinejs@3.13.3/dist/cdn.min.js"></script>

