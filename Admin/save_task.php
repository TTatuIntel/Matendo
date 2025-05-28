<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "matendo_medics";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$facility_id = isset($_POST['facility_id']) ? intval($_POST['facility_id']) : 0;
$assignee_id = isset($_POST['assignee_id']) ? intval($_POST['assignee_id']) : 0;
$description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';
$due_date = isset($_POST['due_date']) ? $conn->real_escape_string($_POST['due_date']) : '';
$priority = isset($_POST['priority']) ? $conn->real_escape_string($_POST['priority']) : '';
$status = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : '';

if ($facility_id <= 0 || $assignee_id <= 0 || empty($description) || empty($due_date) || empty($priority) || empty($status)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO tasks (facility_id, assignee_id, description, due_date, priority, status) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissss", $facility_id, $assignee_id, $description, $due_date, $priority, $status);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'task_id' => $stmt->insert_id]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save task: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>