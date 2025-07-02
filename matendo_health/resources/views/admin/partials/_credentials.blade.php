<div id="passwordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 max-w-lg w-full m-4 transform scale-95 transition-transform duration-300">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Application Approved
            </h3>
            <button onclick="closePasswordModal()" class="text-gray-500 hover:text-gray-700 p-1 rounded" aria-label="Close credentials modal">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6 space-y-6">
            <div class="bg-gray-50 rounded-md p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                    User Credentials
                </h4>
                <div class="space-y-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-600">Email:</span>
                        <div class="flex items-center space-x-2">
                            <span id="snapshotEmail" class="text-gray-900"></span>
                            <button onclick="copyToClipboard('passwordModalEmail')" class="text-blue-600 hover:text-blue-800" title="Copy Email">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                        <input type="text" id="passwordModalEmail" class="hidden" readonly>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-600">Password:</span>
                        <div class="flex items-center space-x-2">
                            <span id="snapshotPassword" class="text-gray-900"></span>
                            <button id="togglePassword" class="text-blue-600 hover:text-blue-800" title="Show/Hide Password">Show</button>
                            <button onclick="copyToClipboard('passwordModalPassword')" class="text-blue-600 hover:text-blue-800" title="Copy Password">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                        <input type="password" id="passwordModalPassword" class="hidden" readonly>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-md p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                    Snapshot
                </h4>
                <div class="flex items-center justify-between text-sm">
                    <span class="font-medium text-gray-600">Application Snapshot:</span>
                    <div class="flex space-x-2">
                        <a id="viewSnapshotLink" href="#" class="text-blue-600 hover:text-blue-800 flex items-center" target="_blank">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Snapshot
                        </a>
                        <a id="downloadPdfLink" href="#" class="text-blue-600 hover:text-blue-800 flex items-center" target="_blank">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="flex justify-center space-x-4">
                <form id="regeneratePasswordForm" action="#" method="POST" class="action-form" onsubmit="event.preventDefault(); handleRegeneratePassword(this);">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors flex items-center" data-loading-text="Regenerating...">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Regenerate Password
                    </button>
                </form>
                <button onclick="closePasswordModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    async function handleRegeneratePassword(form) {
        const url = form.action;
        const formData = new FormData(form);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        const button = form.querySelector('button[type="submit"]');
        button.disabled = true;
        button.innerHTML = `<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Regenerating...`;

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            if (response.ok) {
                showToast('Password regenerated successfully!', 'success');
                document.getElementById('passwordModalPassword').value = result.password;
                document.getElementById('snapshotPassword').textContent = result.password;
            } else {
                throw new Error(result.message || 'Failed to regenerate password');
            }
        } catch (error) {
            showToast(`Failed to regenerate password: ${error.message}`, 'error');
        } finally {
            button.disabled = false;
            button.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Regenerate Password
            `;
        }
    }
</script>