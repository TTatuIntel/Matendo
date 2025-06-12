<!-- resources/views/admin/application_snapshot.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Snapshot - {{ $application->reference_number }}</title>
    <style>
        body {
            font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .id-card {
            width: 350px;
            height: 520px; /* Increased slightly to accommodate contact info */
            background-color: #ffffff;
            border: 2px solid #005c9d; /* Matendo Health primary color (placeholder) */
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
        }
        .header {
            background: linear-gradient(135deg, #005c9d, #0078c1); /* Gradient for modern look */
            color: #ffffff;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        .header img.logo {
            position: absolute;
            left: 15px;
            top: 15px;
            height: 45px; /* Slightly larger logo */
            object-fit: contain;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            font-weight: 500;
            text-transform: uppercase;
        }
        .header .ref-number {
            font-size: 12px;
            margin-top: 8px;
            opacity: 0.85;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 15px 20px;
        }
        .content h2 {
            font-size: 14px;
            color: #005c9d;
            margin: 0 0 12px;
            font-weight: 500;
        }
        .content p {
            margin: 6px 0;
            font-size: 11px;
            line-height: 1.5;
        }
        .label {
            font-weight: 600;
            color: #2d3748;
            display: inline-block;
            width: 90px; /* Adjusted for tighter alignment */
        }
        .value {
            color: #4a5568;
        }
        .status {
            background-color: #e6f3ff;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
            font-size: 10px;
            text-transform: uppercase;
            color: #005c9d;
            margin-top: 8px;
            font-weight: 600;
        }
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            background-color: #f7fafc;
            padding: 12px;
            text-align: center;
            font-size: 9px;
            color: #4a5568;
            border-top: 1px solid #e2e8f0;
            line-height: 1.6;
        }
        .footer .contact {
            margin: 3px 0;
        }
        .footer a {
            color: #005c9d;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }
            .id-card {
                box-shadow: none;
                border: 1px solid #000;
            }
            .footer a {
                text-decoration: none; /* Remove hover effects for print */
            }
        }
    </style>
</head>
<body>
    <div class="id-card">
        <div class="header">
            <!-- Replace with Matendo Health's actual logo URL or local path -->
            <img src="{{ asset('images/logo.png') }}" alt="Matendo Health Logo" class="logo">
            <h1>Matendo Health</h1>
            <div class="ref-number">Application ID: {{ $application->reference_number }}</div>
        </div>
        <div class="content">
            <h2>Applicant Information</h2>
            <p><span class="label">Name:</span> <span class="value">{{ $application->first_name }} {{ $application->last_name }}</span></p>
            <p><span class="label">Email:</span> <span class="value">{{ $application->email }}</span></p>
            <p><span class="label">Phone:</span> <span class="value">{{ $application->phone ?? 'N/A' }}</span></p>
            <p><span class="label">Address:</span> <span class="value">{{ $application->address ?? 'N/A' }}</span></p>
            <p><span class="label">Specialization:</span> <span class="value">{{ $application->specialization ?? 'N/A' }}</span></p>
            <p><span class="label">Status:</span> <span class="status">{{ $application->status }}</span></p>
            <p><span class="label">Applied On:</span> <span class="value">{{ $application->created_at->format('Y-m-d H:i:s') }}</span></p>
        </div>
        <div class="footer">
            <div class="contact">Matendo Health</div>
            <div class="contact">Email: <a href="mailto:info@matendohealth.com">info@matendohealth.com</a></div>
            <div class="contact">Phone: +256 781053 105</div>
            <div class="contact">Address: P.O. Box 1234-00100, Nairobi, Kenya</div>
            <div class="contact"><a href="https://matendohealth.com">matendohealth.com</a></div>
        </div>
    </div>
</body>
</html>