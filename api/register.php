<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'POST only'], 405);
}

Security::requireCsrf();
Security::rateLimit('auth-register', 5);

$email    = trim((string)($_POST['email']     ?? ''));
$password = (string)($_POST['password']       ?? '');
$first    = trim((string)($_POST['firstName'] ?? ''));
$last     = trim((string)($_POST['lastName']  ?? ''));
$role     = in_array($_POST['role'] ?? '', ['client','professional','facility'], true)
          ? $_POST['role'] : 'client';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'error' => 'Invalid email.'], 422);
}
if (mb_strlen($password) < 10) {
    json_response(['success' => false, 'error' => 'Password must be at least 10 characters.'], 422);
}
if (mb_strlen($first) < 2 || mb_strlen($last) < 2) {
    json_response(['success' => false, 'error' => 'Please enter your full name.'], 422);
}

try {
    $hash = password_hash($password . (Env::get('APP_PEPPER', '') ?? ''), PASSWORD_ARGON2ID);
    $pdo  = DB::pdo();
    $pdo->prepare("
        INSERT INTO users (email, password_hash, first_name, last_name, role)
        VALUES (:e, :p, :f, :l, :r)
    ")->execute([':e' => $email, ':p' => $hash, ':f' => $first, ':l' => $last, ':r' => $role]);

    // Auto-login.
    $id = (int)$pdo->lastInsertId();
    session_regenerate_id(true);
    Security::rotateCsrf();
    $_SESSION['user'] = [
        'id'    => $id,
        'email' => $email,
        'role'  => $role,
        'name'  => trim($first . ' ' . $last),
    ];

    json_response([
        'success'  => true,
        'message'  => 'Account created. Welcome to Matendo.',
        'redirect' => url('auth/dashboard.php'),
    ]);
} catch (PDOException $e) {
    if (($e->errorInfo[1] ?? 0) === 1062) {
        json_response(['success' => false, 'error' => 'Email already registered. Try signing in.'], 409);
    }
    error_log('register failed: ' . $e->getMessage());
    json_response(['success' => false, 'error' => 'Could not create account.'], 500);
}
