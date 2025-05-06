<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED);

$host = 'localhost';
$dbname = 'matendo_medics';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    if (empty($_POST['fullName']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['address']) ||
        empty($_POST['careRequirements']) || empty($_POST['emergencyContact']) || empty($_POST['emergencyPhone'])) {
        die(json_encode(["success" => false, "message" => "Required fields are missing"]));
    }

    // Sanitize and collect personal care form data
    $fullName = htmlspecialchars($_POST['fullName'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ? $_POST['email'] : '';
    $phone = preg_replace('/[^0-9+]/', '', $_POST['phone'] ?? '');
    $address = htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES, 'UTF-8');
    $careType = is_array($_POST['careType']) ? implode(', ', array_map('htmlspecialchars', $_POST['careType'])) : htmlspecialchars($_POST['careType'] ?? '', ENT_QUOTES, 'UTF-8');
    $careRequirements = htmlspecialchars($_POST['careRequirements'] ?? '', ENT_QUOTES, 'UTF-8');
    $schedule = is_array($_POST['schedule']) ? implode(', ', array_map('htmlspecialchars', $_POST['schedule'])) : htmlspecialchars($_POST['schedule'] ?? '', ENT_QUOTES, 'UTF-8');
    $medicalConditions = htmlspecialchars($_POST['medicalConditions'] ?? '', ENT_QUOTES, 'UTF-8');
    $medications = htmlspecialchars($_POST['medications'] ?? '', ENT_QUOTES, 'UTF-8');
    $allergies = htmlspecialchars($_POST['allergies'] ?? '', ENT_QUOTES, 'UTF-8');
    $emergencyContact = htmlspecialchars($_POST['emergencyContact'] ?? '', ENT_QUOTES, 'UTF-8');
    $emergencyPhone = preg_replace('/[^0-9+]/', '', $_POST['emergencyPhone'] ?? '');
    $referenceNumber = htmlspecialchars($_POST['referenceNumber'] ?? '', ENT_QUOTES, 'UTF-8');
    $submissionDate = htmlspecialchars($_POST['submissionDate'] ?? '', ENT_QUOTES, 'UTF-8');
    $csrfToken = htmlspecialchars($_POST['csrfToken'] ?? '', ENT_QUOTES, 'UTF-8');

    // Insert into personal_care_requests table
    $stmt = $pdo->prepare("INSERT INTO personal_care_requests (
        full_name, email, phone, address, care_type, care_requirements, schedule, 
        medical_conditions, medications, allergies, emergency_contact, emergency_phone, 
        reference_number, submission_date, csrf_token
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $fullName, $email, $phone, $address, $careType, $careRequirements, $schedule,
        $medicalConditions, $medications, $allergies, $emergencyContact, $emergencyPhone,
        $referenceNumber, $submissionDate, $csrfToken
    ]) or die(print_r($stmt->errorInfo(), true));

    echo json_encode(["success" => true, "message" => "Request submitted successfully", "referenceNumber" => $referenceNumber]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>