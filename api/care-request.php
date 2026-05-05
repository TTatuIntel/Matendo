<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'POST only'], 405);
}

Security::requireCsrf();
Security::rateLimit('care-request', 5);

$errors = [];
$fullName  = trim((string)($_POST['fullName'] ?? ''));
$email     = trim((string)($_POST['email']    ?? ''));
$phone     = trim((string)($_POST['phone']    ?? ''));
$address   = trim((string)($_POST['address']  ?? ''));
$careType  = trim((string)($_POST['careType'] ?? ''));
$careReq   = trim((string)($_POST['careRequirements'] ?? ''));

if (mb_strlen($fullName) < 3) $errors['fullName'] = 'Name too short.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email.';
if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) $errors['phone'] = 'Invalid phone.';
if ($careType === '') $errors['careType'] = 'Please choose a care type.';

$schedule = (array)($_POST['schedule'] ?? []);
if ($errors) json_response(['success' => false, 'errors' => $errors], 422);

// Encrypt PHI columns at rest.
$conditions  = Security::encrypt(trim((string)($_POST['medicalConditions'] ?? '')));
$medications = Security::encrypt(trim((string)($_POST['medications'] ?? '')));
$allergies   = Security::encrypt(trim((string)($_POST['allergies'] ?? '')));

$ref = Security::generateReference('CARE');
$pdo = DB::pdo();
$pdo->prepare("
    INSERT INTO care_requests
        (reference_number, user_id, full_name, email, phone, address,
         care_type, other_care_type, care_requirements, schedule,
         medical_conditions_enc, medications_enc, allergies_enc,
         emergency_contact, emergency_phone)
    VALUES
        (:ref, :user, :name, :email, :phone, :addr,
         :ctype, :other_ctype, :creq, :sched,
         :mc, :meds, :al,
         :ec, :ep)
")->execute([
    ':ref'         => $ref,
    ':user'        => current_user()['id'] ?? null,
    ':name'        => $fullName,
    ':email'       => $email,
    ':phone'       => $phone,
    ':addr'        => $address ?: null,
    ':ctype'       => $careType,
    ':other_ctype' => trim((string)($_POST['otherCareType'] ?? '')) ?: null,
    ':creq'        => $careReq ?: null,
    ':sched'       => implode(', ', array_map('strval', $schedule)) ?: null,
    ':mc'          => $conditions,
    ':meds'        => $medications,
    ':al'          => $allergies,
    ':ec'          => trim((string)($_POST['emergencyContact'] ?? '')) ?: null,
    ':ep'          => trim((string)($_POST['emergencyPhone'] ?? '')) ?: null,
]);

json_response([
    'success'   => true,
    'reference' => $ref,
    'message'   => 'Your personal care request has been received. A care coordinator will reach out shortly.',
]);
