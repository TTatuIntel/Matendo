<?php
<<<<<<< Updated upstream
session_start();
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$username = 'root';
$password = ''; // Empty password
$database = 'matendb';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Function to generate a unique reference code
function generateReferenceCode() {
    return 'REF-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
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
        // Generate unique reference code
        $reference_code = generateReferenceCode();
        
        // Sanitize and collect form data
        $first_name = sanitizeInput($_POST['firstName'] ?? '');
        $last_name = sanitizeInput($_POST['lastName'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $address = sanitizeInput($_POST['address'] ?? '');
        $location = sanitizeInput($_POST['location'] ?? null);
        $coordinates = sanitizeInput($_POST['coordinates'] ?? null);
        $profession = sanitizeInput($_POST['profession'] ?? '');
        $other_profession = sanitizeInput($_POST['otherProfession'] ?? null);
        $specialization = sanitizeInput($_POST['specialization'] ?? null);
        $years_experience = intval($_POST['yearsExperience'] ?? 0);
        $license_number = sanitizeInput($_POST['licenseNumber'] ?? null);
        
        // Handle work_type and shift_type arrays
        $work_type = isset($_POST['workType']) ? json_encode($_POST['workType']) : null;
        $shift_type = isset($_POST['shiftType']) ? json_encode($_POST['shiftType']) : null;
        
        $preferred_location = sanitizeInput($_POST['preferredLocation'] ?? null);
        $start_date = sanitizeInput($_POST['startDate'] ?? null);
        
        // Set default values
        $status = 'pending';
        $confirmed = 0;
        
        // Handle file uploads
        $resume = null;
        $license_doc = null;
        $certifications = null;
        
        // Process resume upload
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
            $upload_dir = 'uploads/resumes/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = $reference_code . '_' . basename($_FILES['resume']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $target_file)) {
                $resume = $target_file;
            }
        }
        
        // Process license document upload
        if (isset($_FILES['license']) && $_FILES['license']['error'] == 0) {
            $upload_dir = 'uploads/licenses/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = $reference_code . '_' . basename($_FILES['license']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['license']['tmp_name'], $target_file)) {
                $license_doc = $target_file;
            }
        }
        
        // Process certifications upload
        if (isset($_FILES['certifications']) && $_FILES['certifications']['error'] == 0) {
            $upload_dir = 'uploads/certifications/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = $reference_code . '_' . basename($_FILES['certifications']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['certifications']['tmp_name'], $target_file)) {
                $certifications = $target_file;
            }
        }
        
        // Prepare SQL statement
        $sql = "INSERT INTO applications (
                    reference_code, first_name, last_name, email, phone, address, 
                    location, coordinates, profession, other_profession, 
                    specialization, years_experience, license_number, resume, 
                    license_doc, certifications, work_type, shift_type, 
                    preferred_location, start_date, status, confirmed, created_at, updated_at
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
                )";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param(
            "sssssssssssisssssssssi",
            $reference_code, $first_name, $last_name, $email, $phone, $address,
            $location, $coordinates, $profession, $other_profession,
            $specialization, $years_experience, $license_number, $resume,
            $license_doc, $certifications, $work_type, $shift_type,
            $preferred_location, $start_date, $status, $confirmed
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
            'message' => 'Application submitted successfully',
            'reference_number' => $reference_code,
            'submission_date' => date('Y-m-d H:i:s')
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
=======
ini_set('display_errors', 1);
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
    // Generate reference number and submission date
    $referenceNumber = 'HP-' . strtoupper(uniqid());
    $submissionDate = date('Y-m-d H:i:s');
    
    // Process file uploads
    $uploadDir = "uploads/professionals/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $resumeFile = '';
    $licenseFile = '';
    $certificationsFile = '';
    
    // Handle resume upload
    if (!empty($_FILES['resume']['name'])) {
        $resumeFile = $uploadDir . basename($_FILES['resume']['name']);
        move_uploaded_file($_FILES['resume']['tmp_name'], $resumeFile);
    }
    
    // Handle license upload (optional)
    if (!empty($_FILES['license']['name'])) {
        $licenseFile = $uploadDir . basename($_FILES['license']['name']);
        move_uploaded_file($_FILES['license']['tmp_name'], $licenseFile);
    }
    
    // Handle certifications upload (optional)
    if (!empty($_FILES['certifications']['name'])) {
        $certificationsFile = $uploadDir . basename($_FILES['certifications']['name']);
        move_uploaded_file($_FILES['certifications']['tmp_name'], $certificationsFile);
    }
    
    // Sanitize and collect form data
    $firstName = htmlspecialchars($_POST['firstName'] ?? '', ENT_QUOTES, 'UTF-8');
    $lastName = htmlspecialchars($_POST['lastName'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ? $_POST['email'] : '';
    $phone = preg_replace('/[^0-9+]/', '', $_POST['phone'] ?? '');
    $address = htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES, 'UTF-8');
    $location = htmlspecialchars($_POST['location'] ?? '', ENT_QUOTES, 'UTF-8');
    $coordinates = htmlspecialchars($_POST['coordinates'] ?? '', ENT_QUOTES, 'UTF-8');
    
    $profession = htmlspecialchars($_POST['profession'] ?? '', ENT_QUOTES, 'UTF-8');
    if ($profession === 'Other') {
        $profession = htmlspecialchars($_POST['otherProfession'] ?? '', ENT_QUOTES, 'UTF-8');
    }
    $specialization = htmlspecialchars($_POST['specialization'] ?? '', ENT_QUOTES, 'UTF-8');
    $yearsExperience = filter_var($_POST['yearsExperience'] ?? 0, FILTER_VALIDATE_INT);
    $licenseNumber = htmlspecialchars($_POST['licenseNumber'] ?? '', ENT_QUOTES, 'UTF-8');
    
    $workTypes = is_array($_POST['workType']) ? implode(', ', array_map('htmlspecialchars', $_POST['workType'])) : '';
    $shiftTypes = is_array($_POST['shiftType']) ? implode(', ', array_map('htmlspecialchars', $_POST['shiftType'])) : '';
    $preferredLocation = htmlspecialchars($_POST['preferredLocation'] ?? '', ENT_QUOTES, 'UTF-8');
    $startDate = htmlspecialchars($_POST['startDate'] ?? '', ENT_QUOTES, 'UTF-8');
    
    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($address) || 
        empty($profession) || empty($yearsExperience) || empty($workTypes) || empty($shiftTypes) || 
        empty($startDate) || empty($resumeFile)) {
        echo json_encode(["success" => false, "message" => "All required fields must be filled"]);
        exit;
    }
    
    try {
        // Prepare and execute SQL query
        $stmt = $pdo->prepare("INSERT INTO healthcare_professionals (
            first_name, last_name, email, phone, address, location, coordinates,
            profession, specialization, years_experience, license_number,
            work_types, shift_types, preferred_location, start_date,
            resume_file, license_file, certifications_file,
            reference_number, submission_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $firstName, $lastName, $email, $phone, $address, $location, $coordinates,
            $profession, $specialization, $yearsExperience, $licenseNumber,
            $workTypes, $shiftTypes, $preferredLocation, $startDate,
            $resumeFile, $licenseFile, $certificationsFile,
            $referenceNumber, $submissionDate
        ]);
        
        // Return success response
        echo json_encode([
            "success" => true, 
            "message" => "Application submitted successfully",
            "reference_number" => $referenceNumber,
            "submission_date" => $submissionDate
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
>>>>>>> Stashed changes
?>