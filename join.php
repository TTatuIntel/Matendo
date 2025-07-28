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
const ALLOWED_RESUME_TYPES = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
];
const ALLOWED_DOC_TYPES = [
    'application/pdf',
    'image/jpeg',
    'image/png',
    'image/jpg'
];

function generateReferenceCode() {
    return 'REF-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
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

    $reference_code = generateReferenceCode();

    // Personal/professional info
    $first_name = sanitizeInput($_POST['firstName'] ?? '');
    $last_name = sanitizeInput($_POST['lastName'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $location = sanitizeInput($_POST['location'] ?? null);
    $coordinates = sanitizeInput($_POST['coordinates'] ?? null);
    $profession = sanitizeInput($_POST['profession'] ?? '');
    $other_profession = ($profession === 'Other') ? sanitizeInput($_POST['otherProfession'] ?? null) : null;
    $specialization = sanitizeInput($_POST['specialization'] ?? null);
    $years_experience = intval($_POST['yearsExperience'] ?? 0);
    $license_number = sanitizeInput($_POST['licenseNumber'] ?? null);

    $work_type = json_encode($_POST['workType'] ?? []);
    $shift_type = json_encode($_POST['shiftType'] ?? []);
    $preferred_location = sanitizeInput($_POST['preferredLocation'] ?? null);
    $start_date = sanitizeInput($_POST['startDate'] ?? null);

    // Files (base64 in *_base64)
    list($resume_base64, $resume_name, $resume_mime, $resume_size) = getBase64File('resume', ALLOWED_RESUME_TYPES);
    list($license_base64, $license_name, $license_mime, $license_size) = getBase64File('license', ALLOWED_DOC_TYPES);
    list($certifications_base64, $certifications_name, $certifications_mime, $certifications_size) = getBase64File('certifications', ALLOWED_DOC_TYPES);

    if (!$resume_base64) throw new Exception("Resume file is required.");

    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $form_metadata = json_encode([
        'submission_timestamp' => date('Y-m-d H:i:s'),
        'form_version' => '1.0'
    ]);

    $stmt = $conn->prepare("
        INSERT INTO applications (
            reference_number, first_name, last_name, email, phone, address, location, coordinates,
            profession, other_profession, specialization, years_experience, license_number,
            resume_base64, resume_name, resume_mime, resume_size,
            license_base64, license_name, license_mime, license_size,
            certifications_base64, certifications_name, certifications_mime, certifications_size,
            work_type, shift_type, preferred_location, start_date,
            status, confirmed, ip_address, user_agent, form_metadata,
            created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            'pending', 0, ?, ?, ?,
            NOW(), NOW()
        )
    ");

    if (!$stmt) throw new Exception('Failed to prepare statement: ' . $conn->error);

    $stmt->bind_param(
        "sssssssssssisssisississsisssssss",
        $reference_code,
        $first_name,
        $last_name,
        $email,
        $phone,
        $address,
        $location,
        $coordinates,
        $profession,
        $other_profession,
        $specialization,
        $years_experience,
        $license_number,
        $resume_base64,
        $resume_name,
        $resume_mime,
        $resume_size,
        $license_base64,
        $license_name,
        $license_mime,
        $license_size,
        $certifications_base64,
        $certifications_name,
        $certifications_mime,
        $certifications_size,
        $work_type,
        $shift_type,
        $preferred_location,
        $start_date,
        $ip_address,
        $user_agent,
        $form_metadata
    );

    if (!$stmt->execute()) throw new Exception('Failed to save application: ' . $stmt->error);

    $application_id = $conn->insert_id;
    echo json_encode([
        'success' => true,
        'message' => 'Application submitted successfully',
        'reference_number' => $reference_code,
        'submission_date' => date('Y-m-d H:i:s'),
        'application_id' => $application_id
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
