<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'POST only'], 405);
}

Security::requireCsrf();
Security::rateLimit('newsletter', 5);

$email = trim((string)($_POST['email'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'error' => 'Invalid email.'], 422);
}

DB::pdo()->prepare("
    INSERT INTO newsletter_subscriptions (email)
    VALUES (:e)
    ON DUPLICATE KEY UPDATE unsubscribed_at = NULL
")->execute([':e' => $email]);

json_response(['success' => true, 'message' => 'Subscribed. Watch your inbox for confirmation.']);
