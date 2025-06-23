<?php
$host = 'localhost';
$db   = 'matendo_medics';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';



$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function generateReferenceNumber() {
    return 'REQ-' . strtoupper(bin2hex(random_bytes(4)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facility_name = $_POST['facilityName'] ?? '';
    $contact_person = $_POST['contactPerson'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $coordinates = $_POST['coordinates'] ?? null;
    $facility_type = isset($_POST['facilityType']) ? (is_array($_POST['facilityType']) ? implode(', ', $_POST['facilityType']) : $_POST['facilityType']) : '';
    $positions = isset($_POST['positions']) ? (is_array($_POST['positions']) ? implode(', ', $_POST['positions']) : $_POST['positions']) : '';
    $employment_type = isset($_POST['duration']) ? (is_array($_POST['duration']) ? implode(', ', $_POST['duration']) : $_POST['duration']) : '';
    $shift_type = isset($_POST['shiftType']) ? (is_array($_POST['shiftType']) ? implode(', ', $_POST['shiftType']) : $_POST['shiftType']) : '';
    $staff_number = $_POST['staffNumber'] ?? 0;
    $start_date = $_POST['startDate'] ?? null;
    $job_requirement_option = $_POST['job-requirement-option'] ?? '';
    $qualifications = $_POST['qualifications'] ?? null;
    $experience = $_POST['experience'] ?? null;
    $job_description = $_POST['jobDescription'] ?? null;
    $reference_number = generateReferenceNumber();
    $csrf_token = bin2hex(random_bytes(32));
    $status = 'pending';
    $priority = null;
    $confirmed = 0;
    $created_at = date('Y-m-d H:i:s');
    $updated_at = $created_at;

    $job_description_file = null;
    if ($job_requirement_option === 'upload' && isset($_FILES['jobDescriptionFile']) && $_FILES['jobDescriptionFile']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $filename = basename($_FILES['jobDescriptionFile']['name']);
        $target_file = $upload_dir . time() . '_' . $filename;
        if (move_uploaded_file($_FILES['jobDescriptionFile']['tmp_name'], $target_file)) {
            $job_description_file = $target_file;
        }
    }

    $sql = "INSERT INTO facility_requests (
                facility_name, contact_person, email, phone, coordinates,
                facility_type, positions, employment_type, shift_type,
                staff_number, start_date, job_requirement_option,
                qualifications, experience, job_description, job_description_file,
                reference_number, csrf_token, status, priority, confirmed,
                created_at, updated_at
            ) VALUES (
                :facility_name, :contact_person, :email, :phone, :coordinates,
                :facility_type, :positions, :employment_type, :shift_type,
                :staff_number, :start_date, :job_requirement_option,
                :qualifications, :experience, :job_description, :job_description_file,
                :reference_number, :csrf_token, :status, :priority, :confirmed,
                :created_at, :updated_at
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':facility_name' => $facility_name,
        ':contact_person' => $contact_person,
        ':email' => $email,
        ':phone' => $phone,
        ':coordinates' => $coordinates,
        ':facility_type' => $facility_type,
        ':positions' => $positions,
        ':employment_type' => $employment_type,
        ':shift_type' => $shift_type,
        ':staff_number' => $staff_number,
        ':start_date' => $start_date,
        ':job_requirement_option' => $job_requirement_option,
        ':qualifications' => $qualifications,
        ':experience' => $experience,
        ':job_description' => $job_description,
        ':job_description_file' => $job_description_file,
        ':reference_number' => $reference_number,
        ':csrf_token' => $csrf_token,
        ':status' => $status,
        ':priority' => $priority,
        ':confirmed' => $confirmed,
        ':created_at' => $created_at,
        ':updated_at' => $updated_at,
    ]);

    echo json_encode([
        'success' => true,
        'reference_number' => $reference_number,
        'submission_date' => $created_at
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
