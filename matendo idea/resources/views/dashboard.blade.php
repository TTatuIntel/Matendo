<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .smooth-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .btn-primary {
            background: linear-gradient(135deg, #059669, #0d9488);
            border: 2px solid #065f46;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #047857, #0f766e);
            border-color: #064e3b;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #1e40af, #3730a3);
            border: 2px solid #1e3a8a;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: linear-gradient(135deg, #1d4ed8, #4338ca);
            border-color: #1e3a8a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }
        .btn-danger {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            border: 2px solid #991b1b;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-danger:hover {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border-color: #7f1d1d;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
        }
        .table-row:hover {
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            transform: scale(1.001);
        }
        .modal-backdrop {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-green-50 to-blue-50">
    
    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Applications Overview Section -->
        <h2 class="text-3xl font-bold text-gray-800 mb-8 flex items-center">
            <div class="w-2 h-8 bg-gradient-to-b from-green-600 to-blue-600 rounded-full mr-4"></div>
            Applications Overview
        </h2>
        
        <div class="flex space-x-8 overflow-x-auto pb-6 mb-12">
            <!-- Pending Review Card -->
            <div class="min-w-[340px] flex-shrink-0 p-8 bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg card-hover smooth-transition border border-yellow-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-yellow-700 text-xl flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        Pending Review
                    </h3>
                    <span class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-400 to-amber-400 text-yellow-900 text-sm font-bold rounded-full shadow-sm">
                        30
                    </span>
                </div>
                <p class="text-gray-700 mb-4 text-base">Applications waiting for admin review and verification.</p>
                <div class="space-y-2 text-sm">
                    <p class="text-gray-600">Oldest pending: <span class="font-semibold text-gray-800">5 days ago</span></p>
                    <p class="text-gray-600">Average review time: <span class="font-semibold text-gray-800">3 days</span></p>
                </div>
            </div>

            <!-- Approved Card -->
            <div class="min-w-[340px] flex-shrink-0 p-8 bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg card-hover smooth-transition border border-green-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-green-700 text-xl flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Approved
                    </h3>
                    <span class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-400 to-emerald-400 text-green-900 text-sm font-bold rounded-full shadow-sm">
                        85
                    </span>
                </div>
                <p class="text-gray-700 mb-4 text-base">Applications successfully verified and approved for platform access.</p>
                <div class="space-y-2 text-sm">
                    <p class="text-gray-600">New approvals this month: <span class="font-semibold text-gray-800">10</span></p>
                    <p class="text-gray-600">Average onboarding time: <span class="font-semibold text-gray-800">2 days</span></p>
                </div>
            </div>

            <!-- Rejected Card -->
            <div class="min-w-[340px] flex-shrink-0 p-8 bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg card-hover smooth-transition border border-red-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-red-700 text-xl flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Rejected
                    </h3>
                    <span class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-400 to-rose-400 text-red-900 text-sm font-bold rounded-full shadow-sm">
                        8
                    </span>
                </div>
                <p class="text-gray-700 mb-4 text-base">Applications declined due to incomplete or invalid information.</p>
                <div class="space-y-2 text-sm">
                    <p class="text-gray-600">Common reasons: <span class="font-semibold text-gray-800">Missing documents, expired licenses</span></p>
                </div>
            </div>
        </div>

        <!-- Pending Applications Table Section -->
        <h2 class="text-3xl font-bold text-gray-800 mb-8 flex items-center">
            <div class="w-2 h-8 bg-gradient-to-b from-green-600 to-blue-600 rounded-full mr-4"></div>
            Pending Applications
        </h2>
        
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 sticky top-0">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">Ref Number</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">Name</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">Profession</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">Submitted</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="table-row smooth-transition cursor-pointer">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">APP-2024-001</td>
                            <td class="px-6 py-4 text-sm text-gray-800">John Smith</td>
                            <td class="px-6 py-4 text-sm text-gray-700">Registered Nurse</td>
                            <td class="px-6 py-4 text-sm text-gray-600">2024-05-28</td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="openModal()" class="btn-secondary px-6 py-2 rounded-lg text-sm font-semibold">
                                    View Details
                                </button>
                            </td>
                        </tr>
                        <tr class="table-row smooth-transition cursor-pointer">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">APP-2024-002</td>
                            <td class="px-6 py-4 text-sm text-gray-800">Sarah Johnson</td>
                            <td class="px-6 py-4 text-sm text-gray-700">Physical Therapist</td>
                            <td class="px-6 py-4 text-sm text-gray-600">2024-05-29</td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="openModal()" class="btn-secondary px-6 py-2 rounded-lg text-sm font-semibold">
                                    View Details
                                </button>
                            </td>
                        </tr>
                        <tr class="table-row smooth-transition cursor-pointer">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">APP-2024-003</td>
                            <td class="px-6 py-4 text-sm text-gray-800">Michael Davis</td>
                            <td class="px-6 py-4 text-sm text-gray-700">Medical Assistant</td>
                            <td class="px-6 py-4 text-sm text-gray-600">2024-05-30</td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="openModal()" class="btn-secondary px-6 py-2 rounded-lg text-sm font-semibold">
                                    View Details
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Application Modal -->
    <div id="applicationModal" class="fixed inset-0 modal-backdrop flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[85vh] overflow-y-auto m-4 smooth-transition">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex justify-between items-center rounded-t-2xl">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                    <svg class="w-7 h-7 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Application Details - APP-2024-001
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full smooth-transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-8">
                <!-- Review Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Personal Information -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200">
                        <h4 class="text-lg font-bold text-blue-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            Personal Information
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Name:</span>
                                <span class="text-gray-800">John Smith</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Email:</span>
                                <span class="text-gray-800">john.smith@email.com</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Phone:</span>
                                <span class="text-gray-800">+1 (555) 123-4567</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Address:</span>
                                <span class="text-gray-800 text-right">123 Main St, City, State 12345</span>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200">
                        <h4 class="text-lg font-bold text-green-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Professional Information
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Profession:</span>
                                <span class="text-gray-800">Registered Nurse</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Specialization:</span>
                                <span class="text-gray-800">ICU/Critical Care</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Experience:</span>
                                <span class="text-gray-800">5 years</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Start Date:</span>
                                <span class="text-gray-800">2024-06-15</span>
                            </div>
                        </div>
                    </div>

                    <!-- Work Preferences -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200">
                        <h4 class="text-lg font-bold text-purple-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            Work Preferences
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Work Type:</span>
                                <span class="text-gray-800">Full-time, Part-time</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Shift Type:</span>
                                <span class="text-gray-800">Day shift, Night shift</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Location:</span>
                                <span class="text-gray-800">Metro Area</span>
                            </div>
                        </div>
                    </div>

                    <!-- Supporting Documents -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200">
                        <h4 class="text-lg font-bold text-amber-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            Supporting Documents
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-600">Resume:</span>
                                <a href="#" class="text-blue-600 hover:text-blue-800 underline font-medium">View</a>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-600">License:</span>
                                <a href="#" class="text-blue-600 hover:text-blue-800 underline font-medium">View</a>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-600">Certifications:</span>
                                <a href="#" class="text-blue-600 hover:text-blue-800 underline font-medium">View</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-center space-x-6 pt-6 border-t border-gray-200">
                    <button class="btn-primary px-8 py-3 rounded-xl text-base font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Approve Application
                    </button>
                    
                    <button class="btn-danger px-8 py-3 rounded-xl text-base font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reject Application
                    </button>
                    
                    <button onclick="closeModal()" class="bg-gray-200 hover:bg-gray-300 border-2 border-gray-400 text-gray-700 px-8 py-3 rounded-xl text-base font-semibold smooth-transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('applicationModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('applicationModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('applicationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>