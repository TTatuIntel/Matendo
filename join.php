<?php
session_start();
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'matendo_medics';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

function generateReferenceCode() {
    return 'REF-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
}
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $reference_code = generateReferenceCode();

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
        $work_type = isset($_POST['workType']) ? json_encode($_POST['workType']) : null;
        $shift_type = isset($_POST['shiftType']) ? json_encode($_POST['shiftType']) : null;
        $preferred_location = sanitizeInput($_POST['preferredLocation'] ?? null);
        $start_date = sanitizeInput($_POST['startDate'] ?? null);
        $status = 'pending';
        $confirmed = 0;
<<<<<<< Updated upstream

        function getFileBlob($key) {
            return isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK
                ? file_get_contents($_FILES[$key]['tmp_name']) : null;
        }
        function getFileName($key) {
            return $_FILES[$key]['name'] ?? null;
        }
        function getFileType($key) {
            return $_FILES[$key]['type'] ?? null;
        }

        $resume = getFileBlob('resume');
        $resume_name = getFileName('resume');
        $resume_type = getFileType('resume');

        $license_doc = getFileBlob('license');
        $license_name = getFileName('license');
        $license_type = getFileType('license');

        $certifications = getFileBlob('certifications');
        $certifications_name = getFileName('certifications');
        $certifications_type = getFileType('certifications');

        $stmt = $conn->prepare("
            INSERT INTO applications (
                reference_number, first_name, last_name, email, phone, address, location, coordinates, profession,
                other_profession, specialization, years_experience, license_number, resume, resume_name, resume_type,
                license_doc, license_name, license_type, certifications, certifications_name, certifications_type,
                work_type, shift_type, preferred_location, start_date, status, confirmed, created_at, updated_at
            ) VALUES (
                ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW()
            )
        ");

        $stmt->bind_param(
            "sssssssssssissbssbssbssssssi",
            $reference_code, $first_name, $last_name, $email, $phone, $address, $location, $coordinates,
            $profession, $other_profession, $specialization, $years_experience, $license_number,
            $resume, $resume_name, $resume_type,
            $license_doc, $license_name, $license_type,
            $certifications, $certifications_name, $certifications_type,
            $work_type, $shift_type, $preferred_location, $start_date,
            $status, $confirmed
=======
        
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
                    reference_number, first_name, last_name, email, phone, address, 
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
>>>>>>> Stashed changes
        );

        $stmt->send_long_data(13, $resume);
        $stmt->send_long_data(16, $license_doc);
        $stmt->send_long_data(19, $certifications);

        $stmt->execute();
        echo json_encode([
            'success' => true,
            'message' => 'Application submitted successfully',
            'reference_number' => $reference_code,
            'submission_date' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    } finally {
        $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
