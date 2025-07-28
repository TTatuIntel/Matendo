<?php
session_start();
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'matendo_medics';

const MAX_FILE_SIZE = 5 * 1024 * 1024;
const ALLOWED_DOC_TYPES = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg',
    'image/png',
    'image/jpg'
];

function generateReferenceNumber() {
    return 'REQ-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
}

function sanitizeInput($data) {
    return $data === null ? null : htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function getBase64File($key, $allowed_types) {
    if (!isset($_FILES[$key]) || $_FILES[$key]['error'] !== UPLOAD_ERR_OK) return [null, null, null, null];
    $file = $_FILES[$key];
    if ($file['size'] > MAX_FILE_SIZE) throw new Exception(ucfirst($key) . ' file is too large. Max size 5MB.');
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mimeType, $allowed_types)) throw new Exception("Invalid $key file type.");
    $fileContent = file_get_contents($file['tmp_name']);
    if ($fileContent === false) throw new Exception("Failed to read $key file.");
    return [base64_encode($fileContent), sanitizeInput($file['name']), $mimeType, $file['size']];
}

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") throw new Exception('Invalid request method');
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) throw new Exception('Database connection failed');
    $conn->set_charset("utf8mb4");

    $reference_number = generateReferenceNumber();

    // Sanitize and collect form data
    $facility_name = sanitizeInput($_POST['facilityName'] ?? '');
    $contact_person = sanitizeInput($_POST['contactPerson'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $coordinates = sanitizeInput($_POST['coordinates'] ?? null);
    
    // Handle JSON fields for multi-select
    $facility_type = json_encode($_POST['facilityType'] ?? []);
    $other_facility_type = sanitizeInput($_POST['otherFacilityType'] ?? null);
    $positions = json_encode($_POST['positions'] ?? []);
    $other_position = sanitizeInput($_POST['otherPosition'] ?? null);
    $employment_type = json_encode($_POST['duration'] ?? []);
    $shift_type = json_encode($_POST['shiftType'] ?? []);
    
    $staff_number = intval($_POST['staffNumber'] ?? 0);
    $start_date = sanitizeInput($_POST['startDate'] ?? null);
    $job_requirement_option = sanitizeInput($_POST['job-requirement-option'] ?? '');
    $qualifications = sanitizeInput($_POST['qualifications'] ?? null);
    $experience = sanitizeInput($_POST['experience'] ?? null);
    $job_description = sanitizeInput($_POST['jobDescription'] ?? null);

    // Handle file upload for job description
    list($job_description_file, $job_description_file_name, $job_description_file_type, $job_description_file_size) = 
        ($job_requirement_option === 'upload') ? getBase64File('jobDescriptionFile', ALLOWED_DOC_TYPES) : [null, null, null, null];

    // Additional metadata
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $csrf_token = substr(md5(uniqid(mt_rand(), true)), 0, 32);
    $form_metadata = json_encode([
        'submission_timestamp' => date('Y-m-d H:i:s'),
        'form_version' => '1.0',
        'job_requirement_option' => $job_requirement_option
    ]);

    $stmt = $conn->prepare("
        INSERT INTO facility_requests (
            reference_number, facility_name, contact_person, email, phone, coordinates,
            facility_type, other_facility_type, positions, other_position, 
            employment_type, shift_type, staff_number, start_date, 
            job_requirement_option, qualifications, experience, job_description,
            job_description_file, job_description_file_name, job_description_file_type, job_description_file_size,
            csrf_token, status, priority, confirmed, ip_address, user_agent, form_metadata,
            created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'normal', 0, ?, ?, ?,
            NOW(), NOW()
        )
    ");

    if (!$stmt) throw new Exception('Failed to prepare statement: ' . $conn->error);

    $stmt->bind_param(
        "sssssssssssissssssssisssss",
        $reference_number,
        $facility_name,
        $contact_person,
        $email,
        $phone,
        $coordinates,
        $facility_type,
        $other_facility_type,
        $positions,
        $other_position,
        $employment_type,
        $shift_type,
        $staff_number,
        $start_date,
        $job_requirement_option,
        $qualifications,
        $experience,
        $job_description,
        $job_description_file,
        $job_description_file_name,
        $job_description_file_type,
        $job_description_file_size,
        $csrf_token,
        $ip_address,
        $user_agent,
        $form_metadata
    );

    if (!$stmt->execute()) throw new Exception('Failed to save facility request: ' . $stmt->error);

    $request_id = $conn->insert_id;
    echo json_encode([
        'success' => true,
        'message' => 'Facility request submitted successfully',
        'reference_number' => $reference_number,
        'submission_date' => date('Y-m-d H:i:s'),
        'request_id' => $request_id
    ]);
    $stmt->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) $conn->close();
}
?>