<?php
header('Content-Type: application/json');

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=matendo_medics;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

function generateReferenceNumber() {
    return 'REQ-' . strtoupper(bin2hex(random_bytes(4)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $referenceNumber = generateReferenceNumber();
    $csrfToken       = bin2hex(random_bytes(32));
    $submissionDate  = date('Y-m-d H:i:s');

    // Map your personalCareForm fields:
    $fullName          = $_POST['fullName']          ?? '';
    $email             = $_POST['email']             ?? '';
    $phone             = $_POST['phone']             ?? '';
    $address           = $_POST['address']           ?? '';
    $careType          = $_POST['careType']          ?? '';
    $careRequirements  = $_POST['careRequirements']  ?? '';
    $schedule          = $_POST['schedule']          ?? [];
    if (!is_array($schedule)) {
        $schedule = [$schedule];
    }
    $medicalConditions = $_POST['medicalConditions'] ?? '';
    $medications       = $_POST['medications']       ?? '';
    $allergies         = $_POST['allergies']         ?? '';
    $emergencyContact  = $_POST['emergencyContact']  ?? '';
    $emergencyPhone    = $_POST['emergencyPhone']    ?? '';

    $stmt = $pdo->prepare("
        INSERT INTO individual_requests
            (full_name, email, phone, address,
             care_type, care_requirements, schedule,
             medical_conditions, medications,
             emergency_contact, emergency_phone,
             reference_number, csrf_token,
             created_at, updated_at)
        VALUES
            (:full_name, :email, :phone, :address,
             :care_type, :care_requirements, :schedule,
             :medical_conditions, :medications,
             :emergency_contact, :emergency_phone,
             :reference_number, :csrf_token,
             :created_at, :updated_at)
    ");
    $stmt->execute([
        ':full_name'            => $fullName,
        ':email'                => $email,
        ':phone'                => $phone,
        ':address'              => $address,
        ':care_type'            => $careType,
        ':care_requirements'    => $careRequirements,
        ':schedule'             => implode(',', $schedule),
        ':medical_conditions'   => $medicalConditions,
        ':medications'          => $medications,
        ':emergency_contact'    => $emergencyContact,
        ':emergency_phone'      => $emergencyPhone,
        ':reference_number'     => $referenceNumber,
        ':csrf_token'           => $csrfToken,
        ':created_at'           => $submissionDate,
        ':updated_at'           => $submissionDate,
    ]);

    echo json_encode([
        'success'         => true,
        'referenceNumber' => $referenceNumber,
        'submissionDate'  => $submissionDate
    ]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}
