<?php
header('Content-Type: application/json');

// Database connection details
$host = "localhost";
$username = "root";
$password = "";
$database = "matendo";

// Connect to MySQL
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit;
}

// Generate a random reference number
function generateReferenceNumber() {
    return 'REQ-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
}

$facilityName      = $conn->real_escape_string($_POST['facilityName'] ?? '');
$contactPerson     = $conn->real_escape_string($_POST['contactPerson'] ?? '');
$email             = $conn->real_escape_string($_POST['email'] ?? '');
$phone             = $conn->real_escape_string($_POST['phone'] ?? '');
$coordinates       = $conn->real_escape_string($_POST['coordinates'] ?? '');

$facilityTypes     = $_POST['facilityType'] ?? [];
if (!is_array($facilityTypes)) $facilityTypes = [$facilityTypes];
$facilityTypes     = array_map('strip_tags', $facilityTypes);
$facilityTypeStr   = implode(', ', $facilityTypes);
$otherFacilityType = $conn->real_escape_string($_POST['otherFacilityType'] ?? '');

$positions         = $_POST['positions'] ?? [];
if (!is_array($positions)) $positions = [$positions];
$positions         = array_map('strip_tags', $positions);
$positionsStr      = implode(', ', $positions);
$otherPosition     = $conn->real_escape_string($_POST['otherPosition'] ?? '');

$durationArr       = $_POST['duration'] ?? [];
if (!is_array($durationArr)) $durationArr = [$durationArr];
$durationStr       = implode(', ', $durationArr);

$shiftTypeArr      = $_POST['shiftType'] ?? [];
if (!is_array($shiftTypeArr)) $shiftTypeArr = [$shiftTypeArr];
$shiftTypeStr      = implode(', ', $shiftTypeArr);

$staffNumber       = intval($_POST['staffNumber'] ?? 0);
$startDate         = $conn->real_escape_string($_POST['startDate'] ?? '');

$jobRequirementOption = $_POST['job-requirement-option'] ?? 'none';
$qualifications       = $conn->real_escape_string($_POST['qualifications'] ?? '');
$experience           = $conn->real_escape_string($_POST['experience'] ?? '');
$jobDescriptionText   = $conn->real_escape_string($_POST['jobDescription'] ?? '');

$jobDescriptionFileContent = null;
$jobDescriptionFileName = '';
$jobDescriptionFileType = '';
if (
    isset($_FILES['jobDescriptionFile']) &&
    $_FILES['jobDescriptionFile']['error'] === UPLOAD_ERR_OK
) {
    $jobDescriptionFileTmp  = $_FILES['jobDescriptionFile']['tmp_name'];
    $jobDescriptionFileName = $_FILES['jobDescriptionFile']['name'];
    $jobDescriptionFileType = $_FILES['jobDescriptionFile']['type'];
    $jobDescriptionFileContent = file_get_contents($jobDescriptionFileTmp);
}

// Generate reference number and date
$referenceNumber = generateReferenceNumber();
$submissionDate = date('Y-m-d H:i:s');

$stmt = $conn->prepare(
    "INSERT INTO facility_requests
    (
        reference_number, submission_date, facility_name, contact_person, email, phone, coordinates,
        facility_type, other_facility_type, positions, other_position,
        duration, shift_type, staff_number, start_date,
        job_requirement_option, qualifications, experience, job_description,
        job_description_file, job_description_file_name, job_description_file_type
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if ($stmt) {
    $null = null;
    $stmt->bind_param(
        "ssssssssssssssssssssss",
        $referenceNumber,
        $submissionDate,
        $facilityName,
        $contactPerson,
        $email,
        $phone,
        $coordinates,
        $facilityTypeStr,
        $otherFacilityType,
        $positionsStr,
        $otherPosition,
        $durationStr,
        $shiftTypeStr,
        $staffNumber,
        $startDate,
        $jobRequirementOption,
        $qualifications,
        $experience,
        $jobDescriptionText,
        $jobDescriptionFileContent,
        $jobDescriptionFileName,
        $jobDescriptionFileType
    );
    if ($jobDescriptionFileContent !== null) {
        $stmt->send_long_data(19, $jobDescriptionFileContent);
    }
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'reference' => $referenceNumber,
            'submission_date' => $submissionDate
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
$conn->close();
