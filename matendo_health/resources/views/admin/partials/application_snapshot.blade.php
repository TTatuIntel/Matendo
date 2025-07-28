<!-- resources/views/admin/application_snapshot.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Snapshot - {{ $application->reference_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            color: #333;
        }
        .header {
            text-align: center;
            background-color: #2563eb;
            color: white;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #f1f5f9;
            padding: 10px 15px;
            margin-bottom: 15px;
            border-left: 4px solid #2563eb;
            font-weight: bold;
            font-size: 16px;
        }
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #64748b;
            padding: 8px 15px 8px 0;
            width: 180px;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-approved {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
        .documents-list {
            margin-top: 10px;
        }
        .document-item {
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .work-preferences {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 6px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Healthcare Worker Application</h1>
        <p>Application Snapshot - Generated on {{ $generated_at->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="content">
        <!-- Application Overview -->
        <div class="section">
            <div class="section-title">Application Overview</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Reference Number:</div>
                    <div class="info-value"><strong>{{ $application->reference_number }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="status-badge status-approved">Approved</span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Submitted Date:</div>
                    <div class="info-value">{{ $application->created_at->format('F j, Y \a\t g:i A') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Processed Date:</div>
                    <div class="info-value">{{ now()->format('F j, Y \a\t g:i A') }}</div>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="section">
            <div class="section-title">Personal Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Full Name:</div>
                    <div class="info-value">{{ $application->first_name }} {{ $application->last_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email Address:</div>
                    <div class="info-value">{{ $application->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Phone Number:</div>
                    <div class="info-value">{{ $application->phone ?? 'Not provided' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Address:</div>
                    <div class="info-value">{{ $application->address ?? 'Not provided' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Location:</div>
                    <div class="info-value">{{ $application->location ?? 'Not provided' }}</div>
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="section">
            <div class="section-title">Professional Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Profession:</div>
                    <div class="info-value">{{ $application->profession }}</div>
                </div>
                @if($application->other_profession)
                <div class="info-row">
                    <div class="info-label">Other Profession:</div>
                    <div class="info-value">{{ $application->other_profession }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Specialization:</div>
                    <div class="info-value">{{ $application->specialization ?? 'Not specified' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Years of Experience:</div>
                    <div class="info-value">{{ $application->years_experience }} years</div>
                </div>
                <div class="info-row">
                    <div class="info-label">License Number:</div>
                    <div class="info-value">{{ $application->license_number ?? 'Not provided' }}</div>
                </div>
            </div>
        </div>

        <!-- Work Preferences -->
        <div class="section">
            <div class="section-title">Work Preferences</div>
            <div class="work-preferences">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Work Type:</div>
                        <div class="info-value">
                            @if($application->work_type)
                                {{ is_array($application->work_type) ? implode(', ', $application->work_type) : $application->work_type }}
                            @else
                                Not specified
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Shift Type:</div>
                        <div class="info-value">
                            @if($application->shift_type)
                                {{ is_array($application->shift_type) ? implode(', ', $application->shift_type) : $application->shift_type }}
                            @else
                                Not specified
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Preferred Location:</div>
                        <div class="info-value">{{ $application->preferred_location ?? 'Not specified' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Available From:</div>
                        <div class="info-value">
                            {{ $application->start_date ? $application->start_date->format('F j, Y') : 'Not specified' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submitted Documents -->
        <div class="section">
            <div class="section-title">Submitted Documents</div>
            <div class="documents-list">
                <div class="document-item">
                    <strong>Resume:</strong> 
                    {{ $application->resume_name ? '✓ ' . $application->resume_name : '✗ Not provided' }}
                </div>
                <div class="document-item">
                    <strong>Professional License:</strong> 
                    {{ $application->license_name ? '✓ ' . $application->license_name : '✗ Not provided' }}
                </div>
                <div class="document-item">
                    <strong>Certifications:</strong> 
                    {{ $application->certifications_name ? '✓ ' . $application->certifications_name : '✗ Not provided' }}
                </div>
            </div>
        </div>

        <!-- User Account Information -->
        <div class="section">
            <div class="section-title">User Account Created</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">User ID:</div>
                    <div class="info-value">{{ $user->id }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Account Email:</div>
                    <div class="info-value">{{ $user->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">User Type:</div>
                    <div class="info-value">{{ ucfirst($user->usertype) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email Verified:</div>
                    <div class="info-value">{{ $user->email_verified_at ? 'Yes' : 'No' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Account Created:</div>
                    <div class="info-value">{{ $user->created_at->format('F j, Y \a\t g:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>Healthcare Worker Registration System</strong></p>
        <p>This is an automatically generated document. Please keep this for your records.</p>
        <p>Generated on {{ $generated_at->format('F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>