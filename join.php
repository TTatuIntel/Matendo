<?php
// Start output buffering
ob_start();

// Enable all error reporting but don't display to users
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Set custom error log path (make sure this directory exists and is writable)
$error_log_path = __DIR__ . '/php_errors.log';
ini_set('log_errors', 1);
ini_set('error_log', $error_log_path);

// Set JSON header
header('Content-Type: application/json');

// Create debug log function
function debug_log($message) {
    file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

debug_log("Script started");

// Database configuration
$db_config = [
    'host' => 'localhost',
    'dbname' => 'matendo_medics',
    'username' => 'root',
    'password' => ''
];

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    debug_log("Request method validated");

    // Log all input data for debugging
    debug_log("POST data: " . print_r($_POST, true));
    debug_log("FILES data: " . print_r($_FILES, true));

    // Check for unexpected output
    $buffer_content = ob_get_contents();
    if (!empty($buffer_content)) {
        debug_log("Unexpected output in buffer: " . $buffer_content);
        ob_clean();
    }

    // Database connection
    debug_log("Attempting database connection");
    $conn = new mysqli(
        $db_config['host'],
        $db_config['username'],
        $db_config['password'],
        $db_config['dbname']
    );

    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    debug_log("Database connected successfully");

    // Set charset to utf8mb4
    if (!$conn->set_charset("utf8mb4")) {
        debug_log("Warning: Could not set charset to utf8mb4");
    }

    // Validate required fields
    $required_fields = [
        'firstName', 'lastName', 'email', 'phone', 'address',
        'profession', 'yearsExperience', 'workType', 'shiftType', 'startDate'
    ];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Required field '$field' is missing or empty");
        }
    }
    debug_log("All required fields present");

    // Process file uploads with better validation
    $resume = processFileUpload('resume', false, [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ]);
    
    $license_doc = processFileUpload('license_doc', true, [
        'application/pdf',
        'image/jpeg',
        'image/png'
    ]);
    
    $certifications = processFileUpload('certifications', true, [
        'application/pdf',
        'image/jpeg',
        'image/png'
    ]);

    // Generate reference and date
    $reference_number = 'REQ-' . strtoupper(substr(md5(uniqid()), 0, 8));
    $submission_date = date('Y-m-d H:i:s');

    // Prepare data for database
    $data = [
        'reference_number' => $reference_number,
        'first_name' => filter_var($_POST['firstName'], FILTER_SANITIZE_STRING),
        'last_name' => filter_var($_POST['lastName'], FILTER_SANITIZE_STRING),
        'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
        'phone' => filter_var($_POST['phone'], FILTER_SANITIZE_STRING),
        'address' => filter_var($_POST['address'], FILTER_SANITIZE_STRING),
        'location' => filter_var($_POST['location'] ?? '', FILTER_SANITIZE_STRING),
        'coordinates' => filter_var($_POST['coordinates'] ?? '', FILTER_SANITIZE_STRING),
        'profession' => ($_POST['profession'] === 'Other') 
            ? filter_var($_POST['otherProfession'], FILTER_SANITIZE_STRING)
            : filter_var($_POST['profession'], FILTER_SANITIZE_STRING),
        'specialization' => filter_var($_POST['specialization'] ?? '', FILTER_SANITIZE_STRING),
        'years_experience' => intval($_POST['yearsExperience']),
        'license_number' => filter_var($_POST['licenseNumber'] ?? '', FILTER_SANITIZE_STRING),
        'work_type' => implode(', ', array_map('filter_var', $_POST['workType'])),
        'shift_type' => implode(', ', array_map('filter_var', $_POST['shiftType'])),
        'preferred_location' => filter_var($_POST['preferredLocation'] ?? '', FILTER_SANITIZE_STRING),
        'start_date' => filter_var($_POST['startDate'], FILTER_SANITIZE_STRING),
        'submission_date' => $submission_date
    ];

    // Prepare SQL statement
    $sql = "INSERT INTO healthcare_professionals (
        reference_number, first_name, last_name, email, phone, address, location, coordinates,
        profession, specialization, years_experience, license_number, resume_data, resume_name,
        license_doc_data, license_doc_name, certifications_data, certifications_name,
        work_type, shift_type, preferred_location, start_date, submission_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    debug_log("Preparing SQL statement");
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $null = null; // For send_long_data
    $stmt->bind_param(
        "ssssssssssisbssbssbsssss",
        $data['reference_number'],
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['phone'],
        $data['address'],
        $data['location'],
        $data['coordinates'],
        $data['profession'],
        $data['specialization'],
        $data['years_experience'],
        $data['license_number'],
        $null, // resume_data (will use send_long_data)
        $resume['name'],
        $null, // license_doc_data
        $license_doc['name'],
        $null, // certifications_data
        $certifications['name'],
        $data['work_type'],
        $data['shift_type'],
        $data['preferred_location'],
        $data['start_date'],
        $data['submission_date']
    );

    // Handle BLOB data
    if ($resume['data']) {
        $stmt->send_long_data(12, $resume['data']);
    }
    if ($license_doc['data']) {
        $stmt->send_long_data(14, $license_doc['data']);
    }
    if ($certifications['data']) {
        $stmt->send_long_data(16, $certifications['data']);
    }

    // Execute the statement
    debug_log("Executing statement");
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    // Success response
    $response = [
        'success' => true,
        'message' => 'Application submitted successfully',
        'reference_number' => $reference_number,
        'submission_date' => $submission_date
    ];
    
    debug_log("Success: " . print_r($response, true));
    echo json_encode($response);

} catch (Exception $e) {
    $error_message = "Error: " . $e->getMessage() . "\nStack trace:\n" . $e->getTraceAsString();
    debug_log($error_message);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your application',
        'error' => $e->getMessage() // Only in development - remove in production
    ]);
} finally {
    // Clean up resources
    if (isset($stmt)) {
        $stmt->close();
        debug_log("Statement closed");
    }
    if (isset($conn)) {
        $conn->close();
        debug_log("Database connection closed");
    }
    
    // Flush output buffer
    $buffer_content = ob_get_contents();
    if (!empty($buffer_content)) {
        debug_log("Buffer content before flush: " . $buffer_content);
    }
    ob_end_flush();
    
    debug_log("Script completed");
}

/**
 * Process file upload with validation
 */
function processFileUpload($field_name, $is_optional = false, $allowed_mime_types = []) {
    if (!isset($_FILES[$field_name])) {
        if ($is_optional) {
            return ['data' => null, 'name' => null];
        }
        throw new Exception("File '$field_name' is required");
    }

    $file = $_FILES[$field_name];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        if ($file['error'] === UPLOAD_ERR_NO_FILE && $is_optional) {
            return ['data' => null, 'name' => null];
        }
        throw new Exception("File upload error: " . $file['error']);
    }

    // Check file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("File '$field_name' exceeds maximum size of 5MB");
    }

    // Verify MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_mime_types)) {
        throw new Exception("Invalid file type for '$field_name'. Allowed: " . implode(', ', $allowed_mime_types));
    }

    // Read file contents
    $file_data = file_get_contents($file['tmp_name']);
    if ($file_data === false) {
        throw new Exception("Failed to read file '$field_name'");
    }

    return [
        'data' => $file_data,
        'name' => basename($file['name'])
    ];
}