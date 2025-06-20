<?php
session_start();
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$username = 'root';
$password = ''; // Empty password
$database = '';

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
        $facility_name = sanitizeInput($_POST['facilityName'] ?? '');
        $contact_person = sanitizeInput($_POST['contactPerson'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $coordinates = sanitizeInput($_POST['coordinates'] ?? null);
        $location = sanitizeInput($_POST['location'] ?? null); // Added location field
        
        // Facility details - Convert to JSON
        $facility_types = isset($_POST['facilityType']) ? json_encode($_POST['facilityType']) : json_encode([]);
        $other_facility_type = sanitizeInput($_POST['otherFacilityType'] ?? null);
        
        // Positions - Convert to JSON
        $positions_needed = isset($_POST['positions']) ? json_encode($_POST['positions']) : json_encode([]);
        $other_position = sanitizeInput($_POST['otherPosition'] ?? null);
        
        // Employment details - Convert to JSON
        $employment_types = isset($_POST['duration']) ? json_encode($_POST['duration']) : json_encode([]);
        $shift_types = isset($_POST['shiftType']) ? json_encode($_POST['shiftType']) : json_encode([]);
        $staff_number = intval($_POST['staffNumber'] ?? 0);
        $start_date = sanitizeInput($_POST['startDate'] ?? null);
        
        // Job requirements
        $requirement_option = sanitizeInput($_POST['job-requirement-option'] ?? 'none');
        $qualifications = sanitizeInput($_POST['qualifications'] ?? null);
        $experience = sanitizeInput($_POST['experience'] ?? null);
        $job_description = sanitizeInput($_POST['jobDescription'] ?? null);
        
        // Set default values
        $status = 'pending';
        $confirmed = 0;
        
        // Handle file upload
        $job_description_file = null;
        
        if ($requirement_option === 'upload' && isset($_FILES['jobDescriptionFile']) && $_FILES['jobDescriptionFile']['error'] == 0) {
            $upload_dir = 'uploads/job_descriptions/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = $reference_number . '_' . basename($_FILES['jobDescriptionFile']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['jobDescriptionFile']['tmp_name'], $target_file)) {
                $job_description_file = $target_file;
            }
        }
        
        // Prepare SQL statement
        $sql = "INSERT INTO facility_requests (
                    reference_number, facility_name, contact_person, email, phone, coordinates, location,
                    facility_types, other_facility_type, positions_needed, other_position, 
                    employment_types, shift_types, staff_number, start_date,
                    requirement_option, qualifications, experience, job_description, 
                    job_description_file, status, confirmed, created_at, updated_at
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
                )";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param(
            "ssssssssssssisssssssi",
            $reference_number, $facility_name, $contact_person, $email, $phone, $coordinates, $location,
            $facility_types, $other_facility_type, $positions_needed, $other_position,
            $employment_types, $shift_types, $staff_number, $start_date,
            $requirement_option, $qualifications, $experience, $job_description,
            $job_description_file, $status, $confirmed
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
            'message' => 'Facility request submitted successfully',
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