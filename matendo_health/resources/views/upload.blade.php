<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Dashboard - Upload Documents</title>
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
                        Upload Medical Documents
                    </h2>
                    <div class="flex space-x-4">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">Back to Dashboard</a>
                        <a href="{{ route('display') }}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">View Records</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Success/Error Messages -->
                <div id="alert" class="hidden mb-6 p-4 rounded-lg">
                    <span id="alert-message"></span>
                </div>

                <!-- Upload Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Upload Medical Documents</h3>

                        <!-- Upload Area -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors cursor-pointer" id="upload-area">
                            <div class="bg-blue-100 p-4 rounded-full mx-auto w-16 h-16 flex items-center justify-center mb-4">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Upload medical documents</h3>
                            <p class="text-sm text-gray-500 mb-4">Drag and drop files here, or click to browse</p>

                            <div class="mb-4">
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Document Category (Optional)</label>
                                <select id="category" name="category" class="mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm w-full max-w-xs mx-auto">
                                    <option value="">Select Category</option>
                                    <option value="lab_results">Lab Results</option>
                                    <option value="prescriptions">Prescriptions</option>
                                    <option value="medical_reports">Medical Reports</option>
                                </select>
                            </div>

                            <input type="file" multiple class="hidden" id="file-upload" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <button type="button" id="select-files-btn" class="bg-blue-500 text-white px-6 py-3 rounded-md hover:bg-blue-600 transition inline-flex items-center space-x-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Select Files</span>
                            </button>

                            <p class="mt-4 text-xs text-gray-500">
                                Supported formats: PDF, JPG, PNG, DOC, DOCX (Max: 10MB per file)
                            </p>
                        </div>

                        <!-- Selected Files Display -->
                        <div id="selected-files" class="mt-6 hidden">
                            <h4 class="text-md font-medium text-gray-900 mb-3">Selected Files</h4>
                            <div id="files-list" class="space-y-2"></div>
                            <div class="mt-4 flex space-x-3">
                                <button type="button" id="upload-btn" class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">
                                    <span id="upload-text">Upload Files</span>
                                    <span id="upload-spinner" class="hidden ml-2">
                                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                                <button type="button" id="clear-btn" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                                    Clear All
                                </button>
                            </div>
                        </div>

                        <!-- Upload Progress -->
                        <div id="upload-progress" class="mt-4 hidden">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p id="progress-text" class="text-sm text-gray-600 mt-2">Uploading...</p>
                        </div>
                    </div>
                </div>

                <!-- Document Categories -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Document Categories</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <div class="flex items-center mb-2">
                                    <div class="bg-blue-100 p-2 rounded-full mr-3">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-blue-800">Lab Results</h4>
                                </div>
                                <p class="text-sm text-blue-600">Blood tests, imaging results, pathology reports</p>
                            </div>

                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <div class="flex items-center mb-2">
                                    <div class="bg-green-100 p-2 rounded-full mr-3">
                                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-green-800">Prescriptions</h4>
                                </div>
                                <p class="text-sm text-green-600">Medication prescriptions, pharmacy records</p>
                            </div>

                            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                <div class="flex items-center mb-2">
                                    <div class="bg-purple-100 p-2 rounded-full mr-3">
                                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-purple-800">Medical Reports</h4>
                                </div>
                                <p class="text-sm text-purple-600">Doctor visits, consultation notes, discharge summaries</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Uploads -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Recent Uploads</h3>
                            <div class="flex space-x-2">
                                <button class="category-filter-btn px-3 py-1 rounded-md bg-blue-500 text-white" data-category="">All</button>
                                <button class="category-filter-btn px-3 py-1 rounded-md bg-blue-100 text-blue-800" data-category="lab_results">Lab Results</button>
                                <button class="category-filter-btn px-3 py-1 rounded-md bg-green-100 text-green-800" data-category="prescriptions">Prescriptions</button>
                                <button class="category-filter-btn px-3 py-1 rounded-md bg-purple-100 text-purple-800" data-category="medical_reports">Medical Reports</button>
                            </div>
                        </div>
                        <div id="recent-uploads">
                            @forelse($documents as $document)
                                <div class="document-item flex items-center justify-between p-3 bg-gray-50 rounded-lg border mb-2" data-category="{{ $document->category ?? '' }}">
                                    <div class="flex items-center">
                                        <div class="bg-@php echo $document->category === 'lab_results' ? 'blue' : ($document->category === 'prescriptions' ? 'green' : 'purple'); @endphp-100 p-2 rounded-full mr-3">
                                            <svg class="h-5 w-5 text-@php echo $document->category === 'lab_results' ? 'blue' : ($document->category === 'prescriptions' ? 'green' : 'purple'); @endphp-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="@php
                                                    $ext = pathinfo($document->filename, PATHINFO_EXTENSION);
                                                    echo $ext === 'pdf' ? 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z' :
                                                    (in_array($ext, ['jpg','jpeg','png']) ? 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' :
                                                    'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z');
                                                @endphp"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $document->filename }}</p>
                                            <p class="text-sm text-gray-500">
                                                Uploaded {{ $document->created_at->format('M d, Y') }} • {{ round($document->size / 1024 / 1024, 2) }} MB
                                                @if($document->category)
                                                     • {{ ucfirst(str_replace('_', ' ', $document->category)) }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                    <a href="{{ route('documents.view', $document->id) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                    <a href="{{ route('documents.download', $document->id) }}" class="text-green-600 hover:text-green-800 text-sm">Download</a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No documents uploaded yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('file-upload');
            const uploadArea = document.getElementById('upload-area');
            const selectedFilesDiv = document.getElementById('selected-files');
            const filesList = document.getElementById('files-list');
            const uploadBtn = document.getElementById('upload-btn');
            const uploadText = document.getElementById('upload-text');
            const uploadSpinner = document.getElementById('upload-spinner');
            const clearBtn = document.getElementById('clear-btn');
            const categorySelect = document.getElementById('category');
            const alertDiv = document.getElementById('alert');
            const alertMessage = document.getElementById('alert-message');
            const recentUploads = document.getElementById('recent-uploads');
            const selectFilesBtn = document.getElementById('select-files-btn');
            const uploadProgress = document.getElementById('upload-progress');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const categoryFilterBtns = document.querySelectorAll('.category-filter-btn');
            const documentItems = document.querySelectorAll('.document-item');

            let selectedFiles = [];

            // Category filter functionality
            categoryFilterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const category = this.dataset.category;

                    // Update active button styling
                    categoryFilterBtns.forEach(b => {
                        if (b === this) {
                            b.classList.remove('bg-blue-100', 'bg-green-100', 'bg-purple-100');
                            b.classList.remove('text-blue-800', 'text-green-800', 'text-purple-800');
                            b.classList.add('bg-blue-500', 'text-white');
                        } else {
                            b.classList.remove('bg-blue-500', 'text-white');
                            if (b.dataset.category === 'lab_results') {
                                b.classList.add('bg-blue-100', 'text-blue-800');
                            } else if (b.dataset.category === 'prescriptions') {
                                b.classList.add('bg-green-100', 'text-green-800');
                            } else if (b.dataset.category === 'medical_reports') {
                                b.classList.add('bg-purple-100', 'text-purple-800');
                            } else {
                                b.classList.add('bg-gray-100', 'text-gray-800');
                            }
                        }
                    });

                    // Filter documents
                    documentItems.forEach(item => {
                        if (category === '' || item.dataset.category === category) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Show message if no documents in category
                    const visibleDocs = Array.from(documentItems).filter(item =>
                        item.style.display !== 'none'
                    ).length;

                    const noDocsMsg = document.querySelector('#recent-uploads > p.text-sm.text-gray-500');
                    if (visibleDocs === 0 && noDocsMsg) {
                        noDocsMsg.style.display = 'block';
                    } else if (noDocsMsg) {
                        noDocsMsg.style.display = 'none';
                    }
                });
            });

            // Make upload area clickable
            uploadArea.addEventListener('click', function(e) {
                if (e.target !== categorySelect && !categorySelect.contains(e.target)) {
                    fileInput.click();
                }
            });

            // Select files button
            selectFilesBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.click();
            });

            // Drag and drop functionality
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                uploadArea.classList.add('border-blue-400', 'bg-blue-50');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                uploadArea.classList.remove('border-blue-400', 'bg-blue-50');

                const files = Array.from(e.dataTransfer.files);
                if (files.length === 0) {
                    showAlert('error', 'No files were dropped.');
                    return;
                }

                handleFiles(files);
            });

            // File input change
            fileInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                if (files.length === 0) return;
                handleFiles(files);
            });

            // Handle selected files
            function handleFiles(files) {
                if (!files || files.length === 0) {
                    showAlert('error', 'No files provided.');
                    return;
                }

                const validExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
                const maxSize = 10 * 1024 * 1024; // 10MB
                let validFiles = [];
                let invalidFiles = [];

                files.forEach(file => {
                    const ext = file.name.split('.').pop().toLowerCase();

                    if (!validExtensions.includes(ext)) {
                        invalidFiles.push(`${file.name} (invalid format)`);
                        return;
                    }

                    if (file.size > maxSize) {
                        invalidFiles.push(`${file.name} (too large: ${(file.size / 1024 / 1024).toFixed(2)}MB)`);
                        return;
                    }

                    const alreadySelected = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                    if (alreadySelected) {
                        invalidFiles.push(`${file.name} (already selected)`);
                        return;
                    }

                    validFiles.push(file);
                });

                if (invalidFiles.length > 0) {
                    showAlert('error', `Invalid files: ${invalidFiles.join(', ')}`);
                }

                if (validFiles.length > 0) {
                    selectedFiles = selectedFiles.concat(validFiles);
                    showAlert('success', `Added ${validFiles.length} file(s) for upload.`);
                    displaySelectedFiles();
                }
            }

            // Display selected files
            function displaySelectedFiles() {
                if (selectedFiles.length > 0) {
                    selectedFilesDiv.classList.remove('hidden');
                    filesList.innerHTML = '';

                    selectedFiles.forEach((file, index) => {
                        const fileDiv = document.createElement('div');
                        fileDiv.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg border';

                        const ext = file.name.split('.').pop().toLowerCase();
                        const iconPath = getFileIcon(ext);

                        fileDiv.innerHTML = `
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-2 rounded-full mr-3">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">${file.name}</p>
                                    <p class="text-sm text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB • ${ext.toUpperCase()}</p>
                                </div>
                            </div>
                            <button class="text-red-600 hover:text-red-800 text-sm font-medium px-2 py-1 rounded hover:bg-red-50" onclick="removeFile(${index})">
                                Remove
                            </button>
                        `;
                        filesList.appendChild(fileDiv);
                    });
                } else {
                    selectedFilesDiv.classList.add('hidden');
                }
            }

            // Get file icon based on extension
            function getFileIcon(ext) {
                switch(ext) {
                    case 'pdf':
                        return 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z';
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                        return 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
                    default:
                        return 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                }
            }

            // Remove file function
            window.removeFile = function(index) {
                if (index >= 0 && index < selectedFiles.length) {
                    const removedFile = selectedFiles.splice(index, 1)[0];
                    displaySelectedFiles();
                    fileInput.value = '';
                }
            };

            // Clear all files
            clearBtn.addEventListener('click', function() {
                selectedFiles = [];
                fileInput.value = '';
                displaySelectedFiles();
                showAlert('success', 'All files cleared.');
            });

            // Show alert
            function showAlert(type, message) {
                alertDiv.classList.remove('hidden', 'bg-green-100', 'bg-red-100', 'text-green-800', 'text-red-800');

                if (type === 'success') {
                    alertDiv.classList.add('bg-green-100', 'text-green-800');
                } else {
                    alertDiv.classList.add('bg-red-100', 'text-red-800');
                }

                alertMessage.textContent = message;

                setTimeout(() => {
                    alertDiv.classList.add('hidden');
                }, 5000);
            }

            // Upload files
            uploadBtn.addEventListener('click', async function() {
                if (selectedFiles.length === 0) {
                    showAlert('error', 'No files selected for upload.');
                    return;
                }

                uploadBtn.disabled = true;
                uploadText.classList.add('hidden');
                uploadSpinner.classList.remove('hidden');
                uploadProgress.classList.remove('hidden');

                try {
                    const formData = new FormData();
                    selectedFiles.forEach(file => formData.append('files[]', file));
                    if (categorySelect.value) {
                        formData.append('category', categorySelect.value);
                    }

                    const response = await fetch('{{ route("documents.upload") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: formData,
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Upload failed');
                    }

                    showAlert('success', `Successfully uploaded ${data.files.length} file(s).`);

                    // Reload the page to show new documents
                    window.location.reload();

                } catch (error) {
                    console.error('Upload error:', error);
                    showAlert('error', 'Upload failed: ' + error.message);
                } finally {
                    uploadBtn.disabled = false;
                    uploadText.classList.remove('hidden');
                    uploadSpinner.classList.add('hidden');
                    uploadProgress.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>
</x-app-layout>
