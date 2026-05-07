<?php
declare(strict_types=1);

require_once __DIR__ . '/env.php';

final class Security
{
    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function rotateCsrf(): void
    {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    public static function verifyCsrf(?string $token): bool
    {
        if (!is_string($token) || empty($_SESSION['_csrf'])) return false;
        return hash_equals($_SESSION['_csrf'], $token);
    }

    public static function requireCsrf(): void
    {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!self::verifyCsrf($token)) {
            error_log('CSRF rejected from ' . ($_SERVER['REMOTE_ADDR'] ?? '?'));
            http_response_code(419);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid security token. Refresh and retry.']);
            exit;
        }
    }

    public static function rateLimit(string $route, int $maxPerMinute = 10): void
    {
        $ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $pdo = DB::pdo();

        $pdo->prepare("DELETE FROM rate_limits WHERE created_at < (NOW() - INTERVAL 1 MINUTE)")->execute();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM rate_limits WHERE ip = :ip AND route = :route");
        $stmt->execute([':ip' => $ip, ':route' => $route]);

        if ((int) $stmt->fetchColumn() >= $maxPerMinute) {
            http_response_code(429);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Too many requests. Try again in a minute.']);
            exit;
        }

        $pdo->prepare("INSERT INTO rate_limits (ip, route, created_at) VALUES (:ip, :route, NOW())")
            ->execute([':ip' => $ip, ':route' => $route]);
    }

    public static function encrypt(?string $plaintext): ?string
    {
        if ($plaintext === null || $plaintext === '') return null;

        $iv  = random_bytes(12);
        $tag = '';
        $cipher = openssl_encrypt($plaintext, 'aes-256-gcm', self::appKey(), OPENSSL_RAW_DATA, $iv, $tag, '', 16);

        if ($cipher === false) {
            throw new RuntimeException('Encryption failed');
        }
        return base64_encode($iv . $tag . $cipher);
    }

    public static function decrypt(?string $payload): ?string
    {
        if ($payload === null || $payload === '') return null;

        $raw = base64_decode($payload, true);
        if ($raw === false || strlen($raw) < 28) return null;

        $iv     = substr($raw, 0, 12);
        $tag    = substr($raw, 12, 16);
        $cipher = substr($raw, 28);

        $plain = openssl_decrypt($cipher, 'aes-256-gcm', self::appKey(), OPENSSL_RAW_DATA, $iv, $tag);
        return $plain === false ? null : $plain;
    }

    private static function appKey(): string
    {
        $key = base64_decode(Env::require('APP_KEY'), true);
        if ($key === false || strlen($key) !== 32) {
            throw new RuntimeException('APP_KEY must be 32 random bytes, base64-encoded');
        }
        return $key;
    }

    public static function validateUpload(array $file, array $allowedExt, int $maxBytes): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Upload failed.'];
        }
        if (($file['size'] ?? 0) <= 0 || $file['size'] > $maxBytes) {
            return ['ok' => false, 'error' => 'File too large or empty.'];
        }

        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            return ['ok' => false, 'error' => 'Unsupported file type.'];
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) ?: 'application/octet-stream';
        $allowedMimes = [
            'pdf'  => ['application/pdf'],
            'doc'  => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'jpg'  => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png'  => ['image/png'],
        ];
        if (!isset($allowedMimes[$ext]) || !in_array($mime, $allowedMimes[$ext], true)) {
            return ['ok' => false, 'error' => 'File content does not match its extension.'];
        }
        return ['ok' => true, 'mime' => $mime, 'ext' => $ext];
    }

    public static function storeUpload(array $file, string $ext, string $subdir): string
    {
        $base = realpath(__DIR__ . '/../storage/uploads');
        if ($base === false) {
            throw new RuntimeException('Upload storage missing');
        }

        $dir = $base . DIRECTORY_SEPARATOR . trim($subdir, '/\\');
        if (!is_dir($dir)) mkdir($dir, 0775, true);

        $name = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest = $dir . DIRECTORY_SEPARATOR . $name;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('Could not store upload');
        }
        return trim($subdir, '/\\') . '/' . $name;
    }

    public static function generateReference(string $prefix = 'REF'): string
    {
        return $prefix . '-' . strtoupper(bin2hex(random_bytes(4)));
    }
}
