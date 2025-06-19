<x-app-layout>

@if(session('temp_link'))
    <div class="bg-green-100 text-green-800 p-4 rounded my-4">
        Temporary Link:
        <a href="{{ session('temp_link') }}" class="underline text-blue-600" target="_blank">Open Dashboard</a>
    </div>
@endif
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Dashboard - Fixed Tabs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Medical Data Dashboard
                    </h2>
                    <div class="flex space-x-4">
                        <a href="{{ route('upload') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Upload Documents</a>
                        <a href="{{ route('display') }}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">View Records</a>
                    </div>
                    <form action="{{ route('users.generate-temp-link', auth()->user()->id) }}" method="POST" class="mb-6">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                            Generate Temporary Access Link
                        </button>
                    </form>
                </div>
            </div>
        </div>


        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Navigation Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition cursor-pointer">
                        <div class="p-6 text-gray-900 flex items-center">
                            <div class="bg-blue-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">Enter Medical Data</h3>
                                <p class="text-sm text-gray-500">Record your health measurements</p>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('upload') }}" class="block">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition cursor-pointer">
                            <div class="p-6 text-gray-900 flex items-center">
                                <div class="bg-green-100 p-3 rounded-full mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium">Upload Documents</h3>
                                    <p class="text-sm text-gray-500">Upload medical reports or images</p>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('display') }}" class="block">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition cursor-pointer">
                            <div class="p-6 text-gray-900 flex items-center">
                                <div class="bg-purple-100 p-3 rounded-full mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium">View Records</h3>
                                    <p class="text-sm text-gray-500">See your health history</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Medical Data Entry Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Enter Medical Data</h3>

                        <!-- Tabs Navigation -->
                        <div class="border-b border-gray-200 mb-6 overflow-x-auto">
                            <ul class="flex flex-nowrap -mb-px" id="healthTabs" role="tablist">
                                <li class="mr-2" role="presentation">
                                    <button class="tab-button inline-block p-4 border-b-2 rounded-t-lg whitespace-nowrap" id="vitals-tab" data-target="vitals">Vitals</button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button class="tab-button inline-block p-4 border-b-2 rounded-t-lg whitespace-nowrap" id="activity-tab" data-target="activity">Activity & Hydration</button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button class="tab-button inline-block p-4 border-b-2 rounded-t-lg whitespace-nowrap" id="pain-tab" data-target="pain">Pain & Emotion</button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button class="tab-button inline-block p-4 border-b-2 rounded-t-lg whitespace-nowrap" id="sleep-tab" data-target="sleep">Sleep & Rest</button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button class="tab-button inline-block p-4 border-b-2 rounded-t-lg whitespace-nowrap" id="wellbeing-tab" data-target="wellbeing">General Well-being</button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button class="tab-button inline-block p-4 border-b-2 rounded-t-lg whitespace-nowrap" id="labs-tab" data-target="labs">Labs & Biomarkers</button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button class="tab-button inline-block p-4 border-b-2 rounded-t-lg whitespace-nowrap" id="infection-tab" data-target="infection">Infection Markers</button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button class="tab-button inline-block p-4 border-b-2 rounded-t-lg whitespace-nowrap" id="treatments-tab" data-target="treatments">Treatments</button>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab Contents -->
                        <div>
                            <!-- Vitals Section -->
                            <div class="tab-content p-4 rounded-lg bg-gray-50" id="vitals">
                                <h3 class="text-lg font-semibold mb-4">Vital Signs</h3>
                                <form class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" data-category="vitals">
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Blood Pressure (mmHg)</label>
                                        <div class="flex space-x-2">
                                            <input type="number" name="systolic" placeholder="Systolic" class="w-full p-2 border rounded-md">
                                            <input type="number" name="diastolic" placeholder="Diastolic" class="w-full p-2 border rounded-md">
                                        </div>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Heart Rate (bpm)</label>
                                        <input type="number" name="heart_rate" class="w-full p-2 border rounded-md">
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Temperature (°C)</label>
                                        <input type="number" name="temperature" step="0.1" class="w-full p-2 border rounded-md">
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                                        <div class="flex space-x-2">
                                            <input type="number" name="weight" step="0.1" class="w-full p-2 border rounded-md">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </div>
                                    <div class="md:col-span-2 lg:col-span-3">
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Save Vitals</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Activity & Hydration Section -->
                            <div class="tab-content p-4 rounded-lg bg-gray-50 hidden" id="activity">
                                <h3 class="text-lg font-semibold mb-4">Activity & Hydration</h3>
                                <form class="grid grid-cols-1 md:grid-cols-2 gap-6" data-category="activity">
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Daily Steps</label>
                                        <div class="flex space-x-2">
                                            <input type="number" name="daily_steps" class="w-full p-2 border rounded-md">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Exercise Duration (minutes)</label>
                                        <input type="number" name="exercise_duration" class="w-full p-2 border rounded-md">
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Water Intake (liters)</label>
                                        <input type="number" name="water_intake" step="0.1" class="w-full p-2 border rounded-md">
                                    </div>
                                    <div class="md:col-span-2">
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Save Activity Data</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Pain & Emotion Section -->
                            <div class="tab-content p-4 rounded-lg bg-gray-50 hidden" id="pain">
                                <h3 class="text-lg font-semibold mb-4">Pain & Emotion</h3>
                                <form class="grid grid-cols-1 md:grid-cols-2 gap-6" data-category="pain">
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Pain Level (0-10)</label>
                                        <input type="range" name="pain_level" min="0" max="10" step="1" class="w-full">
                                        <div class="flex justify-between text-xs text-gray-500">
                                            <span>0 (None)</span>
                                            <span>10 (Worst)</span>
                                        </div>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mood</label>
                                        <div class="flex space-x-4">
                                            <button type="button" class="mood-btn p-2 rounded-full bg-red-100 hover:bg-red-200" data-mood="angry">😠</button>
                                            <button type="button" class="mood-btn p-2 rounded-full bg-yellow-100 hover:bg-yellow-200" data-mood="sad">😞</button>
                                            <button type="button" class="mood-btn p-2 rounded-full bg-blue-100 hover:bg-blue-200" data-mood="neutral">😐</button>
                                            <button type="button" class="mood-btn p-2 rounded-full bg-green-100 hover:bg-green-200" data-mood="happy">😊</button>
                                            <button type="button" class="mood-btn p-2 rounded-full bg-purple-100 hover:bg-purple-200" data-mood="excited">😁</button>
                                        </div>
                                        <input type="hidden" name="mood" id="mood-input">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    </div>
                                    <div class="md:col-span-2">
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Save Emotional Data</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Sleep & Rest Section -->
                            <div class="tab-content p-4 rounded-lg bg-gray-50 hidden" id="sleep">
                                <h3 class="text-lg font-semibold mb-4">Sleep & Rest</h3>
                                <form class="grid grid-cols-1 md:grid-cols-2 gap-6" data-category="sleep">
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Sleep Duration (hours)</label>
                                        <div class="flex space-x-2">
                                            <input type="number" name="sleep_duration" step="0.5" min="0" max="24" class="w-full p-2 border rounded-md">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Sleep Quality (1-10)</label>
                                        <input type="range" name="sleep_quality" min="1" max="10" step="1" class="w-full">
                                    </div>
                                    <div class="md:col-span-2">
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Save Sleep Data</button>
                                    </div>
                                </form>
                            </div>

                            <!-- General Well-being Section -->
                            <div class="tab-content p-4 rounded-lg bg-gray-50 hidden" id="wellbeing">
                                <h3 class="text-lg font-semibold mb-4">General Well-being</h3>
                                <form class="grid grid-cols-1 md:grid-cols-2 gap-6" data-category="wellbeing">
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Energy Level (1-10)</label>
                                        <input type="range" name="energy_level" min="1" max="10" step="1" class="w-full">
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Appetite</label>
                                        <div class="flex space-x-2">
                                            <select name="appetite" class="w-full p-2 border rounded-md">
                                                <option value="">Select appetite level</option>
                                                <option value="very_poor">Very Poor</option>
                                                <option value="poor">Poor</option>
                                                <option value="normal">Normal</option>
                                                <option value="good">Good</option>
                                                <option value="very_good">Very Good</option>
                                            </select>
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </div>
                                    <div class="md:col-span-2">
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Save Well-being Data</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Labs & Biomarkers Section -->
                            <div class="tab-content p-4 rounded-lg bg-gray-50 hidden" id="labs">
                                <h3 class="text-lg font-semibold mb-4">Labs & Biomarkers</h3>
                                <form class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" data-category="labs">
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">HbA1c (%)</label>
                                        <div class="flex space-x-2">
                                            <input type="number" name="hba1c" step="0.1" class="w-full p-2 border rounded-md">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Cholesterol (mg/dL)</label>
                                        <input type="number" name="cholesterol" class="w-full p-2 border rounded-md">
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Blood Sugar (mg/dL)</label>
                                        <input type="number" name="blood_sugar" class="w-full p-2 border rounded-md">
                                    </div>
                                    <div class="md:col-span-2 lg:col-span-3">
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Save Lab Data</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Infection Markers Section -->
                            <div class="tab-content p-4 rounded-lg bg-gray-50 hidden" id="infection">
                                <h3 class="text-lg font-semibold mb-4">Infection Markers</h3>
                                <form class="grid grid-cols-1 md:grid-cols-2 gap-6" data-category="infection">
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">CD4 Count (cells/µL)</label>
                                        <div class="flex space-x-2">
                                            <input type="number" name="cd4_count" class="w-full p-2 border rounded-md">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Viral Load (copies/mL)</label>
                                        <input type="number" name="viral_load" class="w-full p-2 border rounded-md">
                                    </div>
                                    <div class="md:col-span-2">
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Save Infection Data</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Treatments Section -->
                            <div class="tab-content p-4 rounded-lg bg-gray-50 hidden" id="treatments">
                                <h3 class="text-lg font-semibold mb-4">Treatments</h3>
                                <form class="grid grid-cols-1 md:grid-cols-2 gap-6" data-category="treatments">
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Medication Name</label>
                                        <div class="flex space-x-2">
                                            <input type="text" name="medication_name" class="w-full p-2 border rounded-md">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Dosage</label>
                                        <input type="text" name="dosage" class="w-full p-2 border rounded-md">
                                    </div>
                                    <div class="md:col-span-2">
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Save Treatment Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Popup Overlay -->
        <div id="success-popup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full text-center">
                <div class="bg-green-100 p-3 rounded-full mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Data Saved Successfully!</h3>
                <p class="text-sm text-gray-500 mt-2">Your <span id="popup-category"></span> data has been saved.</p>
                <button id="close-popup" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Close</button>
            </div>
        </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing tabs...');

            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            const moodButtons = document.querySelectorAll('.mood-btn');
            const moodInput = document.getElementById('mood-input');
            const forms = document.querySelectorAll('form[data-category]');
            const successPopup = document.getElementById('success-popup');
            const popupCategory = document.getElementById('popup-category');
            const closePopup = document.getElementById('close-popup');

            const categories = ['vitals', 'activity', 'pain', 'sleep', 'wellbeing', 'labs', 'infection', 'treatments'];

            console.log('Found', tabButtons.length, 'tab buttons');
            console.log('Found', tabContents.length, 'tab contents');
            console.log('Found', forms.length, 'forms');

            // Function to show a specific tab
            function showTab(targetId) {
                console.log('Showing tab:', targetId);

                tabContents.forEach(content => {
                    content.classList.add('hidden');
                    console.log('Hiding:', content.id);
                });

                const targetContent = document.getElementById(targetId);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                    console.log('Showing:', targetId);
                } else {
                    console.error('Target content not found:', targetId);
                }

                tabButtons.forEach(btn => {
                    btn.classList.remove('border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-600', 'hover:border-gray-300');
                });

                const activeButton = document.querySelector(`[data-target="${targetId}"]`);
                if (activeButton) {
                    activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-600', 'hover:border-gray-300');
                    activeButton.classList.add('border-blue-500', 'text-blue-600');
                }
            }

            // Add click event listeners to tab buttons
            tabButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('data-target');
                    console.log('Tab clicked:', targetId);
                    showTab(targetId);
                });
            });

            // Mood button functionality
            if (moodButtons.length > 0 && moodInput) {
                moodButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        moodButtons.forEach(btn => btn.classList.remove('ring-2', 'ring-blue-500'));
                        this.classList.add('ring-2', 'ring-blue-500');
                        moodInput.value = this.getAttribute('data-mood');
                    });
                });
            }

            // Form submission with AJAX
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const category = this.getAttribute('data-category');
                    const formData = new FormData(this);
                    const data = {};
                    formData.forEach((value, key) => {
                        if (key !== '_token') {
                            data[key] = value;
                        }
                    });

                    fetch('{{ route("medical-records.store") }}', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({
        category: category,
        data: data,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }),
})
.then(response => {
    if (!response.ok) {
        return response.json().then(err => { throw err; });
    }
    return response.json();
})
.then(result => {
    // Show success popup
    popupCategory.textContent = category.charAt(0).toUpperCase() + category.slice(1);
    successPopup.classList.remove('hidden');

    // Reset form
    this.reset();
    if (moodInput && category === 'pain') {
        moodButtons.forEach(btn => btn.classList.remove('ring-2', 'ring-blue-500'));
        moodInput.value = '';
    }

    // Switch to next category after 3 seconds
    setTimeout(() => {
        successPopup.classList.add('hidden');
        const currentIndex = categories.indexOf(category);
        const nextCategory = categories[(currentIndex + 1) % categories.length];
        showTab(nextCategory);
    }, 3000);
})
.catch(error => {
    console.error('Error:', error);
    alert('Error: ' + (error.message || 'Failed to save data. Please check the values and try again.'));
});
                });
            });

            // Close popup button
            closePopup.addEventListener('click', () => {
                successPopup.classList.add('hidden');
            });

            // Initialize first tab as active
            if (tabButtons.length > 0) {
                const firstTarget = tabButtons[0].getAttribute('data-target');
                console.log('Initializing first tab:', firstTarget);
                showTab(firstTarget);
            }

            console.log('Tab initialization complete');
        });
    </script>
</body>
</html>
</x-app-layout>
