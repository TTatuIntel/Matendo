<!DOCTYPE html>
<html>
<head>
    <title>Applications Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; text-align: left; }
    </style>
</head>
<body>
    <h1>Applications Report</h1>
    <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Reference</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Applied On</th>
                <th>Specialization</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $app)
            <tr>
                <td>{{ $app->id }}</td>
                <td>{{ $app->reference_number }}</td>
                <td>{{ $app->first_name }} {{ $app->last_name }}</td>
                <td>{{ $app->email }}</td>
                <td>{{ ucfirst($app->status) }}</td>
                <td>{{ $app->created_at->format('Y-m-d') }}</td>
                <td>{{ $app->specialization }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
