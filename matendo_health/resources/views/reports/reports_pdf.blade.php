
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Comprehensive Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Comprehensive Report</h1>

    <h2>Applications</h2>
    <table>
        <thead>
            <tr>
                <th>Reference Number</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Profession</th>
                <th>Specialization</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($applications as $application)
                <tr>
                    <td>{{ $application->reference_number }}</td>
                    <td>{{ $application->full_name }}</td>
                    <td>{{ $application->email }}</td>
                    <td>{{ $application->profession }}</td>
                    <td>{{ $application->specialization }}</td>
                    <td>{{ ucfirst($application->status) }}</td>
                    <td>{{ $application->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Facility Requests</h2>
    <table>
        <thead>
            <tr>
                <th>Facility Name</th>
                <th>Contact Person</th>
                <th>Email</th>
                <th>Facility Type</th>
                <th>Status</th>
                <th>Submitted At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($facilityRequests as $request)
                <tr>
                    <td>{{ $request->facility_name }}</td>
                    <td>{{ $request->contact_person }}</td>
                    <td>{{ $request->email }}</td>
                    <td>{{ $request->facility_type }}</td>
                    <td>{{ ucfirst($request->status) }}</td>
                    <td>{{ $request->submission_date ? $request->submission_date->format('Y-m-d') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Individual Requests</h2>
    <table>
        <thead>
            <tr>
                <th>Client Name</th>
                <th>Email</th>
                <th>Care Type</th>
                <th>Status</th>
                <th>Submitted At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($individualRequests as $request)
                <tr>
                    <td>{{ $request->full_name }}</td>
                    <td>{{ $request->email }}</td>
                    <td>{{ $request->care_type }}</td>
                    <td>{{ ucfirst($request->status) }}</td>
                    <td>{{ $request->submission_date ? $request->submission_date->format('Y-m-d') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Health Workers</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Specialty</th>
                <th>Status</th>
                <th>Verified At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($healthWorkers as $healthworker)
                <tr>
                    <td>{{ $healthworker->name }}</td>
                    <td>{{ $healthworker->user->email ?? 'N/A' }}</td>
                    <td>{{ $healthworker->specialty }}</td>
                    <td>{{ ucfirst($healthworker->status) }}</td>
                    <td>{{ $healthworker->verified_at ? $healthworker->verified_at->format('Y-m-d') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Tasks</h2>
    <table>
        <thead>
            <tr>
                <th>Facility Name</th>
                <th>Positions</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Assigned To</th>
                <th>Start Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
                <tr>
                    <td>{{ $task->facility_name }}</td>
                    <td>{{ $task->positions }}</td>
                    <td>{{ ucfirst($task->status) }}</td>
                    <td>{{ $task->priority ?? 'Medium' }}</td>
                    <td>{{ $task->assignedHealthworker->name ?? 'Unassigned' }}</td>
                    <td>{{ $task->start_date ? $task->start_date->format('Y-m-d') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Summary Statistics</h2>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Metric</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Applications</td><td>Pending</td><td>{{ $stats['applications']['pending'] }}</td></tr>
            <tr><td>Applications</td><td>Approved</td><td>{{ $stats['applications']['approved'] }}</td></tr>
            <tr><td>Applications</td><td>Rejected</td><td>{{ $stats['applications']['rejected'] }}</td></tr>
            <tr><td>Applications</td><td>Avg Review Time</td><td>{{ $stats['applications']['avgReviewTime'] }}</td></tr>
            <tr><td>Applications</td><td>New Approved</td><td>{{ $stats['applications']['newApproved'] }}</td></tr>
            <tr><td>Facility Requests</td><td>Pending</td><td>{{ $stats['facilityRequests']['pending'] }}</td></tr>
            <tr><td>Facility Requests</td><td>Approved</td><td>{{ $stats['facilityRequests']['approved'] }}</td></tr>
            <tr><td>Facility Requests</td><td>Rejected</td><td>{{ $stats['facilityRequests']['rejected'] }}</td></tr>
            <tr><td>Facility Requests</td><td>Total</td><td>{{ $stats['facilityRequests']['total'] }}</td></tr>
            <tr><td>Individual Requests</td><td>Pending</td><td>{{ $stats['individualRequests']['pending'] }}</td></tr>
            <tr><td>Individual Requests</td><td>Approved</td><td>{{ $stats['individualRequests']['approved'] }}</td></tr>
            <tr><td>Individual Requests</td><td>Rejected</td><td>{{ $stats['individualRequests']['rejected'] }}</td></tr>
            <tr><td>Individual Requests</td><td>Total</td><td>{{ $stats['individualRequests']['total'] }}</td></tr>
            <tr><td>Health Workers</td><td>Total</td><td>{{ $stats['healthWorkers']['total'] }}</td></tr>
            <tr><td>Health Workers</td><td>Verified</td><td>{{ $stats['healthWorkers']['verified'] }}</td></tr>
            <tr><td>Health Workers</td><td>Unverified</td><td>{{ $stats['healthWorkers']['unverified'] }}</td></tr>
            <tr><td>Tasks</td><td>Pending</td><td>{{ $stats['tasks']['pending'] }}</td></tr>
            <tr><td>Tasks</td><td>Approved</td><td>{{ $stats['tasks']['approved'] }}</td></tr>
            <tr><td>Tasks</td><td>Rejected</td><td>{{ $stats['tasks']['rejected'] }}</td></tr>
            <tr><td>Tasks</td><td>Completed</td><td>{{ $stats['tasks']['completed'] }}</td></tr>
            <tr><td>Tasks</td><td>Total</td><td>{{ $stats['tasks']['total'] }}</td></tr>
        </tbody>
    </table>
</body>
</html>

