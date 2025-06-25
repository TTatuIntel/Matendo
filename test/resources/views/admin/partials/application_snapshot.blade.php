<!-- resources/views/admin/application_snapshot.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Application Snapshot - {{ $application->reference_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #1f2937; }
        .section { margin-bottom: 20px; }
        .label { font-weight: bold; }
        p { margin: 5px 0; }
    </style>
</head>
<body>
    <h1>Application Snapshot - {{ $application->reference_number }}</h1>
    
    <div class="section">
        <h2>Personal Information</h2>
        <p><span class="label">Name:</span> {{ $application->first_name }} {{ $application->last_name }}</p>
        <p><span class="label">Email:</span> {{ $application->email }}</p>
        <p><span class="label">Phone:</span> {{ $application->phone ?? 'N/A' }}</p>
        <p><span class="label">Address:</span> {{ $application->address ?? 'N/A' }}</p>
    </div>
    
    <div class="section">
        <h2>Professional Information</h2>
        <p><span class="label">Profession:</span> {{ $application->profession ?? 'N/A' }}</p>
        <p><span class="label">Specialization:</span> {{ $application->specialization ?? 'N/A' }}</p>
        <p><span class="label">Experience:</span> {{ $application->years_experience }} years</p>
        <p><span class="label">Start Date:</span> {{ $application->start_date?->format('Y-m-d') ?? 'N/A' }}</p>
        <p><span class="label">License Number:</span> {{ $application->license_number ?? 'N/A' }}</p>
    </div>
    
    <div class="section">
        <h2>Work Preferences</h2>
        <p><span class="label">Work Type:</span> {{ $application->work_type ?? 'N/A' }}</p>
        <p><span class="label">Shift Type:</span> {{ $application->shift_type ?? 'N/A' }}</p>
        <p><span class="label">Preferred Location:</span> {{ $application->preferred_location ?? 'N/A' }}</p>
    </div>
    
    <div class="section">
        <h2>Supporting Documents</h2>
        <p><span class="label">Resume:</span> {{ $application->resume ? 'Provided' : 'Not Provided' }}</p>
        <p><span class="label">License:</span> {{ $application->license_doc ? 'Provided' : 'Not Provided' }}</p>
        <p><span class="label">Certifications:</span> {{ $application->certifications ? 'Provided' : 'Not Provided' }}</p>
    </div>
    
    <div class="section">
        <h2>Application Details</h2>
        <p><span class="label">Reference Number:</span> {{ $application->reference_number }}</p>
        <p><span class="label">Status:</span> {{ ucfirst($application->status) }}</p>
        <p><span class="label">Submitted At:</span> {{ $application->created_at->format('Y-m-d H:i:s') }}</p>
    </div>
    
    <div class="section">
        <h2>User Credentials</h2>
        <p><span class="label">Email:</span> {{ $user->email }}</p>
        <p><span class="label">Password:</span> [Redacted for security]</p>
    </div>
</body>
</html>