<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'POST only'], 405);
}

Security::requireCsrf();
Security::rateLimit('auth-login', 10);

$email    = trim((string)($_POST['email']    ?? ''));
$password = (string)($_POST['password'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    json_response(['success' => false, 'error' => 'Please enter your email and password.'], 422);
}

$stmt = DB::pdo()->prepare("SELECT * FROM users WHERE email = :e AND status = 'active' LIMIT 1");
$stmt->execute([':e' => $email]);
$u = $stmt->fetch();

if ($u && !empty($u['locked_until']) && strtotime($u['locked_until']) > time()) {
    json_response(['success' => false, 'error' => 'Account temporarily locked. Try again later.'], 423);
}

if (!$u || !password_verify($password . (Env::get('APP_PEPPER', '') ?? ''), $u['password_hash'])) {
    if ($u) {
        DB::pdo()->prepare("UPDATE users SET failed_logins = failed_logins + 1,
            locked_until = IF(failed_logins + 1 >= 5, DATE_ADD(NOW(), INTERVAL 15 MINUTE), locked_until)
            WHERE id = :id")->execute([':id' => $u['id']]);
    }
    json_response(['success' => false, 'error' => 'Email or password is incorrect.'], 401);
}

session_regenerate_id(true);
Security::rotateCsrf();
$_SESSION['user'] = [
    'id'    => (int)$u['id'],
    'email' => $u['email'],
    'role'  => $u['role'],
    'name'  => trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')),
];
DB::pdo()->prepare("UPDATE users SET last_login_at = NOW(), failed_logins = 0 WHERE id = :id")
    ->execute([':id' => $u['id']]);

json_response([
    'success'  => true,
    'message'  => 'Signed in. Loading your dashboard…',
    'redirect' => url('auth/dashboard.php'),
]);
