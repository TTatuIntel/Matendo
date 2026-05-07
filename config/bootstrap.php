<?php
declare(strict_types=1);

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/security.php';

Env::load(__DIR__ . '/../.env');

$debug = Env::bool('APP_DEBUG', false);
ini_set('display_errors',         $debug ? '1' : '0');
ini_set('display_startup_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');

$logDir = __DIR__ . '/../storage/logs';
if (!is_dir($logDir)) @mkdir($logDir, 0775, true);
ini_set('error_log', $logDir . '/php-error.log');
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('matendosid');
    session_start();
}

if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(self), microphone=(), camera=()');
}

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf(): string         { return Security::csrfToken(); }
function csrf_field(): string   { return '<input type="hidden" name="_csrf" value="' . e(csrf()) . '">'; }

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function send_mail(string $to, string $subject, string $body, ?string $replyTo = null): bool
{
    $from     = Env::get('MAIL_FROM',      'info@matendohealth.com') ?? 'info@matendohealth.com';
    $fromName = Env::get('MAIL_FROM_NAME', 'Matendo Health')         ?? 'Matendo Health';

    $headers = [
        'From'         => sprintf('%s <%s>', $fromName, $from),
        'Reply-To'     => $replyTo ?: $from,
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/plain; charset=utf-8',
        'X-Mailer'     => 'Matendo/1.0',
    ];
    $headerLines = [];
    foreach ($headers as $k => $v) $headerLines[] = "$k: $v";

    if (Env::bool('APP_DEBUG', false)) {
        @file_put_contents(
            __DIR__ . '/../storage/logs/mail.log',
            sprintf("[%s] To: %s\nSubject: %s\n%s\n\n%s\n---\n",
                date('c'), $to, $subject, implode("\n", $headerLines), $body),
            FILE_APPEND
        );
        return true;
    }

    $ok = @mail($to, $subject, $body, implode("\r\n", $headerLines));
    if (!$ok) error_log("send_mail failed: to={$to} subject={$subject}");
    return $ok;
}

function contact_email(): string
{
    return Env::get('CONTACT_EMAIL', 'info@matendohealth.com') ?? 'info@matendohealth.com';
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_auth(?string $role = null): array
{
    $u = current_user();
    if (!$u) redirect(url('auth/login.php'));

    if ($role !== null && ($u['role'] ?? null) !== $role) {
        http_response_code(403);
        exit('Forbidden');
    }
    return $u;
}

function base_path(): string
{
    static $base = null;
    if ($base !== null) return $base;

    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $dir    = rtrim(str_replace('\\', '/', dirname($script)), '/');

    foreach (['/auth', '/api', '/legal', '/public'] as $sub) {
        if (str_ends_with($dir, $sub)) {
            $dir = substr($dir, 0, -strlen($sub));
            break;
        }
    }
    return $base = $dir === '' ? '' : $dir;
}

function asset(string $path): string
{
    $clean = ltrim($path, '/');
    $url   = base_path() . '/' . $clean;

    if (preg_match('~\.(css|js)$~i', $clean)) {
        $disk = __DIR__ . '/../' . $clean;
        if (is_file($disk)) $url .= '?v=' . filemtime($disk);
    }
    return $url;
}

function url(string $path = ''): string
{
    $path = ltrim($path, '/');

    if ($path === '' || preg_match('~^index\.php([?#].*)?$~', $path, $m)) {
        return base_path() . '/' . ($m[1] ?? '');
    }
    if (!str_starts_with($path, 'api/')) {
        $path = preg_replace('~\.php(?=$|[?#])~', '', $path, 1);
    }
    return base_path() . '/' . $path;
}
