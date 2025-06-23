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
    die("Connection failed: " . $e->getMessage());
}

function generateReferenceNumber() {
    return 'IND-' . strtoupper(bin2hex(random_bytes(4)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['fullName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $care_type = $_POST['careType'] ?? '';
    $care_requirements = $_POST['careRequirements'] ?? null;
    $schedule = isset($_POST['schedule']) ? (is_array($_POST['schedule']) ? implode(', ', $_POST['schedule']) : $_POST['schedule']) : null;
    $medical_conditions = $_POST['medicalConditions'] ?? null;
    $medications = $_POST['medications'] ?? null;
    $emergency_contact = $_POST['emergencyContact'] ?? '';
    $emergency_phone = $_POST['emergencyPhone'] ?? '';
    $qualifications = $_POST['qualifications'] ?? null;
    $experience = $_POST['experience'] ?? null;
    $job_description = $_POST['jobDescription'] ?? null;
    $reference_number = generateReferenceNumber();
    $csrf_token = bin2hex(random_bytes(32));
    $status = 'pending';
    $confirmed = 0;
    $created_at = date('Y-m-d H:i:s');
    $updated_at = $created_at;

    $job_description_file = null;
    if (isset($_FILES['jobDescriptionFile']) && $_FILES['jobDescriptionFile']['error'] === UPLOAD_ERR_OK) {
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

    $sql = "INSERT INTO individual_requests (
                full_name, email, phone, address, care_type,
                care_requirements, schedule, medical_conditions,
                medications, emergency_contact, emergency_phone,
                reference_number, csrf_token, qualifications, experience,
                job_description, job_description_file,
                status, confirmed, created_at, updated_at
            ) VALUES (
                :full_name, :email, :phone, :address, :care_type,
                :care_requirements, :schedule, :medical_conditions,
                :medications, :emergency_contact, :emergency_phone,
                :reference_number, :csrf_token, :qualifications, :experience,
                :job_description, :job_description_file,
                :status, :confirmed, :created_at, :updated_at
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':full_name' => $full_name,
        ':email' => $email,
        ':phone' => $phone,
        ':address' => $address,
        ':care_type' => $care_type,
        ':care_requirements' => $care_requirements,
        ':schedule' => $schedule,
        ':medical_conditions' => $medical_conditions,
        ':medications' => $medications,
        ':emergency_contact' => $emergency_contact,
        ':emergency_phone' => $emergency_phone,
        ':reference_number' => $reference_number,
        ':csrf_token' => $csrf_token,
        ':qualifications' => $qualifications,
        ':experience' => $experience,
        ':job_description' => $job_description,
        ':job_description_file' => $job_description_file,
        ':status' => $status,
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
