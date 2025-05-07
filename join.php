<?php
// Start output buffering at the very beginning
ob_start();

// Set headers first
header('Content-Type: application/json');

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$host = 'localhost';
$dbname = 'matendo_medics';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    file_put_contents('debug.log', "Database connection failed: " . $conn->connect_error . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    ob_end_flush();
    exit;
}

// Function to generate a unique reference number
function generateReferenceNumber() {
    return 'REQ-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
}

// Function to validate and process file uploads
function processFile($file, $optional = false, $allowedTypes = [], $maxSize = 5 * 1024 * 1024) {
    if (!$optional && (is_null($file) || $file['error'] === UPLOAD_ERR_NO_FILE)) {
        return ['success' => false, 'message' => "Please upload a valid file"];
    }

    if (is_null($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'data' => null, 'name' => null];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => "Error uploading file"];
    }

    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => "File exceeds the maximum size of " . ($maxSize / 1024 / 1024) . "MB"];
    }

    $fileType = mime_content_type($file['tmp_name']);
    // Allow application/octet-stream for optional files
    if ($optional && $fileType === 'application/octet-stream') {
        file_put_contents('debug.log', "Warning: File has generic MIME type (application/octet-stream), accepting anyway\n", FILE_APPEND);
    } else {
        if (!in_array($fileType, $allowedTypes)) {
            $error = "File must be one of the allowed types: " . implode(', ', $allowedTypes) . " (got $fileType)";
            file_put_contents('debug.log', $error . "\n", FILE_APPEND);
            return ['success' => false, 'message' => $error];
        }
    }

    $fileData = file_get_contents($file['tmp_name']);
    if ($fileData === false) {
        return ['success' => false, 'message' => "Failed to read file"];
    }

    return [
        'success' => true,
        'data' => $fileData,
        'name' => $file['name']
    ];
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Log all form data for debugging
    file_put_contents('debug.log', print_r($_POST, true) . "\n" . print_r($_FILES, true) . "\n", FILE_APPEND);

    // Collect form data
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING) ?? '';
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING) ?? '';
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING) ?? '';
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING) ?? '';
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING) ?? '';
    $coordinates = filter_input(INPUT_POST, 'coordinates', FILTER_SANITIZE_STRING) ?? '';
    $profession = filter_input(INPUT_POST, 'profession', FILTER_SANITIZE_STRING) ?? '';
    $other_profession = filter_input(INPUT_POST, 'other_profession', FILTER_SANITIZE_STRING) ?? '';
    $specialization = filter_input(INPUT_POST, 'specialization', FILTER_SANITIZE_STRING) ?? '';
    $years_experience = filter_input(INPUT_POST, 'years_experience', FILTER_VALIDATE_INT) ?? 0;
    $license_number = filter_input(INPUT_POST, 'license_number', FILTER_SANITIZE_STRING) ?? '';
    $work_types = isset($_POST['work_type']) ? implode(', ', array_map('htmlspecialchars', $_POST['work_type'])) : '';
    $shift_types = isset($_POST['shift_type']) ? implode(', ', array_map('htmlspecialchars', $_POST['shift_type'])) : '';
    $preferred_location = filter_input(INPUT_POST, 'preferred_location', FILTER_SANITIZE_STRING) ?? '';
    $start_date = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING) ?? '';
    $confirm_checkbox = isset($_POST['confirm_checkbox']) ? 1 : 0;

    // Validate required fields
    if (empty($first_name) || !preg_match('/^[A-Za-z]{2,}$/', $first_name)) {
        throw new Exception('Invalid first name');
    }
    if (empty($last_name) || !preg_match('/^[A-Za-z]{2,}$/', $last_name)) {
        throw new Exception('Invalid last name');
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email');
    }
    if (empty($phone) || !preg_match('/^\+?[0-9\s\-()]{7,20}$/', $phone)) {
        throw new Exception('Invalid phone number');
    }
    if (empty($address)) {
        throw new Exception('Address is required');
    }
    if (empty($profession)) {
        throw new Exception('Profession is required');
    }
    if ($profession === 'Other' && empty($other_profession)) {
        throw new Exception('Please specify your profession');
    }
    if ($years_experience === false || $years_experience < 0) {
        throw new Exception('Invalid years of experience');
    }
    if (empty($work_types)) {
        throw new Exception('Please select at least one work type');
    }
    if (empty($shift_types)) {
        throw new Exception('Please select at least one shift type');
    }
    if (empty($start_date)) {
        throw new Exception('Start date is required');
    }
    if (!$confirm_checkbox) {
        throw new Exception('Please confirm the information');
    }

    // Process files
    $resumeResult = processFile($_FILES['resume'] ?? null, false, ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
    if (!$resumeResult['success']) {
        throw new Exception($resumeResult['message']);
    }

    $licenseDocResult = processFile($_FILES['license_doc'] ?? null, true, ['application/pdf', 'image/jpeg', 'image/png', 'application/octet-stream']);
    if (!$licenseDocResult['success']) {
        throw new Exception($licenseDocResult['message']);
    }

    $certificationsResult = processFile($_FILES['certifications'] ?? null, true, ['application/pdf', 'image/jpeg', 'image/png', 'application/octet-stream']);
    if (!$certificationsResult['success']) {
        throw new Exception($certificationsResult['message']);
    }

    // Generate reference number and submission date
    $reference_number = generateReferenceNumber();
    $submission_date = date('Y-m-d H:i:s');

    // Prepare profession
    $final_profession = $profession === 'Other' ? $other_profession : $profession;

    // Prepare SQL query
    $stmt = $conn->prepare("INSERT INTO healthcare_professionals (
        reference_number, first_name, last_name, email, phone, address, location, coordinates,
        profession, specialization, years_experience, license_number, resume_data, resume_name,
        license_doc_data, license_doc_name, certifications_data, certifications_name,
        work_type, shift_type, preferred_location, start_date, submission_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception('Database query preparation failed: ' . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param(
        "ssssssssssisssbsssssss",
        $reference_number,
        $first_name,
        $last_name,
        $email,
        $phone,
        $address,
        $location,
        $coordinates,
        $final_profession,
        $specialization,
        $years_experience,
        $license_number,
        $resumeResult['data'],
        $resumeResult['name'],
        $licenseDocResult['data'],
        $licenseDocResult['name'],
        $certificationsResult['data'],
        $certificationsResult['name'],
        $work_types,
        $shift_types,
        $preferred_location,
        $start_date,
        $submission_date
    );

    // Handle long data for BLOBs
    if ($resumeResult['data']) {
        $stmt->send_long_data(12, $resumeResult['data']);
    }
    if ($licenseDocResult['data']) {
        $stmt->send_long_data(14, $licenseDocResult['data']);
    }
    if ($certificationsResult['data']) {
        $stmt->send_long_data(16, $certificationsResult['data']);
    }

    // Execute the query
    if ($stmt->execute()) {
        file_put_contents('debug.log', "Query executed successfully\n", FILE_APPEND);
        echo json_encode([
            'success' => true,
            'message' => 'Form submitted successfully.',
            'reference_number' => $reference_number,
            'submission_date' => $submission_date
        ]);
    } else {
        throw new Exception('Database execution failed: ' . $stmt->error);
    }
} catch (Exception $e) {
    file_put_contents('debug.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
    ob_end_flush();
}
?>