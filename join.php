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
