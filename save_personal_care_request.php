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
    return 'IND-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
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
    $full_name = sanitizeInput($_POST['fullName'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $care_type = sanitizeInput($_POST['careType'] ?? '');
    $care_requirements = sanitizeInput($_POST['careRequirements'] ?? null);
    
    // Handle schedule as JSON for multi-select (consistent with join.php pattern)
    $schedule = json_encode($_POST['schedule'] ?? []);
    
    $medical_conditions = sanitizeInput($_POST['medicalConditions'] ?? null);
    $medications = sanitizeInput($_POST['medications'] ?? null);
    $emergency_contact = sanitizeInput($_POST['emergencyContact'] ?? '');
    $emergency_phone = sanitizeInput($_POST['emergencyPhone'] ?? '');
    $qualifications = sanitizeInput($_POST['qualifications'] ?? null);
    $experience = sanitizeInput($_POST['experience'] ?? null);
    $job_description = sanitizeInput($_POST['jobDescription'] ?? null);

    // Handle file upload for job description (using base64 storage like join.php)
    list($job_description_base64, $job_description_name, $job_description_mime, $job_description_size) = 
        getBase64File('jobDescriptionFile', ALLOWED_DOC_TYPES);

    // Additional metadata (consistent with join.php)
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $csrf_token = substr(md5(uniqid(mt_rand(), true)), 0, 32);
    $form_metadata = json_encode([
        'submission_timestamp' => date('Y-m-d H:i:s'),
        'form_version' => '1.0',
        'care_type' => $care_type
    ]);

    // Updated SQL to match the new migration schema
    $stmt = $conn->prepare("
        INSERT INTO individual_requests (
            reference_number, full_name, email, phone, address, care_type,
            care_requirements, schedule, medical_conditions, medications,
            emergency_contact, emergency_phone, qualifications, experience,
            job_description, job_description_base64, job_description_name, 
            job_description_mime, job_description_size,
            csrf_token, status, priority, confirmed, ip_address, user_agent, form_metadata,
            created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'normal', 0, ?, ?, ?,
            NOW(), NOW()
        )
    ");

    if (!$stmt) throw new Exception('Failed to prepare statement: ' . $conn->error);

    $stmt->bind_param(
        "sssssssssssssssssisssss",
        $reference_number,
        $full_name,
        $email,
        $phone,
        $address,
        $care_type,
        $care_requirements,
        $schedule,
        $medical_conditions,
        $medications,
        $emergency_contact,
        $emergency_phone,
        $qualifications,
        $experience,
        $job_description,
        $job_description_base64,
        $job_description_name,
        $job_description_mime,
        $job_description_size,
        $csrf_token,
        $ip_address,
        $user_agent,
        $form_metadata
    );

    if (!$stmt->execute()) throw new Exception('Failed to save individual care request: ' . $stmt->error);

    $request_id = $conn->insert_id;
    
    // Success response (matching join.php format)
    echo json_encode([
        'success' => true,
        'message' => 'Individual care request submitted successfully',
        'reference_number' => $reference_number,
        'submission_date' => date('Y-m-d H:i:s'),
        'request_id' => $request_id
    ]);
    
    $stmt->close();

} catch (Exception $e) {
    // Error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => 'INDIVIDUAL_REQUEST_ERROR',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} finally {
    if (isset($conn)) $conn->close();
}
?>