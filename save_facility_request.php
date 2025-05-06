<?php
ini_set('display_errors', 1); // Enable for debugging
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
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
    // Sanitize and collect form data
    $facilityName = htmlspecialchars($_POST['facilityName'] ?? '', ENT_QUOTES, 'UTF-8');
    $contactPerson = htmlspecialchars($_POST['contactPerson'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ? $_POST['email'] : '';
    $phone = preg_replace('/[^0-9+]/', '', $_POST['phone'] ?? '');
    $coordinates = htmlspecialchars($_POST['coordinates'] ?? '', ENT_QUOTES, 'UTF-8');
    $facilityType = is_array($_POST['facilityType']) ? implode(', ', array_map('htmlspecialchars', $_POST['facilityType'])) : htmlspecialchars($_POST['facilityType'] ?? '', ENT_QUOTES, 'UTF-8');
    $positions = is_array($_POST['positions']) ? implode(', ', array_map('htmlspecialchars', $_POST['positions'])) : htmlspecialchars($_POST['positions'] ?? '', ENT_QUOTES, 'UTF-8');
    $employmentType = is_array($_POST['duration']) ? implode(', ', array_map('htmlspecialchars', $_POST['duration'])) : htmlspecialchars($_POST['duration'] ?? '', ENT_QUOTES, 'UTF-8');
    $shiftType = is_array($_POST['shiftType']) ? implode(', ', array_map('htmlspecialchars', $_POST['shiftType'])) : htmlspecialchars($_POST['shiftType'] ?? '', ENT_QUOTES, 'UTF-8');
    $staffNumber = filter_var($_POST['staffNumber'] ?? '', FILTER_VALIDATE_INT) ? (int)$_POST['staffNumber'] : 0;
    $startDate = htmlspecialchars($_POST['startDate'] ?? '', ENT_QUOTES, 'UTF-8');
    $jobRequirementOption = htmlspecialchars($_POST['job-requirement-option'] ?? '', ENT_QUOTES, 'UTF-8');
    $qualifications = htmlspecialchars($_POST['qualifications'] ?? '', ENT_QUOTES, 'UTF-8');
    $experience = htmlspecialchars($_POST['experience'] ?? '', ENT_QUOTES, 'UTF-8');
    $jobDescription = htmlspecialchars($_POST['jobDescription'] ?? '', ENT_QUOTES, 'UTF-8');
    $jobDescriptionFile = $_FILES['jobDescriptionFile']['name'] ? htmlspecialchars($_FILES['jobDescriptionFile']['name'], ENT_QUOTES, 'UTF-8') : '';
    $referenceNumber = htmlspecialchars($_POST['referenceNumber'] ?? '', ENT_QUOTES, 'UTF-8');
    $submissionDate = htmlspecialchars($_POST['submissionDate'] ?? '', ENT_QUOTES, 'UTF-8');
    $csrfToken = htmlspecialchars($_POST['csrfToken'] ?? '', ENT_QUOTES, 'UTF-8');

    // Handle file upload
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["jobDescriptionFile"]["name"]);
    if (!empty($_FILES["jobDescriptionFile"]["name"])) {
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        move_uploaded_file($_FILES["jobDescriptionFile"]["tmp_name"], $targetFile);
    }

    // Prepare and execute SQL query
    $stmt = $pdo->prepare("INSERT INTO facility_hiring_requests (
        facility_name, contact_person, email, phone, coordinates, facility_type, positions, 
        employment_type, shift_type, staff_number, start_date, job_requirement_option, 
        qualifications, experience, job_description, job_description_file, reference_number, 
        submission_date, csrf_token
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $facilityName, $contactPerson, $email, $phone, $coordinates, $facilityType, $positions,
        $employmentType, $shiftType, $staffNumber, $startDate, $jobRequirementOption,
        $qualifications, $experience, $jobDescription, $jobDescriptionFile, $referenceNumber,
        $submissionDate, $csrfToken
    ]) or die(print_r($stmt->errorInfo(), true));

    // Return success response
    echo json_encode(["success" => true, "message" => "Request submitted successfully", "referenceNumber" => $referenceNumber]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>