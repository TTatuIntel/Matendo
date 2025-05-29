<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'matendo_medics';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID
$user_email = $_SESSION['user_email'];
$user_query = $conn->prepare("SELECT id FROM users WHERE email = ?");
$user_query->bind_param("s", $user_email);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();
$user_id = $user['id'];

// Process form data
$response = ['success' => false, 'message' => ''];

try {
    // Begin transaction
    $conn->begin_transaction();

    // Update basic info
    $update_query = $conn->prepare("
        UPDATE users SET 
            full_name = ?, 
            phone = ?, 
            location = ?, 
            summary = ?
        WHERE id = ?
    ");
    $update_query->bind_param(
        "ssssi",
        $_POST['full_name'],
        $_POST['phone'],
        $_POST['location'],
        $_POST['summary'],
        $user_id
    );
    $update_query->execute();

    // Handle experiences
    $conn->query("DELETE FROM user_experiences WHERE user_id = $user_id");
    if (isset($_POST['experience'])) {
        $exp_stmt = $conn->prepare("
            INSERT INTO user_experiences 
            (user_id, title, company, start_date, end_date, current, responsibilities)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($_POST['experience'] as $exp) {
            $current = isset($exp['current']) ? 1 : 0;
            $end_date = $current ? null : $exp['end_date'];
            $exp_stmt->bind_param(
                "issssis",
                $user_id,
                $exp['title'],
                $exp['company'],
                $exp['start_date'],
                $end_date,
                $current,
                $exp['responsibilities']
            );
            $exp_stmt->execute();
        }
    }

    // Handle education
    $conn->query("DELETE FROM user_education WHERE user_id = $user_id");
    if (isset($_POST['education'])) {
        $edu_stmt = $conn->prepare("
            INSERT INTO user_education 
            (user_id, degree, institution, completion_date, gpa)
            VALUES (?, ?, ?, ?, ?)
        ");
        foreach ($_POST['education'] as $edu) {
            $edu_stmt->bind_param(
                "issss",
                $user_id,
                $edu['degree'],
                $edu['institution'],
                $edu['completion_date'],
                $edu['gpa']
            );
            $edu_stmt->execute();
        }
    }

    // Handle skills
    $conn->query("DELETE FROM user_skills WHERE user_id = $user_id");
    if (isset($_POST['skills'])) {
        $skills = json_decode($_POST['skills']);
        $skill_stmt = $conn->prepare("
            INSERT INTO user_skills (user_id, skill) VALUES (?, ?)
        ");
        foreach ($skills as $skill) {
            $skill_stmt->bind_param("is", $user_id, $skill);
            $skill_stmt->execute();
        }
    }

    // Handle certifications
    $conn->query("DELETE FROM user_certifications WHERE user_id = $user_id");
    if (isset($_POST['certifications'])) {
        $certs = json_decode($_POST['certifications']);
        $cert_stmt = $conn->prepare("
            INSERT INTO user_certifications (user_id, certification) VALUES (?, ?)
        ");
        foreach ($certs as $cert) {
            $cert_stmt->bind_param("is", $user_id, $cert);
            $cert_stmt->execute();
        }
    }

    // Handle documents
    if (!empty($_FILES['documents'])) {
        $upload_dir = 'uploads/documents/' . $user_id . '/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $doc_stmt = $conn->prepare("
            INSERT INTO user_documents 
            (user_id, document_name, document_path, document_type)
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['documents']['name'][$key];
            $file_type = $_FILES['documents']['type'][$key];
            $file_path = $upload_dir . basename($file_name);
            
            if (move_uploaded_file($tmp_name, $file_path)) {
                $doc_type = strpos($file_type, 'pdf') !== false ? 'pdf' : 
                           (strpos($file_type, 'word') !== false ? 'doc' : 
                           (strpos($file_type, 'image') !== false ? 'image' : 'other');
                
                $doc_stmt->bind_param(
                    "isss",
                    $user_id,
                    $file_name,
                    $file_path,
                    $doc_type
                );
                $doc_stmt->execute();
            }
        }
    }

    // Commit transaction
    $conn->commit();
    $response['success'] = true;
    $response['message'] = 'Profile updated successfully';
    $response['referenceNumber'] = 'PROF-' . time() . '-' . $user_id;
} catch (Exception $e) {
    $conn->rollback();
    $response['message'] = 'Error updating profile: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>