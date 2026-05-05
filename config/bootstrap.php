<?php
declare(strict_types=1);

/**
 * Single entry point. Every PHP page (and every API handler) requires this file
 * before doing anything else. It:
 *   - loads .env
 *   - configures error handling (no leaks to clients)
 *   - starts a hardened session
 *   - exposes helpers: csrf(), csrf_field(), e(), redirect(), json_response()
 */

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/security.php';

Env::load(__DIR__ . '/../.env');

// --- error handling -------------------------------------------------------
$debug = Env::bool('APP_DEBUG', false);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('display_startup_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
$logDir = __DIR__ . '/../storage/logs';
if (!is_dir($logDir)) { @mkdir($logDir, 0775, true); }
ini_set('error_log', $logDir . '/php-error.log');
error_reporting(E_ALL);

// --- session hardening ----------------------------------------------------
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

// --- security headers -----------------------------------------------------
// .htaccess sets the canonical headers; these are a defence-in-depth fallback.
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(self), microphone=(), camera=()');
}

// --- helpers --------------------------------------------------------------
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf(): string
{
    return Security::csrfToken();
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf()) . '">';
}

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

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_auth(string $role = null): array
{
    $u = current_user();
    if (!$u) {
        redirect(url('auth/login.php'));
    }
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
    // Walk up from SCRIPT_NAME until we find the project root marker (.env / composer / index.php).
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    // Strip the filename to get the directory. dirname() on Windows can return a
    // backslash, so normalize separators again before trimming.
    $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');
    // Auth/api/legal pages live one level deep — strip those subfolders for asset/url roots.
    foreach (['/auth', '/api', '/legal'] as $sub) {
        if (str_ends_with($dir, $sub)) { $dir = substr($dir, 0, -strlen($sub)); break; }
    }
    return $base = $dir === '' ? '' : $dir;
}

function asset(string $path): string
{
    return base_path() . '/' . ltrim($path, '/');
}

function url(string $path = ''): string
{
    return base_path() . '/' . ltrim($path, '/');
}
