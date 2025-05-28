<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "matendo_medics";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];
    $action = $data['action'];

    // Fetch the application
    $result = $conn->query("SELECT * FROM healthcare_professionals WHERE id = $id");
    $row = $result->fetch_assoc();

    if ($row) {
        $insert_query = "INSERT INTO " . ($action === 'approve' ? 'approved_applications' : 'rejected_applications') . " 
            (applicant_id, first_name, last_name, email, profession, specialization, years_experience, start_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param(
            "issssiss",
            $row['id'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['profession'],
            $row['specialization'],
            $row['years_experience'],
            $row['start_date']
        );
        $stmt->execute();

        // Remove from original table
        $conn->query("DELETE FROM healthcare_professionals WHERE id = $id");
    }

    echo json_encode(['status' => 'success']);
}

$conn->close();
?>