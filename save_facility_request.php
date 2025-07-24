<?php
$pdo = new PDO('mysql:host=localhost;dbname=matendo_medics;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

function generateReferenceNumber() {
    return 'REQ-' . strtoupper(bin2hex(random_bytes(4)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference_number = generateReferenceNumber();
    $csrf_token = bin2hex(random_bytes(32));
    $created_at = date('Y-m-d H:i:s');

    $job_file = null;
    $job_file_name = null;
    $job_file_type = null;

    if (isset($_FILES['jobDescriptionFile']) && $_FILES['jobDescriptionFile']['error'] === UPLOAD_ERR_OK) {
        $job_file = file_get_contents($_FILES['jobDescriptionFile']['tmp_name']);
        $job_file_name = $_FILES['jobDescriptionFile']['name'];
        $job_file_type = $_FILES['jobDescriptionFile']['type'];
    }

    $stmt = $pdo->prepare("INSERT INTO facility_requests (
        facility_name, contact_person, email, phone, coordinates, facility_type, positions, employment_type, shift_type,
        staff_number, start_date, job_requirement_option, qualifications, experience, job_description,
        job_description_file, job_description_file_name, job_description_file_type,
        reference_number, csrf_token, status, priority, confirmed, created_at, updated_at
    ) VALUES (
        :facility_name, :contact_person, :email, :phone, :coordinates, :facility_type, :positions, :employment_type, :shift_type,
        :staff_number, :start_date, :job_requirement_option, :qualifications, :experience, :job_description,
        :job_description_file, :job_description_file_name, :job_description_file_type,
        :reference_number, :csrf_token, 'pending', NULL, 0, :created_at, :created_at
    )");

    $stmt->execute([
        ':facility_name' => $_POST['facilityName'] ?? '',
        ':contact_person' => $_POST['contactPerson'] ?? '',
        ':email' => $_POST['email'] ?? '',
        ':phone' => $_POST['phone'] ?? '',
        ':coordinates' => $_POST['coordinates'] ?? '',
        ':facility_type' => implode(', ', $_POST['facilityType'] ?? []),
        ':positions' => implode(', ', $_POST['positions'] ?? []),
        ':employment_type' => implode(', ', $_POST['duration'] ?? []),
        ':shift_type' => implode(', ', $_POST['shiftType'] ?? []),
        ':staff_number' => $_POST['staffNumber'] ?? 0,
        ':start_date' => $_POST['startDate'] ?? '',
        ':job_requirement_option' => $_POST['job-requirement-option'] ?? '',
        ':qualifications' => $_POST['qualifications'] ?? '',
        ':experience' => $_POST['experience'] ?? '',
        ':job_description' => $_POST['jobDescription'] ?? '',
        ':job_description_file' => $job_file,
        ':job_description_file_name' => $job_file_name,
        ':job_description_file_type' => $job_file_type,
        ':reference_number' => $reference_number,
        ':csrf_token' => $csrf_token,
        ':created_at' => $created_at,
    ]);

    echo json_encode(['success' => true, 'reference_number' => $reference_number, 'submission_date' => $created_at]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
