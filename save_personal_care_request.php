<?php
session_start();
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$username = 'root';
$password = ''; // Empty password
$database = 'matendo_medics';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Function to generate a unique reference number
function generateReferenceNumber() {
    return 'REQ-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
}

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Generate unique reference number (or use the one provided)
        $reference_number = isset($_POST['referenceNumber']) ? sanitizeInput($_POST['referenceNumber']) : generateReferenceNumber();
        
        // Sanitize and collect form data
        $full_name = sanitizeInput($_POST['fullName'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $address = sanitizeInput($_POST['address'] ?? '');
        $city = sanitizeInput($_POST['city'] ?? '');
        $postal_code = sanitizeInput($_POST['postalCode'] ?? '');
        
        // Care needs
        $care_type = sanitizeInput($_POST['careType'] ?? '');
        $other_care_type = null;
        if ($care_type === 'other') {
            $other_care_type = sanitizeInput($_POST['otherCareType'] ?? '');
        }
        
        $care_requirements = sanitizeInput($_POST['careRequirements'] ?? '');
        
        // Convert schedule to JSON
        $schedule = isset($_POST['schedule']) ? json_encode($_POST['schedule']) : json_encode([]);
        
        // Medical details
        $medical_conditions = sanitizeInput($_POST['medicalConditions'] ?? '');
        $medications = sanitizeInput($_POST['medications'] ?? '');
        $allergies = sanitizeInput($_POST['allergies'] ?? '');
        $emergency_contact = sanitizeInput($_POST['emergencyContact'] ?? '');
        $emergency_phone = sanitizeInput($_POST['emergencyPhone'] ?? '');
        
        // Set default values
        $status = 'pending';
        $confirmed = 0;
        
        // Handle care file upload if present
        $care_file = null;
        if (isset($_FILES['careFile']) && $_FILES['careFile']['error'] == 0) {
            $upload_dir = 'uploads/care_files/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = $reference_number . '_' . basename($_FILES['careFile']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['careFile']['tmp_name'], $target_file)) {
                $care_file = $target_file;
            }
        }
        
        // Prepare SQL statement
        $sql = "INSERT INTO personal_care_requests (
                    reference_number, full_name, email, phone, address, city, postal_code,
                    care_type, other_care_type, care_requirements, schedule, 
                    medical_conditions, medications, allergies, emergency_contact, emergency_phone,
                    care_file, status, confirmed, created_at, updated_at
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
                )";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param(
            "ssssssssssssssssssi",
            $reference_number, $full_name, $email, $phone, $address, $city, $postal_code,
            $care_type, $other_care_type, $care_requirements, $schedule,
            $medical_conditions, $medications, $allergies, $emergency_contact, $emergency_phone,
            $care_file, $status, $confirmed
        );
        
        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        // Close statement
        $stmt->close();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Personal care request submitted successfully',
            'referenceNumber' => $reference_number,
            'submissionDate' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
        // Return error response
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
    // Close connection
    $conn->close();
} else {
    // Not a POST request
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>