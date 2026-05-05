<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'POST only'], 405);
}

Security::requireCsrf();
Security::rateLimit('contact', 5);

$name    = trim((string)($_POST['name']    ?? ''));
$email   = trim((string)($_POST['email']   ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

$errors = [];
if (mb_strlen($name) < 2)                          $errors['name']    = 'Name too short.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))    $errors['email']   = 'Invalid email.';
if (mb_strlen($message) < 10 || mb_strlen($message) > 5000) $errors['message'] = 'Message must be 10–5000 chars.';
if ($errors) json_response(['success' => false, 'errors' => $errors], 422);

DB::pdo()->prepare("
    INSERT INTO contact_messages (name, email, message, user_agent, ip)
    VALUES (:n, :e, :m, :ua, :ip)
")->execute([
    ':n'  => $name,
    ':e'  => $email,
    ':m'  => $message,
    ':ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
    ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
]);

json_response(['success' => true, 'message' => 'Thanks — we will respond within 24 hours.']);
