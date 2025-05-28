<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "matendo_medics";

$conn = new mysqli($servername, $username, $password, $dbname);
$status = $_GET['status'];
$table = 'healthcare_professionals';
if ($status === 'Approved') {
    $table = 'approved_applications';
} elseif ($status === 'Rejected') {
    $table = 'rejected_applications';
}

$result = $conn->query("SELECT * FROM $table");
$applications = [];
while ($row = $result->fetch_assoc()) {
    $applications[] = $row;
}

header('Content-Type: application/json');
echo json_encode($applications);

$conn->close();
?>