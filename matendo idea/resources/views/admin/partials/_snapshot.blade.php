<!DOCTYPE html>
<html>
<head>
    <title>Application Snapshot - {{ $application->full_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #1e40af; }
        .section { margin-bottom: 20px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Application Snapshot - {{ $application->full_name }}</h1>

    <div class="section">
        <h2>Personal Information</h2>
        <p><span class="label">Name:</span> {{ $application->full_name }}</p>
        <p><span class="label">Email:</span> {{ $application->email }}</p>
        <p><span class="label">Phone:</span> {{ $application->phone ?? 'N/A' }}</p>
        <p><span class="label">Address:</span> {{ $application->address ?? 'N/A' }}</p>
        <p><span class="label">Location:</span> {{ $application->location ?? 'N/A' }}</p>
        <p><span class="label">Coordinates:</span> {{ $application->coordinates ?? 'N/A' }}</p>
    </div>

    <div class="section">
        <h2>Professional Information</h2>
        <p><span class="label">Profession:</span> {{ $application->profession ?? 'N/A' }}</p>
        <p><span class="label">Other Profession:</span> {{ $application->other_profession ?? 'N/A' }}</p>
        <p><span class="label">Specialization:</span> {{ $application->specialization ?? 'N/A' }}</p>
        <p><span class="label">Experience:</span> {{ $application->years_experience ?? 'N/A' }} years</p>
        <p><span class="label">License Number:</span> {{ $application->license_number ?? 'N/A' }}</p>
    </div>

    <div class="section">
        <h2>Supporting Documents</h2>
        <p><span class="label">Resume:</span> {{ $application->resume ? 'Uploaded' : 'Not Provided' }}</p>
        <p><span class="label">License:</span> {{ $application->license_doc ? 'Uploaded' : 'Not Provided' }}</p>
        <p><span class="label">Certifications:</span> {{ $application->certifications ? 'Uploaded' : 'Not Provided' }}</p>
    </div>

    <div class="section">
        <h2>Work Preferences</h2>
        <p><span class="label">Work Type:</span> {{ $application->work_type ?? 'N/A' }}</p>
        <p><span class="label">Shift Type:</span> {{ $application->shift_type ?? 'N/A' }}</p>
        <p><span class="label">Preferred Location:</span> {{ $application->preferred_location ?? 'N/A' }}</p>
        <p><span class="label">Start Date:</span> {{ $application->start_date ? $application->start_date->format('Y-m-d') : 'N/A' }}</p>
    </div>

    <div class="section">
        <h2>Application Details</h2>
        <p><span class="label">Status:</span> {{ $status }}</p>
        <p><span class="label">Submitted:</span> {{ $application->created_at->format('Y-m-d') }}</p>
    </div>
</body>
</html>