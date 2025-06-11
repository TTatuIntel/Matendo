<!DOCTYPE html>
<html>
<head>
    <title>Application Snapshot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        h1 {
            color: #2f855a;
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        h2 {
            color: #4a5568;
            font-size: 18px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid #2f855a;
            padding-bottom: 5px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section p {
            margin: 5px 0;
            font-size: 14px;
        }
        .section strong {
            color: #4a5568;
            width: 150px;
            display: inline-block;
        }
        .status {
            color: #2f855a;
            font-weight: bold;
            text-transform: uppercase;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #718096;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Application Snapshot</h1>
        <p class="status">Status: <?php echo e($status); ?></p>

        <div class="section">
            <h2>Personal Information</h2>
            <p><strong>Name:</strong> <?php echo e($application->first_name); ?> <?php echo e($application->last_name); ?></p>
            <p><strong>Email:</strong> <?php echo e($application->email); ?></p>
            <p><strong>Phone:</strong> <?php echo e($application->phone); ?></p>
            <p><strong>Address:</strong> <?php echo e($application->address); ?></p>
        </div>

        <div class="section">
            <h2>Professional Information</h2>
            <p><strong>Profession:</strong> <?php echo e($application->profession); ?></p>
            <p><strong>Specialization:</strong> <?php echo e($application->specialization ?? 'N/A'); ?></p>
            <p><strong>Experience:</strong> <?php echo e($application->years_experience); ?> years</p>
            <p><strong>License Number:</strong> <?php echo e($application->license_number ?? 'N/A'); ?></p>
        </div>

        <div class="section">
            <h2>Work Preferences</h2>
            <p><strong>Work Type:</strong> <?php echo e($application->work_type ?? 'N/A'); ?></p>
            <p><strong>Shift Type:</strong> <?php echo e($application->shift_type ?? 'N/A'); ?></p>
            <p><strong>Preferred Location:</strong> <?php echo e($application->preferred_location ?? 'N/A'); ?></p>
            <p><strong>Start Date:</strong> <?php echo e($application->start_date ? $application->start_date->format('Y-m-d') : 'N/A'); ?></p>
        </div>

        <div class="section">
            <h2>Supporting Documents</h2>
            <p><strong>Resume:</strong> <?php echo e($application->resume ? 'Available' : 'N/A'); ?></p>
            <p><strong>License:</strong> <?php echo e($application->license_doc ? 'Available' : 'N/A'); ?></p>
            <p><strong>Certifications:</strong> <?php echo e($application->certifications ? 'Available' : 'N/A'); ?></p>
        </div>

        <div class="footer">
            <p>Generated on <?php echo e(now()->format('Y-m-d H:i:s')); ?></p>
            <p>Matendo Application System</p>
        </div>
    </div>
</body>
</html><?php /**PATH D:\lugs\matendo\resources\views/admin/partials/_snapshot.blade.php ENDPATH**/ ?>