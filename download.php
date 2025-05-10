<?php
$host = 'localhost';
$username = 'root'; // Replace with your DB username
$password = ''; // Replace with your DB password
$database = 'matendo_medics';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$type = $_GET['type'] ?? '';
$id = intval($_GET['id'] ?? 0);
if (!$id || !in_array($type, ['resume', 'license', 'certifications'])) {
    die("Invalid request");
}

$column_map = [
    'resume' => ['data' => 'resume_data', 'name' => 'resume_name'],
    'license' => ['data' => 'license_doc_data', 'name' => 'license_doc_name'],
    'certifications' => ['data' => 'certifications_data', 'name' => 'certifications_name']
];

$query = "SELECT {$column_map[$type]['data']}, {$column_map[$type]['name']} FROM healthcare_professionals WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row && $row[$column_map[$type]['data']]) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $row[$column_map[$type]['name']] . '"');
    echo $row[$column_map[$type]['data']];
} else {
    die("File not found");
}

$stmt->close();
$conn->close();
?>