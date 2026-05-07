<?php
declare(strict_types=1);

final class Env
{
    private static bool $loaded = false;

    public static function load(string $path): void
    {
        if (self::$loaded) return;
        self::$loaded = true;

        if (!is_readable($path)) return;

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) continue;

            [$k, $v] = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v);

            if ((str_starts_with($v, '"') && str_ends_with($v, '"')) ||
                (str_starts_with($v, "'") && str_ends_with($v, "'"))) {
                $v = substr($v, 1, -1);
            }

            $_ENV[$k] = $v;
            putenv("$k=$v");
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $v = $_ENV[$key] ?? getenv($key);
        if ($v === false || $v === null || $v === '') return $default;
        return (string) $v;
    }

    public static function require(string $key): string
    {
        $v = self::get($key);
        if ($v === null) {
            throw new RuntimeException("Missing required env var: $key");
        }
        return $v;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $v = self::get($key);
        if ($v === null) return $default;
        return in_array(strtolower($v), ['1', 'true', 'yes', 'on'], true);
    }
}
