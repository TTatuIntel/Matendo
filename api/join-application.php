<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'POST only'], 405);
}

Security::requireCsrf();
Security::rateLimit('join-application', 5);

$errors = [];
$first  = trim((string)($_POST['firstName'] ?? ''));
$last   = trim((string)($_POST['lastName']  ?? ''));
$email  = trim((string)($_POST['email']     ?? ''));
$phone  = trim((string)($_POST['phone']     ?? ''));
$prof   = trim((string)($_POST['profession']?? ''));

if (!preg_match('/^[A-Za-z\' \-]{2,}$/', $first)) $errors['firstName'] = 'Invalid first name.';
if (!preg_match('/^[A-Za-z\' \-]{2,}$/', $last))  $errors['lastName']  = 'Invalid last name.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))   $errors['email'] = 'Invalid email.';
if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone))$errors['phone'] = 'Invalid phone.';
if ($prof === '') $errors['profession'] = 'Profession required.';

$years = (int)($_POST['yearsExperience'] ?? -1);
if ($years < 0 || $years > 60) $errors['yearsExperience'] = 'Invalid years of experience.';

if ($errors) json_response(['success' => false, 'errors' => $errors], 422);

// --- file uploads (resume required, license/certs optional) -------------
$maxBytes = (int)(Env::get('UPLOAD_MAX_BYTES', '10485760'));
$resumePath = null; $resumeMeta = [];
if (empty($_FILES['resume']['name'])) {
    json_response(['success' => false, 'errors' => ['resume' => 'Resume is required.']], 422);
}
$check = Security::validateUpload($_FILES['resume'], ['pdf','doc','docx'], $maxBytes);
if (!$check['ok']) json_response(['success' => false, 'errors' => ['resume' => $check['error']]], 422);
$resumePath = Security::storeUpload($_FILES['resume'], $check['ext'], 'professionals');
$resumeMeta = ['name' => $_FILES['resume']['name'], 'mime' => $check['mime'], 'size' => (int)$_FILES['resume']['size']];

$licensePath = null; $licenseMeta = [];
if (!empty($_FILES['license']['name'])) {
    $c = Security::validateUpload($_FILES['license'], ['pdf','jpg','jpeg','png'], $maxBytes);
    if (!$c['ok']) json_response(['success' => false, 'errors' => ['license' => $c['error']]], 422);
    $licensePath = Security::storeUpload($_FILES['license'], $c['ext'], 'professionals');
    $licenseMeta = ['name' => $_FILES['license']['name'], 'mime' => $c['mime'], 'size' => (int)$_FILES['license']['size']];
}

$certPath = null; $certMeta = [];
if (!empty($_FILES['certifications']['name'])) {
    $c = Security::validateUpload($_FILES['certifications'], ['pdf','jpg','jpeg','png'], $maxBytes);
    if (!$c['ok']) json_response(['success' => false, 'errors' => ['certifications' => $c['error']]], 422);
    $certPath = Security::storeUpload($_FILES['certifications'], $c['ext'], 'professionals');
    $certMeta = ['name' => $_FILES['certifications']['name'], 'mime' => $c['mime'], 'size' => (int)$_FILES['certifications']['size']];
}

$ref = Security::generateReference('REF');
$pdo = DB::pdo();
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("
        INSERT INTO professionals
            (user_id, reference_number, first_name, last_name, email, phone, address, location, coordinates,
             profession, other_profession, specialization, years_experience, license_number,
             work_type, shift_type, preferred_location, available_from, status)
        VALUES
            (:user, :ref, :first, :last, :email, :phone, :addr, :loc, :coords,
             :prof, :other, :spec, :years, :lic,
             :wt, :st, :ploc, :avail, 'pending')
    ");
    $stmt->execute([
        ':user'   => current_user()['id'] ?? null,
        ':ref'    => $ref,
        ':first'  => $first,
        ':last'   => $last,
        ':email'  => $email,
        ':phone'  => $phone,
        ':addr'   => trim((string)($_POST['address'] ?? '')) ?: null,
        ':loc'    => trim((string)($_POST['location'] ?? '')) ?: null,
        ':coords' => trim((string)($_POST['coordinates'] ?? '')) ?: null,
        ':prof'   => $prof,
        ':other'  => trim((string)($_POST['otherProfession'] ?? '')) ?: null,
        ':spec'   => trim((string)($_POST['specialization'] ?? '')) ?: null,
        ':years'  => $years,
        ':lic'    => trim((string)($_POST['licenseNumber'] ?? '')) ?: null,
        ':wt'     => json_encode((array)($_POST['workType']  ?? []), JSON_UNESCAPED_UNICODE),
        ':st'     => json_encode((array)($_POST['shiftType'] ?? []), JSON_UNESCAPED_UNICODE),
        ':ploc'   => trim((string)($_POST['preferredLocation'] ?? '')) ?: null,
        ':avail'  => trim((string)($_POST['startDate'] ?? '')) ?: null,
    ]);
    $profId = (int)$pdo->lastInsertId();

    $insDoc = $pdo->prepare("
        INSERT INTO professional_documents
            (professional_id, kind, storage_path, original_name, mime_type, size_bytes)
        VALUES (:p, :kind, :path, :name, :mime, :sz)
    ");
    $insDoc->execute([':p' => $profId, ':kind' => 'resume', ':path' => $resumePath,
                      ':name' => $resumeMeta['name'], ':mime' => $resumeMeta['mime'], ':sz' => $resumeMeta['size']]);
    if ($licensePath) {
        $insDoc->execute([':p' => $profId, ':kind' => 'license', ':path' => $licensePath,
                          ':name' => $licenseMeta['name'], ':mime' => $licenseMeta['mime'], ':sz' => $licenseMeta['size']]);
    }
    if ($certPath) {
        $insDoc->execute([':p' => $profId, ':kind' => 'certification', ':path' => $certPath,
                          ':name' => $certMeta['name'], ':mime' => $certMeta['mime'], ':sz' => $certMeta['size']]);
    }
    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    error_log('join-application failed: ' . $e->getMessage());
    json_response(['success' => false, 'error' => 'Could not save application. Please try again.'], 500);
}

json_response([
    'success'   => true,
    'reference' => $ref,
    'message'   => 'Application received. Screening typically takes 3–5 business days.',
]);
