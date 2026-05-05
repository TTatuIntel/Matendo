<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'POST only'], 405);
}

Security::requireCsrf();
Security::rateLimit('facility-request', 5);

// --- validation ----------------------------------------------------------
$errors = [];

$facilityName  = trim((string)($_POST['facilityName']  ?? ''));
$contactPerson = trim((string)($_POST['contactPerson'] ?? ''));
$email         = trim((string)($_POST['email']         ?? ''));
$phone         = trim((string)($_POST['phone']         ?? ''));
$coordinates   = trim((string)($_POST['coordinates']   ?? ''));

if (mb_strlen($facilityName) < 3)  $errors['facilityName']  = 'Facility name too short.';
if (mb_strlen($contactPerson) < 3) $errors['contactPerson'] = 'Contact name too short.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email.';
if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) $errors['phone'] = 'Invalid phone.';

$facilityTypes = (array)($_POST['facilityType'] ?? []);
$positions     = (array)($_POST['positions']    ?? []);
$duration      = (array)($_POST['duration']     ?? []);
$shiftType     = (array)($_POST['shiftType']    ?? []);

$staffNumber = (int)($_POST['staffNumber'] ?? 0);
if ($staffNumber < 1 || $staffNumber > 1000) $errors['staffNumber'] = 'Staff number must be 1–1000.';

$startDate = trim((string)($_POST['startDate'] ?? ''));
if ($startDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
    $errors['startDate'] = 'Invalid start date.';
}

$reqOpt = $_POST['job-requirement-option'] ?? 'none';
if (!in_array($reqOpt, ['none', 'upload', 'manual'], true)) $reqOpt = 'none';

if ($errors) {
    json_response(['success' => false, 'errors' => $errors], 422);
}

// --- file upload (optional) ---------------------------------------------
$jobDescPath = null; $jobDescName = null; $jobDescMime = null;
if ($reqOpt === 'upload' && !empty($_FILES['jobDescriptionFile']['name'])) {
    $maxBytes = (int)(Env::get('UPLOAD_MAX_BYTES', '10485760'));
    $check = Security::validateUpload($_FILES['jobDescriptionFile'], ['pdf','doc','docx'], $maxBytes);
    if (!$check['ok']) {
        json_response(['success' => false, 'error' => $check['error']], 422);
    }
    $jobDescName = $_FILES['jobDescriptionFile']['name'];
    $jobDescMime = $check['mime'];
    $jobDescPath = Security::storeUpload($_FILES['jobDescriptionFile'], $check['ext'], 'facility');
}

// --- persist -------------------------------------------------------------
$ref = Security::generateReference('REQ');
$pdo = DB::pdo();
$stmt = $pdo->prepare("
    INSERT INTO facility_requests
        (reference_number, user_id, facility_name, contact_person, email, phone, coordinates,
         facility_type, other_facility_type, positions, other_position, duration, shift_type,
         staff_number, start_date, job_requirement_option, qualifications, experience,
         job_description, job_description_path, job_description_name, job_description_mime)
    VALUES
        (:ref, :user, :fname, :cp, :email, :phone, :coords,
         :ftype, :other_ftype, :positions, :other_pos, :duration, :shift,
         :staffn, :sdate, :ropt, :quals, :exp,
         :jdesc, :jpath, :jname, :jmime)
");
$stmt->execute([
    ':ref'         => $ref,
    ':user'        => current_user()['id'] ?? null,
    ':fname'       => $facilityName,
    ':cp'          => $contactPerson,
    ':email'       => $email,
    ':phone'       => $phone,
    ':coords'      => $coordinates ?: null,
    ':ftype'       => implode(', ', array_map('strval', $facilityTypes)) ?: null,
    ':other_ftype' => trim((string)($_POST['otherFacilityType'] ?? '')) ?: null,
    ':positions'   => implode(', ', array_map('strval', $positions)) ?: null,
    ':other_pos'   => trim((string)($_POST['otherPosition'] ?? '')) ?: null,
    ':duration'    => implode(', ', array_map('strval', $duration)) ?: null,
    ':shift'       => implode(', ', array_map('strval', $shiftType)) ?: null,
    ':staffn'      => $staffNumber,
    ':sdate'       => $startDate ?: null,
    ':ropt'        => $reqOpt,
    ':quals'       => trim((string)($_POST['qualifications'] ?? '')) ?: null,
    ':exp'         => trim((string)($_POST['experience'] ?? '')) ?: null,
    ':jdesc'       => trim((string)($_POST['jobDescription'] ?? '')) ?: null,
    ':jpath'       => $jobDescPath,
    ':jname'       => $jobDescName,
    ':jmime'       => $jobDescMime,
]);

json_response([
    'success'   => true,
    'reference' => $ref,
    'message'   => 'Your hiring request was received. Our team will be in touch within 24 hours.',
]);
