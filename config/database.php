<?php
declare(strict_types=1);

require_once __DIR__ . '/env.php';

final class DB
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) return self::$pdo;

        $host    = Env::get('DB_HOST', '127.0.0.1');
        $port    = Env::get('DB_PORT', '3306');
        $name    = Env::require('DB_NAME');
        $user    = Env::require('DB_USER');
        $pass    = Env::get('DB_PASS', '') ?? '';
        $charset = Env::get('DB_CHARSET', 'utf8mb4');

        $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=$charset";

        try {
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
            ]);
        } catch (PDOException $e) {
            error_log('DB connect failed: ' . $e->getMessage());
            throw new RuntimeException('Database unavailable');
        }

        return self::$pdo;
    }
}
